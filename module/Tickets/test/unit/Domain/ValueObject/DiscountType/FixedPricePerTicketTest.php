<?php

namespace OpenTickets\Tickets\Domain\ValueObject\DiscountType;

use OpenTickets\Tickets\Domain\Service\Configuration;
use OpenTickets\Tickets\Domain\ValueObject\Basket;
use OpenTickets\Tickets\Domain\ValueObject\Money;
use OpenTickets\Tickets\Domain\ValueObject\Price;
use OpenTickets\Tickets\Domain\ValueObject\TicketReservation;

class FixedPricePerTicketTest extends \PHPUnit\Framework\TestCase
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
     * @dataProvider provideTestApply
     * @param $basket
     * @param Price $expected
     */
    public function testApply(Basket $basket, Price $discount, Price $expected)
    {
        $sut = new FixedPerTicket($discount);
        $this->assertTrue($expected->equals($sut->apply($basket)), 'Price didn\'t match expected value');
    }

    public function provideTestApply()
    {
        return [
            [
                Basket::fromReservations(
                    $this->config,
                    new TicketReservation($this->config->getTicketType('std'), 'abc'),
                    new TicketReservation($this->config->getTicketType('std'), 'abc')
                ),
                Price::fromNetCost(new Money(1000, $this->config->getCurrency()), $this->config->getTaxRate()),
                Price::fromNetCost(new Money(2000, $this->config->getCurrency()), $this->config->getTaxRate())
            ],
            [
                Basket::fromReservations(
                    $this->config,
                    new TicketReservation($this->config->getTicketType('std'), 'abc')
                ),
                Price::fromNetCost(new Money(1000, $this->config->getCurrency()), $this->config->getTaxRate()),
                Price::fromNetCost(new Money(1000, $this->config->getCurrency()), $this->config->getTaxRate())
            ]
        ];
    }
}