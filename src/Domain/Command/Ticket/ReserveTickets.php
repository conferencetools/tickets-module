<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
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
        return !(null === $this->discountCode);
    }

    /**
     * @return DiscountCode
     */
    public function getDiscountCode(): DiscountCode
    {
        return $this->discountCode;
    }
}
