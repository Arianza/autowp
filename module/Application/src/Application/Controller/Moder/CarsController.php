<?php

namespace Application\Controller\Moder;

use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

use Application\Form\Moder\Car as CarForm;
use Application\Form\Moder\CarOrganize as CarOrganizeForm;
use Application\Form\Moder\CarOrganizePictures as CarOrganizePicturesForm;
use Application\Form\Moder\ItemLanguages as ItemLanguagesForm;
use Application\HostManager;
use Application\Model\Brand as BrandModel;
use Application\Model\BrandVehicle;
use Application\Model\DbTable;
use Application\Model\Modification;
use Application\Model\PictureItem;
use Application\Model\VehicleType;
use Application\Service\SpecificationsService;

use Autowp\Commons\Paginator\Adapter\Zend1DbTableSelect;
use Autowp\Message\MessageService;

use geoPHP;
use Point;

use Zend_Db_Expr;

use Exception;

class CarsController extends AbstractActionController
{
    private $allowedLanguages = ['en'];

    /**
     * @var DbTable\Item\ParentTable
     */
    private $itemParentTable;

    private $textStorage;

    private $translator;

    /**
     * @var Form
     */
    private $descForm;

    /**
     * @var Form
     */
    private $textForm;

    /**
     * @var Form
     */
    private $itemParentForm;

    /**
     * @var Form
     */
    private $filterForm;

    /**
     * @var HostManager
     */
    private $hostManager;

    /**
     * @var BrandVehicle
     */
    private $brandVehicle;

    /**
     * @var MessageService
     */
    private $message;

    /**
     * @var SpecificationsService
     */
    private $specificationsService;

    /**
     * @var PictureItem
     */
    private $pictureItem;

    /**
     * @var Form
     */
    private $logoForm;

    public function __construct(
        HostManager $hostManager,
        $textStorage,
        $translator,
        Form $descForm,
        Form $textForm,
        Form $itemParentForm,
        Form $filterForm,
        Form $logoForm,
        BrandVehicle $brandVehicle,
        MessageService $message,
        SpecificationsService $specificationsService,
        PictureItem $pictureItem,
        array $languages
    ) {
        $this->hostManager = $hostManager;
        $this->textStorage = $textStorage;
        $this->translator = $translator;
        $this->descForm = $descForm;
        $this->textForm = $textForm;
        $this->itemParentForm = $itemParentForm;
        $this->filterForm = $filterForm;
        $this->logoForm = $logoForm;
        $this->brandVehicle = $brandVehicle;
        $this->message = $message;
        $this->specificationsService = $specificationsService;
        $this->pictureItem = $pictureItem;
        $this->allowedLanguages = $languages;
    }

    private function canMove(DbTable\Item\Row $car)
    {
        return $this->user()->isAllowed('car', 'move');
    }

    private function getVehicleTypeOptions($table, $parentId = null)
    {
        if ($parentId) {
            $filter = [
                'parent_id = ?' => $parentId
            ];
        } else {
            $filter = 'parent_id is null';
        }

        $rows = $table->fetchAll($filter, 'position');
        $result = [];
        foreach ($rows as $row) {
            $result[$row->id] = $row->name;

            foreach ($this->getVehicleTypeOptions($table, $row->id) as $key => $value) {
                $result[$key] = '...' . $this->translate($value);
            }
        }

        return $result;
    }

    public function indexAction()
    {
        if (! $this->user()->inheritsRole('moder')) {
            return $this->forbiddenAction();
        }

        $specTable = new DbTable\Spec();
        $specOptions = $this->loadSpecs($specTable, null, 0);

        $vehicleTypeTable = new DbTable\Vehicle\Type();
        $vehicleTypeOptions = $this->getVehicleTypeOptions($vehicleTypeTable, null);

        $this->filterForm->setAttribute('action', $this->url()->fromRoute(null, [], [], true));

        $this->filterForm->get('spec')->setValueOptions(array_replace(['' => '--'], $specOptions));
        $this->filterForm->get('vehicle_type_id')->setValueOptions(array_replace([
            ''      => '--',
            'empty' => 'moder/items/filter/vehicle-type/empty'
        ], $vehicleTypeOptions));
        $this->filterForm->get('vehicle_childs_type_id')->setValueOptions(array_replace([
            '' => '--'
        ], $vehicleTypeOptions));

        if ($this->getRequest()->isPost()) {
            $this->filterForm->setData($this->params()->fromPost());
            if ($this->filterForm->isValid()) {
                $params = $this->filterForm->getData();
                foreach ($params as $key => $value) {
                    if (strlen($value) <= 0) {
                        unset($params[$key]);
                    }
                }

                return $this->redirect()->toRoute('moder/cars/params', array_replace($params, [
                    'action' => 'index'
                ]));
            }
        }

        $this->filterForm->setData(array_replace(['order' => '1'], $this->params()->fromRoute()));

        $itemTable = $this->catalogue()->getItemTable();

        $select = $itemTable->select(true);

        if ($this->filterForm->isValid()) {
            $values = $this->filterForm->getData();

            $group = false;

            if ($values['name']) {
                $select->where('item.name like ?', '%' . $values['name'] . '%');
            }

            if ($values['no_name']) {
                $select->where('item.name not like ?', '%' . $values['no_name'] . '%');
            }

            if ($values['item_type_id']) {
                $select->where('item.item_type_id = ?', $values['item_type_id']);
            }

            if ($values['vehicle_type_id']) {
                if ($values['vehicle_type_id'] == 'empty') {
                    $select
                        ->joinLeft('vehicle_vehicle_type', 'item.id = vehicle_vehicle_type.vehicle_id', null)
                        ->where('vehicle_vehicle_type.vehicle_id is null');
                } else {
                    $select
                        ->join('vehicle_vehicle_type', 'item.id = vehicle_vehicle_type.vehicle_id', null)
                        ->where('vehicle_vehicle_type.vehicle_type_id = ?', $values['vehicle_type_id']);
                }
            }

            if ($values['vehicle_childs_type_id']) {
                $group = true;
                $select
                    ->join(
                        ['cpc_childs' => 'item_parent_cache'],
                        'item.id = cpc_childs.parent_id',
                        null
                    )
                    ->join(
                        ['vvt_child' => 'vehicle_vehicle_type'],
                        'cpc_childs.item_id = vvt_child.vehicle_id',
                        null
                    )
                    ->join('car_types_parents', 'vvt_child.vehicle_type_id = car_types_parents.id', null)
                    ->where('car_types_parents.parent_id = ?', $values['vehicle_childs_type_id']);
            }

            if ($values['spec']) {
                $select->where('item.spec_id = ?', $values['spec']);
            }

            if ($values['from_year']) {
                $select->where('item.begin_year = ?', $values['from_year']);
            }

            if ($values['to_year']) {
                $select->where('item.end_year = ?', $values['to_year']);
            }

            if ($values['parent_id']) {
                $select
                    ->join(['item_parent_cache'], 'item.id = item_parent_cache.item_id', null)
                    ->where('item_parent_cache.parent_id = ?', $values['parent_id']);
            }

            /*if ($values['no_category']) {
                $itemParentTable = new DbTable\Item\ParentTable();

                $ids = $itemParentTable->getAdapter()->fetchCol(
                    $itemParentTable->getAdapter()->select()
                        ->from('item_parent_cache', 'item_id')
                        ->where('parent_id = ?', $values['no_category'])
                );

                if ($ids) {
                    $expr = $itemTable->getAdapter()->quoteInto(
                        'item.id = no_category.item_id and no_category.parent_id in (?)',
                        $ids
                    );
                    $select
                        ->joinLeft(['no_category' => 'item_parent_cache'], $expr, null)
                        ->where('no_category.item_id is null');
                }
            }*/

            if ($values['no_parent']) {
                $select
                    ->joinLeft(
                        'item_parent_cache',
                        'item.id = item_parent_cache.item_id and item.id <> item_parent_cache.parent_id',
                        null
                    )
                    ->where('item_parent_cache.item_id IS NULL');
            }

            if ($values['text']) {
                $select
                    ->join('item_language', 'item.id = item_language.item_id', null)
                    ->join('textstorage_text', 'item_language.text_id = textstorage_text.id', null)
                    ->where('textstorage_text.text like ?', '%' . $values['text'] . '%');
            }

            switch ($values['order']) {
                case '0':
                    $select->order('id asc');
                    break;

                default:
                case '1':
                    $select->order('id desc');
                    break;
            }

            if ($group) {
                $select->group('item.id');
            }
        }

        $paginator = new \Zend\Paginator\Paginator(
            new Zend1DbTableSelect($select)
        );

        $paginator
            ->setItemCountPerPage(10)
            ->setCurrentPageNumber($this->params('page'));

        return [
            'form'      => $this->filterForm,
            'paginator' => $paginator,
            'listData'  => $this->car()->listData($paginator->getCurrentItems(), [
                'pictureFetcher' => new \Application\Model\Item\PerspectivePictureFetcher([
                    'type'                 => null,
                    'onlyExactlyPictures'  => false,
                    'dateSort'             => false,
                    'disableLargePictures' => false,
                    'perspectivePageId'    => null,
                    'onlyChilds'           => []
                ]),
                'listBuilder' => new \Application\Model\Item\ListBuilder([
                    'catalogue' => $this->catalogue(),
                    'router'    => $this->getEvent()->getRouter(),
                    'picHelper' => $this->getPluginManager()->get('pic')
                ]),
            ])
        ];
    }

    public function alphaAction()
    {
        if (! $this->user()->inheritsRole('moder')) {
            return $this->forbiddenAction();
        }

        $itemTable = $this->catalogue()->getItemTable();
        $carAdapter = $itemTable->getAdapter();
        $chars = $carAdapter->fetchCol(
            $carAdapter->select()
                ->distinct()
                ->from('item', ['char' => new Zend_Db_Expr('UPPER(LEFT(name, 1))')])
                ->order('char')
        );


        $groups = [
            'numbers' => [],
            'english' => [],
            'other'   => []
        ];

        foreach ($chars as $char) {
            if (preg_match('|^["0-9-]$|isu', $char)) {
                $groups['numbers'][] = $char;
            } elseif (preg_match('|^[A-Za-z]$|isu', $char)) {
                $groups['english'][] = $char;
            } else {
                $groups['other'][] = $char;
            }
        }

        $cars = [];
        $char = null;

        $c = $this->params('char');

        if ($c) {
            $char = mb_substr(trim($c), 0, 1);

            $char = $char;
            $cars = $itemTable->fetchAll(
                $itemTable->select(true)
                    ->where('name LIKE ?', $char.'%')
                    ->order(['name', 'begin_year', 'end_year'])
            );
        }

        return [
            'chars'  => $chars,
            'char'   => $char,
            'groups' => $groups,
            'cars'   => $cars
        ];
    }

    /**
     * @param DbTable\Item\Row $car
     * @return string
     */
    private function carModerUrl(DbTable\Item\Row $car, $full = false, $tab = null, $uri = null)
    {
        $params = [
            'action'  => 'car',
            'item_id' => $car->id,
        ];

        if ($tab) {
            $params['tab'] = $tab;
        }

        return $this->url()->fromRoute('moder/cars/params', $params, [
            'force_canonical' => $full,
            'uri'             => $uri
        ]);
    }

    /**
     * @param \Autowp\User\Model\DbTable\User\Row $user
     * @param bool $full
     * @param \Zend\Uri\Uri $uri
     * @return string
     */
    private function userModerUrl(\Autowp\User\Model\DbTable\User\Row $user, $full = false, $uri = null)
    {
        return $this->url()->fromRoute('users/user', [
            'user_id' => $user->identity ? $user->identity : 'user' . $user->id
        ], [
            'force_canonical' => $full,
            'uri'             => $uri
        ]);
    }

    /**
     * @param DbTable\Item\Row $car
     * @return void
     */
    private function redirectToCar(DbTable\Item\Row $car, $tab = null)
    {
        return $this->redirect()->toUrl($this->carModerUrl($car, true, $tab));
    }

    private function canEditMeta(DbTable\Item\Row $car)
    {
        return $this->user()->isAllowed('car', 'edit_meta');
    }

    public function carPicturesAction()
    {
        if (! $this->user()->inheritsRole('moder')) {
            return $this->forbiddenAction();
        }

        $itemTable = $this->catalogue()->getItemTable();

        $car = $itemTable->find($this->params('item_id'))->current();
        if (! $car) {
            return $this->notFoundAction();
        }

        // all pictures
        $table = $this->catalogue()->getPictureTable();
        $select = $table->select(true)
            ->join('picture_item', 'pictures.id = picture_item.picture_id', null)
            ->where('picture_item.item_id = ?', $car->id)
            ->order(['pictures.status', 'pictures.id']);

        $picturesData = $this->pic()->listData($select, [
            'width' => 6
        ]);

        $canUseTurboGroupCreator = in_array($car['item_type_id'], [
            DbTable\Item\Type::VEHICLE,
            DbTable\Item\Type::ENGINE,
        ]);

        $model = new ViewModel([
            'picturesData'            => $picturesData,
            'canUseTurboGroupCreator' => $canUseTurboGroupCreator
        ]);

        return $model->setTerminal(true);
    }

