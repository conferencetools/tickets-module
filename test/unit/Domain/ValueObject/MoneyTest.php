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

/**
 * @internal
 * @coversNothing
 */
final class MoneyTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $sut = Money::EUR(300);
        $this->assertSame(300, $sut->getAmount());
        $this->assertSame('EUR', $sut->getCurrency());
    }

    /**
     * @dataProvider provideCompare
     *
     * @param Money $a
     * @param Money $b
     * @param int   $expected
     */
    public function testCompare(Money $a, Money $b, $expected)
    {
        $this->assertSame($expected, $a->compare($b));
        $this->assertSame(-1 * $expected, $b->compare($a));
    }

    public function provideCompare()
    {
        $a = new Money(10, 'GBP');
        $b = new Money(10, 'GBP');
        $c = new Money(11, 'GBP');
        $d = new Money(12, 'GBP');

        return [
            [$a, $b, 0],
            [$b, $c, -1],
            [$c, $d, -1],
            [$d, $a, 1],
        ];
    }

    public function testExceptionWithDifferentCurrencies()
    {
        $this->expectException(\InvalidArgumentException::class);

        $sut = new Money(10, 'GBP');
        $money1 = new Money(9, 'USD');

        $sut->compare($money1);
    }

    public function testEquals()
    {
        $sut = new Money(10, 'GBP');
        $money1 = new Money(10, 'GBP');
        $money2 = new Money(11, 'GBP');

        $this->assertTrue($sut->equals($money1));
        $this->assertFalse($sut->equals($money2));
    }

    public function testLessThan()
    {
        $sut = new Money(10, 'GBP');
        $money1 = new Money(9, 'GBP');
        $money2 = new Money(11, 'GBP');

        $this->assertTrue($sut->lessThan($money2));
        $this->assertFalse($sut->lessThan($money1));
    }

    public function testGreaterThan()
    {
        $sut = new Money(10, 'GBP');
        $money1 = new Money(9, 'GBP');
        $money2 = new Money(11, 'GBP');

        $this->assertTrue($sut->greaterThan($money1));
        $this->assertFalse($sut->greaterThan($money2));
    }
}
