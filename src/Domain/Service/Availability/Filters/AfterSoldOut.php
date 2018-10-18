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
use ConferenceTools\Tickets\Domain\Service\Configuration;
use Doctrine\Common\Collections\Collection;

class AfterSoldOut implements FilterInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function filter(Collection $tickets): Collection
    {
        $configuration = $this->configuration;

        $identifiers = $tickets->map(function (TicketCounter $ticketCounter) {
            return $ticketCounter->getTicketType()->getIdentifier();
        });

        //Filter out tickets which have an availability period in the past.
        $p = function (string $identifier) use ($configuration) {
            $metadata = $configuration->getTicketMetadata($identifier);

            return !($metadata->expiredOn(new \DateTime()));
        };
        $identifiers = $identifiers->filter($p);

        $p = function (TicketCounter $ticket) use ($configuration, $identifiers) {
            $metadata = $configuration->getTicketMetadata($ticket->getTicketType()->getIdentifier());

            foreach ($metadata->getAfterSoldOut() as $identifier) {
                if ($identifiers->contains($identifier)) {
                    return false;
                }
            }

            return true;
        };

        return $tickets->filter($p);
    }
}
