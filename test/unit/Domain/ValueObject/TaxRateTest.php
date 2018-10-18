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
final class TaxRateTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideCompare
     *
     * @param TaxRate $a
     * @param TaxRate $b
     * @param int     $expected
     */
    public function testCompare(TaxRate $a, TaxRate $b, $expected)
    {
        $this->assertSame($expected, $a->compare($b));
        $this->assertSame(-1 * $expected, $b->compare($a));
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

        $this->assertTrue($sut->equals($taxrate1));
        $this->assertFalse($sut->equals($taxrate2));
    }

    public function testLessThan()
    {
        $sut = new TaxRate(20);
        $taxrate1 = new TaxRate(25);
        $taxrate2 = new TaxRate(10);

        $this->assertTrue($sut->lessThan($taxrate1));
        $this->assertFalse($sut->lessThan($taxrate2));
    }

    public function testGreaterThan()
    {
        $sut = new TaxRate(20);
        $taxrate1 = new TaxRate(10);
        $taxrate2 = new TaxRate(25);

        $this->assertTrue($sut->greaterThan($taxrate1));
        $this->assertFalse($sut->greaterThan($taxrate2));
    }

    public function testCreate()
    {
        $sut = new TaxRate(15);

        $this->assertSame(15, $sut->getPercentage());
    }
}
