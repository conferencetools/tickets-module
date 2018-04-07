<?php
/**
 * Created by PhpStorm.
 * User: imhotek
 * Date: 15/11/17
 * Time: 16:57
 */

namespace ConferenceTools\Tickets\Domain\Service\Availability\Filters;


use ConferenceTools\Tickets\Domain\ReadModel\TicketCounts\TicketCounter;
use ConferenceTools\Tickets\Domain\Service\Configuration;
use Doctrine\Common\Collections\Collection;

class IsPrivate implements FilterInterface
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
        $p = function(TicketCounter $ticket) use ($configuration) {
            $metadata = $configuration->getTicketMetadata($ticket->getTicketType()->getIdentifier());
            return !$metadata->isPrivateTicket();
        };
        return $tickets->filter($p);
    }
}