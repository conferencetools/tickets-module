<?php

namespace OpenTickets\Tickets\Domain\Event\Ticket;

use Carnage\Cqrs\Event\EventInterface;
use OpenTickets\Tickets\Domain\ValueObject\TicketType;

class TicketReserved implements EventInterface
{
    private $id;

    private $ticketType;
    /**
     * @var string
     */
    private $purchaseId;

    /**
     * TicketReserved constructor.
     * @param string $id
     * @param TicketType $ticketType
     * @param string $purchaseId
     */
    public function __construct(string $id, TicketType $ticketType, string $purchaseId)
    {
        $this->id = $id;
        $this->ticketType = $ticketType;
        $this->purchaseId = $purchaseId;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return TicketType
     */
    public function getTicketType()
    {
        return $this->ticketType;
    }

    /**
     * @return string
     */
    public function getPurchaseId()
    {
        return $this->purchaseId;
    }
}