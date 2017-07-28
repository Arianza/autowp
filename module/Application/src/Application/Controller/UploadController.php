<?php

namespace Application\Controller;

use Exception;

use geoPHP;
use Point;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

use Autowp\Comments;

use Application\DuplicateFinder;
use Application\ExifGPSExtractor;
use Application\Form\Upload as UploadForm;
use Application\Model\Brand as BrandModel;
use Application\Model\DbTable;
use Application\Model\ItemParent;
use Application\Model\Perspective;
use Application\Model\PictureItem;
use Application\Model\UserPicture;
use Application\Service\TelegramService;

use Zend_Db_Expr;
use Application\Model\Item;

class UploadController extends AbstractActionController
{
    private $partial;

    /**
     * @var TelegramService
     */
    private $telegram;

    /**
     * @var PictureItem
     */
    private $pictureItem;

    /**
     * @var DuplicateFinder
     */
    private $duplicateFinder;

    /**
     * @var Comments\CommentsService
     */
    private $comments;

    /**
     * @var UserPicture
     */
    private $userPicture;

    /**
     * @var Perspective
     */
    private $perspective;

    /**
     * @var ItemParent
     */
    private $itemParent;

    /**
     * @var DbTable\Picture
     */
    private $pictureTable;

    public function __construct(
        $partial,
        TelegramService $telegram,
        PictureItem $pictureItem,
        DuplicateFinder $duplicateFinder,
        Comments\CommentsService $comments,
        UserPicture $userPicture,
        Perspective $perspective,
        ItemParent $itemParent,
        DbTable\Picture $pictureTable
    ) {
        $this->partial = $partial;
        $this->telegram = $telegram;
        $this->pictureItem = $pictureItem;
        $this->duplicateFinder = $duplicateFinder;
        $this->comments = $comments;
        $this->userPicture = $userPicture;
        $this->perspective = $perspective;
        $this->itemParent = $itemParent;
        $this->pictureTable = $pictureTable;
    }

    public function onlyRegisteredAction()
    {
    }

    public function indexAction()
    {
        $user = $this->user()->get();

        if (! $user || $user->deleted) {
            return $this->forward()->dispatch(self::class, [
                'action' => 'only-registered'
            ]);
        }

        $replace = $this->params('replace');
        $replacePicture = false;
        if ($replace) {
            $replacePicture = $this->pictureTable->fetchRow([
                'identity = ?' => $replace
            ]);
        }

        $perspectiveId = null;

        if ($replacePicture) {
            $itemIds = $this->pictureItem->getPictureItems($replacePicture->id);
        } else {
            $itemId = (int)$this->params('item_id');
            $itemIds = $itemId ? [$itemId] : [];
            $perspectiveId = (int)$this->params('perspective_id');
        }

        $selected = false;

        $itemTable = new DbTable\Item();
        $items = $itemTable->find($itemIds);
        $names = [];
        foreach ($items as $item) {
            $selected = true;
            $names[] = $this->car()->formatName($item, $this->language());
        }
        $selectedName = implode(', ', $names);

        $form = null;

        if ($selected) {
            $form = new UploadForm(null, [
                'multipleFiles' => ! $replacePicture,
            ]);
            $form->setAttribute('action', $this->url()->fromRoute('upload/params', [
                'action' => 'send'
            ], [], true));
        }

        $perspectives = $this->perspective->getArray();

        foreach ($perspectives as &$perspective) {
            $perspective['name'] = $this->translate($perspective['name']);
        }
        unset($perspective);

        return [
            'form'         => $form,
            'selected'     => $selected,
            'selectedName' => $selectedName,
            'perspectives' => $perspectives
        ];
    }

