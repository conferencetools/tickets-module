<?php

namespace ConferenceTools\Tickets\Cli\Command;


use Carnage\Cqrs\Command\CommandBusInterface;
use Carnage\Cqrs\Service\EventCatcher;
use ConferenceTools\Tickets\Domain\Service\Configuration;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MarkPurchasePaidFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator->getServiceLocator();
        return MarkPurchasePaid::build(
            $serviceLocator->get(CommandBusInterface::class)
        );
    }
}
