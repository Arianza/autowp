<?php

namespace Application\Controller;

use Zend\Db\TableGateway\TableGateway;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Autowp\TextStorage;

use Application\Model\DbTable;
use Application\Model\Item;
use Application\Service\Mosts;
use Application\Service\SpecificationsService;

class MostsController extends AbstractActionController
{
    private $textStorage;

    /**
     * @var SpecificationsService
     */
    private $specsService = null;

    /**
     * @var TableGateway
     */
    private $perspectiveGroupTable;

    public function __construct(
        TextStorage\Service $textStorage,
        SpecificationsService $specsService,
        TableGateway $perspectiveGroupTable,
        Item $itemModel
    ) {

        $this->textStorage = $textStorage;
        $this->specsService = $specsService;
        $this->perspectiveGroupTable = $perspectiveGroupTable;
        $this->itemModel = $itemModel;
    }

    public function indexAction()
    {
        $service = new Mosts([
            'specs'                 => $this->specsService,
            'perspectiveGroupTable' => $this->perspectiveGroupTable
        ]);

        $language = $this->language();
        $yearsCatname = $this->params('years_catname');
        $carTypeCatname = $this->params('shape_catname');
        $mostCatname = $this->params('most_catname');

        $data = $service->getData([
            'language' => $language,
            'most'     => $mostCatname,
            'years'    => $yearsCatname,
            'carType'  => $carTypeCatname
        ]);

        foreach ($data['sidebar']['mosts'] as &$most) {
            $most['url'] = $this->url()->fromRoute('mosts', $most['params']);
        }
        foreach ($data['sidebar']['carTypes'] as &$carType) {
            $carType['url'] = $this->url()->fromRoute('mosts', $carType['params']);
            foreach ($carType['childs'] as &$child) {
                $child['url'] = $this->url()->fromRoute('mosts', $child['params']);
            }
        }
        foreach ($data['years'] as &$year) {
            $year['url'] = $this->url()->fromRoute('mosts', $year['params']);
        }


        // images
        $formatRequests = [];
        $allPictures = [];
        $idx = 0;
        foreach ($data['carList']['cars'] as $car) {
            foreach ($car['pictures'] as $picture) {
                if ($picture) {
                    $formatRequests[$idx++] = $picture->getFormatRequest();
                    $allPictures[] = $picture->toArray();
                }
            }
        }

        $imageStorage = $this->imageStorage();
        $imagesInfo = $imageStorage->getFormatedImages($formatRequests, 'picture-thumb');

        $pictureTable = new DbTable\Picture();
        $names = $pictureTable->getNameData($allPictures, [
            'language' => $language
        ]);

        $itemLanguageTable = new DbTable\Item\Language();

        $idx = 0;
        foreach ($data['carList']['cars'] as &$car) {
            $description = null;

            $db = $itemLanguageTable->getAdapter();
            $orderExpr = $db->quoteInto('language = ? desc', $this->language());
            $itemLanguageRows = $itemLanguageTable->fetchAll([
                'item_id = ?' => $car['car']['id']
            ], new \Zend_Db_Expr($orderExpr));

            $textIds = [];
            foreach ($itemLanguageRows as $itemLanguageRow) {
                if ($itemLanguageRow->text_id) {
                    $textIds[] = $itemLanguageRow->text_id;
                }
            }

            $description = null;
            if ($textIds) {
                $description = $this->textStorage->getFirstText($textIds);
            }

            $car['description'] = $description;

            $pictures = [];

            $paths = $this->catalogue()->getCataloguePaths($car['car']['id'], [
                'breakOnFirst' => true
            ]);

            foreach ($car['pictures'] as $picture) {
                if ($picture) {
                    $id = $picture->id;

                    $url = null;
                    foreach ($paths as $path) {
                        $url = $this->url()->fromRoute('catalogue', [
                            'action'        => 'brand-item-picture',
                            'brand_catname' => $path['brand_catname'],
                            'car_catname'   => $path['car_catname'],
                            'path'          => $path['path'],
                            'picture_id'    => $picture['identity']
                        ]);
                    }

                    $pictures[] = [
                        'name' => isset($names[$id]) ? $names[$id] : null,
                        'src'  => isset($imagesInfo[$idx]) ? $imagesInfo[$idx]->getSrc() : null,
                        'url'  => $url
                    ];
                    $idx++;
                } else {
                    $pictures[] = null;
                }
            }

            $car['name'] = $this->itemModel->getNameData($car['car'], $language);
            $car['pictures'] = $pictures;
        }
        unset($car);

        $sideBarModel = new ViewModel($data);
        $sideBarModel->setTemplate('application/mosts/sidebar');
        $this->layout()->addChild($sideBarModel, 'sidebar');

        return $data;
    }
}
