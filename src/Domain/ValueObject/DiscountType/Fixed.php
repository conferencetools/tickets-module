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
use ConferenceTools\Tickets\Domain\ValueObject\Money;
use ConferenceTools\Tickets\Domain\ValueObject\Price;
use JMS\Serializer\Annotation as Jms;

class Fixed implements DiscountTypeInterface
{
    /**
     * @JMS\Type("Price")
     *
     * @var Price
     */
    private $discount;

    /**
     * Percentage constructor.
     *
     * @param $discount
     */
    public function __construct(Price $discount)
    {
        $this->discount = $discount;
    }

    public function apply(Basket $to): Price
    {
        return $this->discount;
    }

    /**
     * @return Price
     */
    public function getDiscount(): Price
    {
        return $this->discount;
    }

    public static function fromArray(array $data, Configuration $configuration): DiscountTypeInterface
    {
        if (isset($data['net'])) {
            $amount = new Money($data['net'], $configuration->getCurrency());
            $discount = Price::fromNetCost($amount, $configuration->getTaxRate());
        } else {
            $amount = new Money($data['gross'], $configuration->getCurrency());
            $discount = Price::fromGrossCost($amount, $configuration->getTaxRate());
        }

        return new static($discount);
    }
}