    private function getRandomPicture($car)
    {
        $pictures = $this->catalogue()->getPictureTable();

        $randomPicture = false;
        $statuses = [
            DbTable\Picture::STATUS_ACCEPTED,
            DbTable\Picture::STATUS_INBOX,
            DbTable\Picture::STATUS_REMOVING
        ];
        foreach ($statuses as $status) {
            $randomPicture = $pictures->fetchRow(
                $pictures->select(true)
                    ->join('picture_item', 'pictures.id = picture_item.picture_id', null)
                    ->where('picture_item.item_id = ?', $car->id)
                    ->where('pictures.status = ?', $status)
                    ->order(new Zend_Db_Expr('RAND()'))
                    ->limit(1)
            );
            if ($randomPicture) {
                break;
            }
        }

        return $randomPicture;
    }

    private function getDescriptionForm()
    {
        $this->descForm->setAttribute(
            'action',
            $this->url()->fromRoute('moder/cars/params', [
                'form' => 'car-edit-description'
            ], [], true)
        );

        return $this->descForm;
    }

    private function carToForm(DbTable\Item\Row $car)
    {
        return [
            'name'        => $car->name,
            'full_name'   => $car->full_name,
            'catname'     => $car->catname,
            'body'        => $car->body,
            'spec_id'     => $car->spec_inherit ? 'inherited' : ($car->spec_id ? $car->spec_id : ''),
            'is_concept'  => $car->is_concept_inherit ? 'inherited' : (bool)$car->is_concept,
            'is_group'    => $car->is_group,
            'model_year'  => [
                'begin' => $car->begin_model_year,
                'end'   => $car->end_model_year,
            ],
            'begin' => [
                'year'  => $car->begin_year,
                'month' => $car->begin_month,
            ],
            'end' => [
                'year'  => $car->end_year,
                'month' => $car->end_month,
                'today' => $car->today === null ? '' : $car->today
            ],
            'produced' => [
                'count'   => $car->produced,
                'exactly' => $car->produced_exactly
            ],
        ];
    }

    private function setItemPoint(DbTable\Item\Row $item, $point)
    {
        $itemPointTable = new DbTable\Item\Point();
        $itemPointRow = $itemPointTable->fetchRow([
            'item_id = ?' => $item['id']
        ]);

        if ($point) {
            if (! $itemPointRow) {
                $itemPointRow = $itemPointTable->createRow([
                    'item_id' => $item['id']
                ]);
            }

            $db = $itemPointTable->getAdapter();
            $itemPointRow->point = new Zend_Db_Expr($db->quoteInto('GeomFromText(?)', $point->out('wkt')));
            $itemPointRow->save();
        } else {
            if ($itemPointRow) {
                $itemPointRow->delete();
            }
        }
    }

    private function getItemPoint(DbTable\Item\Row $item)
    {
        $point = null;
        $itemPointTable = new DbTable\Item\Point();
        $itemPointRow = $itemPointTable->fetchRow([
            'item_id = ?' => $item['id']
        ]);
        if ($itemPointRow) {
            if ($itemPointRow->point) {
                geoPHP::version(); // for autoload classes
                $point = geoPHP::load(substr($itemPointRow->point, 4), 'wkb');
            }
        }

        return $point;
    }

    public function carAction()
    {
        if (! $this->user()->inheritsRole('moder')) {
            return $this->forbiddenAction();
        }

        $itemTable = $this->catalogue()->getItemTable();

        $car = $itemTable->find($this->params('item_id'))->current();
        if (! $car) {
            return $this->notFoundAction();
        }

        $pictures = $this->catalogue()->getPictureTable();


        $canEditMeta = $this->canEditMeta($car);

        if ($canEditMeta) {
            $itemParentTable = $this->getCarParentTable();
            $haveChilds = (bool)$itemParentTable->fetchRow([
                'parent_id = ?' => $car->id
            ]);

            $isGroupDisabled = $car->is_group && $haveChilds;

            $specTable = new DbTable\Spec();
            $specOptions = $this->loadSpecs($specTable, null, 0);

            $inheritedSpec = null;
            if ($car->spec_inherit) {
                if ($car->spec_id) {
                    $specRow = $specTable->find($car->spec_id)->current();
                    if ($specRow) {
                        $inheritedSpec = $specRow->short_name;
                    }
                }
            } else {
                $db = $itemTable->getAdapter();
                $avgSpecId = $db->fetchOne(
                    $db->select()
                        ->from($itemTable->info('name'), 'AVG(spec_id)')
                        ->join('item_parent', 'item.id = item_parent.parent_id', null)
                        ->where('item_parent.item_id = ?', $car->id)
                );
                if ($avgSpecId) {
                    $specRow = $specTable->find($avgSpecId)->current();
                    if ($specRow) {
                        $inheritedSpec = $specRow->short_name;
                    }
                }
            }

            $formModerCarEditMeta = new CarForm(null, [
                'itemId'             => $car->id,
                'itemType'           => $car->item_type_id,
                'language'           => $this->language(),
                'translator'         => $this->translator,
                'inheritedIsConcept' => $car->is_concept_inherit ? $car->is_concept : null,
                'isGroupDisabled'    => $isGroupDisabled,
                'specOptions'        => array_replace(['' => '-'], $specOptions),
                'inheritedSpec'      => $inheritedSpec,
            ]);
            $formModerCarEditMeta->setAttribute('action', $this->url()->fromRoute('moder/cars/params', [
                'action'  => 'car',
                'item_id' => $car->id,
                'form'    => 'car-edit-meta'
            ], [], true));

            $data = $this->carToForm($car);

            $vehicleType = new VehicleType();
            $data['vehicle_type_id'] = $vehicleType->getVehicleTypes($car->id);
            $point = $this->getItemPoint($car);
            if ($point) {
                $data['lat'] = $point->y();
                $data['lng'] = $point->x();
            } else {
                $data['lat'] = null;
                $data['lng'] = null;
            }

            $oldData = $car->toArray();
            $oldData['vehicle_type_id'] = $vehicleType->getVehicleTypes($car->id);
            $point = $this->getItemPoint($car);
            if ($point) {
                $data['lat'] = $point->y();
                $data['lng'] = $point->x();
            } else {
                $data['lat'] = null;
                $data['lng'] = null;
            }

            $formModerCarEditMeta->populateValues($data);

            $request = $this->getRequest();

            /*$textForm = null;
            $descriptionForm = null;*/

            if ($request->isPost() && $this->params('form') == 'car-edit-meta') {
                $formModerCarEditMeta->setData($this->params()->fromPost());
                if ($formModerCarEditMeta->isValid()) {
                    $values = $formModerCarEditMeta->getData();

                    $user = $this->user()->get();
                    $ucsTable = new DbTable\User\ItemSubscribe();
                    $ucsTable->subscribe($user, $car);

                    $forceIsGroup = in_array($car->item_type_id, [
                        DbTable\Item\Type::CATEGORY,
                        DbTable\Item\Type::TWINS,
                        DbTable\Item\Type::FACTORY,
                        DbTable\Item\Type::BRAND,
                        DbTable\Item\Type::MUSEUM
                    ]);

                    if ($haveChilds || $forceIsGroup) {
                        $values['is_group'] = 1;
                    }

                    $car->setFromArray($this->prepareCarMetaToSave($values))->save();
                    
                    $this->setLanguageName($car['id'], 'xx', $values['name']);

                    $vehicleType->setVehicleTypes($car->id, (array)$values['vehicle_type_id']);

                    if (isset($values['lat'], $values['lng'])) {
                        if (strlen($values['lat']) && strlen($values['lng'])) {
                            geoPHP::version(); // for autoload classes
                            $point = new Point($values['lng'], $values['lat']);

                            $this->setItemPoint($car, $point);
                        } else {
                            $this->setItemPoint($car, null);
                        }
                    }

                    $itemTable->updateInteritance($car);

                    $newData = $car->toArray();
                    $newData['vehicle_type_id'] = $vehicleType->getVehicleTypes($car->id);

                    $car->updateOrderCache();

                    $this->brandVehicle->refreshAutoByVehicle($car->id);

                    $htmlChanges = [];
                    foreach ($this->buildChangesMessage($oldData, $newData, 'en') as $line) {
                        $htmlChanges[] = htmlspecialchars($line);
                    }

                    $message = sprintf(
                        'Редактирование мета-информации автомобиля %s',
                        htmlspecialchars($this->car()->formatName($car, 'en')).
                        ( count($htmlChanges) ? '<p>'.implode('<br />', $htmlChanges).'</p>' : '')
                    );
                    $this->log($message, $car);

                    $user = $this->user()->get();
                    foreach ($ucsTable->getItemSubscribers($car) as $subscriber) {
                        if ($subscriber && ($subscriber->id != $user->id)) {
                            $uri = $this->hostManager->getUriByLanguage($subscriber->language);

                            $changes = $this->buildChangesMessage($oldData, $newData, $subscriber->language);

                            $message = sprintf(
                                $this->translate(
                                    'pm/user-%s-edited-vehicle-meta-data-%s-%s-%s',
                                    'default',
                                    $subscriber->language
                                ),
                                $this->userModerUrl($user, true, $uri),
                                $this->car()->formatName($car, $subscriber->language),
                                $this->carModerUrl($car, true, null, $uri),
                                ( count($changes) ? implode("\n", $changes) : '')
                            );

                            $this->message->send(null, $subscriber->id, $message);
                        }
                    }

                    return $this->redirectToCar($car, 'meta');
                }
            }
        }

        $canLogo = $this->user()->isAllowed('brand', 'logo');
        if ($canLogo) {
            $this->logoForm->setAttribute('action', $this->url()->fromRoute('moder/cars/params', [
                'action'  => 'car',
                'item_id' => $car->id,
                'tab'     => 'logo',
                'form'    => 'logo'
            ]));

            if ($request->isPost() && $this->params('form') == 'logo') {
                $data = array_merge_recursive(
                    $this->getRequest()->getPost()->toArray(),
                    $this->getRequest()->getFiles()->toArray()
                );
                $this->logoForm->setData($data);
                if ($this->logoForm->isValid()) {
                    $tempFilepath = $data['logo']['tmp_name'];

                    $imageStorage = $this->imageStorage();

                    $oldImageId = $car->logo_id;

                    $newImageId = $imageStorage->addImageFromFile($tempFilepath, 'brand');
                    $car->logo_id = $newImageId;
                    $car->save();

                    if ($oldImageId) {
                        $imageStorage->removeImage($oldImageId);
                    }

                    $this->log(sprintf(
                        'Закачен логотип %s',
                        htmlspecialchars($car->name)
                    ), $car);

                    $this->flashMessenger()->addSuccessMessage($this->translate('moder/brands/logo/saved'));

                    return $this->redirectToCar($car, 'logo');
                }
            }
        }

        $picturesCount = $pictures->getAdapter()->fetchOne(
            $pictures->getAdapter()->select()
                ->from('pictures', [new Zend_Db_Expr('COUNT(1)')])
                ->join('picture_item', 'pictures.id = picture_item.picture_id', null)
                ->where('picture_item.item_id = ?', $car->id)
        );

        $ucsTable = new DbTable\User\ItemSubscribe();

        $user = $this->user()->get();
        $ucsRow = $ucsTable->fetchRow([
            'user_id = ?' => $user->id,
            'item_id = ?'  => $car->id
        ]);

        $db = $itemTable->getAdapter();

        $carLangTable = new DbTable\Item\Language();
        $langNameCount = $carLangTable->getAdapter()->fetchOne(
            $carLangTable->getAdapter()->select()
                ->from('item_language', 'count(1)')
                ->where('item_id = ?', $car->id)
                ->where('language <> ?', 'xx')
        );

        $catalogueLinksCount = $db->fetchOne(
            $db->select()
                ->from('item_parent', 'count(1)')
                ->where('item_id = ?', $car->id)
        );
        $catalogueLinksCount += $db->fetchOne(
            $db->select()
                ->from('item_parent', 'count(1)')
                ->where('parent_id = ?', $car->id)
        );

        $engineVehiclesCount = $db->fetchOne(
            $db->select()
                ->from('item', 'count(1)')
                ->where('engine_item_id = ?', $car->id)
        );

        $linksCount = $db->fetchOne(
            $db->select()
                ->from('links', 'count(1)')
                ->where('item_id = ?', $car->id)
        );

        $tabs = [
            'meta' => [
                'icon'  => 'glyphicon glyphicon-pencil',
                'title' => 'moder/vehicle/tabs/meta',
                'count' => 0,
            ],
            'name' => [
                'icon'      => 'glyphicon glyphicon-align-left',
                'title'     => 'moder/vehicle/tabs/name',
                'data-load' => $this->url()->fromRoute('moder/cars/params', [
                    'action' => 'car-name'
                ], [], true),
                'count' => $langNameCount,
            ],
            'logo' => [
                'icon'  => 'glyphicon glyphicon-align-left',
                'title' => 'brand/logo',
                'count' => $car->logo_id ? 1 : 0,
            ],
            'catalogue' => [
                'icon'      => false,
                'title'     => 'moder/vehicle/tabs/catalogue',
                'data-load' => $this->url()->fromRoute('moder/cars/params', [
                    'action' => 'car-catalogue'
                ], [], true),
                'count' => $catalogueLinksCount,
            ],
            'vehicles' => [
                'icon'      => false,
                'title'     => 'moder/vehicle/tabs/vehicles',
                'data-load' => $this->url()->fromRoute('moder/cars/params', [
                    'action' => 'engine-vehicles'
                ], [], true),
                'count' => $engineVehiclesCount,
            ],
            'tree' => [
                'icon'      => 'fa fa-tree',
                'title'     => 'moder/vehicle/tabs/tree',
                'data-load' => $this->url()->fromRoute('moder/cars/params', [
                    'action' => 'car-tree'
                ], [], true),
                'count' => 0,
            ],
            'pictures' => [
                'icon'      => 'glyphicon glyphicon-th',
                'title'     => 'moder/vehicle/tabs/pictures',
                'data-load' => $this->url()->fromRoute('moder/cars/params', [
                    'action' => 'car-pictures'
                ], [], true),
                'count' => $picturesCount,
            ],
            'links' => [
                'icon'      => 'glyphicon glyphicon-globe',
                'title'     => 'moder/brands/links',
                'data-load' => $this->url()->fromRoute('moder/cars/params', [
                    'action' => 'car-links'
                ], [], true),
                'count' => $linksCount,
            ]
        ];

        if ($car->item_type_id == DbTable\Item\Type::MUSEUM) {
            unset($tabs['catalogue']);
            unset($tabs['tree']);
        }

        $linksTab = in_array($car->item_type_id, [
            DbTable\Item\Type::BRAND,
            DbTable\Item\Type::MUSEUM
        ]);
        if (! $linksTab) {
            unset($tabs['links']);
        }

        if ($car->item_type_id != DbTable\Item\Type::BRAND) {
            unset($tabs['logo']);
        }

        if ($car->item_type_id != DbTable\Item\Type::VEHICLE) {
            unset($tabs['twins']);
        }

        if ($car->item_type_id != DbTable\Item\Type::ENGINE) {
            unset($tabs['vehicles']);
        }

        $picturesTab = in_array($car->item_type_id, [
            DbTable\Item\Type::ENGINE,
            DbTable\Item\Type::VEHICLE,
            DbTable\Item\Type::BRAND,
            DbTable\Item\Type::FACTORY,
            DbTable\Item\Type::MUSEUM
        ]);
        if (! $picturesTab) {
            unset($tabs['pictures']);
        }

        if (! in_array($car->item_type_id, [DbTable\Item\Type::ENGINE, DbTable\Item\Type::VEHICLE])) {
            unset($tabs['factories']);
        }

        if ($this->user()->get()->id == 1) {
            $tabs['modifications'] = [
                'icon'      => 'glyphicon glyphicon-th',
                'title'     => 'moder/vehicle/tabs/modifications',
                'data-load' => $this->url()->fromRoute('moder/cars/params', [
                    'action' => 'car-modifications'
                ], [], true),
                'count' => 0
            ];
        }

        $currentTab = $this->params('tab', 'meta');
        foreach ($tabs as $id => &$tab) {
            $tab['active'] = $id == $currentTab;
        }

        $specsCount = $this->specificationsService->getSpecsCount($car->id);

        return [
            'canLogo'        => $canLogo,
            'formLogo'       => $this->logoForm,
            'picturesCount'  => $picturesCount,
            'canEditMeta'    => $canEditMeta,
            'car'            => $car,
            'randomPicture'  => $this->getRandomPicture($car),
            'subscribed'     => (bool)$ucsRow,
            'tabs'           => $tabs,
            'specsCount'     => $specsCount,
            'formModerCarEditMeta' => $formModerCarEditMeta
        ];
    }

