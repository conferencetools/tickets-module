<?php

/**
 * Created by PhpStorm.
 * User: imhotek
 * Date: 28/11/16
 * Time: 21:48
 */

namespace ConferenceTools\Tickets\Domain\Command\Ticket;

use Carnage\Cqrs\Command\CommandInterface;
use ConferenceTools\Tickets\Domain\ValueObject\DiscountCode;
use ConferenceTools\Tickets\Domain\ValueObject\TicketReservationRequest;

class ReserveTickets implements CommandInterface
{
    /**
     * @var TicketReservationRequest[]
     */
    private $reservationRequests;

    /**
     * @var DiscountCode
     */
    private $discountCode;

    public function __construct(TicketReservationRequest ...$reservationRequests)
    {
        $this->reservationRequests = $reservationRequests;
    }

    public static function withoutDiscountCode(TicketReservationRequest ...$reservationRequests)
    {
        return new static(...$reservationRequests);
    }

    public static function withDiscountCode(DiscountCode $discountCode, TicketReservationRequest ...$reservationRequests)
    {
        $instance = new static(...$reservationRequests);
        $instance->discountCode = $discountCode;

        return $instance;
    }

    /**
     * @return TicketReservationRequest[]
     */
    public function getReservationRequests(): array
    {
        return $this->reservationRequests;
    }

    public function hasDiscountCode(): bool
    {
        return !($this->discountCode === null);
    }

    /**
     * @return DiscountCode
     */
    public function getDiscountCode(): DiscountCode
    {
        return $this->discountCode;
    }
}
