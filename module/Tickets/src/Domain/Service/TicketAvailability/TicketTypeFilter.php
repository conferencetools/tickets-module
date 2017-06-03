<?php

namespace ConferenceTools\Tickets\Domain\Service\TicketAvailability;

use ConferenceTools\Tickets\Domain\Service\Configuration;

class TicketTypeFilter
{
    private $configuration;

    /**
     * TicketTypeFilter constructor.
     * @param $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getPubliclyAvailableTicketTypeIdentifiers()
    {
        $currentDate = new \DateTime();
        $ticketTypes = [];

        foreach ($this->configuration->getTicketTypes() as $ticketType) {
            $metadata = $this->configuration->getTicketMetadata($ticketType->getIdentifier());
            if ($metadata->isAvailableOn($currentDate) && !$metadata->isPrivateTicket()) {
                $ticketTypes[] = $ticketType->getIdentifier();
            }
        }

        return $ticketTypes;
    }
}