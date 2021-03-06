<?php

namespace Application\Controller\Api;

use Application\Model\CarOfDay;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class DonateControllerFactory implements FactoryInterface
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param string $requestedName
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): DonateController
    {
        $config = $container->get('Config');
        return new DonateController(
            $container->get(CarOfDay::class),
            $config['yandex']
        );
    }
}
