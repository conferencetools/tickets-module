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

namespace ConferenceTools\Tickets\Domain\Service\Availability\Filters;

use ConferenceTools\Tickets\Domain\ReadModel\TicketCounts\TicketCounter;
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
