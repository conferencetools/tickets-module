<?php

namespace OpenTickets\Tickets\Cli\Command;

use Carnage\Cqrs\Command\CommandBusInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TimeoutPurchasesFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator->getServiceLocator();
        return TimeoutPurchases::build(
            $serviceLocator->get('doctrine.entitymanager.orm_default'),
            $serviceLocator->get(CommandBusInterface::class)
        );
    }
}