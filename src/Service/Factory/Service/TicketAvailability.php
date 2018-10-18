<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace ConferenceTools\Tickets\Service\Factory\Service;

use Carnage\Cqorms\Persistence\ReadModel\DoctrineRepository;
use ConferenceTools\Tickets\Domain\ReadModel\TicketCounts\TicketCounter;
use ConferenceTools\Tickets\Domain\Service\Availability\Filters;
use ConferenceTools\Tickets\Domain\Service\Availability\TicketAvailability as TicketAvailabilityService;
use ConferenceTools\Tickets\Domain\Service\Configuration;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TicketAvailability implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $configuration = $serviceLocator->get(Configuration::class);
        $filters = [
            new Filters\IsAvailable(),
            new Filters\AfterSoldOut($configuration),
            new Filters\ByDate($configuration),
            new Filters\IsPrivate($configuration),
        ];

        return new TicketAvailabilityService(
            new DoctrineRepository(TicketCounter::class, $serviceLocator->get(EntityManager::class)),
            ...$filters
        );
    }
}
