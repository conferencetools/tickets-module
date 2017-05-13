<?php

namespace OpenTickets\Tickets\Domain\Service\TicketAvailability;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use OpenTickets\Tickets\Domain\Finder\TicketCounterInterface;
use OpenTickets\Tickets\Domain\ReadModel\TicketCounts\TicketCounter;
use OpenTickets\Tickets\Domain\Service\Configuration;
use OpenTickets\Tickets\Domain\ValueObject\TicketType;

class TicketAvailability
{
    /**
     * @var TicketTypeFilter
     */
    private $filter;

    /**
     * @var TicketCounterInterface
     */
    private $finder;

    /**
     * TicketAvailability constructor.
     *
     * @param TicketTypeFilter $filter
     * @param TicketCounterInterface $finder
     */
    public function __construct(TicketTypeFilter $filter, TicketCounterInterface $finder)
    {
        $this->finder = $finder;
        $this->filter = $filter;
    }

    /**
     * @return TicketCounter[]|Collection
     */
    public function fetchAllAvailableTickets()
    {
        $ticketTypes = $this->filter->getPubliclyAvailableTicketTypeIdentifiers();

        $ticketCounters = $this->finder->byTicketTypeIdentifiers(...$ticketTypes);

        return $ticketCounters->filter(function(TicketCounter $ticketCounter) {
            return $ticketCounter->getRemaining() > 0;
        });
    }

    public function isAvailable(TicketType $ticketType, int $quantity)
    {
        $tickets = $this->fetchAllAvailableTickets();
        return isset($tickets[$ticketType->getIdentifier()]) &&
            $tickets[$ticketType->getIdentifier()]->getRemaining() >= $quantity;
    }
}