    private function saveUpload($form, $itemIds, $perspectiveId, $replacePicture)
    {
        $user = $this->user()->get();

        $values = $form->getData();

        $tempFilePaths = [];
        $data = $form->get('picture')->getValue();

        if ($form->get('picture')->getAttribute('multiple')) {
            foreach ($data as $file) {
                $tempFilePaths[] = $file['tmp_name'];
            }
        } else {
            $tempFilePaths[] = $data['tmp_name'];
        }

        $result = [];

        foreach ($tempFilePaths as $tempFilePath) {
            list ($width, $height, $imageType) = getimagesize($tempFilePath);
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

            $imageId = $this->imageStorage()->addImageFromFile($tempFilePath, 'picture', [
                'extension' => $ext,
                'pattern'   => 'autowp_' . rand()
            ]);

            $image = $this->imageStorage()->getImage($imageId);
            $fileSize = $image->getFileSize();

            $resolution = $this->imageStorage()->getImageResolution($imageId);

            // add record to db
            $picture = $this->pictureTable->createRow([
                'image_id'      => $imageId,
                'width'         => $width,
                'height'        => $height,
                'dpi_x'         => $resolution ? $resolution['x'] : null,
                'dpi_y'         => $resolution ? $resolution['y'] : null,
                'owner_id'      => $user ? $user->id : null,
                'add_date'      => new Zend_Db_Expr('NOW()'),
                'filesize'      => $fileSize,
                'status'        => DbTable\Picture::STATUS_INBOX,
                'removing_date' => null,
                'ip'            => inet_pton($this->getRequest()->getServer('REMOTE_ADDR')),
                'identity'      => $this->pictureTable->generateIdentity(),
                'replace_picture_id' => $replacePicture ? $replacePicture->id : null,
            ]);
            $picture->save();

            if ($itemIds) {
                $this->pictureItem->setPictureItems($picture->id, $itemIds);
                if ($perspectiveId && count($itemIds) == 1) {
                    $this->pictureItem->setProperties($picture->id, $itemIds[0], [
                        'perspective' => $perspectiveId
                    ]);
                }
            }

            // increment uploads counter
            if ($user) {
                $this->userPicture->incrementUploads($user['id']);
            }

            // rename file to new
            $this->imageStorage()->changeImageName($picture->image_id, [
                'pattern' => $this->pictureTable->getFileNamePattern($picture)
            ]);

            // add comment
            if ($values['note']) {
                $this->comments->add([
                    'typeId'             => \Application\Comments::PICTURES_TYPE_ID,
                    'itemId'             => $picture->id,
                    'parentId'           => null,
                    'authorId'           => $user->id,
                    'message'            => $values['note'],
                    'ip'                 => $this->getRequest()->getServer('REMOTE_ADDR'),
                    'moderatorAttention' => Comments\Attention::NONE
                ]);
            }

            $this->comments->subscribe(
                \Application\Comments::PICTURES_TYPE_ID,
                $picture->id,
                $user['id']
            );

            // read gps
            $exif = $this->imageStorage()->getImageEXIF($picture->image_id);
            $extractor = new ExifGPSExtractor();
            $gps = $extractor->extract($exif);
            if ($gps !== false) {
                geoPHP::version();
                $point = new Point($gps['lng'], $gps['lat']);
                $db = $this->pictureTable->getAdapter();
                $pointExpr = new Zend_Db_Expr($db->quoteInto('GeomFromWKB(?)', $point->out('wkb')));

                $picture->point = $pointExpr;
                $picture->save();
            }

            $formatRequest = $this->pictureTable->getFormatRequest($picture);
            $this->imageStorage()->getFormatedImage($formatRequest, 'picture-thumb');
            $this->imageStorage()->getFormatedImage($formatRequest, 'picture-medium');
            $this->imageStorage()->getFormatedImage($formatRequest, 'picture-gallery-full');

            // index
            $this->duplicateFinder->indexImage($picture->id, $tempFilePath);

            $this->telegram->notifyInbox($picture->id);

            $result[] = $picture;
        }

        return $result;
    }

    public function selectBrandAction()
    {
        $brandModel = new BrandModel();

        $language = $this->language();

        $brand = $brandModel->getBrandById($this->params('brand_id'), $language);
        if ($brand) {
            return $this->forward()->dispatch(self::class, [
                'action'   => 'select-in-brand',
                'brand_id' => $brand['id']
            ]);
        }

        $rows = $brandModel->getList($language, function () {
        });

        return [
            'brands' => $rows
        ];
    }

