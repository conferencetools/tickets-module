<?php

namespace ConferenceTools\Tickets\Cli\Command;


use Carnage\Cqrs\Command\CommandBusInterface;
use Carnage\Cqrs\Service\EventCatcher;
use ConferenceTools\Tickets\Domain\Service\Configuration;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class IssueFreeTicketFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator->getServiceLocator();
        return IssueFreeTicket::build(
            $serviceLocator->get(CommandBusInterface::class),
            $serviceLocator->get(Configuration::class),
            $serviceLocator->get('EventListenerManager')->get(EventCatcher::class)
        );
    }
}
