<?php

namespace OpenTickets\Tickets\Domain\ValueObject\DiscountType;

use OpenTickets\Tickets\Domain\ValueObject\Basket;
use OpenTickets\Tickets\Domain\ValueObject\Price;

interface DiscountTypeInterface
{
    public function apply(Basket $to): Price;
}