    private function buildChangesMessage($oldData, $newData, $language)
    {
        $fields = [
            'name'             => ['str', 'moder/vehicle/changes/name-%s-%s'],
            'body'             => ['str', 'moder/vehicle/changes/body-%s-%s'],
            'begin_year'       => ['int', 'moder/vehicle/changes/from/year-%s-%s'],
            'begin_month'      => ['int', 'moder/vehicle/changes/from/month-%s-%s'],
            'end_year'         => ['int', 'moder/vehicle/changes/to/year-%s-%s'],
            'end_month'        => ['int', 'moder/vehicle/changes/to/month-%s-%s'],
            'today'            => ['bool', 'moder/vehicle/changes/to/today-%s-%s'],
            'produced'         => ['int', 'moder/vehicle/changes/produced/count-%s-%s'],
            'produced_exactly' => ['bool', 'moder/vehicle/changes/produced/exactly-%s-%s'],
            'is_concept'       => ['bool', 'moder/vehicle/changes/is-concept-%s-%s'],
            'is_group'         => ['bool', 'moder/vehicle/changes/is-group-%s-%s'],
            'begin_model_year' => ['int', 'moder/vehicle/changes/model-years/from-%s-%s'],
            'end_model_year'   => ['int', 'moder/vehicle/changes/model-years/to-%s-%s'],
            'spec_id'          => ['spec_id', 'moder/vehicle/changes/spec-%s-%s'],
            'vehicle_type_id'  => ['vehicle_type_id', 'moder/vehicle/changes/car-type-%s-%s']
        ];

        $changes = [];
        foreach ($fields as $field => $info) {
            $message = $this->translate($info[1], 'default', $language);
            switch ($info[0]) {
                case 'int':
                    $old = is_null($oldData[$field]) ? null : (int)$oldData[$field];
                    $new = is_null($newData[$field]) ? null : (int)$newData[$field];
                    if ($old !== $new) {
                        $changes[] = sprintf($message, $old, $new);
                    }
                    break;
                case 'str':
                    $old = is_null($oldData[$field]) ? null : (string)$oldData[$field];
                    $new = is_null($newData[$field]) ? null : (string)$newData[$field];
                    if ($old !== $new) {
                        $changes[] = sprintf($message, $old, $new);
                    }
                    break;
                case 'bool':
                    $old = is_null($oldData[$field])
                        ? null
                        : $this->translate($oldData[$field]
                            ? 'moder/vehicle/changes/boolean/true'
                            : 'moder/vehicle/changes/boolean/false');
                    $new = is_null($newData[$field])
                        ? null
                        : $this->translate($newData[$field]
                            ? 'moder/vehicle/changes/boolean/true'
                            : 'moder/vehicle/changes/boolean/false');
                    if ($old !== $new) {
                        $changes[] = sprintf($message, $old, $new);
                    }
                    break;

                case 'spec_id':
                    $specTable = new DbTable\Spec();
                    $old = $oldData[$field];
                    $new = $newData[$field];
                    if ($old !== $new) {
                        $old = $specTable->find($old)->current();
                        $new = $specTable->find($new)->current();
                        $changes[] = sprintf($message, $old ? $old->short_name : '-', $new ? $new->short_name : '-');
                    }
                    break;

                case 'vehicle_type_id':
                    $vehicleTypeTable = new DbTable\Vehicle\Type();
                    $old = $oldData[$field];
                    $new = $newData[$field];
                    $old = $old ? (array)$old : [];
                    $new = $new ? (array)$new : [];
                    if (array_diff($old, $new) !== array_diff($new, $old)) {
                        $oldNames = [];
                        foreach ($vehicleTypeTable->find($old) as $row) {
                            $oldNames[] = $this->translate($row->name);
                        }
                        $newNames = [];
                        foreach ($vehicleTypeTable->find($new) as $row) {
                            $newNames[] = $this->translate($row->name);
                        }
                        $changes[] = sprintf(
                            $message,
                            $oldNames ? implode(', ', $oldNames) : '-',
                            $newNames ? implode(', ', $newNames) : '-'
                        );
                    }
                    break;
            }
        }

        return $changes;
    }

    public function carSelectBrandAction()
    {
        if (! $this->user()->inheritsRole('moder')) {
            return $this->forbiddenAction();
        }

        $itemTable = $this->catalogue()->getItemTable();
        $car = $itemTable->fetchRow([
            'id = ?' => (int)$this->params('item_id'),
            'item_type_id IN (?)' => [DbTable\Item\Type::ENGINE, DbTable\Item\Type::VEHICLE]
        ]);
        if (! $car) {
            return $this->notFoundAction();
        }

        $canMove = $this->canMove($car);
        if (! $canMove) {
            return $this->forbiddenAction();
        }

        return [
            'brands' => $itemTable->fetchAll(
                $itemTable->select(true)
                    ->where('item.item_type_id = ?', DbTable\Item\Type::BRAND)
                    ->order(['item.position', 'item.name'])
            ),
            'car' => $car
        ];
    }

    public function subscribeAction()
    {
        if (! $this->user()->inheritsRole('moder')) {
            return $this->forbiddenAction();
        }

        if (! $this->getRequest()->isPost()) {
            return $this->forbiddenAction();
        }

        $itemTable = $this->catalogue()->getItemTable();

        $car = $itemTable->find($this->params('item_id'))->current();
        if (! $car) {
            return $this->notFoundAction();
        }

        $user = $this->user()->get();
        $ucsTable = new DbTable\User\ItemSubscribe();
        $ucsTable->subscribe($user, $car);

        return new JsonModel([
            'ok' => true
        ]);
    }

    public function unsubscribeAction()
    {
        if (! $this->user()->inheritsRole('moder')) {
            return $this->forbiddenAction();
        }

        if (! $this->getRequest()->isPost()) {
            return $this->forbiddenAction();
        }

        $itemTable = $this->catalogue()->getItemTable();

        $car = $itemTable->find($this->params('item_id'))->current();
        if (! $car) {
            return $this->notFoundAction();
        }

        $user = $this->user()->get();
        $ucsTable = new DbTable\User\ItemSubscribe();
        $ucsTable->unsubscribe($user, $car);

        return new JsonModel([
            'ok' => true
        ]);
    }

    public function rebuildTreeAction()
    {
        if (! $this->user()->inheritsRole('moder')) {
            return $this->forbiddenAction();
        }

        $itemTable = $this->catalogue()->getItemTable();

        $car = $itemTable->find($this->params('item_id'))->current();
        if (! $car) {
            return $this->notFoundAction();
        }

        $canEditMeta = $this->canEditMeta($car);

        if (! $canEditMeta) {
            return $this->notFoundAction();
        }

        $cpcTable = new DbTable\Item\ParentCache();

        $cpcTable->rebuildCache($car);

        return $this->redirect()->toRoute('moder/cars/params', [
            'action' => 'tree'
        ], [], true);
    }

    public function removeParentAction()
    {
        if (! $this->user()->inheritsRole('moder')) {
            return $this->forbiddenAction();
        }

        if (! $this->getRequest()->isPost()) {
            return $this->notFoundAction();
        }

        $itemTable = $this->catalogue()->getItemTable();

        $car = $itemTable->find($this->params('item_id'))->current();
        if (! $car) {
            return $this->notFoundAction();
        }

        $canEditMeta = $this->canEditMeta($car);

        if (! $canEditMeta) {
            return $this->notFoundAction();
        }

        $parentCar = $itemTable->find($this->params('parent_id'))->current();
        if (! $parentCar) {
            return $this->notFoundAction();
        }

        $this->brandVehicle->remove($parentCar->id, $car->id);

        $itemTable->updateInteritance($car);

        $vehicleType = new VehicleType();
        $vehicleType->refreshInheritanceFromParents($car->id);

        $this->specificationsService->updateActualValues($car->id);

        $message = sprintf(
            '%s перестал быть родительским автомобилем для %s',
            htmlspecialchars($this->car()->formatName($parentCar, 'en')),
            htmlspecialchars($this->car()->formatName($car, 'en'))
        );
        $this->log($message, [$car, $parentCar]);


        $ucsTable = new DbTable\User\ItemSubscribe();
        $user = $this->user()->get();

        $subscribers = [];
        foreach ($ucsTable->getItemSubscribers($car) as $subscriber) {
            $subscribers[$subscriber->id] = $subscriber;
        }

        foreach ($ucsTable->getItemSubscribers($parentCar) as $subscriber) {
            $subscribers[$subscriber->id] = $subscriber;
        }

        foreach ($subscribers as $subscriber) {
            if ($subscriber->id != $user->id) {
                $uri = $this->hostManager->getUriByLanguage($subscriber->language);

                $message = sprintf(
                    $this->translate(
                        'pm/user-%s-removed-item-%s-%s-from-item-%s-%s',
                        'default',
                        $subscriber->language
                    ),
                    $this->userModerUrl($user, true, $uri),
                    $this->car()->formatName($car, $subscriber->language),
                    $this->carModerUrl($car, true, null, $uri),
                    $this->car()->formatName($parentCar, $subscriber->language),
                    $this->carModerUrl($parentCar, true, null, $uri)
                );

                $this->message->send(null, $subscriber->id, $message);
            }
        }

        return $this->redirect()->toUrl($this->getRequest()->getServer('HTTP_REFERER'));
    }

