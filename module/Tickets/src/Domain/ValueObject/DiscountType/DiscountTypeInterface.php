<?php

namespace ConferenceTools\Tickets\Domain\ValueObject\DiscountType;

use ConferenceTools\Tickets\Domain\Service\Configuration;
use ConferenceTools\Tickets\Domain\ValueObject\Basket;
use ConferenceTools\Tickets\Domain\ValueObject\Money;
use ConferenceTools\Tickets\Domain\ValueObject\Price;

interface DiscountTypeInterface
{
    public static function fromArray(array $data, Configuration $configuration): DiscountTypeInterface;

    public function apply(Basket $to): Price;
}
