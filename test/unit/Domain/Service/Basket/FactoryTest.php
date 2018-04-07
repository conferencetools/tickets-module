<?php

namespace ConferenceTools\Tickets\Domain\Service\Basket;

use Carnage\Cqrs\Aggregate\Identity\GeneratorInterface;
use ConferenceTools\Tickets\Domain\Service\Configuration;
use ConferenceTools\Tickets\Domain\ValueObject\DiscountCode;
use ConferenceTools\Tickets\Domain\ValueObject\DiscountType\Percentage;
use ConferenceTools\Tickets\Domain\ValueObject\TicketReservation;
use ConferenceTools\Tickets\Domain\ValueObject\TicketReservationRequest;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHPUnit\Framework\TestCase;
use Mockery as m;

class FactoryTest extends MockeryTestCase
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
                'free' => ['name' => 'Free', 'cost' => 0, 'available' => 100, 'metadata' => ['private' => true]]
            ],
            'financial' => [
                'taxRate' => 10,
                'currency' => 'GBP',
                'displayTax' => true
            ]
        ]);
    }

    public function testBasket()
    {
        $generator = m::mock(GeneratorInterface::class);
        $ticketIds = ['id1', 'id2'];
        $generator->shouldReceive('generateIdentity')->andReturnValues($ticketIds);

        $validator = m::mock(BasketValidator::class);
        $validator->shouldReceive('validate');

        $sut = new Factory(
            $generator,
            $this->config,
            $validator
        );

        $basket = $sut->basket(
            new TicketReservationRequest(
                $this->config->getTicketType('std'),
                2
            )
        );

        $ticketReservations = $basket->getTickets();
        $ticketIdsFromBasket = array_map(
            function(TicketReservation $item) {
                return $item->getReservationId();
            },
            $ticketReservations
        );

        self::assertEquals(2, count($ticketReservations));
        self::assertEquals($ticketIds, $ticketIdsFromBasket);
        self::assertFalse($basket->hasDiscountCode());
    }

    public function testBasketWithDiscount()
    {
        $generator = m::mock(GeneratorInterface::class);
        $ticketIds = ['id1', 'id2'];
        $generator->shouldReceive('generateIdentity')->andReturnValues($ticketIds);

        $validator = m::mock(BasketValidator::class);
        $validator->shouldReceive('validate');

        $sut = new Factory(
            $generator,
            $this->config,
            $validator
        );

        $discountCode = new DiscountCode(
            'discount',
            'Discount',
            new Percentage(15)
        );
        $basket = $sut->basketWithDiscount(
            $discountCode,
            new TicketReservationRequest(
                $this->config->getTicketType('std'),
                2
            )
        );

        $ticketReservations = $basket->getTickets();

        self::assertEquals(2, count($ticketReservations));
        self::assertTrue($basket->hasDiscountCode());
        self::assertSame($discountCode, $basket->getDiscountCode());
    }
}
