<?php


namespace OpenTickets\Tickets\Domain\ValueObject;


class MoneyTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $sut = Money::EUR(300);
        self::assertEquals(300, $sut->getAmount());
        self::assertEquals('EUR', $sut->getCurrency());
    }

    /**
     * @dataProvider provideCompare
     *
     * @param Money $a
     * @param Money $b
     * @param int $expected
     */
    public function testCompare(Money $a, Money $b, $expected)
    {
        self::assertEquals($expected, $a->compare($b));
        self::assertEquals(-1 * $expected, $b->compare($a));
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
        $this->setExpectedException(\InvalidArgumentException::class);

        $sut = new Money(10, 'GBP');
        $money1 = new Money(9, 'USD');

        $sut->compare($money1);
    }

    public function testEquals()
    {
        $sut = new Money(10, 'GBP');
        $money1 = new Money(10, 'GBP');
        $money2 = new Money(11, 'GBP');

        self::assertTrue($sut->equals($money1));
        self::assertFalse($sut->equals($money2));
    }

    public function testLessThan()
    {
        $sut = new Money(10, 'GBP');
        $money1 = new Money(9, 'GBP');
        $money2 =  new Money(11, 'GBP');

        self::assertTrue($sut->lessThan($money2));
        self::assertFalse($sut->lessThan($money1));
    }

    public function testGreaterThan()
    {
        $sut = new Money(10, 'GBP');
        $money1 = new Money(9, 'GBP');
        $money2 = new Money(11, 'GBP');

        self::assertTrue($sut->greaterThan($money1));
        self::assertFalse($sut->greaterThan($money2));
    }
}
