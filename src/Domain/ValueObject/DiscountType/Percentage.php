<?php

namespace ConferenceTools\Tickets\Domain\ValueObject\DiscountType;

use ConferenceTools\Tickets\Domain\Service\Configuration;
use ConferenceTools\Tickets\Domain\ValueObject\Basket;
use ConferenceTools\Tickets\Domain\ValueObject\Price;
use JMS\Serializer\Annotation as Jms;

class Percentage implements DiscountTypeInterface
{
    /**
     * @JMS\Type("integer")
     * @var int
     */
    private $percentage;

    /**
     * Percentage constructor.
     * @param int $percentage
     */
    public function __construct($percentage)
    {
        $this->percentage = $percentage;
    }

    public function apply(Basket $to): Price
    {
        return $to->getPreDiscountTotal()->multiply($this->percentage / 100);
    }

    /**
     * @return int
     */
    public function getPercentage(): int
    {
        return $this->percentage;
    }

    public static function fromArray(array $data, Configuration $configuration): DiscountTypeInterface
    {
        return new static($data['percentage']);
    }
}