    public function selectInBrandAction()
    {
        $brandModel = new BrandModel();

        $language = $this->language();

        $brand = $brandModel->getBrandById($this->params('brand_id'), $language);

        if (! $brand) {
            return $this->forward()->dispatch(self::class, [
                'action' => 'select-brand'
            ]);
        }

        $itemTable = new DbTable\Item();

        $haveConcepts = (bool)$itemTable->fetchRow(
            $itemTable->select(true)
                ->join('item_parent_cache', 'item.id = item_parent_cache.item_id', null)
                ->where('item_parent_cache.parent_id = ?', $brand['id'])
                ->where('item.is_concept')
        );

        $db = $itemTable->getAdapter();

        $rows = $db->fetchAll(
            $db->select()
                ->from('item', [
                    'item.id',
                    'name' => 'if(item_language.name, item_language.name, item.name)',
                    'item.begin_model_year', 'item.end_model_year',
                    'spec' => 'spec.short_name',
                    'spec_full' => 'spec.name',
                    'item.body', 'item.today',
                    'item.begin_year', 'item.end_year',
                    'item.is_group'
                ])
                ->joinLeft('item_language', 'item.id = item_language.item_id and item_language.language = :lang', null)
                ->joinLeft('spec', 'item.spec_id = spec.id', null)
                ->join('item_parent', 'item.id = item_parent.item_id', null)
                ->where('item_parent.parent_id = ?', $brand['id'])
                ->where('NOT item.is_concept')
                ->where('item.item_type_id = ?', Item::VEHICLE)
                ->order([
                    'item.name',
                    'item.begin_year',
                    'item.end_year',
                    'item.begin_model_year',
                    'item.end_model_year'
                ])
                ->bind([
                    'lang' => $this->language()
                ])
        );
        $vehicles = $this->prepareCars($rows);

        $rows = $db->fetchAll(
            $db->select()
                ->from('item', [
                    'item.id',
                    'name' => 'if(item_language.name, item_language.name, item.name)',
                    'item.begin_model_year', 'item.end_model_year',
                    'spec' => 'spec.short_name',
                    'spec_full' => 'spec.name',
                    'item.body', 'item.today',
                    'item.begin_year', 'item.end_year',
                    'item.is_group'
                ])
                ->joinLeft('item_language', 'item.id = item_language.item_id and item_language.language = :lang', null)
                ->joinLeft('spec', 'item.spec_id = spec.id', null)
                ->join('item_parent', 'item.id = item_parent.item_id', null)
                ->where('item_parent.parent_id = ?', $brand['id'])
                ->where('item.item_type_id = ?', Item::ENGINE)
                ->order([
                    'item.name',
                    'item.begin_year',
                    'item.end_year',
                    'item.begin_model_year',
                    'item.end_model_year'
                ])
                ->bind([
                    'lang' => $this->language()
                ])
        );
        $engines = $this->prepareCars($rows);

        return [
            'brand'        => $brand,
            'vehicles'     => $vehicles,
            'engines'      => $engines,
            'haveConcepts' => $haveConcepts,
            'conceptsUrl'  => $this->url()->fromRoute('upload/params', [
                'action' => 'concepts',
            ], [], true),
        ];
    }

    private function prepareCars($rows)
    {
        $items = [];
        foreach ($rows as $row) {
            $items[] = [
                'begin_model_year' => $row['begin_model_year'],
                'end_model_year'   => $row['end_model_year'],
                'spec'             => $row['spec'],
                'spec_full'        => $row['spec_full'],
                'body'             => $row['body'],
                'name'             => $row['name'],
                'begin_year'       => $row['begin_year'],
                'end_year'         => $row['end_year'],
                'today'            => $row['today'],
                'url'  => $this->url()->fromRoute('upload/params', [
                    'action'  => 'index',
                    'item_id' => $row['id']
                ], [], true),
                'haveChilds' => $this->itemParent->hasChildItems($row['id']),
                'isGroup'    => $row['is_group'],
                'type'       => null,
                'loadUrl'    => $this->url()->fromRoute('upload/params', [
                    'action'  => 'car-childs',
                    'item_id' => $row['id']
                ], [], true),
            ];
        }

        return $items;
    }

