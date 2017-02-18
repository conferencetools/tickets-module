<?php


namespace OpenTickets\Tickets\Domain\Finder;

use Doctrine\Common\Collections\Collection;

interface TicketCounterInterface
{
    public function byTicketTypeIdentifiers(string ...$identifiers): Collection;
}