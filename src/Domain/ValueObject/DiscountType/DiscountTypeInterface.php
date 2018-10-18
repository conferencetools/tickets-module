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

namespace ConferenceTools\Tickets\Domain\ValueObject\DiscountType;

use ConferenceTools\Tickets\Domain\Service\Configuration;
use ConferenceTools\Tickets\Domain\ValueObject\Basket;
use ConferenceTools\Tickets\Domain\ValueObject\Price;

interface DiscountTypeInterface
{
    public static function fromArray(array $data, Configuration $configuration): self;

    public function apply(Basket $to): Price;
}
