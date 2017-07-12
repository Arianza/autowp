<?php

namespace Autowp\Traffic\Controller;

use Zend\Mvc\Controller\AbstractActionController;

use Autowp\Traffic\TrafficControl;

class ConsoleController extends AbstractActionController
{
    /**
     * @var TrafficControl
     */
    private $service;

    public function __construct(TrafficControl $service)
    {
        $this->service = $service;
    }

    public function autobanAction()
    {
        $this->service->autoBan();

        $this->getResponse()->setContent("done\n");
    }

    public function googleAction()
    {
        $this->service->autoWhitelist();

        $this->getResponse()->setContent("done\n");
    }

    public function gcAction()
    {
        $count = $this->service->garbageCollect();

        $this->getResponse()->setContent(
            sprintf("%d ip monitoring and banned ip rows was deleted\ndone\n", $count)
        );
    }
}
