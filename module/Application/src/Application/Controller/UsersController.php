<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

use Autowp\Commons\Paginator\Adapter\Zend1DbTableSelect;
use Autowp\Traffic\TrafficControl;
use Autowp\User\Model\DbTable\User;
use Autowp\User\Model\DbTable\User\Rename as UserRename;

use Application\Comments;
use Application\Model\Brand as BrandModel;
use Application\Model\DbTable;
use Application\Model\Contact;

use Zend_Db_Expr;

use DateInterval;
use DateTime;
use DateTimeZone;

class UsersController extends AbstractActionController
{
    private $cache;

    /**
     * @var TrafficControl
     */
    private $trafficControl;

    /**
     * @var Comments
     */
    private $comments;

    public function __construct(
        $cache,
        TrafficControl $trafficControl,
        Comments $comments
    ) {
        $this->cache = $cache;
        $this->trafficControl = $trafficControl;
        $this->comments = $comments;
    }

    private function getUser()
    {
        $users = new User();

        $identity = $this->params('user_id');

        if (preg_match('|^user([0-9]+)$|isu', $identity, $match)) {
            return $users->fetchRow([
                'id = ?' => (int)$match[1],
                'identity is null',
                'not deleted'
            ]);
        }

        return $users->fetchRow([
            'identity = ?' => $identity,
            'not deleted'
        ]);
    }

    private function getLastComments($user)
    {
        $paginator = $this->comments->service()->getMessagesPaginator([
            'user'            => $user->id,
            'exclude_type'    => \Application\Comments::FORUMS_TYPE_ID,
            'exclude_deleted' => true,
            'order'           => 'datetime DESC'
        ]);

        $paginator->setItemCountPerPage(15);

        $lastComments = [];
        foreach ($paginator->getCurrentItems() as $row) {
            $lastComments[] = [
                'url'     => $this->comments->getMessageRowUrl($row),
                'message' => $this->comments->getMessagePreview($row['message'])
            ];
        }

        return $lastComments;
    }

    public function userAction()
    {
        $users = new User();

        $user = $this->getUser();

        if (! $user) {
            return $this->notFoundAction();
        }

        $pictureTable = $this->catalogue()->getPictureTable();
        $pictureAdapter = $pictureTable->getAdapter();
        $picturesExists = $pictureAdapter->fetchOne(
            $pictureAdapter->select()
                ->from('pictures', new Zend_Db_Expr('COUNT(1)'))
                ->where('owner_id = ?', $user->id)
                ->where('status = ?', DbTable\Picture::STATUS_ACCEPTED)
        );

        $pictureTable = $this->catalogue()->getPictureTable();
        $lastPictureRows = $pictureTable->fetchAll(
            $pictureTable->select()
                ->from('pictures')
                ->where('owner_id = ?', $user->id)
                ->order('id DESC')
                ->limit(12)
        );

        $names = $pictureTable->getNameData($lastPictureRows, [
            'language' => $this->language(),
            'large'    => true
        ]);

        $lastPictures = [];
        foreach ($lastPictureRows as $lastPictureRow) {
            $lastPictures[] = [
                'url'  => $this->pic()->url($lastPictureRow->identity),
                'name' => $names[$lastPictureRow->id]
            ];
        }

        $userRenames = new UserRename();
        $renames = $userRenames->fetchAll(
            $userRenames->select(true)
                ->where('user_id = ?', $user->id)
                ->order('date DESC')
        );

        $ban = $canBan = $canViewIp = $canDeleteUser = false;
        if ($this->user()->logedIn()) {
            if ($this->user()->get()->id != $user->id) {
                $canBan = $this->user()->isAllowed('user', 'ban');
                $canDeleteUser = $this->user()->isAllowed('user', 'delete');
            }
            $canViewIp = $this->user()->isAllowed('user', 'ip');
        }

        if ($canBan && $user->last_ip !== null) {
            if ($user->last_ip) {
                $ban = $this->trafficControl->getBanInfo(inet_ntop($user->last_ip));
                if ($ban) {
                    $ban['user'] = $users->find($ban['user_id'])->current();
                }
            }
        }

        $uaTable = new DbTable\User\Account();

        $uaRows = $uaTable->fetchAll([
            'user_id = ?' => $user->id
        ]);

        $contact = new Contact();

        $currentUser = $this->user()->get();
        $isMe = $currentUser && ($currentUser->id == $user->id);
        $inContacts = $currentUser && ! $isMe && $contact->exists($currentUser->id, $user->id);
        $canBeInContacts = $currentUser && ! $currentUser->deleted && ! $isMe ;

        return [
            'currentUser'     => $user,
            'ban'             => $ban,
            'canBan'          => $canBan,
            'canRemovePhoto'  => $canBan,
            'canViewIp'       => $canViewIp,
            'canDeleteUser'   => $canDeleteUser,
            'accounts'        => $uaRows,
            'inContacts'      => $inContacts,
            'canBeInContacts' => $canBeInContacts,
            'contactApiUrl'   => sprintf('/api/contacts/%d', $user->id),
            'picturesExists'  => $picturesExists,
            'lastPictures'    => $lastPictures,
            'lastComments'    => $this->getLastComments($user),
            'renames'         => $renames
        ];
    }

