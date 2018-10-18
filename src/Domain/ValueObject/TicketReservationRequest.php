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

namespace ConferenceTools\Tickets\Domain\ValueObject;

class TicketReservationRequest
{
    private $ticketType;
    private $quantity;

    /**
     * TicketReservationRequest constructor.
     *
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
