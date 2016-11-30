<?php

/**
 * Created by PhpStorm.
 * User: imhotek
 * Date: 28/11/16
 * Time: 21:48
 */

namespace OpenTickets\Tickets\Domain\Command\Ticket;

use Carnage\Cqrs\Command\CommandInterface;
use OpenTickets\Tickets\Domain\ValueObject\TicketReservationRequest;

class ReserveTickets implements CommandInterface
{
    /**
     * @var TicketReservationRequest[]
     */
    private $reservationRequests;

    public function __construct(TicketReservationRequest ...$reservationRequests)
    {
        $this->reservationRequests = $reservationRequests;
    }

    /**
     * @return TicketReservationRequest[]
     */
    public function getReservationRequests(): array
    {
        return $this->reservationRequests;
    }
}