    public function addParentAction()
    {
        if (! $this->user()->inheritsRole('moder')) {
            return $this->forbiddenAction();
        }

        /*if (!$this->getRequest()->isPost()) {
         return $this->notFoundAction();
         }*/

        $itemTable = $this->catalogue()->getItemTable();

        $car = $itemTable->find($this->params('item_id'))->current();
        if (! $car) {
            return $this->notFoundAction();
        }

        $canEditMeta = $this->canEditMeta($car);

        if (! $canEditMeta) {
            return $this->notFoundAction();
        }

        $parentCar = $itemTable->find($this->params('parent_id'))->current();
        if (! $parentCar) {
            return $this->notFoundAction();
        }

        $this->brandVehicle->create($parentCar->id, $car->id);

        $itemTable->updateInteritance($car);

        $vehicleType = new VehicleType();
        $vehicleType->refreshInheritanceFromParents($car->id);

        $this->specificationsService->updateActualValues($car->id);

        $message = sprintf(
            '%s выбран как родительский автомобиль для %s',
            htmlspecialchars($this->car()->formatName($parentCar, 'en')),
            htmlspecialchars($this->car()->formatName($car, 'en'))
        );
        $this->log($message, [$car, $parentCar]);

        $ucsTable = new DbTable\User\ItemSubscribe();
        $user = $this->user()->get();

        $subscribers = [];
        foreach ($ucsTable->getItemSubscribers($car) as $subscriber) {
            $subscribers[$subscriber->id] = $subscriber;
        }

        foreach ($ucsTable->getItemSubscribers($parentCar) as $subscriber) {
            $subscribers[$subscriber->id] = $subscriber;
        }

        foreach ($subscribers as $subscriber) {
            if ($subscriber->id != $user->id) {
                $uri = $this->hostManager->getUriByLanguage($subscriber->language);

                $message = sprintf(
                    $this->translate(
                        'pm/user-%s-adds-item-%s-%s-to-item-%s-%s',
                        'default',
                        $subscriber->language
                    ),
                    $this->userModerUrl($user, true, $uri),
                    $this->car()->formatName($car, $subscriber->language),
                    $this->carModerUrl($car, true, null, $uri),
                    $this->car()->formatName($parentCar, $subscriber->language),
                    $this->carModerUrl($parentCar, true, null, $uri)
                );

                $this->message->send(null, $subscriber->id, $message);
            }
        }


        $url = $this->url()->fromRoute('moder/cars/params', [
            'action' => 'car',
            'tab'    => 'catalogue'
        ], [], true);
        if ($this->getRequest()->isXmlHttpRequest()) {
            return new JsonModel([
                'ok'  => true,
                'url' => $url
            ]);
        } else {
            return $this->redirect()->toUrl($url);
        }
    }

    public function carAutocompleteAction()
    {
        if (! $this->user()->inheritsRole('moder')) {
            return $this->forbiddenAction();
        }

        $itemTable = $this->catalogue()->getItemTable();

        $carRow = $itemTable->find($this->params('item_id'))->current();
        if (! $carRow) {
            return $this->notFoundAction();
        }

        $query = trim($this->params()->fromQuery('q'));

        $result = [];

        $language = $this->language();
        $imageStorage = $this->imageStorage();

        $beginYear = false;
        $endYear = false;
        $today = false;
        $body = false;

        $pattern = "|^" .
                "(([0-9]{4})([-–]([^[:space:]]{2,4}))?[[:space:]]+)?(.*?)( \((.+)\))?( '([0-9]{4})(–(.+))?)?" .
            "$|isu";

        if (preg_match($pattern, $query, $match)) {
            $query = trim($match[5]);
            $body = isset($match[7]) ? trim($match[7]) : null;
            $beginYear = isset($match[9]) ? (int)$match[9] : null;
            $endYear = isset($match[11]) ? $match[11] : null;
            $beginModelYear = isset($match[2]) ? (int)$match[2] : null;
            $endModelYear = isset($match[4]) ? $match[4] : null;

            if ($endYear == 'н.в.') {
                $today = true;
                $endYear = false;
            } else {
                $eyLength = strlen($endYear);
                if ($eyLength) {
                    if ($eyLength == 2) {
                        $endYear = $beginYear - $beginYear % 100 + $endYear;
                    } else {
                        $endYear = (int)$endYear;
                    }
                } else {
                    $endYear = false;
                }
            }

            if ($endModelYear == 'н.в.') {
                $today = true;
                $endModelYear = false;
            } else {
                $eyLength = strlen($endModelYear);
                if ($eyLength) {
                    if ($eyLength == 2) {
                        $endModelYear = $beginModelYear - $beginModelYear % 100 + $endModelYear;
                    } else {
                        $endModelYear = (int)$endModelYear;
                    }
                } else {
                    $endModelYear = false;
                }
            }
        }

        $specTable = new DbTable\Spec();
        $specRow = $specTable->fetchRow([
            'INSTR(?, short_name)' => $query
        ]);

        $specId = null;
        if ($specRow) {
            $specId = $specRow->id;
            $query = trim(str_replace($specRow->short_name, '', $query));
        }

        $allowedItemTypes = [$carRow->item_type_id];
        if (in_array($carRow->item_type_id, [DbTable\Item\Type::VEHICLE, DbTable\Item\Type::ENGINE])) {
            $allowedItemTypes[] = DbTable\Item\Type::CATEGORY;
            $allowedItemTypes[] = DbTable\Item\Type::TWINS;
            $allowedItemTypes[] = DbTable\Item\Type::BRAND;
            $allowedItemTypes[] = DbTable\Item\Type::FACTORY;
        }

        if (in_array($carRow->item_type_id, [DbTable\Item\Type::BRAND])) {
            $allowedItemTypes[] = DbTable\Item\Type::BRAND;
        }

        $select = $itemTable->select(true)
            ->where('item.is_group')
            ->where('item.item_type_id IN (?)', $allowedItemTypes)
            ->join('item_language', 'item.id = item_language.item_id', null)
            ->where('item_language.name like ?', $query . '%')
            ->group('item.id')
            ->order(['length(item.name)', 'item.is_group desc', 'item.name'])
            ->limit(15);

        if ($specId) {
            $select->where('spec_id = ?', $specId);
        }

        if ($beginYear) {
            $select->where('item.begin_year = ?', $beginYear);
        }
        if ($today) {
            $select->where('item.today');
        } elseif ($endYear) {
            $select->where('item.end_year = ?', $endYear);
        }
        if ($body) {
            $select->where('item.body like ?', $body . '%');
        }

        if ($beginModelYear) {
            $select->where('item.begin_model_year = ?', $beginModelYear);
        }

        if ($endModelYear) {
            $select->where('item.end_model_year = ?', $endModelYear);
        }

        $expr = $itemTable->getAdapter()->quoteInto(
            'item.id = item_parent_cache.item_id and item_parent_cache.parent_id = ?',
            $carRow->id
        );
        $select
            ->joinLeft('item_parent_cache', $expr, null)
            ->where('item_parent_cache.item_id is null');


        $carRows = $itemTable->fetchAll($select);

        foreach ($carRows as $carRow) {
            $img = false;
            if ($carRow['logo_id']) {
                $imageInfo = $imageStorage->getFormatedImage($carRow['logo_id'], 'brandicon2');
                if ($imageInfo) {
                    $img = $imageInfo->getSrc();
                }
            }

            $result[] = [
                'url'      => $this->url()->fromRoute('moder/cars/params', [
                    'action'    => 'add-parent',
                    'parent_id' => $carRow->id
                ], [], true),
                'is_group' => (boolean)$carRow->is_group,
                'name'     => $this->car()->formatName($carRow, $language),
                'type'     => 'car',
                'image'    => $img,
            ];
        }

        return new JsonModel($result);
    }

    public function engineVehiclesAction()
    {
        if (! $this->user()->inheritsRole('moder')) {
            return $this->forbiddenAction();
        }

        $itemTable = $this->catalogue()->getItemTable();

        $engine = $itemTable->find($this->params('item_id'))->current();
        if (! $engine) {
            return $this->notFoundAction();
        }

        $items = $itemTable->fetchAll([
            'engine_item_id = ?' => $engine->id
        ]);

        $model = new ViewModel([
            'items' => $items
        ]);

        return $model->setTerminal(true);
    }

    private function carTreeWalk(DbTable\Item\Row $car, $itemParentRow = null)
    {
        $data = [
            'name'   => $this->car()->formatName($car, $this->language()),
            'url'    => $this->carModerUrl($car),
            'childs' => [],
            'type'   => $itemParentRow ? $itemParentRow->type : null
        ];

        $itemParentTable = $this->getCarParentTable();
        $itemParentRows = $itemParentTable->fetchAll(
            $itemParentTable->select(true)
                ->join('item', 'item_parent.item_id = item.id', null)
                ->where('item_parent.parent_id = ?', $car['id'])
                ->order(array_merge(['item_parent.type'], $this->catalogue()->itemOrdering()))
        );

        $itemTable = $this->catalogue()->getItemTable();
        foreach ($itemParentRows as $itemParentRow) {
            $carRow = $itemTable->find($itemParentRow->item_id)->current();
            if ($carRow) {
                $data['childs'][] = $this->carTreeWalk($carRow, $itemParentRow);
            }
        }

        return $data;
    }

    public function carTreeAction()
    {
        if (! $this->user()->inheritsRole('moder')) {
            return $this->forbiddenAction();
        }

        $itemTable = $this->catalogue()->getItemTable();

        $car = $itemTable->find($this->params('item_id'))->current();
        if (! $car) {
            return $this->notFoundAction();
        }

        $model = new ViewModel([
            'car' => $this->carTreeWalk($car)
        ]);

        return $model->setTerminal(true);
    }

    public function carCatalogueAction()
    {
        if (! $this->user()->inheritsRole('moder')) {
            return $this->forbiddenAction();
        }

        $itemTable = $this->catalogue()->getItemTable();

        $car = $itemTable->find($this->params('item_id'))->current();
        if (! $car) {
            return $this->notFoundAction();
        }

        $canAddBrand = in_array($car->item_type_id, [DbTable\Item\Type::VEHICLE, DbTable\Item\Type::ENGINE]);

        $relevantBrands = [];

        if ($canAddBrand && strlen($car->name) > 0) {
            $rows = $itemTable->fetchAll(
                $itemTable->select(true)
                    ->where('item.item_type_id = ?', DbTable\Item\Type::BRAND)
                    ->where('INSTR(?, name)', $car->name)
            );

            foreach ($rows as $row) {
                $relevantBrands[$row->id] = $row;
            }

            $brandRows = $itemTable->fetchAll(
                $itemTable->select(true)
                    ->where('item.item_type_id = ?', DbTable\Item\Type::BRAND)
                    ->join('item_parent_cache', 'item.id = item_parent_cache.parent_id', null)
                    ->where('item_parent_cache.item_id = ?', $car->id)
            );

            foreach ($brandRows as $brand) {
                unset($relevantBrands[$brand->id]);
            }
        }
        $canUseTree = true;

        $parents = [];
        $childs = [];

        $itemParentTable = $this->getCarParentTable();

        $order = array_merge(['item_parent.type'], $this->catalogue()->itemOrdering());

        $itemParentRows = $itemParentTable->fetchAll(
            $itemParentTable->select(true)
                ->join('item', 'item_parent.parent_id = item.id', null)
                ->where('item_parent.item_id = ?', $car->id)
                ->order($order)
        );
        $parents = $this->perepareCatalogueCars($itemParentRows, true);

        $itemParentRows = $itemParentTable->fetchAll(
            $itemParentTable->select(true)
                ->join('item', 'item_parent.item_id = item.id', null)
                ->where('item_parent.parent_id = ?', $car->id)
                ->order($order)
        );
        $childs = $this->perepareCatalogueCars($itemParentRows, false);

        $model = new ViewModel([
            'car'            => $car,
            'canMove'        => $this->canMove($car),
            'canAddBrand'    => $canAddBrand,
            'publicUrls'     => $this->carPublicUrls($car),
            'relevantBrands' => $relevantBrands,
            'canUseTree'     => $canUseTree,
            'parents'        => $parents,
            'childs'         => $childs
        ]);

        return $model->setTerminal(true);
    }

