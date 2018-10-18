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

namespace OpenTickets\Tickets\Domain\Service\Basket;

use ConferenceTools\Tickets\Domain\Service\Basket\ValidateBasket;
use ConferenceTools\Tickets\Domain\Service\Configuration;
use ConferenceTools\Tickets\Domain\ValueObject\Basket;
use ConferenceTools\Tickets\Domain\ValueObject\TicketReservation;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class ValidateBasketTest extends TestCase
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

    public function testValidateThrowsOnEmptyBasket()
    {
        $sut = new ValidateBasket();

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('You must choose at least 1 ticket to purchase');

        Basket::fromReservations(
            $this->config,
            $sut
        );
    }

    public function testValidate()
    {
        $sut = new ValidateBasket();

        Basket::fromReservations(
            $this->config,
            $sut,
            new TicketReservation(
                $this->config->getTicketType('std'),
                'id1'
            )
        );
    }
}