    public function picturesAction()
    {
        $user = $this->getUser();

        if (! $user) {
            return $this->notFoundAction();
        }


        // СПИСОК БРЕНДОВ
        $brandModel = new BrandModel();

        $options = [
            'language' => $this->language(),
            'columns'  => [
                'logo_id',
                'pictures_count' => new Zend_Db_Expr('COUNT(distinct pictures.id)')
            ]
        ];

        $rows = $brandModel->getList($options, function ($select) use ($user) {
            $select
                ->join('item_parent_cache', 'item.id = item_parent_cache.parent_id', null)
                ->join('picture_item', 'item_parent_cache.item_id = picture_item.item_id', null)
                ->join('pictures', 'picture_item.picture_id = pictures.id', null)
                ->where('pictures.owner_id = ?', $user->id)
                ->where('pictures.status = ?', DbTable\Picture::STATUS_ACCEPTED)
                ->group('item.id');
        });

        $brands = [];
        foreach ($rows as $row) {
            $brands[] = [
                'logo_id'       => $row['logo_id'],
                'name'          => $row['name'],
                'catname'       => $row['catname'],
                'picturesCount' => $row['pictures_count'],
                'url'           => $this->url()->fromRoute('users/user/pictures/brand', [
                    'user_id'       => $user->identity ? $user->identity : 'user' . $user->id,
                    'brand_catname' => $row['catname']
                ])
            ];
        }

        return [
            'brands' => $brands,
            'user'   => $user
        ];
    }

    public function brandpicturesAction()
    {
        $user = $this->getUser();

        if (! $user) {
            return $this->notFoundAction();
        }

        $language = $this->language();

        $brandModel = new BrandModel();
        $brand = $brandModel->getBrandByCatname($this->params('brand_catname'), $language);

        if (! $brand) {
            return $this->notFoundAction();
        }

        $pictures = $this->catalogue()->getPictureTable();
        $select = $pictures->select(true)
            ->join('picture_item', 'pictures.id = picture_item.picture_id', null)
            ->join('item_parent_cache', 'picture_item.item_id = item_parent_cache.item_id', null)
            ->where('pictures.owner_id = ?', $user->id)
            ->where('pictures.status = ?', DbTable\Picture::STATUS_ACCEPTED)
            ->where('item_parent_cache.parent_id = ?', $brand['id'])
            ->group('pictures.id')
            ->order(['pictures.add_date DESC', 'pictures.id DESC']);

        $paginator = new Paginator(
            new Zend1DbTableSelect($select)
        );

        $paginator
            ->setItemCountPerPage(18)
            ->setCurrentPageNumber($this->params('page'));

        $select->limitPage($paginator->getCurrentPageNumber(), $paginator->getItemCountPerPage());

        $picturesData = $this->pic()->listData($select, [
            'width' => 6
        ]);

        return [
            'user'         => $user,
            'brand'        => $brand,
            'paginator'    => $paginator,
            'picturesData' => $picturesData,
            'urlParams'    => [
                'user_id'       => $user->identity ? $user->identity : 'user' . $user->id,
                'brand_catname' => $brand['catname']
            ]
        ];
    }

