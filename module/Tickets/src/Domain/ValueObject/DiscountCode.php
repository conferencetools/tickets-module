<?php

namespace OpenTickets\Tickets\Domain\ValueObject;

use OpenTickets\Tickets\Domain\ValueObject\DiscountType\DiscountTypeInterface;

class DiscountCode
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $displayName;

    /**
     * @var DiscountTypeInterface
     */
    private $discountType;

    /**
     * DiscountCode constructor.
     * @param $code
     * @param $displayName
     * @param $discountType
     */
    public function __construct(string $code, string $displayName, DiscountTypeInterface $discountType)
    {
        $this->code = $code;
        $this->displayName = $displayName;
        $this->discountType = $discountType;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @return DiscountTypeInterface
     */
    public function getDiscountType(): DiscountTypeInterface
    {
        return $this->discountType;
    }

    /**
     * @param Basket $to
     * @return Price
     */
    public function apply(Basket $to): Price
    {
        return $this->discountType->apply($to);
    }
}