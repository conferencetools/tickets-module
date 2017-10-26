<?php


namespace ConferenceTools\Tickets\Domain\ValueObject;


class TaxRateTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideCompare
     *
     * @param TaxRate $a
     * @param TaxRate $b
     * @param int $expected
     */
    public function testCompare(TaxRate $a, TaxRate $b, $expected)
    {
        self::assertEquals($expected, $a->compare($b));
        self::assertEquals(-1 * $expected, $b->compare($a));
    }

    public function provideCompare()
    {
        $a = new TaxRate(20);
        $b = new TaxRate(20);
        $c = new TaxRate(25);
        $d = new TaxRate(30);

        return [
            [$a, $b, 0],
            [$b, $c, -1],
            [$c, $d, -1],
            [$d, $a, 1],
        ];
    }

    public function testEquals()
    {
        $sut = new TaxRate(20);
        $taxrate1 = new TaxRate(20);
        $taxrate2 = new TaxRate(10);

        self::assertTrue($sut->equals($taxrate1));
        self::assertFalse($sut->equals($taxrate2));
    }


    public function testLessThan()
    {
        $sut = new TaxRate(20);
        $taxrate1 = new TaxRate(25);
        $taxrate2 = new TaxRate(10);

        self::assertTrue($sut->lessThan($taxrate1));
        self::assertFalse($sut->lessThan($taxrate2));
    }

    public function testGreaterThan()
    {
        $sut = new TaxRate(20);
        $taxrate1 = new TaxRate(10);
        $taxrate2 = new TaxRate(25);

        self::assertTrue($sut->greaterThan($taxrate1));
        self::assertFalse($sut->greaterThan($taxrate2));
    }

    public function testCreate()
    {
        $sut = new TaxRate(15);

        self::assertEquals(15, $sut->getPercentage());
    }
}
