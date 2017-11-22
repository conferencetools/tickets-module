<?php

namespace ConferenceTools\Tickets\Domain\Service\Availability;

use Carnage\Cqrs\Persistence\ReadModel\RepositoryInterface;
use ConferenceTools\Tickets\Domain\Service\Configuration;
use ConferenceTools\Tickets\Domain\ValueObject\DiscountCode;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use ConferenceTools\Tickets\Domain\ReadModel\Counts\TicketCounter;
use ConferenceTools\Tickets\Domain\ValueObject\TicketType;
use ConferenceTools\Tickets\Domain\Service\Availability\Filters\FilterInterface;

class DiscountCodeAvailability
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * TicketAvailability constructor.
     * @param RepositoryInterface $repository
     * @param FilterInterface[] $filters
     */
    public function __construct()//Configuration $configuration)
    {
        //$this->configuration = $configuration;
    }

    public function isAvailable(DiscountCode $discountCode)
    {
        return true;
    }
}