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

namespace ConferenceTools\Tickets\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Jms;

/**
 * Class TaxRate.
 *
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
     *
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
     * @param TaxRate $other
     *
     * @return bool
     */
    public function equals(self $other): bool
    {
        return $other->percentage === $this->percentage;
    }

    /**
     * @param TaxRate $other
     *
     * @return int
     */
    public function compare(self $other): int
    {
        if ($this->percentage < $other->percentage) {
            return -1;
        }
        if ($this->percentage === $other->percentage) {
            return 0;
        }

        return 1;
    }

    /**
     * @param TaxRate $other
     *
     * @return bool
     */
    public function greaterThan(self $other): bool
    {
        return 1 === $this->compare($other);
    }

    /**
     * @param TaxRate $other
     *
     * @return bool
     */
    public function lessThan(self $other): bool
    {
        return -1 === $this->compare($other);
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
}
