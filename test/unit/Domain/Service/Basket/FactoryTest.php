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

namespace ConferenceTools\Tickets\Domain\Service\Basket;

use Carnage\Cqrs\Aggregate\Identity\GeneratorInterface;
use ConferenceTools\Tickets\Domain\Service\Configuration;
use ConferenceTools\Tickets\Domain\ValueObject\DiscountCode;
use ConferenceTools\Tickets\Domain\ValueObject\DiscountType\Percentage;
use ConferenceTools\Tickets\Domain\ValueObject\TicketReservation;
use ConferenceTools\Tickets\Domain\ValueObject\TicketReservationRequest;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * @internal
 * @coversNothing
 */
final class FactoryTest extends MockeryTestCase
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
                'free' => ['name' => 'Free', 'cost' => 0, 'available' => 100, 'metadata' => ['private' => true]],
            ],
            'financial' => [
                'taxRate' => 10,
                'currency' => 'GBP',
                'displayTax' => true,
            ],
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
            function (TicketReservation $item) {
                return $item->getReservationId();
            },
            $ticketReservations
        );

        $this->assertCount(2, $ticketReservations);
        $this->assertSame($ticketIds, $ticketIdsFromBasket);
        $this->assertFalse($basket->hasDiscountCode());
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

        $this->assertCount(2, $ticketReservations);
        $this->assertTrue($basket->hasDiscountCode());
        $this->assertSame($discountCode, $basket->getDiscountCode());
    }
}
