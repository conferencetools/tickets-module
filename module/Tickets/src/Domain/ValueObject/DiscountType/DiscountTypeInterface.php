<?php

namespace OpenTickets\Tickets\Domain\ValueObject\DiscountType;

use OpenTickets\Tickets\Domain\Service\Configuration;
use OpenTickets\Tickets\Domain\ValueObject\Basket;
use OpenTickets\Tickets\Domain\ValueObject\Money;
use OpenTickets\Tickets\Domain\ValueObject\Price;

interface DiscountTypeInterface
{
    public static function fromArray(array $data, Configuration $configuration): DiscountTypeInterface;

    public function apply(Basket $to): Price;
}