    /**
     * @return DbTable\Item\ParentTable
     */
    private function getCarParentTable()
    {
        return $this->itemParentTable
            ? $this->itemParentTable
            : $this->itemParentTable = new DbTable\Item\ParentTable();
    }

    private function walkUpUntilBrand($id, array $path)
    {
        $urls = [];

        $parentRows = $this->getCarParentTable()->fetchAll([
            'item_id = ?' => $id
        ]);

        $itemTable = $this->catalogue()->getItemTable();

        foreach ($parentRows as $parentRow) {
            $brand = $itemTable->fetchRow([
                'item_type_id = ?' => DbTable\Item\Type::BRAND,
                'id = ?'           => $parentRow->parent_id
            ]);

            if ($brand) {
                $urls[] = $this->url()->fromRoute('catalogue', [
                    'action'        => 'brand-item',
                    'brand_catname' => $brand->catname,
                    'car_catname'   => $parentRow->catname,
                    'path'          => $path
                ]);
            }

            $urls = array_merge(
                $urls,
                $this->walkUpUntilBrand($parentRow->parent_id, array_merge([$parentRow->catname], $path))
            );
        }

        return $urls;
    }

    private function carPublicUrls(DbTable\Item\Row $car)
    {
        if ($car['item_type_id'] == DbTable\Item\Type::FACTORY) {
            return [
                $this->url()->fromRoute('factories/factory', [
                    'action' => 'factory',
                    'id'     => $car['id'],
                ])
            ];
        }

        if ($car['item_type_id'] == DbTable\Item\Type::CATEGORY) {
            return [
                $this->url()->fromRoute('categories', [
                    'action'           => 'category',
                    'category_catname' => $car['catname'],
                ])
            ];
        }

        if ($car['item_type_id'] == DbTable\Item\Type::TWINS) {
            return [
                $this->url()->fromRoute('twins/group', [
                    'id' => $car['id'],
                ])
            ];
        }

        if ($car['item_type_id'] == DbTable\Item\Type::BRAND) {
            return [
                $this->url()->fromRoute('catalogue', [
                    'brand_catname' => $car['catname'],
                ])
            ];
        }

        return $this->walkUpUntilBrand($car->id, []);
    }

    private function perepareCatalogueCars($itemParentRows, $parent)
    {
        $cars = [];

        $itemTable = $this->catalogue()->getItemTable();
        $itemParentTable = new DbTable\Item\ParentTable();
        $itemParentLanguageTable = new DbTable\Item\ParentLanguage();
        $db = $itemParentLanguageTable->getAdapter();

        $parentIds = [];
        foreach ($itemParentRows as $itemParentRow) {
            $parentIds = $itemParentRow->parent_id;
        }

        $language = $this->language();

        $langSortExpr = new Zend_Db_Expr(
            $db->quoteInto('language = ? desc', $language)
        );

        foreach ($itemParentRows as $itemParentRow) {
            $carRow = $itemTable->fetchRow([
                'id = ?' => $parent ? $itemParentRow->parent_id : $itemParentRow->item_id
            ]);
            if (! $carRow) {
                throw new Exception("Broken car parent link");
            }

            $duplicateRow = null;
            if (! $parent) {
                $select = $itemTable->select(true)
                    ->join('item_parent', 'item.id = item_parent.item_id', null)
                    ->join('item_parent_cache', 'item_parent.item_id = item_parent_cache.parent_id', null)
                    ->where('item_parent_cache.item_id = ?', $carRow->id)
                    ->where('item_parent.parent_id = ?', $itemParentRow->parent_id)
                    ->where('item_parent.item_id <> ?', $carRow->id)
                    ->where('item_parent.type = ?', $itemParentRow->type);

                $duplicateRow = $itemTable->fetchRow($select);
            } else {
                /*$select = $itemTable->select(true)
                 ->where('item.id IN (?)', $parentIds)
                 ->where('item.id <> ?', $carRow->id)
                 ->join('item_parent_cache', 'item.id = item_parent_cache.item_id', null)
                 ->where('item_parent_cache.parent_id = ?', $carRow->id)
                 ->where('not item_parent_cache.tuning')
                 ->where('not item_parent_cache.sport');*/

                $select = $itemTable->select(true)
                    ->join('item_parent', 'item.id = item_parent.parent_id', null)
                    ->where('item_parent.item_id = ?', $itemParentRow->item_id)
                    ->where('item_parent.parent_id <> ?', $carRow->id)
                    ->join('item_parent_cache', 'item.id = item_parent_cache.item_id', null)
                    ->where('item_parent_cache.parent_id = ?', $carRow->id)
                    ->where('not item_parent_cache.tuning')
                    ->where('not item_parent_cache.sport')
                    ->where('item_parent.type = ?', DbTable\Item\ParentTable::TYPE_DEFAULT);

                $duplicateRow = $itemTable->fetchRow($select);
            }

            $itemParentLanguageRow = $itemParentLanguageTable->fetchRow([
                'item_id = ?'   => $itemParentRow->item_id,
                'parent_id = ?' => $itemParentRow->parent_id,
                'length(name) > 0'
            ], $langSortExpr);

            $cars[] = [
                'id'         => $carRow->id,
                'name'       => $carRow->getNameData($language),
                'publicUrls' => $this->carPublicUrls($carRow),
                'type'       => $itemParentRow->type,
                'duplicateRow' => $duplicateRow,
                'url'        => $this->url()->fromRoute('moder/cars/params', [
                    'action'  => 'car',
                    'item_id' => $carRow->id,
                    'tab'     => 'catalogue'
                ]),
                'parent'    => [
                    'type'    => $itemParentRow->type,
                    'name'    => $itemParentLanguageRow ? $itemParentLanguageRow->name : null,
                    'catname' => $itemParentRow->catname,
                ],
                'deleteUrl' => $this->url()->fromRoute('moder/cars/params', [
                    'action'     => 'remove-parent',
                    'item_id'    => $parent ? $itemParentRow->item_id : $carRow->id,
                    'parent_id'  => $parent ? $carRow->id : $itemParentRow->parent_id,
                ], [], true),
                'typeUrl' => $this->url()->fromRoute('moder/cars/params', [
                    'action'     => 'car-parent-set-type',
                    'item_id'    => $itemParentRow->item_id,
                    'parent_id'  => $itemParentRow->parent_id
                ], [], true),
                'catnameUrl' => $this->url()->fromRoute('moder/cars/params', [
                    'action'     => 'car-parent-set-catname',
                    'item_id'    => $itemParentRow->item_id,
                    'parent_id'  => $itemParentRow->parent_id
                ], [], true),
                'editUrl' => $this->url()->fromRoute('moder/item-parent/params', [
                    'action'    => 'item',
                    'item_id'   => $itemParentRow->item_id,
                    'parent_id' => $itemParentRow->parent_id
                ])
            ];
        }

        return $cars;
    }