    private function prepareCarParentRows($rows)
    {
        $items = [];
        foreach ($rows as $row) {
            $items[] = [
                'begin_model_year' => $row['begin_model_year'],
                'end_model_year'   => $row['end_model_year'],
                'spec'             => $row['spec'],
                'spec_full'        => $row['spec_full'],
                'body'             => $row['body'],
                'name'             => $row['name'],
                'begin_year'       => $row['begin_year'],
                'end_year'         => $row['end_year'],
                'today'            => $row['today'],
                'url'  => $this->url()->fromRoute('upload/params', [
                    'action'  => 'index',
                    'item_id' => $row['id']
                ], [], true),
                'haveChilds' => $this->itemParent->hasChildItems($row['id']),
                'isGroup'    => $row['is_group'],
                'type'       => $row['type'],
                'loadUrl'    => $this->url()->fromRoute('upload/params', [
                    'action'  => 'car-childs',
                    'item_id' => $row['id']
                ], [], true),
            ];
        }

        return $items;
    }

    public function carChildsAction()
    {
        $user = $this->user()->get();
        if (! $user) {
            return $this->forward()->dispatch(self::class, [
                'action' => 'only-registered'
            ]);
        }

        $itemTable = new DbTable\Item();

        $car = $itemTable->find($this->params('item_id'))->current();
        if (! $car) {
            return $this->notfoundAction();
        }

        $db = $itemTable->getAdapter();

        $rows = $db->fetchAll(
            $db->select()
                ->from('item', [
                    'item.id',
                    'name' => 'if(item_language.name, item_language.name, item.name)',
                    'item.begin_model_year', 'item.end_model_year',
                    'spec' => 'spec.short_name',
                    'spec_full' => 'spec.name',
                    'item.body', 'item.today',
                    'item.begin_year', 'item.end_year',
                    'item.is_group'
                ])
                ->joinLeft('item_language', 'item.id = item_language.item_id and item_language.language = :lang', null)
                ->joinLeft('spec', 'item.spec_id = spec.id', null)
                ->join('item_parent', 'item.id = item_parent.item_id', 'type')
                ->where('item_parent.parent_id = ?', $car->id)
                ->order(['item_parent.type', 'item.name', 'item.begin_year', 'item.end_year'])
                ->bind([
                    'lang' => $this->language()
                ])
        );

        $viewModel = new ViewModel([
            'cars' => $this->prepareCarParentRows($rows)
        ]);

        return $viewModel->setTerminal(true);
    }

    public function conceptsAction()
    {
        $user = $this->user()->get();
        if (! $user) {
            return $this->forward()->dispatch(self::class, [
                'action' => 'only-registered'
            ]);
        }

        $itemTable = new DbTable\Item();
        $brand = $itemTable->fetchRow([
            'item_type_id = ?' => Item::BRAND,
            'id = ?'           => (int)$this->params('brand_id')
        ]);
        if (! $brand) {
            return $this->notfoundAction();
        }

        $db = $itemTable->getAdapter();

        $rows = $db->fetchAll(
            $db->select()
                ->from('item', [
                    'item.id',
                    'name' => 'if(item_language.name, item_language.name, item.name)',
                    'item.begin_model_year', 'item.end_model_year',
                    'spec' => 'spec.short_name',
                    'spec_full' => 'spec.name',
                    'item.body', 'item.today',
                    'item.begin_year', 'item.end_year',
                    'item.is_group'
                ])
                ->joinLeft('item_language', 'item.id = item_language.item_id and item_language.language = :lang', null)
                ->joinLeft('spec', 'item.spec_id = spec.id', null)
                ->join('item_parent_cache', 'item.id = item_parent_cache.item_id', null)
                ->where('item_parent_cache.parent_id = ?', $brand->id)
                ->where('item.is_concept')
                ->order(['item.name', 'item.begin_year', 'item.end_year'])
                ->group('item.id')
                ->bind([
                    'lang' => $this->language()
                ])
        );

        $concepts = $this->prepareCars($rows);

        $viewModel = new ViewModel([
            'concepts' => $concepts,
        ]);

        return $viewModel->setTerminal(true);
    }

