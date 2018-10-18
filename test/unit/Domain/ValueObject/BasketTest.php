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

use ConferenceTools\Tickets\Domain\Service\Basket\BasketValidator;
use ConferenceTools\Tickets\Domain\Service\Configuration;
use ConferenceTools\Tickets\Domain\ValueObject\DiscountType\Percentage;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * @internal
 * @coversNothing
 */
final class BasketTest extends MockeryTestCase
{
    /**
     * @var Configuration
     */
    private $config;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->config = Configuration::fromArray([
            'tickets' => [
                'early' => ['name' => 'Early Bird', 'cost' => 5000, 'available' => 75],
                'std' => ['name' => 'Standard', 'cost' => 10000, 'available' => 150],
                'free' => ['name' => 'Free', 'cost' => 0, 'available' => 0],
            ],
            'financial' => [
                'taxRate' => 10,
                'currency' => 'GBP',
                'displayTax' => true,
            ],
        ]);
    }

    /**
     * @dataProvider provideGetTotalNoDiscount
     *
     * @param array $reservations
     */
    public function testGetTotalNoDiscount(array $reservations, Price $expected)
    {
        $validator = m::mock(BasketValidator::class);
        $validator->shouldReceive('validate');

        $sut = Basket::fromReservations(
            $this->config,
            $validator,
            ...$reservations
        );

        $this->assertTrue($expected->equals($sut->getTotal()), 'Total didn\'t match expected total');
    }

    public function provideGetTotalNoDiscount()
    {
        $stdReservation = new TicketReservation($this->config->getTicketType('std'), 'abc');
        $earlyReservation = new TicketReservation($this->config->getTicketType('early'), 'abc');

        return [
            [
                [$stdReservation],
                Price::fromNetCost(new Money(10000, $this->config->getCurrency()), $this->config->getTaxRate()),
            ],
            [
                [$stdReservation, $stdReservation],
                Price::fromNetCost(new Money(20000, $this->config->getCurrency()), $this->config->getTaxRate()),
            ],
            [
                [$stdReservation, $earlyReservation],
                Price::fromNetCost(new Money(15000, $this->config->getCurrency()), $this->config->getTaxRate()),
            ],
        ];
    }

    /**
     * @dataProvider provideGetTotalWithDiscount
     *
     * @param array $reservations
     */
    public function testGetTotalWithDiscount(array $reservations, Price $expected)
    {
        $validator = m::mock(BasketValidator::class);
        $validator->shouldReceive('validate');

        $sut = Basket::fromReservationsWithDiscount(
            $this->config,
            $validator,
            new DiscountCode('50off', '50% off', new Percentage(50)),
            ...$reservations
        );

        $this->assertTrue($expected->equals($sut->getTotal()), 'Total didn\'t match expected total');
    }

    public function provideGetTotalWithDiscount()
    {
        $stdReservation = new TicketReservation($this->config->getTicketType('std'), 'abc');
        $earlyReservation = new TicketReservation($this->config->getTicketType('early'), 'abc');

        return [
            [
                [$stdReservation],
                Price::fromNetCost(new Money(5000, $this->config->getCurrency()), $this->config->getTaxRate()),
            ],
            [
                [$stdReservation, $stdReservation],
                Price::fromNetCost(new Money(10000, $this->config->getCurrency()), $this->config->getTaxRate()),
            ],
            [
                [$stdReservation, $earlyReservation],
                Price::fromNetCost(new Money(7500, $this->config->getCurrency()), $this->config->getTaxRate()),
            ],
        ];
    }

    /**
     * @dataProvider provideContainingOnly
     *
     * @param $reservations
     * @param $ticketTypes
     * @param $expected
     */
    public function testContainingOnly($reservations, $ticketTypes, $expected)
    {
        $validator = m::mock(BasketValidator::class);
        $validator->shouldReceive('validate');

        $sut = Basket::fromReservations(
            $this->config,
            $validator,
            ...$reservations
        );

        $result = $sut->containingOnly(...$ticketTypes);

        $this->assertSame($expected, array_values($result->getTickets()));
    }

    public function provideContainingOnly()
    {
        $stdReservation = new TicketReservation($this->config->getTicketType('std'), 'abc');
        $earlyReservation = new TicketReservation($this->config->getTicketType('early'), 'abc');

        return [
            [
                [$stdReservation],
                [$this->config->getTicketType('std')],
                [$stdReservation],
            ],
            [
                [$stdReservation, $stdReservation],
                [$this->config->getTicketType('std')],
                [$stdReservation, $stdReservation],
            ],
            [
                [$stdReservation, $earlyReservation],
                [$this->config->getTicketType('std')],
                [$stdReservation],
            ],
            [
                [$stdReservation, $earlyReservation],
                [$this->config->getTicketType('std'), $this->config->getTicketType('early')],
                [$stdReservation, $earlyReservation],
            ],
        ];
    }
}
