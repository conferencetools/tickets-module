<?php

namespace ConferenceTools\Tickets\Domain\Model\Ticket;

use Carnage\Cqrs\Aggregate\AbstractAggregate;
use ConferenceTools\Tickets\Domain\Event\Ticket\DiscountCodeApplied;
use ConferenceTools\Tickets\Domain\Event\Ticket\TicketAssigned;
use ConferenceTools\Tickets\Domain\Event\Ticket\TicketCancelled;
use ConferenceTools\Tickets\Domain\Event\Ticket\TicketPurchaseCreated;
use ConferenceTools\Tickets\Domain\Event\Ticket\TicketPurchaseTimedout;
use ConferenceTools\Tickets\Domain\Event\Ticket\TicketPurchaseTotalPriceCalculated;
use ConferenceTools\Tickets\Domain\Event\Ticket\TicketPurchasePaid;
use ConferenceTools\Tickets\Domain\Event\Ticket\TicketReleased;
use ConferenceTools\Tickets\Domain\Event\Ticket\TicketReserved;
use ConferenceTools\Tickets\Domain\ValueObject\Basket;
use ConferenceTools\Tickets\Domain\ValueObject\Delegate;
use ConferenceTools\Tickets\Domain\ValueObject\Money;

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

    public static function create(string $id, Basket $basket)
    {
        $instance = new static();
        $event = new TicketPurchaseCreated($id);
        $instance->apply($event);

        foreach ($basket->getTickets() as $ticket) {
            $event = new TicketReserved($ticket->getReservationId(), $ticket->getTicketType(), $id);
            $instance->apply($event);
        }

        if ($basket->hasDiscountCode()) {
            $event = new TicketPurchaseTotalPriceCalculated($id, $basket->getPreDiscountTotal());
            $instance->apply($event);

            $event = new DiscountCodeApplied($id, $basket->getDiscountCode());
            $instance->apply($event);
        }

        $event = new TicketPurchaseTotalPriceCalculated($id, $basket->getTotal());
        $instance->apply($event);

        return $instance;
    }

    public function completePurchase(string $email, Delegate ...$delegateInformation)
    {
        if (count($delegateInformation) !== count($this->tickets)) {
            throw new \DomainException('Number of delegates\' information supplied doesn\'t match number of tickets');
        }

        $iterator = new \MultipleIterator(\MultipleIterator::MIT_KEYS_NUMERIC|\MultipleIterator::MIT_NEED_ALL);
        $iterator->attachIterator(new \ArrayIterator($delegateInformation), 'delegate');
        $iterator->attachIterator(new \ArrayIterator($this->tickets), 'ticketId');

        foreach ($iterator as $data) {
            $this->assignTicketToDelegate($data[1]->getId(), $data[0]);
        }

        $this->markAsPaid($email);
    }

    public function cancelTicket(string $ticketId)
    {
        if (!$this->isPaid) {
            throw new \DomainException('Cannot cancel a ticket which hasn\'t been paid for');
        }

        $this->apply(new TicketCancelled($this->id, $ticketId));
    }

    protected function applyTicketCancelled(TicketCancelled $event)
    {
        unset($this->tickets[$event->getTicketId()]);
    }

    protected function applyTicketReserved(TicketReserved $event)
    {
        $this->tickets[$event->getId()] = BookedTicket::reserve($event);
    }

    protected function applyTicketPurchaseCreated(TicketPurchaseCreated $event)
    {
        $this->id = $event->getId();
    }

    protected function applyTicketPurchaseTotalPriceCalculated(TicketPurchaseTotalPriceCalculated $event)
    {
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

    public function markAsPaid(string $email)
    {
        $event = new TicketPurchasePaid($this->id, $email);
        $this->apply($event);
    }

    protected function applyTicketPurchasePaid(TicketPurchasePaid $event)
    {
        $this->isPaid = true;
    }
}