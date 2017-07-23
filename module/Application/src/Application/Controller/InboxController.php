<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;

use Application\Service\DayPictures;
use Application\Model\Brand as BrandModel;
use Application\Model\DbTable;
use Application\Model\Item;

class InboxController extends AbstractActionController
{
    const PER_PAGE = 18;
    const BRAND_ALL = 'all';

    /**
     * @var DbTable\Picture
     */
    private $pictureTable;

    public function __construct(DbTable\Picture $pictureTable)
    {
        $this->pictureTable = $pictureTable;
    }

    private function getBrandControl($brand = null)
    {
        $brandModel = new BrandModel();
        $language = $this->language();

        $brands = $brandModel->getList($language, function ($select) use ($language) {
            $db = $select->getAdapter();
            $select->where(
                'item.id IN (?)',
                $db->select()
                    ->from('item', 'id')
                    ->where('item.item_type_id = ?', Item::BRAND)
                    ->join('item_parent_cache', 'item.id = item_parent_cache.parent_id', null)
                    ->join('picture_item', 'item_parent_cache.item_id = picture_item.item_id', null)
                    ->join('pictures', 'picture_item.picture_id = pictures.id', null)
                    ->where('pictures.status = ?', DbTable\Picture::STATUS_INBOX)
                    ->bind([
                        'language' => $language
                    ])
            );
        });

        $url = $this->url()->fromRoute('inbox', [
            'brand' => self::BRAND_ALL,
            'date'  => $this->params('date'),
            'page'  => null
        ]);
        $brandOptions = [
            $url => 'All' //$this->view->translate('all-link')
        ];
        foreach ($brands as $iBrand) {
            $url = $this->url()->fromRoute('inbox', [
                'brand' => $iBrand['catname'],
                'date'  => $this->params('date'),
                'page'  => null
            ]);
            $brandOptions[$url] = $iBrand['name'];
        }

        $currentBrandUrl = $this->url()->fromRoute('inbox', [
            'brand' => $brand ? $brand['catname'] : null,
            'date'  => $this->params('date'),
            'page'  => null
        ]);

        return [
            'brands' => $brandOptions,
            'brand'  => $currentBrandUrl,
        ];
    }

    public function indexAction()
    {
        $brandModel = new BrandModel();
        $language = $this->language();

        $brand = $brandModel->getBrandByCatname($this->params('brand'), $language);

        $select = $this->pictureTable->select(true)
            ->where('pictures.status = ?', DbTable\Picture::STATUS_INBOX);
        if ($brand) {
            $select
                ->join('picture_item', 'pictures.id = picture_item.picture_id', null)
                ->join('item_parent_cache', 'picture_item.item_id = item_parent_cache.item_id', null)
                ->where('item_parent_cache.parent_id = ?', $brand['id'])
                ->group('pictures.id');
        }

        $brandCatname = $brand ? $brand['catname'] : self::BRAND_ALL;

        $service = new DayPictures([
            'timezone'     => $this->user()->timezone(),
            'dbTimezone'   => MYSQL_TIMEZONE,
            'select'       => $select,
            'orderColumn'  => 'add_date',
            'currentDate'  => $this->params('date')
        ]);

        if (! $service->haveCurrentDate() || ! $service->haveCurrentDayPictures()) {
            $lastDate = $service->getLastDateStr();

            if (! $lastDate) {
                return $this->notFoundAction();
            }

            $url = $this->url()->fromRoute('inbox', [
                'brand' => $brandCatname,
                'date'  => $lastDate,
                'page'  => null
            ]);
            return $this->redirect()->toUrl($url);
        }

        $currentDateStr = $service->getCurrentDateStr();
        if ($this->params('date') != $currentDateStr) {
            $url = $this->url()->fromRoute('inbox', [
                'brand' => $brandCatname,
                'date'  => $currentDateStr,
                'page'  => null
            ]);
            return $this->redirect()->toUrl($url);
        }

        $paginator = $service->getPaginator()
            ->setItemCountPerPage(self::PER_PAGE)
            ->setCurrentPageNumber($this->params('page'));

        $select = $service->getCurrentDateSelect()
            ->limitPage($paginator->getCurrentPageNumber(), $paginator->getItemCountPerPage());

        $picturesData = $this->pic()->listData($select, [
            'width' => 6
        ]);


        return array_replace(
            $this->getBrandControl($brand),
            [
                'picturesData' => $picturesData,
                'paginator' => $paginator,
                'prev'      => [
                    'date'  => $service->getPrevDate(),
                    'count' => $service->getPrevDateCount(),
                    'url'   => $this->url()->fromRoute('inbox', [
                        'brand' => $brandCatname,
                        'date'  => $service->getPrevDateStr(),
                        'page'  => null
                    ])
                ],
                'current'   => [
                    'date'  => $service->getCurrentDate(),
                    'count' => $service->getCurrentDateCount(),
                ],
                'next'      => [
                    'date'  => $service->getNextDate(),
                    'count' => $service->getNextDateCount(),
                    'url'   => $this->url()->fromRoute('inbox', [
                        'brand' => $brandCatname,
                        'date'  => $service->getNextDateStr(),
                        'page'  => null
                    ])
                ],
                'urlParams' => [
                    'brand' => $brandCatname,
                    'date'  => $this->params('date')
                ]
            ]
        );
    }
}
