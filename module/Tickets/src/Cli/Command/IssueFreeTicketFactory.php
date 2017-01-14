<?php

namespace OpenTickets\Tickets\Cli\Command;


use Carnage\Cqrs\Command\CommandBusInterface;
use Carnage\Cqrs\Service\EventCatcher;
use OpenTickets\Tickets\Domain\Service\Configuration;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class IssueFreeTicketFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return IssueFreeTicket::build(
            $serviceLocator->get(CommandBusInterface::class),
            $serviceLocator->get(Configuration::class),
            $serviceLocator->get('EventListenerManager')->get(EventCatcher::class)
        );
    }
}
