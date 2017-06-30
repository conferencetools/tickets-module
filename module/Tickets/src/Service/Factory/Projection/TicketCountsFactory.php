<?php

namespace ConferenceTools\Tickets\Service\Factory\Projection;

use ConferenceTools\Tickets\Domain\Service\Configuration as TicketsConfiguration;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TicketCountsFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        $serviceLocator = $serviceLocator->getServiceLocator();
        return new $requestedName(
            $serviceLocator->get('doctrine.entitymanager.orm_default'),
            $serviceLocator->get(TicketsConfiguration::class)
        );
    }
}