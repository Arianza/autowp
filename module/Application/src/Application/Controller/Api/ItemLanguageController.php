<?php

namespace Application\Controller\Api;

use Zend\Db\TableGateway\TableGateway;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

use Autowp\TextStorage\Service as TextStorage;

use Application\Hydrator\Api\RestHydrator;
use Application\Model\BrandVehicle;
use Application\Model\DbTable;
use Application\HostManager;
use Autowp\Message\MessageService;

class ItemLanguageController extends AbstractRestfulController
{
    /**
     * @var TableGateway
     */
    private $table;

    /**
     * @var TextStorage
     */
    private $textStorage;

    /**
     * @var RestHydrator
     */
    private $hydrator;

    /**
     * @var BrandVehicle
     */
    private $brandVehicle;

    /**
     * @var HostManager
     */
    private $hostManager;

    /**
     * @var InputFilter
     */
    private $putInputFilter;

    public function __construct(
        TableGateway $table,
        TextStorage $textStorage,
        RestHydrator $hydrator,
        BrandVehicle $brandVehicle,
        HostManager $hostManager,
        InputFilter $putInputFilter,
        MessageService $message
    ) {
        $this->table = $table;
        $this->textStorage = $textStorage;
        $this->hydrator = $hydrator;
        $this->brandVehicle = $brandVehicle;
        $this->hostManager = $hostManager;
        $this->putInputFilter = $putInputFilter;
        $this->message = $message;
    }

    public function indexAction()
    {
        if (! $this->user()->inheritsRole('moder')) {
            return $this->forbiddenAction();
        }

        $rows = $this->table->select([
            'item_id'       => (int)$this->params('id'),
            'language <> ?' => 'xx'
        ]);

        $items = [];
        foreach ($rows as $row) {
            $items[] = $this->hydrator->extract($row);
        }

        return new JsonModel([
            'items' => $items
        ]);
    }

    public function getAction()
    {
        if (! $this->user()->inheritsRole('moder')) {
            return $this->forbiddenAction();
        }

        $row = $this->table->select([
            'item_id'  => (int)$this->params('id'),
            'language' => (string)$this->params('language')
        ])->current();

        if (! $row) {
            return $this->notFoundAction();
        }

        return new JsonModel($this->hydrator->extract($row));
    }

    public function putAction()
    {
        if (! $this->user()->inheritsRole('moder')) {
            return $this->forbiddenAction();
        }

        $itemTable = $this->catalogue()->getItemTable();

        $item = $itemTable->find($this->params('id'))->current();
        if (! $item) {
            return $this->notFoundAction();
        }

        $user = $this->user()->get();

        $data = $this->processBodyContent($this->getRequest());

        $fields = [];
        foreach (array_keys($data) as $key) {
            if ($this->putInputFilter->has($key)) {
                $fields[] = $key;
            }
        }

        $this->putInputFilter->setValidationGroup($fields);

        $this->putInputFilter->setData($data);

        if (! $this->putInputFilter->isValid()) {
            return $this->inputFilterResponse($this->putInputFilter);
        }

        $data = $this->putInputFilter->getValues();

        $language = (string)$this->params('language');

        $row = $this->table->select([
            'item_id'  => $item['id'],
            'language' => $language
        ])->current();

        $set = [];

        $changes = [];

        if (array_key_exists('name', $data)) {
            $oldName = $row ? $row['name'] : '';
            $newName = (string)$data['name'];

            if ($oldName !== $newName) {
                $set['name'] = $newName;
                $changes[] = 'moder/vehicle/name';
            }
        }

        if (array_key_exists('text', $data)) {
            $text = $data['text'];
            $textChanged = false;
            if ($row && $row['text_id']) {
                $textChanged = ($text != $this->textStorage->getText($row['text_id']));

                $this->textStorage->setText($row['text_id'], $text, $user->id);
            } elseif ($text) {
                $textChanged = true;

                $textId = $this->textStorage->createText($text, $user->id);
                $set['text_id'] = $textId;
            }

            if ($textChanged) {
                $changes[] = 'moder/item/short-description';
            }
        }

        $fullTextChanged = false;

        if (array_key_exists('full_text', $data)) {
            $fullText = $data['full_text'];
            $fullTextChanged = false;
            if ($row && $row['full_text_id']) {
                $fullTextChanged = ($fullText != $this->textStorage->getText($row['full_text_id']));

                $this->textStorage->setText($row['full_text_id'], $fullText, $user->id);
            } elseif ($fullText) {
                $fullTextChanged = true;

                $fullTextId = $this->textStorage->createText($fullText, $user->id);
                $set['full_text_id'] = $fullTextId;
            }

            if ($fullTextChanged) {
                $changes[] = 'moder/item/full-description';
            }
        }

        if ($set) {
            $primaryKey = [
                'item_id'  => $item['id'],
                'language' => $language
            ];

            if ($row) {
                $this->table->update($set, $primaryKey);
            } else {
                $this->table->insert(array_merge($set, $primaryKey));
            }

            $this->brandVehicle->refreshAutoByVehicle($item->id);
        }

        if ($changes) {
            $ucsTable = new DbTable\User\ItemSubscribe();
            $ucsTable->subscribe($user, $item);

            $language = $this->language();

            foreach ($ucsTable->getItemSubscribers($item) as $subscriber) {
                if ($subscriber && ($subscriber->id != $user->id)) {
                    $uri = $this->hostManager->getUriByLanguage($subscriber->language);

                    $changesStr = [];
                    foreach ($changes as $field) {
                        $changesStr[] = $this->translate(
                            $field,
                            'default',
                            $subscriber->language
                        ) . ' (' . $language . ')';
                    }

                    $message = sprintf(
                        $this->translate(
                            'pm/user-%s-edited-item-language-%s-%s',
                            'default',
                            $subscriber->language
                        ),
                        $this->userModerUrl($user, true, $uri),
                        $this->car()->formatName($item, $subscriber->language),
                        $this->itemModerUrl($item, true, null, $uri),
                        implode("\n", $changesStr)
                    );

                    $this->message->send(null, $subscriber->id, $message);
                }
            }

            $this->log(sprintf(
                'Редактирование языковых названия, описания и полного описания автомобиля %s',
                htmlspecialchars($this->car()->formatName($item, 'en'))
            ), $item);
        }

        return $this->getResponse()->setStatusCode(200);
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
     * @return string
     */
    private function itemModerUrl(DbTable\Item\Row $item, $full = false, $tab = null, $uri = null)
    {
        $url = 'moder/items/item/' . $item['id'];

        if ($tab) {
            $url .= '?' . http_build_query([
                'tab' => $tab
            ]);
        }

        return $this->url()->fromRoute('ng', ['path' => ''], [
            'force_canonical' => $full,
            'uri'             => $uri
        ]) . $url;
    }
}