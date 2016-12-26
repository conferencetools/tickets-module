<?php

namespace OpenTickets\Tickets\Service\Factory\Projection;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TicketCountsFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        $serviceLocator = $serviceLocator->getServiceLocator();
        $config = $serviceLocator->get('Config');
        return new $requestedName(
            $serviceLocator->get('doctrine.entitymanager.orm_default'),
            $config['opentickets']['tickets']
        );
    }
}