    public function cropSaveAction()
    {
        $picture = $this->pictureTable->find($this->params()->fromPost('id'))->current();
        if (! $picture) {
            return $this->notfoundAction();
        }

        $user = $this->user()->get();
        if (! $user) {
            return $this->forbiddenAction();
        }

        if ($picture->owner_id != $user->id) {
            return $this->forbiddenAction();
        }

        if ($picture->status != DbTable\Picture::STATUS_INBOX) {
            return $this->forbiddenAction();
        }

        $left = round($this->params()->fromPost('x'));
        $top = round($this->params()->fromPost('y'));
        $width = round($this->params()->fromPost('w'));
        $height = round($this->params()->fromPost('h'));

        $left = max(0, $left);
        $left = min($picture->width, $left);
        $width = max(400, $width);
        $width = min($picture->width, $width);

        $top = max(0, $top);
        $top = min($picture->height, $top);
        $height = max(300, $height);
        $height = min($picture->height, $height);

        if ($left > 0 || $top > 0 || $width < $picture->width || $height < $picture->height) {
            $picture->setFromArray([
                'crop_left'   => $left,
                'crop_top'    => $top,
                'crop_width'  => $width,
                'crop_height' => $height
            ]);
        } else {
            $picture->setFromArray([
                'crop_left'   => null,
                'crop_top'    => null,
                'crop_width'  => null,
                'crop_height' => null
            ]);
        }
        $picture->save();

        $this->imageStorage()->flush([
            'image' => $picture->image_id
        ]);

        $this->log(sprintf(
            'Выделение области на картинке %s',
            htmlspecialchars($this->pic()->name($picture, $this->language()))
        ), [$picture]);

        $image = $this->imageStorage()->getFormatedImage($this->pictureTable->getFormatRequest($picture), 'picture-thumb');

        return new JsonModel([
            'ok'  => true,
            'src' => $image->getSrc()
        ]);
    }

    public function sendAction()
    {
        $user = $this->user()->get();

        if (! $user || $user->deleted) {
            return $this->forward()->dispatch(self::class, [
                'action' => 'only-registered'
            ]);
        }

        $request = $this->getRequest();

        if (! $request->isPost()) {
            return $this->forbiddenAction();
        }

        $replace = $this->params('replace');
        $replacePicture = false;
        if ($replace) {
            $replacePicture = $this->pictureTable->fetchRow([
                'identity = ?' => $replace
            ]);
        }

        $perspectiveId = null;

        if ($replacePicture) {
            $itemIds = $this->pictureItem->getPictureItems($replacePicture->id);
            if (count($itemIds) == 1) {
                $perspectiveId = $this->pictureItem->getPerspective($replacePicture->id, $itemIds[0]);
            }
        } else {
            $itemId = (int)$this->params('item_id');
            $itemIds = $itemId ? [$itemId] : [];
            $perspectiveId = (int)$this->params('perspective_id');
        }

        $itemTable = new DbTable\Item();
        $selectedItems = $itemTable->find($itemIds);

        if (count($selectedItems) <= 0) {
            return $this->forbiddenAction();
        }

        $form = new UploadForm(null, [
            'multipleFiles' => ! $replacePicture,
        ]);

        $data = array_merge_recursive(
            $request->getPost()->toArray(),
            $request->getFiles()->toArray()
        );
        $form->setData($data);
        if (! $form->isValid()) {
            $this->getResponse()->setStatusCode(400);
            return new JsonModel($form->getMessages());
        }

        $pictures = $this->saveUpload($form, $itemIds, $perspectiveId, $replacePicture);

        $result = [];
        foreach ($pictures as $picture) {
            $image = $this->imageStorage()->getFormatedImage(
                $this->pictureTable->getFormatRequest($picture),
                'picture-gallery-full'
            );

            if ($image) {
                $picturesData = $this->pic()->listData([$picture]);

                $html = $this->partial->__invoke('application/picture', array_replace(
                    $picturesData['items'][0],
                    [
                        'disableBehaviour' => false,
                        'isModer'          => false
                    ]
                ));

                $cPerspectiveId = null;
                $perspectiveUrl = null;
                if (count($itemIds) == 1) {
                    $itemId = $itemIds[0];
                    $cPerspectiveId = $this->pictureItem->getPerspective($picture['id'], $itemId);
                    $perspectiveUrl = $this->url()->fromRoute('api/picture-item/update', [
                        'picture_id' => $picture['id'],
                        'item_id'    => $itemId
                    ]);
                }

                $result[] = [
                    'id'     => $picture['id'],
                    'html'   => $html,
                    'width'  => $picture->width,
                    'height' => $picture->height,
                    'src'    => $image->getSrc(),
                    'perspectiveUrl' => $perspectiveUrl,
                    'perspectiveId'  => $cPerspectiveId
                ];
            }
        }

        $this->getResponse()->setStatusCode(200);
        return new JsonModel($result);
    }
}
