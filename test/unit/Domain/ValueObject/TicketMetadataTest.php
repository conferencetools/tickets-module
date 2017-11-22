<?php

namespace ConferenceTools\Tickets\Domain\ValueObject;

use ConferenceTools\Tickets\Domain\Service\Configuration;
use ConferenceTools\Tickets\Domain\ValueObject\DiscountType\Percentage;

class TicketMetadataTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideAvailableOn
     * @param $test
     * @param $expected
     */
    public function testAvailableOn($test, $expected)
    {
        $ticketType = new TicketType('z', Price::fromNetCost(new Money(0, 'gbp'), new TaxRate(20)), 'Z');

        $sut = TicketMetadata::fromArray($ticketType, []);
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