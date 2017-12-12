<?php

namespace ConferenceTools\Tickets\Domain\Service\Basket;

use ConferenceTools\Tickets\Domain\ValueObject\Basket;

interface BasketValidator
{
    public function validate(Basket $basket): void;
}
