<?php

namespace ConferenceTools\Tickets\Domain\Service\Availability;

use Carnage\Cqrs\Persistence\ReadModel\RepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use ConferenceTools\Tickets\Domain\ReadModel\TicketCounts\TicketCounter;
use ConferenceTools\Tickets\Domain\ValueObject\TicketType;
use ConferenceTools\Tickets\Domain\Service\Availability\Filters\FilterInterface;

class TicketAvailability
{
    /**
     * @var FilterInterface[]
     */
    private $filters;

    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * TicketAvailability constructor.
     * @param RepositoryInterface $repository
     * @param FilterInterface[] $filters
     */
    public function __construct(RepositoryInterface $repository, FilterInterface ...$filters)
    {
        $this->filters = $filters;
        $this->repository = $repository;
    }

    /**
     * @return TicketCounter[]|Collection
     */
    public function fetchAllAvailableTickets()
    {
        $tickets = $this->repository->matching(new Criteria());

        return $this->reindex($this->filterSet($tickets));
    }

    public function isAvailable(TicketType $ticketType, int $quantity)
    {
        $tickets = $this->fetchAllAvailableTickets();
        return isset($tickets[$ticketType->getIdentifier()]) &&
            $tickets[$ticketType->getIdentifier()]->getRemaining() >= $quantity;
    }

    private function filterSet(Collection $tickets): Collection
    {
        foreach ($this->filters as $filter) {
            /** @var FilterInterface $tickets */
            $tickets = $filter->filter($tickets);
        }

        return $tickets;
    }

    private function reindex(Collection $tickets): Collection
    {
        $result = [];
        foreach($tickets as $ticket) {
            /** @var TicketCounter $ticket */
            $result[$ticket->getTicketType()->getIdentifier()] = $ticket;
        }

        return new ArrayCollection($result);
    }
}