    public function onlineAction()
    {
        $userTable = new User();

        $now = new DateTime();
        $now->setTimezone(new DateTimeZone(MYSQL_TIMEZONE));
        $now->sub(new DateInterval('PT5M'));

        $viewModel = new ViewModel([
            'users' => $userTable->fetchAll(
                $userTable->select(true)
                    ->where('last_online >= ?', $now->format(MYSQL_DATETIME_FORMAT))
                //->join('session', 'users.id = session.user_id', null)
                //->where('session.modified >= ?', time() - 5 * 60)
                //->group('users.id')
            )
        ]);
        $viewModel->setTerminal($this->getRequest()->isXmlHttpRequest());

        return $viewModel;
    }

    private function specsRating()
    {
        $userTable = new User();
        $itemTable = new DbTable\Item();

        $select = $userTable->select(true)
            ->where('not deleted')
            ->limit(30)
            ->where('specs_volume > 0')
            ->order('specs_volume desc');

        $valueTitle = 'users/rating/specs-volume';

        $db = $itemTable->getAdapter();

        $precisionLimit = 50;

        $users = [];
        foreach ($userTable->fetchAll($select) as $idx => $user) {
            $brands = [];
            if ($idx < 5) {
                $cacheKey = 'RATING_USER_BRAND_5_'.$precisionLimit.'_' . $user->id;
                $brands = $this->cache->getItem($cacheKey, $success);
                if (! $success) {
                    $carSelect = $db->select()
                        ->from('item', ['id', 'count(1)'])
                        ->where('item.item_type_id = ?', DbTable\Item\Type::BRAND)
                        ->join('item_parent_cache', 'item.id = item_parent_cache.parent_id', null)
                        ->join('attrs_user_values', 'item_parent_cache.item_id = attrs_user_values.item_id', null)
                        ->where('attrs_user_values.user_id = ?', $user->id)
                        ->group('item.id')
                        ->order('count(1) desc')
                        ->limit($precisionLimit);

                    $data = [];
                    $pairs = $db->fetchPairs($carSelect);
                    foreach ($pairs as $brandId => $value) {
                        if (! isset($data[$brandId])) {
                            $data[$brandId] = $value;
                        } else {
                            $data[$brandId] += $value;
                        }
                    }

                    arsort($data, SORT_NUMERIC);
                    $data = array_slice($data, 0, 3, true);

                    foreach ($data as $brandId => $value) {
                        $row = $itemTable->fetchRow([
                            'id = ?'           => $brandId,
                            'item_type_id = ?' => DbTable\Item\Type::BRAND
                        ]);
                        $brands[] = [
                            'name' => $row->name,
                            'url'  => $this->url()->fromRoute('catalogue', [
                                'action'        => 'brand',
                                'brand_catname' => $row->catname
                            ]),
                            'value' => $value
                        ];
                    }
                }

                $this->cache->setItem($cacheKey, $brands);
            }

            $users[] = [
                'row'    => $user,
                'volume' => $user->specs_volume,
                'brands' => $brands,
                'weight' => $user->specs_weight
            ];
        }

        return [
            'users'      => $users,
            'rating'     => 'specs',
            'valueTitle' => $valueTitle
        ];
    }

    private function picturesRating()
    {
        $userTable = new User();
        $itemTable = new DbTable\Item();

        $select = $userTable->select(true)
            ->where('not deleted')
            ->limit(30)
            ->where('pictures_total > 0')
            ->order('pictures_total desc');

        $valueTitle = 'users/rating/pictures';

        $users = [];
        foreach ($userTable->fetchAll($select) as $idx => $user) {
            $brands = [];
            if ($idx < 10) {
                $cacheKey = 'RATING_USER_PICTURES_BRAND_6_' . $user->id;
                $brands = $this->cache->getItem($cacheKey, $success);
                if (! $success) {
                    $select = $itemTable->select(true)
                        ->where('item.item_type_id = ?', DbTable\Item\Type::BRAND)
                        ->join('item_parent_cache', 'item.id = item_parent_cache.parent_id', null)
                        ->join('picture_item', 'item_parent_cache.item_id = picture_item.item_id', null)
                        ->join('pictures', 'picture_item.picture_id = pictures.id', null)
                        ->group('item.id')
                        ->where('pictures.owner_id = ?', $user->id)
                        ->order('count(distinct pictures.id) desc')
                        ->limit(3);

                    foreach ($itemTable->fetchAll($select) as $brand) {
                        $brands[] = [
                            'name' => $brand->name,
                            'url'  => $this->url()->fromRoute('catalogue', [
                                'action'        => 'brand',
                                'brand_catname' => $brand->catname
                            ]),
                        ];
                    }
                }

                $this->cache->setItem($cacheKey, $brands);
            }

            $users[] = [
                'row'    => $user,
                'volume' => $user->pictures_total,
                'brands' => $brands
            ];
        }

        return [
            'users'      => $users,
            'rating'     => 'pictures',
            'valueTitle' => $valueTitle
        ];
    }

