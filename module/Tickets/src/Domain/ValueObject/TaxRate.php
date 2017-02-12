<?php

namespace OpenTickets\Tickets\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Jms;

/**
 * Class TaxRate
 * @package OpenTickets\Tickets\Domain\ValueObject
 * @ORM\Embeddable
 */
class TaxRate
{
    /**
     * @var string
     * @Jms\Type("integer")
     * @ORM\Column(type="integer")
     */
    private $percentage;

    /**
     * TaxRate constructor.
     * @param $percentage
     */
    public function __construct(int $percentage)
    {
        $this->percentage = $percentage;
    }

    /**
     * @return int
     */
    public function getPercentage(): int
    {
        return $this->percentage;
    }

    public function calculateTaxFromNet(Money $net): Money
    {
        return $net->multiply($this->getPercentageAsFloat());
    }

    public function calculateGross(Money $net): Money
    {
        return $net->add($this->calculateTaxFromNet($net));
    }

    public function calculateTaxFromGross(Money $gross): Money
    {
        return $gross->subtract($gross->multiply($this->getInversePercentage()));
    }

    public function calculateNet(Money $gross): Money
    {
        return $gross->subtract($this->calculateTaxFromGross($gross));
    }

    /**
     * @return float
     */
    private function getInversePercentage()
    {
        return 1 / (1 + $this->getPercentageAsFloat());
    }

    /**
     * @return float
     */
    private function getPercentageAsFloat(): float
    {
        return (float) ($this->percentage / 100);
    }

    /**
     * @param TaxRate $other
     * @return bool
     */
    public function equals(TaxRate $other): bool
    {
        return ($other->percentage === $this->percentage);
    }

    /**
     * @param TaxRate $other
     * @return int
     */
    public function compare(TaxRate $other): int
    {
        if ($this->percentage < $other->percentage) {
            return -1;
        } elseif ($this->percentage == $other->percentage) {
            return 0;
        } else {
            return 1;
        }
    }

    /**
     * @param TaxRate $other
     * @return bool
     */
    public function greaterThan(TaxRate $other): bool
    {
        return 1 === $this->compare($other);
    }

    /**
     * @param TaxRate $other
     * @return bool
     */
    public function lessThan(TaxRate $other): bool
    {
        return -1 === $this->compare($other);
    }
}