<?php

namespace OpenTickets\Tickets\Domain\Model\Ticket;

use Carnage\Cqrs\Aggregate\AbstractAggregate;
use OpenTickets\Tickets\Domain\Event\Ticket\TicketAssigned;
use OpenTickets\Tickets\Domain\Event\Ticket\TicketPurchaseTimedout;
use OpenTickets\Tickets\Domain\Event\Ticket\TicketPurchaseCreated;
use OpenTickets\Tickets\Domain\Event\Ticket\TicketPurchasePaid;
use OpenTickets\Tickets\Domain\Event\Ticket\TicketReleased;
use OpenTickets\Tickets\Domain\Event\Ticket\TicketReserved;
use OpenTickets\Tickets\Domain\ValueObject\Delegate;
use OpenTickets\Tickets\Domain\ValueObject\Money;
use OpenTickets\Tickets\Domain\ValueObject\TicketReservation;

class TicketPurchase extends AbstractAggregate
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var Money
     */
    private $total;

    /**
     * @var bool
     */
    private $isPaid = false;

    /**
     * @var bool
     */
    private $isTimedout = false;
    
    /**
     * @var BookedTicket[]
     */
    private $tickets = [];

    public function getId()
    {
        return $this->id;
    }

    public static function create(string $id, TicketReservation ...$tickets)
    {
        $instance = new static();
        $total = new Money(0, 'GBP');

        foreach ($tickets as $ticket) {
            $total->add($ticket->getTicketType()->getPrice());
            $event = new TicketReserved($ticket->getReservationId(), $ticket->getTicketType(), $id);
            $instance->apply($event);
        }

        $event = new TicketPurchaseCreated($id, $total);
        $instance->apply($event);

        return $instance;
    }

    public function completePurchase(Delegate ...$delegateInformation)
    {
        if (count($delegateInformation) !== count($this->tickets)) {
            throw new \DomainException('Number of delegates\' information supplied doesn\'t match number of tickets');
        }

        $iterator = new \MultipleIterator(\MultipleIterator::MIT_KEYS_NUMERIC|\MultipleIterator::MIT_NEED_ALL);
        $iterator->attachIterator(new \ArrayIterator($delegateInformation), 'delegate');
        $iterator->attachIterator(new \ArrayIterator($this->tickets), 'ticketId');

        foreach ($iterator as $data) {
            $this->assignTicketToDelegate($data['ticketId'], $data['delegate']);
        }

        $this->markAsPaid();
    }

    protected function applyTicketReserved(TicketReserved $event)
    {
        $this->tickets[$event->getId()] = BookedTicket::reserve($event);
    }

    protected function applyTicketPurchaseCreated(TicketPurchaseCreated $event)
    {
        $this->id = $event->getId();
        $this->total = $event->getTotal();
    }

    public function assignTicketToDelegate(string $id, Delegate $delegate)
    {
        $event = new TicketAssigned($id, $this->id, $delegate);
        $this->apply($event);
    }

    protected function applyTicketAssigned(TicketAssigned $event)
    {
        $this->tickets[$event->getTicketId()]->assignToDelegate($event);
    }

    public function timeoutPurchase()
    {
        if ($this->isPaid) {
            throw new \DomainException('Booking cannot timeout once it has been paid for');
        }
        
        $event = new TicketPurchaseTimedout($this->id);
        $this->apply($event);
        
        foreach ($this->tickets as $ticket) {
            $this->apply(new TicketReleased($ticket->getId(), $this->id, $ticket->getTicketType()));
        }
    }

    protected function applyTicketPurchaseTimedout(TicketPurchaseTimedout $event)
    {
        $this->isTimedout = true;
    }

    public function markAsPaid()
    {
        $event = new TicketPurchasePaid($this->id);
        $this->apply($event);
    }

    protected function applyTicketPurchasePaid(TicketPurchasePaid $event)
    {
        $this->isPaid = true;
    }
}