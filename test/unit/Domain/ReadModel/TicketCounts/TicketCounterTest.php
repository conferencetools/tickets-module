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

namespace ConferenceTools\Tickets\Domain\ReadModel\TicketCounts;

use ConferenceTools\Tickets\Domain\ValueObject\Money;
use ConferenceTools\Tickets\Domain\ValueObject\Price;
use ConferenceTools\Tickets\Domain\ValueObject\TaxRate;
use ConferenceTools\Tickets\Domain\ValueObject\TicketType;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class TicketCounterTest extends TestCase
{
    public function testCreate()
    {
        $price = Price::fromNetCost(new Money(100, 'GBP'), new TaxRate(20));
        $ticketType = new TicketType('test', $price, 'Test ticket');

        $sut = new TicketCounter($ticketType, 10);

        $this->assertSame($ticketType, $sut->getTicketType());
        $this->assertSame(10, $sut->getRemaining());
    }

    public function testTicketsReserved()
    {
        $price = Price::fromNetCost(new Money(100, 'GBP'), new TaxRate(20));
        $ticketType = new TicketType('test', $price, 'Test ticket');

        $sut = new TicketCounter($ticketType, 10);
        $sut->ticketsReserved(5);

        $this->assertSame(5, $sut->getRemaining());
    }

    public function testTicketsReleased()
    {
        $price = Price::fromNetCost(new Money(100, 'GBP'), new TaxRate(20));
        $ticketType = new TicketType('test', $price, 'Test ticket');

        $sut = new TicketCounter($ticketType, 10);
        $sut->ticketsReleased(5);

        $this->assertSame(15, $sut->getRemaining());
    }
}
