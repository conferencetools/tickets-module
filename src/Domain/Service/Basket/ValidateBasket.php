<?php

namespace ConferenceTools\Tickets\Domain\Service\Basket;

use ConferenceTools\Tickets\Domain\ValueObject\Basket;

class ValidateBasket implements BasketValidator
{
    public function validate(Basket $basket): void
    {
        if (count($basket->getTickets()) === 0) {
            throw new \DomainException('You must choose at least 1 ticket to purchase');
        }
    }
}