    public function carNameAction()
    {
        if (! $this->user()->inheritsRole('moder')) {
            return $this->forbiddenAction();
        }

        $user = $this->user()->get();

        $itemTable = $this->catalogue()->getItemTable();
        $car = $itemTable->find($this->params('item_id'))->current();
        if (! $car) {
            return $this->notFoundAction();
        }

        $carLangTable = new DbTable\Item\Language();

        $values = [];
        $textIds = [];

        $language = $this->language();

        foreach ($this->allowedLanguages as $code) {
            $carLangRow = $carLangTable->fetchRow([
                'item_id = ?'  => $car->id,
                'language = ?' => $code
            ]);

            $text = null;
            if ($carLangRow && $carLangRow->text_id) {
                $text = $this->textStorage->getText($carLangRow->text_id);
            }

            $fullText = null;
            if ($carLangRow && $carLangRow->full_text_id) {
                $fullText = $this->textStorage->getText($carLangRow->full_text_id);
            }

            $values[$code] = [
                'name'      => $carLangRow ? $carLangRow->name : null,
                'text'      => $text,
                'full_text' => $fullText
            ];

            $textIds[$code] = [
                'text'      => $carLangRow ? $carLangRow->text_id : null,
                'full_text' => $carLangRow ? $carLangRow->full_text_id : null
            ];
        }

        $form = new ItemLanguagesForm(null, [
            'languages' => $this->allowedLanguages
        ]);
        $form->setAttribute('action', $this->url()->fromRoute(null, [], [], true));
        $form->populateValues($values);

        foreach ($textIds as $langCode => $row) {
            $fieldset = $form->get($langCode);
            $fieldset->get('text')->setAttribute('text-id', $row['text']);
            $fieldset->get('full_text')->setAttribute('text-id', $row['full_text']);
        }

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $values = $form->getData();

                $changes = [];

                foreach ($this->allowedLanguages as $lang) {
                    $langValues = $values[$lang];
                    unset($values[$lang]);

                    $fullText = $langValues['full_text'];
                    $text = $langValues['text'];
                    $name = $langValues['name'];

                    $langRow = $carLangTable->fetchRow([
                        'item_id = ?'  => $car->id,
                        'language = ?' => $lang
                    ]);
                    if (! $langRow) {
                        $langRow = $carLangTable->createRow([
                            'item_id'  => $car->id,
                            'language' => $lang
                        ]);
                    }
                    
                    $nameChanged = ($name != $langRow->name);

                    $langRow->setFromArray([
                        'name' => $name,
                    ]);

                    $textChanged = false;

                    if ($langRow->text_id) {
                        $textChanged = ($text != $this->textStorage->getText($langRow->text_id));

                        $this->textStorage->setText($langRow->text_id, $text, $user->id);
                    } elseif ($text) {
                        $textChanged = true;

                        $textId = $this->textStorage->createText($text, $user->id);
                        $langRow->text_id = $textId;
                    }

                    $fullTextChanged = false;

                    if ($langRow->full_text_id) {
                        $fullTextChanged = ($fullText != $this->textStorage->getText($langRow->full_text_id));

                        $this->textStorage->setText($langRow->full_text_id, $fullText, $user->id);
                    } elseif ($fullText) {
                        $fullTextChanged = true;

                        $fullTextId = $this->textStorage->createText($fullText, $user->id);
                        $langRow->full_text_id = $fullTextId;
                    }

                    if ($langRow->name || $langRow->text_id || $langRow->full_text_id) {
                        $langRow->save();
                    }

                    if ($nameChanged) {
                        $changes[$lang][] = 'moder/vehicle/name';
                    }

                    if ($textChanged) {
                        $changes[$lang][] = 'moder/item/short-description';
                    }

                    if ($fullTextChanged) {
                        $changes[$lang][] = 'moder/item/full-description';
                    }
                }

                $this->brandVehicle->refreshAutoByVehicle($car->id);

                if ($changes) {
                    $ucsTable = new DbTable\User\ItemSubscribe();
                    $ucsTable->subscribe($user, $car);
                    
                    foreach ($ucsTable->getItemSubscribers($car) as $subscriber) {
                        if ($subscriber && ($subscriber->id != $user->id)) {
                            $uri = $this->hostManager->getUriByLanguage($subscriber->language);
                    
                            $changesStr = [];
                            foreach ($changes as $language => $fields) {
                                foreach ($fields as $field) {
                                    $changesStr[] = $this->translate(
                                        $field, 
                                        'default',
                                        $subscriber->language
                                    ) . ' (' . $language . ')';
                                }
                            }
                    
                            $message = sprintf(
                                $this->translate(
                                    'pm/user-%s-edited-item-language-%s-%s',
                                    'default',
                                    $subscriber->language
                                ),
                                $this->userModerUrl($user, true, $uri),
                                $this->car()->formatName($car, $subscriber->language),
                                $this->carModerUrl($car, true, null, $uri),
                                implode("\n", $changesStr)
                            );
                    
                            $this->message->send(null, $subscriber->id, $message);
                        }
                    }
                }

                $this->log(sprintf(
                    'Редактирование языковых названия, описания и полного описания автомобиля %s',
                    htmlspecialchars($this->car()->formatName($car, 'en'))
                ), $car);

                return $this->redirect()->toUrl($this->carModerUrl($car, false, 'name'));
            }
        }

        $model = new ViewModel([
            'car'  => $car,
            'form' => $form
        ]);

        return $model->setTerminal(true);
    }

    public function carParentSetTypeAction()
    {
        if (! $this->user()->inheritsRole('moder')) {
            return $this->forbiddenAction();
        }

        $itemTable = $this->catalogue()->getItemTable();

        $car = $itemTable->find($this->params('item_id'))->current();
        if (! $car) {
            return $this->notFoundAction();
        }

        $parent = $itemTable->find($this->params('parent_id'))->current();
        if (! $parent) {
            return $this->notFoundAction();
        }

        $itemParentRow = $this->getCarParentTable()->fetchRow([
            'item_id = ?'   => $car->id,
            'parent_id = ?' => $parent->id
        ]);

        if (! $itemParentRow) {
            return $this->notFoundAction();
        }

        $itemParentRow->type = $this->params()->fromPost('type');
        $itemParentRow->save();

        $cpcTable = new DbTable\Item\ParentCache();
        $cpcTable->rebuildCache($car);

        return new JsonModel([
            'ok' => true
        ]);
    }

    public function carParentSetCatnameAction()
    {
        if (! $this->user()->inheritsRole('moder')) {
            return $this->forbiddenAction();
        }

        $itemTable = $this->catalogue()->getItemTable();

        $car = $itemTable->find($this->params('item_id'))->current();
        if (! $car) {
            return $this->notFoundAction();
        }

        $parent = $itemTable->find($this->params('parent_id'))->current();
        if (! $parent) {
            return $this->notFoundAction();
        }

        $itemParentTable = $this->getCarParentTable();

        $itemParentRow = $itemParentTable->fetchRow([
            'item_id = ?'   => $car->id,
            'parent_id = ?' => $parent->id
        ]);

        if (! $itemParentRow) {
            return $this->notFoundAction();
        }

        $ok = false;
        $messages = [];

        $data = $this->params()->fromPost();
        $takeCatnameFromName = ! isset($data['catname'])
                            || ! strlen($data['catname'])
                            || (! $itemParentRow->manual_catname && ($data['catname'] == $itemParentRow->item_id));
        if ($takeCatnameFromName && isset($data['name'])) {
            $data['catname'] = $data['name'];
        }

        $this->itemParentForm->setData($data);
        if ($this->itemParentForm->isValid()) {
            $values = $this->itemParentForm->getData();

            $row = $itemParentTable->fetchRow([
                'parent_id = ?' => $itemParentRow->parent_id,
                'catname = ?'   => $values['catname'],
                'item_id <> ?'  => $itemParentRow->item_id
            ]);

            if (! $row) {
                $nameIsEmpty = strlen($values['name']) == 0;

                if (! $nameIsEmpty) {
                    $itemParentRow->name = $values['name'];
                } else {
                    $itemParentRow->name = null;
                }

                $catnameIsEmpty = strlen($values['catname']) == 0 || $values['catname'] == '_';
                if (! $catnameIsEmpty) {
                    $itemParentRow->catname = $values['catname'];
                    $itemParentRow->manual_catname = 1;
                } else {
                    $itemParentRow->catname = $itemParentRow->item_id;
                    $itemParentRow->manual_catname = 0;
                }

                $itemParentRow->save();

                $ok = true;
            }
        } else {
            $messages = array_values($this->itemParentForm->catname->getMessages());
        }

        $urls = [
            (int)$car->id => $this->carPublicUrls($car)
        ];

        $itemParentTable = $this->getCarParentTable();

        $itemParentRows = $itemParentTable->fetchAll([
            'parent_id = ?' => $car->id
        ]);
        foreach ($itemParentRows as $cpRow) {
            $carRow = $itemTable->fetchRow([
                'id = ?' => $cpRow->item_id
            ]);
            if (! $carRow) {
                throw new Exception("Broken car parent link");
            }

            $urls[(int)$carRow->id] = $this->carPublicUrls($carRow);
        }

        return new JsonModel([
            'ok'         => $ok,
            'name'       => $itemParentRow->name,
            'catname'    => $itemParentRow->catname,
            'messages'   => $messages,
            'urls'       => $urls
        ]);
    }

    private function carSelectParentWalk(DbTable\Item\Row $car, $itemTypeId)
    {
        $data = [
            'name'   => $car->getNameData($this->language()),
            'url'    => $this->url()->fromRoute('moder/cars/params', [
                'parent_id' => $car['id']
            ], [], true),
            'childs' => []
        ];

        $itemTable = $this->catalogue()->getItemTable();
        $childRows = $itemTable->fetchAll(
            $itemTable->select(true)
                ->join('item_parent', 'item.id = item_parent.item_id', null)
                ->where('item_parent.parent_id = ?', $car['id'])
                ->where('item.is_group')
                ->where('item.item_type_id IN (?)', $itemTypeId)
                ->order(array_merge(['item_parent.type'], $this->catalogue()->itemOrdering()))
        );
        foreach ($childRows as $childRow) {
            $data['childs'][] = $this->carSelectParentWalk($childRow, $itemTypeId);
        }

        return $data;
    }

    public function carSelectParentAction()
    {
        if (! $this->user()->inheritsRole('moder')) {
            return $this->forbiddenAction();
        }

        $itemTable = $this->catalogue()->getItemTable();
        $car = $itemTable->find($this->params('item_id'))->current();
        if (! $car) {
            return $this->notFoundAction();
        }

        $canMove = $this->canMove($car);
        if (! $canMove) {
            return $this->forbiddenAction();
        }

        $parent = $itemTable->find($this->params('parent_id'))->current();

        if ($parent) {
            return $this->forward()->dispatch(self::class, [
                'action'    => 'add-parent',
                'item_id'   => $car->id,
                'parent_id' => $parent->id
            ]);
        }

        $tab = $this->params('tab', 'brands');

        $showBrandsTab = $car->item_type_id != DbTable\Item\Type::CATEGORY;

        if (! $showBrandsTab) {
            $tab = 'categories';
        }

        $showTwinsTab = $car->item_type_id == DbTable\Item\Type::VEHICLE;

        $showFactoriesTab = in_array($car->item_type_id, [
            DbTable\Item\Type::VEHICLE,
            DbTable\Item\Type::ENGINE
        ]);

        $brand = null;
        $brands = [];
        $cars = [];

        if ($tab == 'brands') {
            $brand = $itemTable->fetchRow([
                'item_type_id = ?' => DbTable\Item\Type::BRAND,
                'id = ?'           => (int)$this->params('brand_id')
            ]);

            if ($brand) {
                $rows = $itemTable->fetchAll(
                    $itemTable->select(true)
                        ->where('item.item_type_id = ?', $car->item_type_id)
                        ->join('item_parent', 'item.id = item_parent.item_id', null)
                        ->where('item_parent.parent_id = ?', $brand->id)
                        ->order(['item.name', 'item.body', 'item.begin_year', 'item.begin_model_year'])
                );

                foreach ($rows as $row) {
                    $cars[] = $this->carSelectParentWalk($row, $car->item_type_id);
                }
            } else {
                $brandModel = new \Application\Model\Brand();

                $brands = $brandModel->getList([
                    'language' => $this->language()
                ], null);
            }
        } elseif ($tab == 'categories') {
            $rows = $itemTable->fetchAll(
                $itemTable->select(true)
                    ->where('item.item_type_id = ?', DbTable\Item\Type::CATEGORY)
                    ->joinLeft('item_parent', 'item.id = item_parent.item_id', null)
                    ->where('item_parent.item_id IS NULL')
                    ->order(['item.name', 'item.body', 'item.begin_year', 'item.begin_model_year'])
            );


            if ($car->item_type_id == DbTable\Item\Type::CATEGORY) {
                $itemTypes = [DbTable\Item\Type::CATEGORY];
            } else {
                $itemTypes = [DbTable\Item\Type::CATEGORY]; // , DbTable\Item\Type::VEHICLE
            }

            foreach ($rows as $row) {
                $cars[] = $this->carSelectParentWalk($row, $itemTypes);
            }
        } elseif ($tab == 'twins') {
            $brand = $itemTable->fetchRow([
                'item_type_id = ?' => DbTable\Item\Type::BRAND,
                'id = ?'           => (int)$this->params('brand_id')
            ]);

            if ($brand) {
                $rows = $itemTable->fetchAll(
                    $itemTable->select(true)
                        ->where('item.item_type_id = ?', DbTable\Item\Type::TWINS)
                        ->join(['ipc1' => 'item_parent_cache'], 'ipc1.parent_id = item.id', null)
                        ->join(['ipc2' => 'item_parent_cache'], 'ipc1.item_id = ipc2.item_id', null)
                        ->where('ipc2.parent_id = ?', $brand->id)
                        ->group('item.id')
                        ->order($this->catalogue()->itemOrdering())
                );

                foreach ($rows as $row) {
                    $cars[] = [
                        'name'   => $row->getNameData($this->language()),
                        'url'    => $this->url()->fromRoute('moder/cars/params', [
                            'parent_id' => $row['id']
                        ], [], true),
                        'childs' => []
                    ];
                }
            } else {
                $brandModel = new \Application\Model\Brand();

                $brands = $brandModel->getList([
                    'language' => $this->language()
                ], function ($select) {
                    $select
                        ->join(['ipc1' => 'item_parent_cache'], 'item.id = ipc1.parent_id', null)
                        ->join('item_parent', 'ipc1.item_id = item_parent.item_id', null)
                        ->join(['twins' => 'item'], 'item_parent.parent_id = twins.id', null)
                        ->where('twins.item_type_id = ?', DbTable\Item\Type::TWINS)
                        ->group('item.id');
                });
            }
        } elseif ($tab == 'factories') {
            $rows = $itemTable->fetchAll(
                $itemTable->select(true)
                    ->where('item_type_id = ?', DbTable\Item\Type::FACTORY)
                    ->order($this->catalogue()->itemOrdering())
            );

            foreach ($rows as $row) {
                $cars[] = [
                    'name'   => $row->getNameData($this->language()),
                    'url'    => $this->url()->fromRoute('moder/cars/params', [
                        'parent_id' => $row['id']
                    ], [], true),
                    'childs' => []
                ];
            }
        }

        return [
            'tab'              => $tab,
            'car'              => $car,
            'brand'            => $brand,
            'brands'           => $brands,
            'cars'             => $cars,
            'showBrandsTab'    => $showBrandsTab,
            'showTwinsTab'     => $showTwinsTab,
            'showFactoriesTab' => $showFactoriesTab
        ];
    }

    private function loadSpecs($table, $parentId, $deep = 0)
    {
        if ($parentId) {
            $filter = ['parent_id = ?' => $parentId];
        } else {
            $filter = ['parent_id is null'];
        }

        $result = [];
        foreach ($table->fetchAll($filter, 'short_name') as $row) {
            $result[$row->id] = str_repeat('...', $deep) . $row->short_name;
            $result = array_replace($result, $this->loadSpecs($table, $row->id, $deep + 1));
        }

        return $result;
    }

    public function organizeAction()
    {
        if (! $this->user()->inheritsRole('moder')) {
            return $this->forbiddenAction();
        }

        $itemTable = $this->catalogue()->getItemTable();

        $car = $itemTable->find($this->params('item_id'))->current();
        if (! $car) {
            return $this->notFoundAction();
        }

        $canMove = $this->canMove($car);
        if (! $canMove) {
            return $this->forbiddenAction();
        }

        $itemParentTable = $this->getCarParentTable();
        $itemTable = $this->catalogue()->getItemTable();

        $order = array_merge(['item_parent.type'], $this->catalogue()->itemOrdering());

        $itemParentRows = $itemParentTable->fetchAll(
            $itemParentTable->select(true)
                ->join('item', 'item_parent.item_id = item.id', null)
                ->where('item_parent.parent_id = ?', $car->id)
                ->where('item_parent.type = ?', DbTable\Item\ParentTable::TYPE_DEFAULT)
                ->order($order)
        );

        $childs = [];
        foreach ($itemParentRows as $childRow) {
            $carRow = $itemTable->find($childRow->item_id)->current();
            $childs[$carRow->id] = $this->car()->formatName($carRow, $this->language());
        }

        $specTable = new DbTable\Spec();
        $specOptions = $this->loadSpecs($specTable, null, 0);

        $db = $itemTable->getAdapter();
        $avgSpecId = $db->fetchOne(
            $db->select()
                ->from($itemTable->info('name'), 'AVG(spec_id)')
                ->join('item_parent', 'item.id = item_parent.parent_id', null)
                ->where('item_parent.item_id = ?', $car->id)
        );
        $inheritedSpec = null;
        if ($avgSpecId) {
            $avgSpec = $specTable->find($avgSpecId)->current();
            if ($avgSpec) {
                $inheritedSpec = $avgSpec->short_name;
            }
        }

        $form = new CarOrganizeForm(null, [
            'itemType'           => $car->item_type_id,
            'language'           => $this->language(),
            'childOptions'       => $childs,
            'inheritedIsConcept' => $car->is_concept,
            'specOptions'        => array_replace(['' => '-'], $specOptions),
            'inheritedSpec'      => $inheritedSpec,
            'translator'         => $this->translator
        ]);

        $form->setAttribute('action', $this->url()->fromRoute('moder/cars/params', [], [], true));

        $data = $this->carToForm($car);
        $data['is_group'] = true;

        $vehicleType = new VehicleType();
        $data['vehicle_type_id'] = $vehicleType->getVehicleTypes($car->id);

        $form->populateValues($data);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $values = $form->getData();

                $values['is_group'] = true;

                $newCar = $itemTable->createRow(
                    $this->prepareCarMetaToSave($values)
                );
                $newCar->item_type_id = $car->item_type_id;
                $newCar->save();
                
                $this->setLanguageName($newCar['id'], 'xx', $values['name']);

                $vehicleType->setVehicleTypes($newCar->id, (array)$values['vehicle_type_id']);

                $newCar->updateOrderCache();

                $cpcTable = new DbTable\Item\ParentCache();
                $cpcTable->rebuildCache($newCar);

                $url = $this->url()->fromRoute('moder/cars/params', [
                    'action'  => 'car',
                    'item_id' => $newCar->id
                ]);
                $this->log(sprintf(
                    'Создан новый автомобиль %s',
                    htmlspecialchars($this->car()->formatName($newCar, 'en'))
                ), $newCar);

                $this->brandVehicle->create($car->id, $newCar->id);

                $message = sprintf(
                    '%s выбран как родительский автомобиль для %s',
                    htmlspecialchars($this->car()->formatName($car, 'en')),
                    htmlspecialchars($this->car()->formatName($newCar, 'en'))
                );
                $this->log($message, [$car, $newCar]);

                $itemTable->updateInteritance($newCar);


                $childCarRows = $itemTable->find($values['childs']);

                foreach ($childCarRows as $childCarRow) {
                    $this->brandVehicle->create($newCar->id, $childCarRow->id);

                    $message = sprintf(
                        '%s выбран как родительский автомобиль для %s',
                        htmlspecialchars($this->car()->formatName($newCar, 'en')),
                        htmlspecialchars($this->car()->formatName($childCarRow, 'en'))
                    );
                    $this->log($message, [$newCar, $childCarRow]);

                    $this->brandVehicle->remove($car->id, $childCarRow->id);

                    $message = sprintf(
                        '%s перестал быть родительским автомобилем для %s',
                        htmlspecialchars($this->car()->formatName($car, 'en')),
                        htmlspecialchars($this->car()->formatName($childCarRow, 'en'))
                    );
                    $this->log($message, [$car, $childCarRow]);

                    $itemTable->updateInteritance($childCarRow);
                }

                $this->specificationsService->updateActualValues($newCar->id);

                $user = $this->user()->get();
                $ucsTable = new DbTable\User\ItemSubscribe();
                $ucsTable->subscribe($user, $newCar);

                return $this->redirect()->toUrl($this->carModerUrl($car, false, 'catalogue'));
            }
        }

        return [
            'car'    => $car,
            //'childs' => $childs,
            'form'   => $form
        ];
    }

    private function prepareCarMetaToSave(array $values)
    {
        $endYear = (int)$values['end']['year'];

        $today = null;
        if ($endYear) {
            if ($endYear < date('Y')) {
                $today = 0;
            } else {
                $today = null;
            }
        } else {
            if (strlen($values['end']['today'])) {
                $today = $values['end']['today'] ? 1 : 0;
            } else {
                $today = null;
            }
        }

        if (isset($values['is_concept'])) {
            $isConcept = false;
            $isConceptInherit = false;
            if ($values['is_concept'] == 'inherited') {
                $isConceptInherit = true;
            } else {
                $isConcept = (bool)$values['is_concept'];
            }
        } else {
            $isConcept = false;
            $isConceptInherit = true;
        }

        $catname = null;
        if (isset($values['catname'])) {
            if (! $values['catname']) {
                $values['catname'] = $values['name'];
            }

            $filter = new \Autowp\ZFComponents\Filter\FilenameSafe();
            $catname = $filter->filter($values['catname']);
        }

        $result = [
            'name'               => $values['name'],
            'full_name'          => isset($values['full_name']) && $values['full_name'] ? $values['full_name'] : null,
            'catname'            => $catname,
            'body'               => isset($values['body']) ? $values['body'] : '',
            'begin_year'         => $values['begin']['year'] ? $values['begin']['year'] : null,
            'begin_month'        => $values['begin']['month'] ? $values['begin']['month'] : null,
            'end_year'           => $endYear ? $endYear : null,
            'end_month'          => $values['end']['month'] ? $values['end']['month'] : null,
            'today'              => $today,
            'is_concept'         => $isConcept ? 1 : 0,
            'is_concept_inherit' => $isConceptInherit ? 1 : 0,
            'is_group'           => isset($values['is_group']) && $values['is_group'] ? 1 : 0,
            'begin_model_year'   => null,
            'end_model_year'     => null,
            'produced_exactly'   => 0
        ];

        if (array_key_exists('model_year', $values)) {
            $result['begin_model_year'] = $values['model_year']['begin'] ? $values['model_year']['begin'] : null;
            $result['end_model_year']   = $values['model_year']['end'] ? $values['model_year']['end'] : null;
        }

        if (array_key_exists('vehicle_type_id', $values)) {
            $result['vehicle_type_id'] = $values['vehicle_type_id'];
        }

        if (array_key_exists('spec_id', $values)) {
            $specId = null;
            $specInherit = false;
            if ($values['spec_id'] == 'inherited') {
                $specInherit = true;
            } else {
                $specId = (int)$values['spec_id'];
                if (! $specId) {
                    $specId = null;
                }
            }

            $result['spec_id'] = $specId;
            $result['spec_inherit'] = $specInherit ? 1 : 0;
        }

        if (array_key_exists('produced', $values)) {
            $result['produced'] = strlen($values['produced']['count']) ? (int)$values['produced']['count'] : null;
            $result['produced_exactly'] = $values['produced']['exactly'] ? 1 : 0;
        }

        if (array_key_exists('lat', $values)) {
            $result['lat'] = $values['lat'];
        }

        if (array_key_exists('lng', $values)) {
            $result['lng'] = $values['lng'];
        }

        return $result;
    }

    public function newAction()
    {
        if (! $this->user()->isAllowed('car', 'add')) {
            return $this->forbiddenAction();
        }

        $itemTypeId = (int)$this->params('item_type_id');
        switch ($itemTypeId) {
            case DbTable\Item\Type::VEHICLE:
            case DbTable\Item\Type::ENGINE:
                $forceIsGroup = false;
                break;
            case DbTable\Item\Type::CATEGORY:
            case DbTable\Item\Type::TWINS:
            case DbTable\Item\Type::BRAND:
            case DbTable\Item\Type::FACTORY:
            case DbTable\Item\Type::MUSEUM:
                $forceIsGroup = true;
                break;
            default:
                return $this->notFoundAction();
        }

        $itemTable = $this->catalogue()->getItemTable();

        $parentCar = $itemTable->fetchRow([
            'id = ?' => (int)$this->params('parent_id')
        ]);

        if ($parentCar) {
            if (! $this->brandVehicle->isAllowedCombination($itemTypeId, $parentCar['item_type_id'])) {
                return $this->forbiddenAction();
            }
        }

        $specTable = new DbTable\Spec();
        $specOptions = $this->loadSpecs($specTable, null, 0);

        $inheritedSpec = null;
        if ($parentCar) {
            if ($parentCar->spec_id) {
                $specRow = $specTable->find($parentCar->spec_id)->current();
                if ($specRow) {
                    $inheritedSpec = $specRow->short_name;
                }
            }
        }

        $form = new CarForm(null, [
            'itemType'           => $itemTypeId,
            'inheritedIsConcept' => $parentCar ? $parentCar->is_concept : null,
            'specOptions'        => array_replace(['' => '-'], $specOptions),
            'inheritedSpec'      => $inheritedSpec,
            'translator'         => $this->translator
        ]);
        $form->setAttribute('action', $this->url()->fromRoute('moder/cars/params', [], [], true));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $values = $this->prepareCarMetaToSave($form->getData());

                if ($forceIsGroup) {
                    $values['is_group'] = 1;
                }

                $car = $itemTable->createRow($values);
                $car->item_type_id = $itemTypeId;
                $car->save();

                if (isset($values['lat'], $values['lng'])) {
                    if (strlen($values['lat']) && strlen($values['lng'])) {
                        geoPHP::version(); // for autoload classes
                        $point = new Point($values['lng'], $values['lat']);

                        $this->setItemPoint($car, $point);
                    } else {
                        $this->setItemPoint($car, null);
                    }
                }
                
                $this->setLanguageName($car['id'], 'xx', $values['name']);

                $vehicleType = new VehicleType();
                $vehicleType->setVehicleTypes($car->id, (array)$values['vehicle_type_id']);

                $car->updateOrderCache();

                $cpcTable = new DbTable\Item\ParentCache();
                $cpcTable->rebuildCache($car);

                $vehicleType = new VehicleType();
                $vehicleType->refreshInheritanceFromParents($car->id);

                $namespace = new \Zend\Session\Container('Moder_Car');
                $namespace->lastCarId = $car->id;

                $url = $this->url()->fromRoute('moder/cars/params', [
                    'action'  => 'car',
                    'item_id' => $car->id
                ]);
                $this->log(sprintf(
                    'Создан новый автомобиль %s',
                    htmlspecialchars($this->car()->formatName($car, 'en'))
                ), $car);

                $user = $this->user()->get();
                $ucsTable = new DbTable\User\ItemSubscribe();
                $ucsTable->subscribe($user, $car);

                if ($parentCar) {
                    $this->brandVehicle->create($parentCar->id, $car->id);

                    $message = sprintf(
                        '%s выбран как родительский автомобиль для %s',
                        htmlspecialchars($this->car()->formatName($parentCar, 'en')),
                        htmlspecialchars($this->car()->formatName($car, 'en'))
                    );
                    $this->log($message, [$car, $parentCar]);
                }

                $itemTable->updateInteritance($car);

                $this->specificationsService->updateInheritedValues($car->id);

                return $this->redirect()->toUrl($url);
            }
        }

        return [
            'itemTypeId' => $itemTypeId,
            'parentCar'  => $parentCar,
            'form'       => $form
        ];
    }

    private function pictureUrl(DbTable\Picture\Row $picture)
    {
        return $this->url()->fromRoute('moder/pictures/params', [
            'action'     => 'picture',
            'picture_id' => $picture->id
        ]);
    }
    
    private function setLanguageName($carId, $language, $name)
    {
        $carLangTable = new DbTable\Item\Language();
        
        $carLangRow = $carLangTable->fetchRow([
            'item_id = ?'  => $carId,
            'language = ?' => $language
        ]);
        
        if (! $carLangRow) {
            $carLangRow = $carLangTable->createRow([
                'item_id'  => $carId,
                'language' => $language
            ]);
        }
        $carLangRow['name'] = $name;
        $carLangRow->save();
    }

    public function organizePicturesAction()
    {
        if (! $this->user()->inheritsRole('moder')) {
            return $this->forbiddenAction();
        }

        $itemTable = $this->catalogue()->getItemTable();

        $car = $itemTable->find($this->params('item_id'))->current();
        if (! $car) {
            return $this->notFoundAction();
        }

        $canMove = $this->canMove($car);
        if (! $canMove) {
            return $this->forbiddenAction();
        }

        $itemParentTable = $this->getCarParentTable();
        $itemTable = $this->catalogue()->getItemTable();
        $imageStorage = $this->imageStorage();

        $childs = [];
        $pictureTable = $this->catalogue()->getPictureTable();
        $rows = $pictureTable->fetchAll(
            $pictureTable->select(true)
                ->join('picture_item', 'pictures.id = picture_item.picture_id', null)
                ->where('picture_item.item_id = ?', $car->id)
                ->order(['pictures.status', 'pictures.id'])
        );
        foreach ($rows as $row) {
            $request = DbTable\Picture\Row::buildFormatRequest($row->toArray());
            $imageInfo = $imageStorage->getFormatedImage($request, 'picture-thumb');
            if ($imageInfo) {
                $childs[$row->id] = $imageInfo->getSrc();
            }
        }

        $specTable = new DbTable\Spec();
        $specOptions = $this->loadSpecs($specTable, null, 0);

        $db = $itemTable->getAdapter();
        $avgSpecId = $db->fetchOne(
            $db->select()
                ->from($itemTable->info('name'), 'AVG(spec_id)')
                ->join('item_parent', 'item.id = item_parent.parent_id', null)
                ->where('item_parent.item_id = ?', $car->id)
        );
        $inheritedSpec = null;
        if ($avgSpecId) {
            $avgSpec = $specTable->find($avgSpecId)->current();
            if ($avgSpec) {
                $inheritedSpec = $avgSpec->short_name;
            }
        }

        $form = new CarOrganizePicturesForm(null, [
            'language'           => $this->language(),
            'childOptions'       => $childs,
            'inheritedIsConcept' => $car->is_concept,
            'specOptions'        => array_replace(['' => '-'], $specOptions),
            'inheritedSpec'      => $inheritedSpec,
            'translator'         => $this->translator
        ]);

        $form->setAttribute('action', $this->url()->fromRoute('moder/cars/params', [], [], true));

        $data = $this->carToForm($car);
        $data['is_group'] = false;

        $vehicleType = new VehicleType();
        $data['vehicle_type_id'] = $vehicleType->getVehicleTypes($car->id);

        $form->populateValues($data);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $values = $form->getData();

                $values['is_group'] = false;
                $values['produced_exactly'] = false;
                $values['description'] = '';

                $newCar = $itemTable->createRow(
                    $this->prepareCarMetaToSave($values)
                );
                $newCar->item_type_id = $car->item_type_id;
                $newCar->save();
                
                $this->setLanguageName($newCar['id'], 'xx', $values['name']);

                $vehicleType->setVehicleTypes($newCar->id, (array)$values['vehicle_type_id']);

                $newCar->updateOrderCache();

                $cpcTable = new DbTable\Item\ParentCache();
                $cpcTable->rebuildCache($newCar);

                $url = $this->url()->fromRoute('moder/cars/params', [
                    'action'  => 'car',
                    'item_id' => $newCar->id
                ]);
                $this->log(sprintf(
                    'Создан новый автомобиль %s',
                    htmlspecialchars($this->car()->formatName($newCar, 'en'))
                ), $newCar);

                $car->is_group = 1;
                $car->save();

                $this->brandVehicle->create($car->id, $newCar->id);

                $message = sprintf(
                    '%s выбран как родительский автомобиль для %s',
                    htmlspecialchars($this->car()->formatName($car, 'en')),
                    htmlspecialchars($this->car()->formatName($newCar, 'en'))
                );
                $this->log($message, [$car, $newCar]);

                $itemTable->updateInteritance($newCar);


                $pictureRows = $pictureTable->find($values['childs']);

                foreach ($pictureRows as $pictureRow) {
                    $this->pictureItem->changePictureItem($pictureRow->id, $car->id, $newCar->id);

                    $this->imageStorage()->changeImageName($pictureRow->image_id, [
                        'pattern' => $pictureRow->getFileNamePattern(),
                    ]);

                    $this->log(sprintf(
                        'Картинка %s связана с автомобилем %s',
                        htmlspecialchars($pictureRow->id),
                        htmlspecialchars($this->car()->formatName($car, 'en'))
                    ), [$car, $pictureRow]);
                }

                $brandModel = new BrandModel();

                $this->specificationsService->updateActualValues($newCar->id);

                $user = $this->user()->get();
                $ucsTable = new DbTable\User\ItemSubscribe();
                $ucsTable->subscribe($user, $newCar);

                return $this->redirect()->toUrl($this->carModerUrl($car, false, 'catalogue'));
            }
        }

        return [
            'car'    => $car,
            //'childs' => $childs,
            'form'   => $form
        ];
    }

    private function carMofificationsGroupModifications(DbTable\Item\Row $car, $groupId)
    {
        $modModel = new Modification();
        $mTable = new DbTable\Modification();
        $db = $mTable->getAdapter();
        $itemTable = $this->catalogue()->getItemTable();

        $language = $this->language();

        $select = $mTable->select(true)
            ->join('item_parent_cache', 'modification.item_id = item_parent_cache.parent_id', null)
            ->where('item_parent_cache.item_id = ?', $car->id)
            ->order('modification.name');

        if ($groupId) {
            $select->where('modification.group_id = ?', $groupId);
        } else {
            $select->where('modification.group_id IS NULL');
        }

        $modifications = [];
        foreach ($mTable->fetchAll($select) as $mRow) {
            $picturesCount = $db->fetchOne(
                $db->select()
                    ->from('modification_picture', 'count(1)')
                    ->where('modification_picture.modification_id = ?', $mRow->id)
                    ->join('pictures', 'modification_picture.picture_id = pictures.id', null)
                    ->join('picture_item', 'pictures.id = picture_item.picture_id', null)
                    ->join('item_parent_cache', 'picture_item.item_id = item_parent_cache.item_id', null)
                    ->where('item_parent_cache.parent_id = ?', $car->id)
            );

            $isInherited = $mRow->item_id != $car->id;
            $inheritedFrom = null;

            if ($isInherited) {
                $carRow = $itemTable->fetchRow(
                    $itemTable->select(true)
                        ->join('item_parent_cache', 'item.id = item_parent_cache.parent_id', null)
                        ->join('modification', 'item.id = modification.item_id', null)
                        ->where('modification.id = ?', $mRow['id'])
                );

                if ($carRow) {
                    $inheritedFrom = [
                        'name' => $this->car()->formatName($carRow, $language),
                        'url'  => $this->carModerUrl($carRow)
                    ];
                }
            }

            $modifications[] = [
                'inherited'     => $isInherited,
                'inheritedFrom' => $inheritedFrom,
                'name'      => $mRow->name,
                'url'       => $this->url()->fromRoute('moder/modification/params', [
                    'action'          => 'edit',
                    'item_id'         => $car['id'],
                    'modification_id' => $mRow->id
                ], [], true),
                'count'     => $picturesCount,
                'canDelete' => ! $isInherited && $modModel->canDelete($mRow->id),
                'deleteUrl' => $this->url()->fromRoute('moder/modification/params', [
                    'action'     => 'delete',
                    'id'         => $mRow->id
                ], [], true)
            ];
        }

        return $modifications;
    }

    public function carModificationsAction()
    {
        if (! $this->user()->inheritsRole('moder')) {
            return $this->forbiddenAction();
        }

        $itemTable = $this->catalogue()->getItemTable();

        $car = $itemTable->find($this->params('item_id'))->current();
        if (! $car) {
            return $this->notFoundAction();
        }

        $mgTable = new DbTable\Modification\Group();

        $mgRows = $mgTable->fetchAll(
            $mgTable->select(true)
        );

        $groups = [];
        foreach ($mgRows as $mgRow) {
            $groups[] = [
                'name'          => $mgRow->name,
                'modifications' => $this->carMofificationsGroupModifications($car, $mgRow->id)
            ];
        }

        $groups[] = [
            'name'          => null,
            'modifications' => $this->carMofificationsGroupModifications($car, null),
        ];

        $model = new ViewModel([
            'car'    => $car,
            'groups' => $groups
        ]);
        return $model->setTerminal(true);
    }

    public function carModificationPicturesAction()
    {
        if (! $this->user()->inheritsRole('moder')) {
            return $this->forbiddenAction();
        }

        $itemTable = $this->catalogue()->getItemTable();

        $car = $itemTable->find($this->params('item_id'))->current();
        if (! $car) {
            return $this->notFoundAction();
        }

        $mTable = new DbTable\Modification();
        $mpTable = new DbTable\Modification\Picture();
        $mgTable = new DbTable\Modification\Group();
        $pictureTable = new DbTable\Picture();
        $db = $mpTable->getAdapter();
        $imageStorage = $this->imageStorage();
        $language = $this->language();


        $request = $this->getRequest();
        if ($request->isPost()) {
            $picture = (array)$this->params('picture', []);

            foreach ($picture as $pictureId => $modificationIds) {
                $pictureRow = $pictureTable->fetchRow(
                    $pictureTable->select(true)
                        ->where('pictures.id = ?', (int)$pictureId)
                        ->join('picture_item', 'pictures.id = picture_item.picture_id', null)
                        ->join('item_parent_cache', 'picture_item.item_id = item_parent_cache.item_id', null)
                        ->where('item_parent_cache.parent_id = ?', $car->id)
                );

                if ($pictureRow) {
                    foreach ($modificationIds as &$modificationId) {
                        $modificationId = (int)$modificationId;

                        $mpRow = $mpTable->fetchRow([
                            'picture_id = ?'      => $pictureRow->id,
                            'modification_id = ?' => $modificationId
                        ]);
                        if (! $mpRow) {
                            $mpRow = $mpTable->createRow([
                                'picture_id'      => $pictureRow->id,
                                'modification_id' => $modificationId
                            ]);
                            $mpRow->save();
                        }
                    }
                    unset($modificationId); // prevent bugs

                    $select = $mpTable->select(true)
                        ->where('modification_picture.picture_id = ?', $pictureRow->id)
                        ->join('modification', 'modification_picture.modification_id = modification.id', null)
                        ->join('item_parent_cache', 'modification.item_id = item_parent_cache.parent_id', null)
                        ->where('item_parent_cache.item_id = ?', $car->id);

                    if ($modificationIds) {
                        $select->where('modification.id not in (?)', $modificationIds);
                    }

                    $mpRows = $mpTable->fetchAll($select);
                    foreach ($mpRows as $mpRow) {
                        $mpRow->delete();
                    }
                }
            }

            return $this->redirectToCar($car, 'modifications');
        }



        $pictures = [];

        $pictureRows = $pictureTable->fetchAll(
            $pictureTable->select(true)
                ->join('picture_item', 'pictures.id = picture_item.picture_id', null)
                ->join('item_parent_cache', 'picture_item.item_id = item_parent_cache.item_id', null)
                ->where('item_parent_cache.parent_id = ?', $car->id)
                ->order('pictures.id')
        );

        foreach ($pictureRows as $pictureRow) {
            $request = DbTable\Picture\Row::buildFormatRequest($pictureRow->toArray());
            $imageInfo = $imageStorage->getFormatedImage($request, 'picture-thumb');

            $modificationIds = $db->fetchCol(
                $db->select()
                    ->from('modification_picture', 'modification_id')
                    ->where('picture_id = ?', $pictureRow->id)
            );

            $pictures[] = [
                'id'              => $pictureRow->id,
                'name'            => $this->pic()->name($pictureRow, $language),
                'url'             => $this->pic()->href($pictureRow),
                'src'             => $imageInfo ? $imageInfo->getSrc() : null,
                'modificationIds' => $modificationIds
            ];
        }


        $mgRows = $mgTable->fetchAll(
            $mgTable->select(true)
        );

        $groups = [];
        foreach ($mgRows as $mgRow) {
            $mRows = $mTable->fetchAll(
                $mTable->select(true)
                ->where('modification.group_id = ?', $mgRow->id)
                ->join('item_parent_cache', 'modification.item_id = item_parent_cache.parent_id', null)
                ->where('item_parent_cache.item_id = ?', $car->id)
                ->order('modification.name')
            );

            $modifications = [];
            foreach ($mRows as $mRow) {
                $modifications[] = [
                    'id'     => $mRow->id,
                    'name'   => $mRow->name,
                ];
            }

            $groups[] = [
                'name'          => $mgRow->name,
                'modifications' => $modifications
            ];
        }

        $mRows = $mTable->fetchAll(
            $mTable->select(true)
            ->where('modification.group_id IS NULL')
            ->join('item_parent_cache', 'modification.item_id = item_parent_cache.parent_id', null)
            ->where('item_parent_cache.item_id = ?', $car->id)
            ->order('modification.name')
        );

        $modifications = [];
        foreach ($mRows as $mRow) {
            $modifications[] = [
                'id'   => $mRow->id,
                'name' => $mRow->name,
            ];
        }

        $groups[] = [
            'name'          => null,
            'modifications' => $modifications
        ];


        return [
            'pictures' => $pictures,
            'groups'   => $groups
        ];
    }

    public function saveLinksAction()
    {
        $itemTable = $this->catalogue()->getItemTable();
        $item = $itemTable->fetchRow([
            'id = ?'           => (int)$this->params('item_id'),
            'item_type_id = ?' => DbTable\Item\Type::BRAND
        ]);
        if (! $item) {
            return $this->notFoundAction();
        }

        $canEditMeta = $this->canEditMeta($item);
        if (! $canEditMeta) {
            return $this->forbiddenAction();
        }

        $links = new DbTable\Item\Link();

        $data = $this->params()->fromPost('link');
        $data = is_array($data) ? $data : [];
        foreach ($data as $id => $link) {
            $row = $links->find($id)->current();
            if ($row) {
                if (strlen($link['url'])) {
                    $row->name = $link['name'];
                    $row->url = $link['url'];
                    $row->type = $link['type'];

                    $row->save();
                } else {
                    $row->delete();
                }
            }
        }

        if ($new = $this->params()->fromPost('new')) {
            if (strlen($new['url'])) {
                $row = $links->fetchNew();
                $row->item_id = $item->id;
                $row->name = $new['name'];
                $row->url = $new['url'];
                $row->type = $new['type'];

                $row->save();
            }
        }

        return $this->redirectToCar($item, 'links');
    }

    public function carLinksAction()
    {
        if (! $this->user()->inheritsRole('moder')) {
            return $this->forbiddenAction();
        }

        $itemTable = $this->catalogue()->getItemTable();

        $item = $itemTable->find($this->params('item_id'))->current();
        if (! $item) {
            return $this->notFoundAction();
        }

        $linkTable = new DbTable\Item\Link();
        $linkRows = $linkTable->fetchAll([
            'item_id = ?' => $item->id
        ]);

        $links = [];
        foreach ($linkRows as $link) {
            $links[] = [
                'id'   => $link->id,
                'name' => $link->name,
                'url'  => $link->url,
                'type' => $link->type
            ];
        }

        $canEditMeta = $this->canEditMeta($item);

        $model = new ViewModel([
            'canEdit' => $canEditMeta,
            'links'   => $links,
        ]);

        return $model->setTerminal(true);
    }
}
