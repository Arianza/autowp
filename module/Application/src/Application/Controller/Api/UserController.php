<?php

namespace Application\Controller\Api;

use Zend\InputFilter\InputFilter;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

use ReCaptcha\ReCaptcha;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

use Autowp\User\Model\User;

use Application\Hydrator\Api\RestHydrator;
use Application\Service\UsersService;

class UserController extends AbstractRestfulController
{
    /**
     * @var RestHydrator
     */
    private $hydrator;

    /**
     * @var InputFilter
     */
    private $listInputFilter;

    /**
     * @var InputFilter
     */
    private $putInputFilter;

    /**
     * @var UsersService
     */
    private $userService;

    /**
     * @var User
     */
    private $userModel;

    /**
     * @var InputFilter
     */
    private $postInputFilter;

    /**
     * @var array
     */
    private $recaptcha;

    /**
     * @var bool
     */
    private $captchaEnabled;

    public function __construct(
        RestHydrator $hydrator,
        InputFilter $listInputFilter,
        InputFilter $postInputFilter,
        InputFilter $putInputFilter,
        UsersService $userService,
        User $userModel,
        array $recaptcha,
        bool $captchaEnabled
    ) {
        $this->hydrator = $hydrator;
        $this->listInputFilter = $listInputFilter;
        $this->postInputFilter = $postInputFilter;
        $this->putInputFilter = $putInputFilter;
        $this->userService = $userService;
        $this->userModel = $userModel;
        $this->recaptcha = $recaptcha;
        $this->captchaEnabled = $captchaEnabled;
    }

    public function indexAction()
    {
        $user = $this->user()->get();

        $this->listInputFilter->setData($this->params()->fromQuery());

        if (! $this->listInputFilter->isValid()) {
            return $this->inputFilterResponse($this->listInputFilter);
        }

        $data = $this->listInputFilter->getValues();

        $filter = [
            'not_deleted' => true
        ];

        $search = $data['search'];
        if ($search) {
            $filter['search'] = $search . '%';
        }

        $id = (int)$data['id'];
        if ($id) {
            $filter['id'] = $id;
        }

        $paginator = $this->userModel->getPaginator($filter);

        $limit = $data['limit'] ? $data['limit'] : 1;

        $paginator
            ->setItemCountPerPage($limit)
            ->setCurrentPageNumber($data['page']);

        $this->hydrator->setOptions([
            'language' => $this->language(),
            'fields'   => $data['fields'],
            'user_id'  => $user ? $user['id'] : null
        ]);

        $items = [];
        foreach ($paginator->getCurrentItems() as $row) {
            $items[] = $this->hydrator->extract($row);
        }

        return new JsonModel([
            'paginator' => get_object_vars($paginator->getPages()),
            'items'     => $items
        ]);
    }

    public function itemAction()
    {
        $user = $this->user()->get();

        $id = $this->params('id');

        if ($id == 'me') {
            if (! $user) {
                return new ApiProblemResponse(new ApiProblem(401, 'Not authorized'));
            }
            $id = $user['id'];
        }

        $row = $this->userModel->getRow((int)$id);
        if (! $row) {
            return new ApiProblemResponse(new ApiProblem(404, 'Entity not found'));
        }

        $this->hydrator->setOptions([
            'language' => $this->language(),
            //'fields'   => $data['fields'],
            'user_id'  => $user ? $user['id'] : null
        ]);

        return new JsonModel($this->hydrator->extract($row));
    }

