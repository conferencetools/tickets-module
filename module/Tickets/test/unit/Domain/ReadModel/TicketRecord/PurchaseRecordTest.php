<?php

namespace OpenTickets\Tickets\Domain\ReadModel\TicketRecord;

use Doctrine\Common\Collections\Collection;
use OpenTickets\Tickets\Domain\ValueObject\DiscountCode;
use OpenTickets\Tickets\Domain\ValueObject\DiscountType\Percentage;
use OpenTickets\Tickets\Domain\ValueObject\Money;
use OpenTickets\Tickets\Domain\ValueObject\Price;
use OpenTickets\Tickets\Domain\ValueObject\TaxRate;
use OpenTickets\Tickets\Domain\ValueObject\TicketType;
use PHPUnit\Framework\TestCase;

class PurchaseRecordTest extends TestCase
{
    public function testCreate()
    {
        $price = Price::fromNetCost(new Money(0, 'GBP'), new TaxRate(0));
        $sut = new PurchaseRecord('randomid');

        self::assertEquals('randomid', $sut->getPurchaseId());
        self::assertEquals(0, $sut->getTicketCount());
        self::assertEquals(false, $sut->isPaid());
        self::assertEquals(new \DateTime(), $sut->getCreatedAt(), null, 2);
        self::assertInstanceOf(Collection::class, $sut->getTickets());
        self::assertEquals(0, $sut->getTickets()->count());
        self::assertTrue($sut->getTotalCost()->equals($price));
        self::assertFalse($sut->hasDiscountCode());
        self::assertFalse($sut->hasTimedout());
    }

    public function testApplyDiscountCode()
    {
        $sut = new PurchaseRecord('randomid');
        $discountCode = new DiscountCode('code', 'Code', new Percentage(5));
        $sut->applyDiscountCode($discountCode);

        self::assertTrue($sut->hasDiscountCode());
        self::assertEquals($discountCode->getCode(), $sut->getDiscountCode()->getCode());
    }

    public function testSetTotalCost()
    {
        $sut = new PurchaseRecord('randomid');
        $price = Price::fromNetCost(new Money(100, 'GBP'), new TaxRate(20));
        $sut->setTotalCost($price);

        self::assertTrue($price->equals($sut->getTotalCost()));
    }

    public function testAddTicketRecord()
    {
        $price = Price::fromNetCost(new Money(100, 'GBP'), new TaxRate(20));
        $ticketType = new TicketType('test', $price, 'Test ticket');

        $sut = new PurchaseRecord('randomid');
        $sut->addTicketRecord($ticketType, 'ticketid');
        $ticketRecord = $sut->getTicketRecord('ticketid');

        self::assertEquals(1, $sut->getTicketCount());
        self::assertEquals($ticketType, $ticketRecord->getTicketType());
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

        self::assertEquals(1, $sut->getTicketCount());
        self::assertEquals(false, $sut->shouldBeCancelled());

        $sut->cancelTicket('ticketid');

        self::assertEquals(0, $sut->getTicketCount());
        self::assertEquals(true, $sut->shouldBeCancelled());
    }

    public function testPay()
    {
        $sut = new PurchaseRecord('randomid');
        $sut->pay('test@email.com');

        self::assertTrue($sut->isPaid());
        self::assertEquals('test@email.com', $sut->getPurchaserEmail());
    }

    public function testGetTicketSummary()
    {
        $price = Price::fromNetCost(new Money(100, 'GBP'), new TaxRate(20));
        $ticketType = new TicketType('test', $price, 'Test ticket');
        $ticketType2 = new TicketType('test2', $price, 'Test ticket 2');

        $expected = [
            'test' => ['quantity' => 2, 'lineTotal' => $price->add($price), 'ticketType' => $ticketType],
            'test2' => ['quantity' => 1, 'lineTotal' => $price, 'ticketType' => $ticketType2]
        ];

        $sut = new PurchaseRecord('randomid');
        $sut->addTicketRecord($ticketType, 'ticketid');
        $sut->addTicketRecord($ticketType, 'ticketid2');
        $sut->addTicketRecord($ticketType2, 'ticketid3');

        self::assertEquals($expected, $sut->getTicketSummary());
    }
}
