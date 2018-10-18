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

interface BasketValidator
{
    public function validate(Basket $basket): void;
}
