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
final class TicketMetadataTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideAvailableOn
     *
     * @param $test
     * @param $expected
     */
    public function testAvailableOn($test, $expected)
    {
        $ticketType = new TicketType('z', Price::fromNetCost(new Money(0, 'gbp'), new TaxRate(20)), 'Z');

        $sut = TicketMetadata::fromArray($ticketType, []);
        $this->assertSame($expected, $sut->isAvailableOn($test));
    }

    public function provideAvailableOn()
    {
        return [
            [new \DateTime(), true],
            [(new \DateTime())->sub(new \DateInterval('P2D')), false],
            [(new \DateTime())->add(new \DateInterval('P2D')), false],
        ];
    }
}
