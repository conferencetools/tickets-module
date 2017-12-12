<?php

namespace ConferenceTools\Tickets\Domain\Service\Availability\Filters;

use ConferenceTools\Tickets\Domain\ReadModel\TicketCounts\TicketCounter;
use ConferenceTools\Tickets\Domain\Service\Configuration;
use Doctrine\Common\Collections\Collection;

class IsAvailable implements FilterInterface
{
    public function filter(Collection $tickets): Collection
    {
        $p = function (TicketCounter $ticket) {
            return $ticket->getRemaining() > 0;
        };
        return $tickets->filter($p);
    }
}