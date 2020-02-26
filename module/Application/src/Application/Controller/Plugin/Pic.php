<?php

namespace Application\Controller\Plugin;

use ArrayObject;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Application\Model\Picture;
use Application\PictureNameFormatter;

class Pic extends AbstractPlugin
{
    /**
     * @var PictureNameFormatter
     */
    private $pictureNameFormatter;

    /**
     * @var Picture
     */
    private $picture;

    public function __construct(
        PictureNameFormatter $pictureNameFormatter,
        Picture $picture
    ) {
        $this->pictureNameFormatter = $pictureNameFormatter;
        $this->picture = $picture;
    }

    public function name($pictureRow, $language)
    {
        if ($pictureRow instanceof ArrayObject) {
            $pictureRow = (array)$pictureRow;
        }

        $names = $this->picture->getNameData([$pictureRow], [
            'language' => $language,
            'large'    => true
        ]);
        $name = $names[$pictureRow['id']];

        return $this->pictureNameFormatter->format($name, $language);
    }
}
