<?php


namespace OpenTickets\Tickets\Service\Factory\Service;


use Doctrine\ORM\EntityManager;
use OpenTickets\Tickets\Domain\Service\Configuration;
use OpenTickets\Tickets\Domain\Service\TicketAvailability\TicketAvailability as TicketAvailabilityService;
use OpenTickets\Tickets\Domain\Service\TicketAvailability\TicketTypeFilter;
use OpenTickets\Tickets\Finder\TicketCounter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TicketAvailability implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new TicketAvailabilityService(
            new TicketTypeFilter($serviceLocator->get(Configuration::class)),
            new TicketCounter($serviceLocator->get(EntityManager::class))
        );
    }
}