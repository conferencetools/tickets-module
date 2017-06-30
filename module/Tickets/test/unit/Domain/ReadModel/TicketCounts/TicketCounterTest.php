<?php
/**
 * Created by PhpStorm.
 * User: imhotek
 * Date: 13/05/17
 * Time: 13:32
 */

namespace ConferenceTools\Tickets\Domain\ReadModel\TicketCounts;


use ConferenceTools\Tickets\Domain\ValueObject\Money;
use ConferenceTools\Tickets\Domain\ValueObject\Price;
use ConferenceTools\Tickets\Domain\ValueObject\TaxRate;
use ConferenceTools\Tickets\Domain\ValueObject\TicketType;
use PHPUnit\Framework\TestCase;

class TicketCounterTest extends TestCase
{
    public function testCreate()
    {
        $price = Price::fromNetCost(new Money(100, 'GBP'), new TaxRate(20));
        $ticketType = new TicketType('test', $price, 'Test ticket');

        $sut = new TicketCounter($ticketType, 10);

        self::assertEquals($ticketType, $sut->getTicketType());
        self::assertEquals(10, $sut->getRemaining());
    }

    public function testTicketsReserved()
    {
        $price = Price::fromNetCost(new Money(100, 'GBP'), new TaxRate(20));
        $ticketType = new TicketType('test', $price, 'Test ticket');

        $sut = new TicketCounter($ticketType, 10);
        $sut->ticketsReserved(5);

        self::assertEquals(5, $sut->getRemaining());
    }

    public function testTicketsReleased()
    {
        $price = Price::fromNetCost(new Money(100, 'GBP'), new TaxRate(20));
        $ticketType = new TicketType('test', $price, 'Test ticket');

        $sut = new TicketCounter($ticketType, 10);
        $sut->ticketsReleased(5);

        self::assertEquals(15, $sut->getRemaining());
    }
}
