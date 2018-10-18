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

namespace ConferenceTools\Tickets\Domain\Service\Availability;

use Carnage\Cqrs\Persistence\ReadModel\RepositoryInterface;
use ConferenceTools\Tickets\Domain\ReadModel\TicketCounts\TicketCounter;
use ConferenceTools\Tickets\Domain\Service\Availability\Filters\FilterInterface;
use ConferenceTools\Tickets\Domain\ValueObject\TicketType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;

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
     *
     * @param RepositoryInterface $repository
     * @param FilterInterface[]   $filters
     */
    public function __construct(RepositoryInterface $repository, FilterInterface ...$filters)
    {
        $this->filters = $filters;
        $this->repository = $repository;
    }

    /**
     * @return Collection|TicketCounter[]
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
        foreach ($tickets as $ticket) {
            // @var TicketCounter $ticket
            $result[$ticket->getTicketType()->getIdentifier()] = $ticket;
        }

        return new ArrayCollection($result);
    }
}
