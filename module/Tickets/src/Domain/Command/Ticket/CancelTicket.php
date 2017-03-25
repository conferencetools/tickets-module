<?php

namespace OpenTickets\Tickets\Domain\Command\Ticket;

use Carnage\Cqrs\Event\EventInterface;

class CancelTicket implements EventInterface
{
    /**
     * @var string
     */
    private $purchaseId;
    /**
     * @var string
     */
    private $ticketId;

    /**
     * TicketCancelled constructor.
     * @param string $purchaseId
     * @param string $ticketId
     */
    public function __construct(string $purchaseId, string $ticketId)
    {
        $this->purchaseId = $purchaseId;
        $this->ticketId = $ticketId;
    }

    /**
     * @return string
     */
    public function getPurchaseId(): string
    {
        return $this->purchaseId;
    }

    /**
     * @return string
     */
    public function getTicketId(): string
    {
        return $this->ticketId;
    }
}
