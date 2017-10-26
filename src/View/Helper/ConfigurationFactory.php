<?php

namespace ConferenceTools\Tickets\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ConferenceTools\Tickets\Domain\Service\Configuration as TicketsConfiguration;

class ConfigurationFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->getServiceLocator()->get(TicketsConfiguration::class);
        return new Configuration($config);
    }

}