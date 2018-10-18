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
final class Money
{
    /**
     * @JMS\Type("integer")
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @JMS\Type("string")
     * @ORM\Column(type="string")
     */
    private $currency;

    /**
     * Money constructor.
     *
     * @param $amount
     * @param $currency
     */
    public function __construct(int $amount, string $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public static function __callStatic(string $method, array $arguments): self
    {
        return new self($arguments[0], $method);
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param Money $other
     *
     * @return bool
     */
    public function isSameCurrency(self $other): bool
    {
        return $this->currency === $other->currency;
    }

    public function equals(self $other): bool
    {
        return $this->isSameCurrency($other) && $other->amount === $this->amount;
    }

    /**
     * @param Money $other
     *
     * @return int
     */
    public function compare(self $other): int
    {
        $this->assertSameCurrency($other);
        if ($this->amount < $other->amount) {
            return -1;
        }
        if ($this->amount === $other->amount) {
            return 0;
        }

        return 1;
    }

    /**
     * @param Money $other
     *
     * @return bool
     */
    public function greaterThan(self $other): bool
    {
        return 1 === $this->compare($other);
    }

    /**
     * @param Money $other
     *
     * @return bool
     */
    public function lessThan(self $other): bool
    {
        return -1 === $this->compare($other);
    }

    public function add(self $addend): self
    {
        $this->assertSameCurrency($addend);

        return new self($this->amount + $addend->amount, $this->currency);
    }

    public function subtract(self $subtrahend): self
    {
        $this->assertSameCurrency($subtrahend);

        return new self($this->amount - $subtrahend->amount, $this->currency);
    }

    public function multiply($multiple): self
    {
        return new self(ceil($this->amount * $multiple), $this->currency);
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function assertSameCurrency(self $other)
    {
        if (!$this->isSameCurrency($other)) {
            throw new \InvalidArgumentException('Different currencies');
        }
    }
}
