<?php

namespace OpenTickets\Tickets\Domain\Service\TicketAvailability;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use OpenTickets\Tickets\Domain\Finder\TicketCounterInterface;
use OpenTickets\Tickets\Domain\ReadModel\TicketCounts\TicketCounter;
use OpenTickets\Tickets\Domain\Service\Configuration;
use OpenTickets\Tickets\Domain\ValueObject\TicketType;

class TicketAvailability
{
    private $configuration;
    /**
     * @var TicketCounterInterface
     */
    private $finder;

    /**
     * TicketAvailability constructor.
     *
     * @TODO remove dependency on doctrine
     *
     * @param Configuration $configuration
     * @param EntityManagerInterface $em
     */
    public function __construct(Configuration $configuration,TicketCounterInterface $finder)
    {
        $this->configuration = $configuration;
        $this->finder = $finder;
    }

    /**
     * @return TicketCounter[]
     */
    public function fetchAllAvailableTickets()
    {
        $currentDate = new \DateTime();
        $ticketTypes = [];

        foreach ($this->configuration->getTicketTypes() as $ticketType) {
            $metadata = $this->configuration->getTicketMetadata($ticketType->getIdentifier());
            if ($metadata->isAvailableOn($currentDate)) {
                $ticketTypes[] = $ticketType->getIdentifier();
            }
        }

        $ticketCounters = $this->finder->byTicketTypeIdentifiers(...$ticketTypes);

        return $ticketCounters->filter(function (TicketCounter $ticketCounter) {
            return $ticketCounter->getRemaining() > 0;
        });

    }

    public function isAvailable(TicketType $ticketType, int $quantity)
    {
        //refactor form to use ticket type identifier; then this method can be implemented
        //probably wants to take either the the ticket type and then use that
        //inside it's own business logic to determine availability
        //if controller uses tt.ident then it can pull ticket type from configuration object
    }
}