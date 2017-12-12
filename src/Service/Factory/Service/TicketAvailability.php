<?php

namespace ConferenceTools\Tickets\Service\Factory\Service;

use ConferenceTools\Tickets\Domain\ReadModel\TicketCounts\TicketCounter;
use Doctrine\ORM\EntityManager;
use ConferenceTools\Tickets\Domain\Service\Configuration;
use ConferenceTools\Tickets\Domain\Service\Availability\TicketAvailability as TicketAvailabilityService;
use ConferenceTools\Tickets\Domain\Service\Availability\Filters;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Carnage\Cqorms\Persistence\ReadModel\DoctrineRepository;

class TicketAvailability implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $configuration = $serviceLocator->get(Configuration::class);
        $filters = [
            new Filters\IsAvailable(),
            new Filters\AfterSoldOut($configuration),
            new Filters\ByDate($configuration),
            new Filters\IsPrivate($configuration)
        ];

        return new TicketAvailabilityService(
            new DoctrineRepository(TicketCounter::class, $serviceLocator->get(EntityManager::class)),
            ...$filters
        );
    }
}