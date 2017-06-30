<?php

namespace ConferenceTools\Tickets\Domain\ValueObject;

class TicketReservationRequest
{
    private $ticketType;
    private $quantity;

    /**
     * TicketReservationRequest constructor.
     * @param $ticketType
     * @param $quantity
     */
    public function __construct(TicketType $ticketType, int $quantity)
    {
        if ($quantity < 1) {
            throw new \DomainException('Quantity must be greater than 0');
        }

        $this->ticketType = $ticketType;
        $this->quantity = $quantity;
    }

    /**
     * @return TicketType
     */
    public function getTicketType()
    {
        return $this->ticketType;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
}