<?php

namespace ConferenceTools\Tickets\Service\Factory\Service;

use ConferenceTools\Tickets\Domain\Service\Configuration;
use ConferenceTools\Tickets\Domain\Service\Availability\DiscountCodeAvailability as DiscountCodeAvailabilityService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Carnage\Cqorms\Persistence\ReadModel\DoctrineRepository;

class DiscountCodeAvailability implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $configuration = $serviceLocator->get(Configuration::class);

        return new DiscountCodeAvailabilityService($configuration);
    }
}