    private function likesRating()
    {
        $userTable = new User();
        $itemTable = new DbTable\Item();

        $db = $userTable->getAdapter();

        $rows = $db->fetchAll(
            $db->select()
                ->from('comment_message', ['author_id', 'volume' => new Zend_Db_Expr('sum(vote)')])
                ->group('author_id')
                ->order('volume DESC')
                ->limit(30)
        );

        $users = [];
        foreach ($rows as $row) {
            $users[] = [
                'row'    => $userTable->find($row['author_id'])->current(),
                'volume' => $row['volume'],
                'brands' => []
            ];
        }

        return [
            'users'      => $users,
            'rating'     => 'likes',
            'valueTitle' => 'users/rating/likes'
        ];
    }

    private function pictureLikesRating()
    {
        $userTable = new User();
        $itemTable = new DbTable\Item();

        $db = $userTable->getAdapter();

        $rows = $db->fetchAll(
            $db->select()
                ->from('pictures', ['owner_id'])
                ->join('picture_vote', 'pictures.id = picture_vote.picture_id', ['volume' => new Zend_Db_Expr('sum(value)')])
                ->where('pictures.owner_id <> picture_vote.user_id')
                ->group('pictures.owner_id')
                ->order('volume DESC')
                ->limit(30)
        );

        $users = [];
        foreach ($rows as $idx => $row) {
            $fans = [];
            if ($idx < 10) {
                $fanRows = $db->fetchAll(
                    $db->select()
                        ->from('picture_vote', ['user_id', 'volume' => new Zend_Db_Expr('count(1)')])
                        ->join('pictures', 'pictures.id = picture_vote.picture_id', null)
                        ->where('pictures.owner_id = ?', $row['owner_id'])
                        ->group('user_id')
                        ->order('volume desc')
                        ->limit(2)
                );
                foreach ($fanRows as $fanRow) {
                    $fans[] = [
                        'user_id' => $fanRow['user_id'],
                        'volume'  => $fanRow['volume'],
                    ];
                }
            }

            $users[] = [
                'row'    => $userTable->find($row['owner_id'])->current(),
                'volume' => $row['volume'],
                'brands' => [],
                'fans'   => $fans
            ];
        }

        return [
            'users'      => $users,
            'rating'     => 'picture-likes',
            'valueTitle' => 'users/rating/picture-likes'
        ];
    }

    public function ratingAction()
    {
        $rating = $this->params('rating', 'specs');

        switch ($rating) {
            case 'specs':
                return $this->specsRating();
                break;

            case 'pictures':
                return $this->picturesRating();
                break;

            case 'likes':
                return $this->likesRating();
                break;

            case 'picture-likes':
                return $this->pictureLikesRating();
                break;
        }

        return $this->notFoundAction();
    }

    public function commentsAction()
    {
        $user = $this->getUser();

        if (! $user) {
            return $this->notFoundAction();
        }

        $order = $this->params('order');

        $paginator = $this->comments->service()->getPaginatorByUser($user->id, $order);

        $paginator
            ->setItemCountPerPage(30)
            ->setCurrentPageNumber($this->params('page'));

        $comments = [];
        foreach ($paginator->getCurrentItems() as $commentRow) {
            $comments[] = [
                'url'     => $this->comments->getMessageRowUrl($commentRow),
                'message' => $this->comments->getMessagePreview($commentRow['message']),
                'vote'    => $commentRow['vote']
            ];
        }

        $orders = [
            'new'      => 'users/comments/order/new',
            'old'      => 'users/comments/order/old',
            'positive' => 'users/comments/order/positive',
            'negative' => 'users/comments/order/negative'
        ];

        $currentOrder = 'new';
        foreach ($orders as $key => $name) {
            if ($key == $order) {
                $currentOrder = $key;
                break;
            }
        }

        return [
            'user'      => $user,
            'comments'  => $comments,
            'paginator' => $paginator,
            'orders'    => $orders,
            'order'     => $currentOrder
        ];
    }
}
