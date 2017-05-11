<?php

namespace OpenTickets\Tickets\Domain\ValueObject;

use OpenTickets\Tickets\Domain\Service\Configuration;
use OpenTickets\Tickets\Domain\ValueObject\DiscountType\Percentage;

class TicketMetadataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideAvailableOn
     * @param $test
     * @param $expected
     */
    public function testAvailableOn($test, $expected)
    {
        $ticketType = new TicketType('z', Price::fromNetCost(new Money(0, 'gbp'), new TaxRate(20)), 'Z');

        $sut = TicketMetadata::createWithoutDates($ticketType, false);
        $this->assertEquals($expected, $sut->isAvailableOn($test));
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