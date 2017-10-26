<?php


namespace ConferenceTools\Tickets\Service\Factory\Service;


use Doctrine\ORM\EntityManager;
use ConferenceTools\Tickets\Domain\Service\Configuration;
use ConferenceTools\Tickets\Domain\Service\TicketAvailability\TicketAvailability as TicketAvailabilityService;
use ConferenceTools\Tickets\Domain\Service\TicketAvailability\TicketTypeFilter;
use ConferenceTools\Tickets\Finder\TicketCounter;
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