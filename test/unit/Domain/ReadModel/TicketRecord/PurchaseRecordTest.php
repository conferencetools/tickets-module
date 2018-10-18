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

namespace ConferenceTools\Tickets\Domain\ReadModel\TicketRecord;

use ConferenceTools\Tickets\Domain\ValueObject\DiscountCode;
use ConferenceTools\Tickets\Domain\ValueObject\DiscountType\Percentage;
use ConferenceTools\Tickets\Domain\ValueObject\Money;
use ConferenceTools\Tickets\Domain\ValueObject\Price;
use ConferenceTools\Tickets\Domain\ValueObject\TaxRate;
use ConferenceTools\Tickets\Domain\ValueObject\TicketType;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class PurchaseRecordTest extends TestCase
{
    public function testCreate()
    {
        $price = Price::fromNetCost(new Money(0, 'GBP'), new TaxRate(0));
        $sut = new PurchaseRecord('randomid');

        $this->assertSame('randomid', $sut->getPurchaseId());
        $this->assertSame(0, $sut->getTicketCount());
        $this->assertFalse($sut->isPaid());
        $this->assertEquals(new \DateTime(), $sut->getCreatedAt(), null, 2);
        $this->assertInstanceOf(Collection::class, $sut->getTickets());
        $this->assertSame(0, $sut->getTickets()->count());
        $this->assertTrue($sut->getTotalCost()->equals($price));
        $this->assertFalse($sut->hasDiscountCode());
        $this->assertFalse($sut->hasTimedout());
    }

    public function testApplyDiscountCode()
    {
        $sut = new PurchaseRecord('randomid');
        $discountCode = new DiscountCode('code', 'Code', new Percentage(5));
        $sut->applyDiscountCode($discountCode);

        $this->assertTrue($sut->hasDiscountCode());
        $this->assertSame($discountCode->getCode(), $sut->getDiscountCode()->getCode());
    }

    public function testSetTotalCost()
    {
        $sut = new PurchaseRecord('randomid');
        $price = Price::fromNetCost(new Money(100, 'GBP'), new TaxRate(20));
        $sut->setTotalCost($price);

        $this->assertTrue($price->equals($sut->getTotalCost()));
    }

    public function testAddTicketRecord()
    {
        $price = Price::fromNetCost(new Money(100, 'GBP'), new TaxRate(20));
        $ticketType = new TicketType('test', $price, 'Test ticket');

        $sut = new PurchaseRecord('randomid');
        $sut->addTicketRecord($ticketType, 'ticketid');
        $ticketRecord = $sut->getTicketRecord('ticketid');

        $this->assertSame(1, $sut->getTicketCount());
        $this->assertSame($ticketType, $ticketRecord->getTicketType());
    }

    public function testGetInvalidTicketRecord()
    {
        $this->expectException(\InvalidArgumentException::class);
        $sut = new PurchaseRecord('randomid');
        $sut->getTicketRecord('ticketid');
    }

    public function testCancelTicket()
    {
        $price = Price::fromNetCost(new Money(100, 'GBP'), new TaxRate(20));
        $ticketType = new TicketType('test', $price, 'Test ticket');

        $sut = new PurchaseRecord('randomid');
        $sut->addTicketRecord($ticketType, 'ticketid');
        $sut->addTicketRecord($ticketType, 'ticketid2');
        $sut->cancelTicket('ticketid2');

        $this->assertSame(1, $sut->getTicketCount());
        $this->assertFalse($sut->shouldBeCancelled());

        $sut->cancelTicket('ticketid');

        $this->assertSame(0, $sut->getTicketCount());
        $this->assertTrue($sut->shouldBeCancelled());
    }

    public function testPay()
    {
        $sut = new PurchaseRecord('randomid');
        $sut->pay('test@email.com');

        $this->assertTrue($sut->isPaid());
        $this->assertSame('test@email.com', $sut->getPurchaserEmail());
    }

    public function testGetTicketSummary()
    {
        $price = Price::fromNetCost(new Money(100, 'GBP'), new TaxRate(20));
        $ticketType = new TicketType('test', $price, 'Test ticket');
        $ticketType2 = new TicketType('test2', $price, 'Test ticket 2');

        $expected = [
            'test' => ['quantity' => 2, 'lineTotal' => $price->add($price), 'ticketType' => $ticketType],
            'test2' => ['quantity' => 1, 'lineTotal' => $price, 'ticketType' => $ticketType2],
        ];

        $sut = new PurchaseRecord('randomid');
        $sut->addTicketRecord($ticketType, 'ticketid');
        $sut->addTicketRecord($ticketType, 'ticketid2');
        $sut->addTicketRecord($ticketType2, 'ticketid3');

        $this->assertSame($expected, $sut->getTicketSummary());
    }
}
