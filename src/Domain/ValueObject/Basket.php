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

use ConferenceTools\Tickets\Domain\Service\Basket\BasketValidator;
use ConferenceTools\Tickets\Domain\Service\Configuration;

class Basket
{
    /**
     * @var TicketReservation[]
     */
    private $tickets;

    /**
     * @var Price
     */
    private $preDiscountTotal;

    /**
     * @var Price
     */
    private $total;

    /**
     * @var DiscountCode
     */
    private $discountCode;

    private function __construct(TicketReservation ...$tickets)
    {
        $this->tickets = $tickets;
    }

    public static function fromReservations(
        Configuration $config,
        BasketValidator $validator,
        TicketReservation ...$tickets
    ) {
        $instance = new self(
            ...$tickets
        );

        $zero = Price::fromNetCost(new Money(0, $config->getCurrency()), $config->getTaxRate());
        $instance->preDiscountTotal = $instance->calculateTotal($zero);

        $instance->total = $instance->preDiscountTotal;

        $validator->validate($instance);

        return $instance;
    }

    public static function fromReservationsWithDiscount(
        Configuration $config,
        BasketValidator $validator,
        DiscountCode $discountCode,
        TicketReservation ...$tickets
    ) {
        $instance = new self(
            ...$tickets
        );

        $zero = Price::fromNetCost(new Money(0, $config->getCurrency()), $config->getTaxRate());
        $instance->preDiscountTotal = $instance->calculateTotal($zero);

        $instance->total = $instance->preDiscountTotal->subtract($discountCode->apply($instance));
        $instance->discountCode = $discountCode;

        $validator->validate($instance);

        return $instance;
    }

    /**
     * @return TicketReservation[]
     */
    public function getTickets(): array
    {
        return $this->tickets;
    }

    public function getTotal(): Price
    {
        return $this->total;
    }

    public function hasDiscountCode(): bool
    {
        return !(null === $this->discountCode);
    }

    /**
     * @TODO make nullable (PHP 7.1)
     *
     * @return DiscountCode
     */
    public function getDiscountCode(): DiscountCode
    {
        return $this->discountCode;
    }

    public function getPreDiscountTotal(): Price
    {
        return $this->preDiscountTotal;
    }

    public function containingOnly(TicketType ...$ticketTypes)
    {
        $filteredReservations = [];
        foreach ($this->tickets as $ticketReservation) {
            if (\in_array($ticketReservation->getTicketType(), $ticketTypes, false)) {
                $filteredReservations[] = $ticketReservation;
            }
        }

        $instance = new self(
            ...$filteredReservations
        );

        $zero = Price::fromNetCost(
            new Money(0, $this->preDiscountTotal->getNet()->getCurrency()),
            $this->preDiscountTotal->getTaxRate()
        );

        $instance->preDiscountTotal = $instance->calculateTotal($zero);
        $instance->total = $instance->preDiscountTotal;

        return $instance;
    }

    private function calculateTotal(Price $total): Price
    {
        foreach ($this->tickets as $ticket) {
            $total = $total->add($ticket->getTicketType()->getPrice());
        }

        return $total;
    }
}
