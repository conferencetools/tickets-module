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
final class PriceTest extends \PHPUnit\Framework\TestCase
{
    public function testFromNetCost()
    {
        $sut = Price::fromNetCost(new Money(10, 'GBP'), new TaxRate(20));
        $this->assertTrue((new Money(12, 'GBP'))->equals($sut->getGross()), 'Gross value incorrect');
        $this->assertTrue((new Money(10, 'GBP'))->equals($sut->getNet()), 'Net value incorrect');
        $this->assertTrue((new TaxRate(20))->equals($sut->getTaxRate()), 'Tax rate incorrect');
    }

    public function testFromGrossCost()
    {
        $sut = Price::fromGrossCost(new Money(12, 'GBP'), new TaxRate(20));
        $this->assertTrue((new Money(12, 'GBP'))->equals($sut->getGross()), 'Gross value incorrect');
        $this->assertTrue((new Money(10, 'GBP'))->equals($sut->getNet()), 'Net value incorrect');
        $this->assertTrue((new TaxRate(20))->equals($sut->getTaxRate()), 'Tax rate incorrect');
    }

    /**
     * @dataProvider provideCompare
     *
     * @param Price $a
     * @param Price $b
     * @param int   $expected
     */
    public function testCompare(Price $a, Price $b, $expected)
    {
        $this->assertSame($expected, $a->compare($b));
        $this->assertSame(-1 * $expected, $b->compare($a));
    }

    public function provideCompare()
    {
        $a = Price::fromNetCost(new Money(10, 'GBP'), new TaxRate(20));
        $b = Price::fromNetCost(new Money(10, 'GBP'), new TaxRate(20));
        $c = Price::fromNetCost(new Money(11, 'GBP'), new TaxRate(20));
        $d = Price::fromNetCost(new Money(12, 'GBP'), new TaxRate(20));

        return [
            [$a, $b, 0],
            [$b, $c, -1],
            [$c, $d, -1],
            [$d, $a, 1],
        ];
    }

    public function testEquals()
    {
        $sut = Price::fromNetCost(new Money(10, 'GBP'), new TaxRate(20));
        $price1 = Price::fromNetCost(new Money(10, 'GBP'), new TaxRate(20));
        $price2 = Price::fromNetCost(new Money(11, 'GBP'), new TaxRate(20));

        $this->assertTrue($sut->equals($price1));
        $this->assertFalse($sut->equals($price2));
    }

    public function testLessThan()
    {
        $sut = Price::fromNetCost(new Money(10, 'GBP'), new TaxRate(20));
        $price1 = Price::fromNetCost(new Money(9, 'GBP'), new TaxRate(20));
        $price2 = Price::fromNetCost(new Money(11, 'GBP'), new TaxRate(20));

        $this->assertTrue($sut->lessThan($price2));
        $this->assertFalse($sut->lessThan($price1));
    }

    public function testGreaterThan()
    {
        $sut = Price::fromNetCost(new Money(10, 'GBP'), new TaxRate(20));
        $price1 = Price::fromNetCost(new Money(9, 'GBP'), new TaxRate(20));
        $price2 = Price::fromNetCost(new Money(11, 'GBP'), new TaxRate(20));

        $this->assertTrue($sut->greaterThan($price1));
        $this->assertFalse($sut->greaterThan($price2));
    }

    public function testExceptionWithUnequalTaxRates()
    {
        $this->expectException(\InvalidArgumentException::class);

        $sut = Price::fromNetCost(new Money(10, 'GBP'), new TaxRate(20));
        $price1 = Price::fromNetCost(new Money(9, 'GBP'), new TaxRate(10));

        $sut->compare($price1);
    }

    public function testAdd()
    {
        $price = Price::fromNetCost(new Money(10, 'GBP'), new TaxRate(20));
        $sut = (Price::fromNetCost(new Money(10, 'GBP'), new TaxRate(20)))->add($price);

        $this->assertTrue($sut->getNet()->equals(new Money(20, 'GBP')), 'Values did not add up');
    }

    public function testSubtract()
    {
        $price = Price::fromNetCost(new Money(5, 'GBP'), new TaxRate(20));
        $sut = (Price::fromNetCost(new Money(10, 'GBP'), new TaxRate(20)))->subtract($price);

        $this->assertTrue($sut->getNet()->equals(new Money(5, 'GBP')), 'Value not subtracted');
    }

    public function testMultiply()
    {
        $sut = (Price::fromNetCost(new Money(10, 'GBP'), new TaxRate(20)))->multiply(3.5);

        $this->assertTrue($sut->getNet()->equals(new Money(35, 'GBP')), 'Value not multiplied');
    }

    public function testGetTax()
    {
        $sut = Price::fromNetCost(new Money(10, 'GBP'), new TaxRate(20));
        $this->assertTrue($sut->getTax()->equals(new Money(2, 'GBP')), 'Tax calculated incorrectly');
    }

    public function testIsSameTaxRate()
    {
        $sut = Price::fromNetCost(new Money(10, 'GBP'), new TaxRate(20));
        $price1 = Price::fromNetCost(new Money(10, 'GBP'), new TaxRate(20));
        $price2 = Price::fromNetCost(new Money(10, 'GBP'), new TaxRate(10));

        $this->assertTrue($sut->isSameTaxRate($price1));
        $this->assertFalse($sut->isSameTaxRate($price2));
    }
}
