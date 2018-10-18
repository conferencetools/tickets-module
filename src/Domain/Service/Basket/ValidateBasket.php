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

namespace ConferenceTools\Tickets\Domain\Service\Basket;

use ConferenceTools\Tickets\Domain\ValueObject\Basket;

class ValidateBasket implements BasketValidator
{
    public function validate(Basket $basket): void
    {
        if (0 === \count($basket->getTickets())) {
            throw new \DomainException('You must choose at least 1 ticket to purchase');
        }
    }
}
