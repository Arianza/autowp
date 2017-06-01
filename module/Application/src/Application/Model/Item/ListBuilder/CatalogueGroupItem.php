<?php

namespace Application\Model\Item\ListBuilder;

use Application\Model\DbTable;

use Exception;

use Zend_Db_Expr;

class CatalogueGroupItem extends CatalogueItem
{
    /**
     * @var DbTable\Item\Language
     */
    private $itemLanguageTable;

    /**
     * @var string
     */
    private $language;

    private $textStorage;

    /**
     * @var array
     */
    private $hasChildSpecs;

    private $type;

    private $itemParentRows = [];

    private $itemParentTable;

    public function __construct(array $options)
    {
        parent::__construct($options);

        $this->itemLanguageTable = new DbTable\Item\Language();
    }

    public function setItemParentTable($table)
    {
        $this->itemParentTable = $table;
    }

    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    public function setTextStorage($textStorage)
    {
        $this->textStorage = $textStorage;

        return $this;
    }

    public function setHasChildSpecs($hasChildSpecs)
    {
        $this->hasChildSpecs = $hasChildSpecs;

        return $this;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    private function isItemHasFullText($itemId)
    {
        $db = $this->itemLanguageTable->getAdapter();
        $orderExpr = $db->quoteInto('language = ? desc', $this->language);
        $itemLanguageRows = $this->itemLanguageTable->fetchAll([
            'item_id = ?' => $itemId
        ], new Zend_Db_Expr($orderExpr));

        $fullTextIds = [];
        foreach ($itemLanguageRows as $itemLanguageRow) {
            if ($itemLanguageRow->full_text_id) {
                $fullTextIds[] = $itemLanguageRow->full_text_id;
            }
        }

        if (! $fullTextIds) {
            return false;
        }

        return (bool)$this->textStorage->getFirstText($fullTextIds);
    }

    private function getItemParentRow($itemId, $parentId)
    {
        if (! isset($this->itemParentRows[$itemId][$parentId])) {
            $this->itemParentRows[$itemId][$parentId] = $this->itemParentTable->fetchRow([
                'item_id = ?'   => $itemId,
                'parent_id = ?' => $parentId
            ]);
        }

        return $this->itemParentRows[$itemId][$parentId];
    }

    public function getDetailsUrl($item)
    {
        $itemParentAdapter = $this->itemParentTable->getAdapter();
        $hasChilds = (bool)$itemParentAdapter->fetchOne(
            $itemParentAdapter->select()
                ->from($this->itemParentTable->info('name'), new Zend_Db_Expr('1'))
                ->where('parent_id = ?', $item['id'])
        );

        $hasHtml = $this->isItemHasFullText($item['id']);

        if (! $hasChilds && ! $hasHtml) {
            return null;
        }

        // found parent row
        $itemParentRow = $this->getItemParentRow($item['id'], $this->itemId);
        if (! $itemParentRow) {
            return null;
        }

        return $this->router->assemble([
            'action'        => 'brand-item',
            'brand_catname' => $this->brand['catname'],
            'car_catname'   => $this->brandItemCatname,
            'path'          => array_merge($this->path, [
                $itemParentRow->catname
            ])
        ], [
            'name' => 'catalogue'
        ]);
    }

    public function getPicturesUrl($item)
    {
        //TODO: more than 1 levels diff fails here
        $itemParentRow = $this->getItemParentRow($item['id'], $this->itemId);
        if (! $itemParentRow) {
            return null;
        }

        return $this->router->assemble([
            'action'        => 'brand-item-pictures',
            'brand_catname' => $this->brand['catname'],
            'car_catname'   => $this->brandItemCatname,
            'path'          => array_merge($this->path, [
                $itemParentRow->catname
            ]),
            'exact'         => false
        ], [
            'name' => 'catalogue'
        ]);
    }

    public function getSpecificationsUrl($item)
    {
        if ($this->hasChildSpecs[$item['id']]) {
            $itemParentRow = $this->getItemParentRow($item['id'], $this->itemId);
            if ($itemParentRow) {
                return $this->router->assemble([
                    'action'        => 'brand-item-specifications',
                    'brand_catname' => $this->brand['catname'],
                    'car_catname'   => $this->brandItemCatname,
                    'path'          => array_merge($this->path, [
                        $itemParentRow->catname
                    ]),
                ], [
                    'name' => 'catalogue'
                ]);
            }
        }

        if (! $this->specsService->hasSpecs($item['id'])) {
            return false;
        }

        switch ($this->type) {
            case DbTable\Item\ParentTable::TYPE_TUNING:
                $typeStr = 'tuning';
                break;

            case DbTable\Item\ParentTable::TYPE_SPORT:
                $typeStr = 'sport';
                break;

            default:
                $typeStr = null;
                break;
        }

        return $this->router->assemble([
            'action'        => 'brand-item-specifications',
            'brand_catname' => $this->brand['catname'],
            'car_catname'   => $this->brandItemCatname,
            'path'          => $this->path,
            'type'          => $typeStr
        ], [
            'name' => 'catalogue'
        ]);
    }

    public function getTypeUrl(DbTable\Item\Row $item, $type)
    {
        switch ($type) {
            case DbTable\Item\ParentTable::TYPE_TUNING:
                $catname = 'tuning';
                break;
            case DbTable\Item\ParentTable::TYPE_SPORT:
                $catname = 'sport';
                break;
            default:
                throw new Exception('Unexpected type');
                break;
        }

        $itemParentRow = $this->getItemParentRow($item->id, $this->itemId);
        if ($itemParentRow) {
            $currentPath = array_merge($this->path, [
                $itemParentRow->catname
            ]);
        } else {
            $currentPath = $this->path;
        }

        return $this->router->assemble([
            'action'        => 'brand-item',
            'brand_catname' => $this->brand['catname'],
            'car_catname'   => $this->brandItemCatname,
            'path'          => $currentPath,
            'type'          => $catname,
            'page'          => null,
        ], [
            'name' => 'catalogue'
        ]);
    }

    public function getPictureUrl($item, array $picture)
    {
        // found parent row
        $itemParentRow = $this->getItemParentRow($item['id'], $this->itemId);
        if (! $itemParentRow) {
            return $this->picHelper->url($picture['identity']);
        }

        return $this->router->assemble([
            'action'        => 'brand-item-picture',
            'brand_catname' => $this->brand['catname'],
            'car_catname'   => $this->brandItemCatname,
            'path'          => array_merge($this->path, [
                $itemParentRow->catname
            ]),
            'picture_id'    => $picture['identity']
        ], [
            'name' => 'catalogue'
        ]);
    }
}
