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
 * Class Money.
 *
 * @ORM\Embeddable()
 */
class Price
{
    /**
     * @var Money
     * @ORM\Embedded(class="ConferenceTools\Tickets\Domain\ValueObject\Money")
     * @Jms\Type("ConferenceTools\Tickets\Domain\ValueObject\Money")
     */
    private $net;

    /**
     * @var TaxRate
     * @ORM\Embedded(class="ConferenceTools\Tickets\Domain\ValueObject\TaxRate")
     * @Jms\Type("ConferenceTools\Tickets\Domain\ValueObject\TaxRate")
     */
    private $taxRate;

    private function __construct(Money $net, TaxRate $taxRate)
    {
        $this->net = $net;
        $this->taxRate = $taxRate;
    }

    /**
     * @return Money
     */
    public function getNet(): Money
    {
        return $this->net;
    }

    /**
     * @return TaxRate
     */
    public function getTaxRate(): TaxRate
    {
        return $this->taxRate;
    }

    public function getGross(): Money
    {
        return $this->taxRate->calculateGross($this->net);
    }

    public function getTax(): Money
    {
        return $this->taxRate->calculateTaxFromNet($this->net);
    }

    public static function fromNetCost(Money $net, TaxRate $taxRate)
    {
        return new static($net, $taxRate);
    }

    public static function fromGrossCost(Money $gross, TaxRate $taxRate)
    {
        return new static($taxRate->calculateNet($gross), $taxRate);
    }

    /**
     * @param Price $other
     *
     * @return bool
     */
    public function isSameTaxRate(self $other): bool
    {
        return $this->taxRate->equals($other->taxRate);
    }

    /**
     * @param Price $other
     *
     * @return bool
     */
    public function equals(self $other): bool
    {
        return $this->isSameTaxRate($other) && $other->net->equals($this->net);
    }

    /**
     * @param Price $other
     *
     * @return int
     */
    public function compare(self $other): int
    {
        $this->assertSameTaxRate($other);
        if ($this->net->lessThan($other->net)) {
            return -1;
        }
        if ($this->net->equals($other->net)) {
            return 0;
        }

        return 1;
    }

    /**
     * @param Price $other
     *
     * @return bool
     */
    public function greaterThan(self $other): bool
    {
        return 1 === $this->compare($other);
    }

    /**
     * @param Price $other
     *
     * @return bool
     */
    public function lessThan(self $other): bool
    {
        return -1 === $this->compare($other);
    }

    public function add(self $addend): self
    {
        $this->assertSameTaxRate($addend);

        return new self($this->net->add($addend->net), $this->taxRate);
    }

    public function subtract(self $subtrahend): self
    {
        $this->assertSameTaxRate($subtrahend);

        return new self($this->net->subtract($subtrahend->net), $this->taxRate);
    }

    public function multiply($multiple): self
    {
        return new self($this->net->multiply($multiple), $this->taxRate);
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function assertSameTaxRate(self $other)
    {
        if (!$this->isSameTaxRate($other)) {
            throw new \InvalidArgumentException('Different tax rates');
        }
    }
}
