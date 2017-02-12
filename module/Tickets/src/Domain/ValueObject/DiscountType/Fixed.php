<?php

namespace OpenTickets\Tickets\Domain\ValueObject\DiscountType;

use OpenTickets\Tickets\Domain\Service\Configuration;
use OpenTickets\Tickets\Domain\ValueObject\Basket;
use OpenTickets\Tickets\Domain\ValueObject\Money;
use OpenTickets\Tickets\Domain\ValueObject\Price;
use JMS\Serializer\Annotation as Jms;

class Fixed implements DiscountTypeInterface
{
    /**
     * @JMS\Type("Price")
     * @var int
     */
    private $discount;

    /**
     * Percentage constructor.
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
