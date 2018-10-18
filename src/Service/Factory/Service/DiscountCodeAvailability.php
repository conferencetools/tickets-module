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

use Carnage\Cqrs\Persistence\ReadModel\InMemoryRepository;
use ConferenceTools\Tickets\Domain\Service\Availability\DiscountCodeAvailability as DiscountCodeAvailabilityService;
use ConferenceTools\Tickets\Domain\Service\Availability\Filters;
use ConferenceTools\Tickets\Domain\Service\Configuration;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DiscountCodeAvailability implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var Configuration $configuration */
        $configuration = $serviceLocator->get(Configuration::class);

        $filters = [
            new Filters\DiscountByDate($configuration),
        ];

        $repository = new InMemoryRepository();
        foreach ($configuration->getDiscountCodes() as $code) {
            $repository->add($code);
        }

        return new DiscountCodeAvailabilityService($repository, ...$filters);
    }
}