    public function putAction()
    {
        $user = $this->user()->get();

        $id = $this->params('id');
        if ($id == 'me') {
            if (! $user) {
                return new ApiProblemResponse(new ApiProblem(401, 'Not authorized'));
            }
            $id = $user['id'];
        }

        $row = $this->userModel->getRow((int)$id);
        if (! $row) {
            return new ApiProblemResponse(new ApiProblem(404, 'Entity not found'));
        }

        $request = $this->getRequest();
        $data = $this->processBodyContent($request);

        $fields = [];
        foreach (array_keys($data) as $key) {
            if ($this->putInputFilter->has($key)) {
                $fields[] = $key;
            }
        }

        if (! $fields) {
            return new ApiProblemResponse(new ApiProblem(400, 'No fields provided'));
        }

        $this->putInputFilter->setValidationGroup($fields);

        $this->putInputFilter->setData($data);
        if (! $this->putInputFilter->isValid()) {
            return $this->inputFilterResponse($this->putInputFilter);
        }

        $values = $this->putInputFilter->getValues();

        if (array_key_exists('deleted', $values)) {
            $can = $this->user()->isAllowed('user', 'delete');
            if (! $can) {
                return $this->forbiddenAction();
            }

            if ($values['deleted'] && ! $row['deleted']) {
                $this->userService->markDeleted($row['id']);

                $this->log(sprintf(
                    'Удаление пользователя №%s',
                    $row['id']
                ), [
                    'users' => $row['id']
                ]);
            }
        }

        return $this->getResponse()->setStatusCode(200);
    }

    public function deletePhotoAction()
    {
        $user = $this->user()->get();

        $id = $this->params('id');
        if ($id == 'me') {
            if (! $user) {
                return new ApiProblemResponse(new ApiProblem(401, 'Not authorized'));
            }
            $id = $user['id'];
        }

        $row = $this->userModel->getRow((int)$id);
        if (! $row) {
            return new ApiProblemResponse(new ApiProblem(404, 'Entity not found'));
        }

        $can = $this->user()->isAllowed('user', 'ban');
        if (! $can) {
            return $this->forbiddenAction();
        }

        $oldImageId = $row['img'];
        if ($oldImageId) {
            $this->userModel->getTable()->update([
                'img' => null
            ], [
                'id' => $row['id']
            ]);

            $this->imageStorage()->removeImage($oldImageId);
        }

        $this->log(sprintf(
            'Удаление фотографии пользователя №%s',
            $row['id']
        ), [
            'users' => $row['id']
        ]);

        return $this->getResponse()->setStatusCode(204);
    }

    public function postAction()
    {
        $request = $this->getRequest();
        if ($this->requestHasContentType($request, self::CONTENT_TYPE_JSON)) {
            $data = $this->jsonDecode($request->getContent());
        } else {
            $data = $request->getPost()->toArray();
        }

        if ($this->captchaEnabled) {
            $namespace = new \Zend\Session\Container('Captcha');
            $verified = isset($namespace->success) && $namespace->success;

            if (! $verified) {
                $recaptcha = new ReCaptcha($this->recaptcha['privateKey']);

                $captchaResponse = null;
                if (isset($data['captcha'])) {
                    $captchaResponse = (string)$data['captcha'];
                }

                $result = $recaptcha->verify($captchaResponse, $this->getRequest()->getServer('REMOTE_ADDR'));

                if (! $result->isSuccess()) {
                    return new ApiProblemResponse(
                        new ApiProblem(400, 'Data is invalid. Check `detail`.', null, 'Validation error', [
                            'invalid_params' => [
                                'captcha' => 'Captcha is invalid'
                            ]
                        ])
                    );
                }

                $namespace->success = true;
            }
        }

        $this->postInputFilter->setData($data);
        if (! $this->postInputFilter->isValid()) {
            return $this->inputFilterResponse($this->postInputFilter);
        }

        $values = $this->postInputFilter->getValues();

        $ip = $request->getServer('REMOTE_ADDR');
        if (! $ip) {
            $ip = '127.0.0.1';
        }

        $user = $this->userService->addUser([
            'email'    => $values['email'],
            'password' => $values['password'],
            'name'     => $values['name'],
            'ip'       => $ip
        ], $this->language());

        $url = $this->url()->fromRoute('api/user/user/item', [
            'id' => $user['id']
        ]);
        $this->getResponse()->getHeaders()->addHeaderLine('Location', $url);

        return $this->getResponse()->setStatusCode(201);
    }
}
