<?php

namespace OpenTickets\Tickets\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use OpenTickets\Tickets\Domain\Service\Configuration as OpenTicketsConfiguration;

class ConfigurationFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->getServiceLocator()->get(OpenTicketsConfiguration::class);
        return new Configuration($config);
    }

}