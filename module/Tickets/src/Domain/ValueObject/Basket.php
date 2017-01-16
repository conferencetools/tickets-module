<?php

namespace OpenTickets\Tickets\Domain\ValueObject;

use OpenTickets\Tickets\Domain\Service\Configuration;

class Basket
{
    private $tickets;
    private $total;

    private function __construct(Price $zero, TicketReservation ...$tickets)
    {
        if (count($tickets) === 0) {
            throw new \InvalidArgumentException('Must put at least one Ticket reservation into a basket');
        }

        $this->tickets = $tickets;
        $this->total = $this->calculateTotal($zero);
    }

    public static function fromReservations(Configuration $config, TicketReservation ...$tickets)
    {
        return new static(
            Price::fromNetCost(new Money(0, $config->getCurrency()), $config->getTaxRate()),
            ...$tickets
        );
    }

    /**
     * @return TicketReservation[]
     */
    public function getTickets(): array
    {
        return $this->tickets;
    }

    /**
     * @return Price
     */
    public function getTotal(): Price
    {
        return $this->total;
    }

    private function calculateTotal(Price $total): Price
    {
        foreach ($this->tickets as $ticket) {
            $total = $total->add($ticket->getTicketType()->getPrice());
        }

        return $total;
    }
}