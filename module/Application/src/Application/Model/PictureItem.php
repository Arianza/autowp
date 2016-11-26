<?php

namespace Application\Model;

use Zend_Db_Table;

use InvalidArgumentException;

class PictureItem
{
    private $table;

    public function __construct()
    {
        $this->table = new Zend_Db_Table([
            'name'    => 'picture_item',
            'primary' => ['picture_id', 'item_id']
        ]);
    }

    /**
     * @param int $pictureId
     * @param int $itemId
     * @throws InvalidArgumentException
     * @return \Zend_Db_Table_Row_Abstract|NULL
     */
    private function getRow($pictureId, $itemId)
    {
        $pictureId = (int)$pictureId;
        $itemId = (int)$itemId;

        if (!$pictureId) {
            throw new InvalidArgumentException("Picture id is invalid");
        }

        if (!$itemId) {
            throw new InvalidArgumentException("Item id is invalid");
        }

        $row = $this->table->fetchRow([
            'picture_id = ?' => $pictureId,
            'item_id = ?'    => $itemId
        ]);

        return $row;
    }

    public function add($pictureId, $itemId)
    {
        $pictureId = (int)$pictureId;
        $itemId = (int)$itemId;

        if (!$pictureId) {
            throw new InvalidArgumentException("Picture id is invalid");
        }

        if (!$itemId) {
            throw new InvalidArgumentException("Item id is invalid");
        }

        $row = $this->getRow($pictureId, $itemId);

        if (!$row) {
            $row = $this->table->createRow([
                'picture_id' => $pictureId,
                'item_id'    => $itemId
            ]);
            $row->save();
        }
    }

    public function isExists($pictureId, $itemId)
    {
        return (bool)$this->getRow($pictureId, $itemId);
    }

    public function changePictureItem($pictureId, $oldItemId, $newItemId)
    {
        $newItemId = (int)$newItemId;

        if (!$newItemId) {
            throw new InvalidArgumentException("Item id is invalid");
        }

        $row = $this->getRow($pictureId, $oldItemId);

        if (!$row) {
            throw new \Exception("Item not found");
        }

        $row->item_id = $newItemId;
        $row->save();
    }

    public function setPictureItems($pictureId, array $itemIds)
    {
        $pictureId = (int)$pictureId;

        if (!$pictureId) {
            throw new InvalidArgumentException("Picture id is invalid");
        }

        foreach ($itemIds as &$itemId) {
            $itemId = (int)$itemId;
            if (!$itemId) {
                throw new InvalidArgumentException("Item id is invalid");
            }
        }
        unset($itemId);

        foreach ($itemIds as $itemId) {
            $row = $this->getRow($pictureId, $itemId);

            if (!$row) {
                $row = $this->table->createRow([
                    'picture_id' => $pictureId,
                    'item_id'    => $itemId
                ]);
                $row->save();
            }
        }

        $filter = [
            'picture_id = ?' => $pictureId
        ];
        if ($itemIds) {
            $filter['item_id not in (?)'] = $itemIds;
        }

        $this->table->delete($filter);
    }

    public function getPictureItems($pictureId)
    {
        $db = $this->table->getAdapter();
        return $db->fetchCol(
            $db->select()
                ->from($this->table->info('name'), 'item_id')
                ->where('picture_id = ?', $pictureId)
        );
    }

    public function setProperties($pictureId, $itemId, array $properties)
    {
        $row = $this->getRow($pictureId, $itemId);
        if ($row) {
            if (array_key_exists('perspective', $properties)) {
                $perspective = $properties['perspective'];
                $row->perspective_id = $perspective ? (int)$perspective : null;
            }

            if (array_key_exists('crop', $properties)) {
                $crop = $properties['crop'];
                if ($crop) {
                    $row->setFromArray([
                        'crop_left'   => $crop['left'],
                        'crop_top'    => $crop['top'],
                        'crop_width'  => $crop['width'],
                        'crop_height' => $crop['height'],
                    ]);
                } else {
                    $row->setFromArray([
                        'crop_left'   => null,
                        'crop_top'    => null,
                        'crop_width'  => null,
                        'crop_height' => null,
                    ]);
                }
            }

            $row->save();
        }
    }

    public function getPerspective($pictureId, $itemId)
    {
        $row = $this->getRow($pictureId, $itemId);
        if (!$row) {
            return null;
        }

        return $row->perspective_id;
    }
}
