<?php

namespace ConferenceTools\Tickets\Domain\ValueObject;

use JMS\Serializer\Annotation as Jms;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Money
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
     * @param $amount
     * @param $currency
     */
    public function __construct(int $amount, string $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    /**
     * @return integer
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

    public static function __callStatic(string $method, array $arguments): Money
    {
        return new self($arguments[0], $method);
    }

    /**
     * @param Money $other
     * @return bool
     */
    public function isSameCurrency(Money $other): bool
    {
        return $this->currency === $other->currency;
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function assertSameCurrency(Money $other)
    {
        if (!$this->isSameCurrency($other)) {
            throw new \InvalidArgumentException('Different currencies');
        }
    }

    public function equals(Money $other): bool
    {
        return ($this->isSameCurrency($other) && $other->amount === $this->amount);
    }

    /**
     * @param Money $other
     * @return int
     */
    public function compare(Money $other): int
    {
        $this->assertSameCurrency($other);
        if ($this->amount < $other->amount) {
            return -1;
        } elseif ($this->amount == $other->amount) {
            return 0;
        } else {
            return 1;
        }
    }

    /**
     * @param Money $other
     * @return bool
     */
    public function greaterThan(Money $other): bool
    {
        return 1 === $this->compare($other);
    }

    /**
     * @param Money $other
     * @return bool
     */
    public function lessThan(Money $other): bool
    {
        return -1 === $this->compare($other);
    }

    public function add(Money $addend): Money
    {
        $this->assertSameCurrency($addend);

        return new self($this->amount + $addend->amount, $this->currency);
    }

    public function subtract(Money $subtrahend): Money
    {
        $this->assertSameCurrency($subtrahend);

        return new self($this->amount - $subtrahend->amount, $this->currency);
    }

    public function multiply($multiple): Money
    {
        return new self(ceil($this->amount * $multiple), $this->currency);
    }
}
