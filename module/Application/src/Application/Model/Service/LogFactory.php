<?php

namespace Application\Model\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class LogFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new \Application\Model\Log(
            $container->get(\Zend\Db\Adapter\AdapterInterface::class)
        );
    }
}
