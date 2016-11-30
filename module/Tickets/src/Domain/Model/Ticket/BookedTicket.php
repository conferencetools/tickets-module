<?php

namespace OpenTickets\Tickets\Domain\Model\Ticket;

use OpenTickets\Tickets\Domain\Event\Ticket\TicketAssigned;
use OpenTickets\Tickets\Domain\Event\Ticket\TicketReserved;
use OpenTickets\Tickets\Domain\ValueObject\Delegate;
use OpenTickets\Tickets\Domain\ValueObject\TicketType;

class BookedTicket
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var TicketType
     */
    private $ticketType;

    /**
     * @var Delegate
     */
    private $delegate;

    public function getId()
    {
        return $this->id;
    }

    public static function reserve(TicketReserved $event)
    {
        $instance = new static();
        $instance->id = $event->getId();
        $instance->ticketType = $event->getTicketType();
        return $instance;
    }
    
    public function assignToDelegate(TicketAssigned $event)
    {
        $this->delegate = $event->getDelegate();
    }

    public function getTicketType(): TicketType
    {
        return $this->ticketType;
    }
}