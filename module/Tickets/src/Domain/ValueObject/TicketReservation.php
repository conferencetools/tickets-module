<?php
/**
 * Created by PhpStorm.
 * User: imhotek
 * Date: 28/11/16
 * Time: 21:18
 */

namespace OpenTickets\Tickets\Domain\ValueObject;


class TicketReservation
{
    private $ticketType;
    private $reservationId;

    public function __construct(TicketType $ticketType, $reservationId)
    {
        $this->ticketType = $ticketType;
        $this->reservationId = $reservationId;
    }

    /**
     * @return TicketType
     */
    public function getTicketType()
    {
        return $this->ticketType;
    }

    /**
     * @return mixed
     */
    public function getReservationId()
    {
        return $this->reservationId;
    }
}