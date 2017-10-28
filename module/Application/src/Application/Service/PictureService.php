<?php

namespace Application\Service;

use Exception;

use geoPHP;
use Point;

use Zend\Db\Sql;

use Autowp\Comments\CommentsService;
use Autowp\Image;

use Application\DuplicateFinder;
use Application\ExifGPSExtractor;
use Application\Model\Picture;
use Application\Model\PictureItem;
use Application\Model\UserPicture;

class PictureService
{
    const QUEUE_LIFETIME = 7; // days

    /**
     * @var Picture
     */
    private $picture;

    /**
     * @var CommentsService
     */
    private $comments;

    /**
     * @var Image\Storage
     */
    private $imageStorage;

    /**
     * @var TelegramService
     */
    private $telegram;

    /**
     * @var DuplicateFinder
     */
    private $duplicateFinder;

    /**
     * @var PictureItem
     */
    private $pictureItem;

    /**
     * @var UserPicture
     */
    private $userPicture;

    public function __construct(
        Picture $picture,
        CommentsService $comments,
        Image\Storage $imageStorage,
        TelegramService $telegram,
        DuplicateFinder $duplicateFinder,
        PictureItem $pictureItem,
        UserPicture $userPicture
    ) {
        $this->picture = $picture;
        $this->comments = $comments;
        $this->imageStorage = $imageStorage;
        $this->telegram = $telegram;
        $this->duplicateFinder = $duplicateFinder;
        $this->pictureItem = $pictureItem;
        $this->userPicture = $userPicture;
    }

    public function clearQueue()
    {
        $select = $this->picture->getTable()->getSql()->select();

        $select->where([
            'status' => Picture::STATUS_REMOVING,
            new Sql\Predicate\Expression(
                '(removing_date is null OR (removing_date < DATE_SUB(CURDATE(), INTERVAL ? DAY) ))',
                [self::QUEUE_LIFETIME]
            ),
        ])->limit(1000);

        $pictures = $this->picture->getTable()->selectWith($select);

        $count = count($pictures);

        if ($count) {
            print sprintf("Removing %d pictures\n", $count);

            foreach ($pictures as $picture) {
                $this->pictureItem->setPictureItems($picture['id'], PictureItem::PICTURE_CONTENT, []);
                $this->pictureItem->setPictureItems($picture['id'], PictureItem::PICTURE_AUTHOR, []);

                $this->comments->deleteTopic(
                    \Application\Comments::PICTURES_TYPE_ID,
                    $picture['id']
                );

                $imageId = $picture['image_id'];
                if ($imageId) {
                    $this->picture->getTable()->delete([
                        'id = ?' => $picture['id']
                    ]);

                    $this->imageStorage->removeImage($imageId);
                } else {
                    print "Brokern image `{$picture['id']}`. Skip\n";
                }
            }
        } else {
            print "Nothing to clear\n";
        }
    }

    public function addPictureFromFile(
        string $path,
        int $userId,
        string $remoteAddr,
        array $itemIds,
        int $perspectiveId,
        int $replacePictureId,
        string $note
    ) {
        list ($width, $height, $imageType) = getimagesize($path);
        $width = (int)$width;
        $height = (int)$height;
        if ($width <= 0) {
            throw new Exception("Width <= 0");
        }

        if ($height <= 0) {
            throw new Exception("Height <= 0");
        }

        // generate filename
        switch ($imageType) {
            case IMAGETYPE_JPEG:
            case IMAGETYPE_PNG:
                break;
            default:
                throw new Exception("Unsupported image type");
        }
        $ext = image_type_to_extension($imageType, false);

        $imageId = $this->imageStorage->addImageFromFile($path, 'picture', [
            'extension' => $ext,
            'pattern'   => 'autowp_' . rand()
        ]);

        $image = $this->imageStorage->getImage($imageId);
        $fileSize = $image->getFileSize();

        $resolution = $this->imageStorage->getImageResolution($imageId);

        // add record to db
        $this->picture->getTable()->insert([
            'image_id'      => $imageId,
            'width'         => $width,
            'height'        => $height,
            'dpi_x'         => $resolution ? $resolution['x'] : null,
            'dpi_y'         => $resolution ? $resolution['y'] : null,
            'owner_id'      => $userId,
            'add_date'      => new Sql\Expression('NOW()'),
            'filesize'      => $fileSize,
            'status'        => Picture::STATUS_INBOX,
            'removing_date' => null,
            'ip'            => inet_pton($remoteAddr),
            'identity'      => $this->picture->generateIdentity(),
            'replace_picture_id' => $replacePictureId ? $replacePictureId : null,
        ]);

        $pictureId = $this->picture->getTable()->getLastInsertValue();

        $picture = $this->picture->getRow(['id' => (int)$pictureId]);

        if ($itemIds) {
            $this->pictureItem->setPictureItems($pictureId, PictureItem::PICTURE_CONTENT, $itemIds);
            if ($perspectiveId && count($itemIds) == 1) {
                $this->pictureItem->setProperties($pictureId, $itemIds[0], PictureItem::PICTURE_CONTENT, [
                    'perspective' => $perspectiveId
                ]);
            }
        }

        // increment uploads counter
        $this->userPicture->incrementUploads($userId);

        // rename file to new
        $this->imageStorage->changeImageName($imageId, [
            'pattern' => $this->picture->getFileNamePattern($picture)
        ]);

        // add comment
        if ($note) {
            $this->comments->add([
                'typeId'             => \Application\Comments::PICTURES_TYPE_ID,
                'itemId'             => $pictureId,
                'parentId'           => null,
                'authorId'           => $userId,
                'message'            => $note,
                'ip'                 => $remoteAddr,
                'moderatorAttention' => \Autowp\Comments\Attention::NONE
            ]);
        }

        $this->comments->subscribe(
            \Application\Comments::PICTURES_TYPE_ID,
            $pictureId,
            $userId
        );

        // read gps
        $exif = $this->imageStorage->getImageEXIF($imageId);
        $extractor = new ExifGPSExtractor();
        $gps = $extractor->extract($exif);
        if ($gps !== false) {
            geoPHP::version();
            $point = new Point($gps['lng'], $gps['lat']);

            $this->picture->getTable()->update([
                'point' => new Sql\Expression('GeomFromWKB(?)', [$point->out('wkb')])
            ], [
                'id' => $pictureId
            ]);
        }

        $formatRequest = $this->picture->getFormatRequest($picture);
        $this->imageStorage->getFormatedImage($formatRequest, 'picture-thumb');
        $this->imageStorage->getFormatedImage($formatRequest, 'picture-medium');
        $this->imageStorage->getFormatedImage($formatRequest, 'picture-gallery-full');

        // index
        $this->duplicateFinder->indexImage($pictureId, $path);

        $this->telegram->notifyInbox($pictureId);

        return $picture;
    }
}
