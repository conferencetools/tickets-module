<?php

namespace ConferenceTools\Tickets\Service\Factory\Service;

use Carnage\Cqrs\Persistence\ReadModel\InMemoryRepository;
use ConferenceTools\Tickets\Domain\Service\Availability\Filters;
use ConferenceTools\Tickets\Domain\Service\Configuration;
use ConferenceTools\Tickets\Domain\Service\Availability\DiscountCodeAvailability as DiscountCodeAvailabilityService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Carnage\Cqorms\Persistence\ReadModel\DoctrineRepository;

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