<?php

namespace ConferenceTools\Tickets\Domain\ValueObject;

use ConferenceTools\Tickets\Domain\Service\Configuration;
use ConferenceTools\Tickets\Domain\ValueObject\DiscountType\Percentage;

class BasketTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Configuration
     */
    private $config;
    public function __construct($name = null, array $data = array(), $dataName = '')
    {

        parent::__construct($name, $data, $dataName);
        $this->config = Configuration::fromArray([
            'tickets' => [
                'early' => ['name' => 'Early Bird', 'cost' => 5000, 'available' => 75],
                'std' => ['name' => 'Standard', 'cost' => 10000, 'available' => 150],
                'free' => ['name' => 'Free', 'cost' => 0, 'available' => 0]
            ],
            'financial' => [
                'taxRate' => 10,
                'currency' => 'GBP',
                'displayTax' => true
            ]
        ]);
    }

    /**
     * @dataProvider provideGetTotalNoDiscount
     * @param array $reservations
     */
    public function testGetTotalNoDiscount(array $reservations, Price $expected)
    {
        $sut = Basket::fromReservations(
            $this->config,
            ... $reservations
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
                Price::fromNetCost(new Money(10000, $this->config->getCurrency()), $this->config->getTaxRate())
            ],
            [
                [$stdReservation, $stdReservation],
                Price::fromNetCost(new Money(20000, $this->config->getCurrency()), $this->config->getTaxRate())
            ],
            [
                [$stdReservation, $earlyReservation],
                Price::fromNetCost(new Money(15000, $this->config->getCurrency()), $this->config->getTaxRate())
            ]
        ];
    }

    /**
     * @dataProvider provideGetTotalWithDiscount
     * @param array $reservations
     */
    public function testGetTotalWithDiscount(array $reservations, Price $expected)
    {
        $sut = Basket::fromReservationsWithDiscount(
            $this->config,
            new DiscountCode('50off', '50% off', new Percentage(50)),
            ... $reservations
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
                Price::fromNetCost(new Money(5000, $this->config->getCurrency()), $this->config->getTaxRate())
            ],
            [
                [$stdReservation, $stdReservation],
                Price::fromNetCost(new Money(10000, $this->config->getCurrency()), $this->config->getTaxRate())
            ],
            [
                [$stdReservation, $earlyReservation],
                Price::fromNetCost(new Money(7500, $this->config->getCurrency()), $this->config->getTaxRate())
            ]
        ];
    }
}