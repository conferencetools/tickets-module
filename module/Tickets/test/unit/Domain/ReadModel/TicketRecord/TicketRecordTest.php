<?php

namespace OpenTickets\Tickets\Domain\ReadModel\TicketRecord;

use OpenTickets\Tickets\Domain\ValueObject\Delegate;
use OpenTickets\Tickets\Domain\ValueObject\Money;
use OpenTickets\Tickets\Domain\ValueObject\Price;
use OpenTickets\Tickets\Domain\ValueObject\TaxRate;
use OpenTickets\Tickets\Domain\ValueObject\TicketType;
use PHPUnit\Framework\TestCase;

class TicketRecordTest extends TestCase
{
    public function testCreate()
    {
        $price = Price::fromNetCost(new Money(100, 'GBP'), new TaxRate(20));
        $ticketType = new TicketType('test', $price, 'Test ticket');
        $purchaseRecord = new PurchaseRecord('randomid');

        $sut = new TicketRecord($ticketType, $purchaseRecord, 'ticketid');

        self::assertEquals($ticketType, $sut->getTicketType());
        self::assertEquals('ticketid', $sut->getTicketId());
        self::assertEquals($purchaseRecord, $sut->getPurchase());
        self::assertEquals(Delegate::emptyObject(), $sut->getDelegate());
    }

    public function testUpdateDelegate()
    {
        $data['firstname']= 'Ed';
        $data['lastname'] = 'Nigma';
        $data['email'] = 'ed.nigma@gmail.com';
        $data['company'] = 'Mystery Inc.';
        $data['twitter'] = '@ed_nigma';
        $data['requirements'] = 'none';

        $delegate = Delegate::fromArray($data);

        $price = Price::fromNetCost(new Money(100, 'GBP'), new TaxRate(20));
        $ticketType = new TicketType('test', $price, 'Test ticket');
        $purchaseRecord = new PurchaseRecord('randomid');

        $sut = new TicketRecord($ticketType, $purchaseRecord, 'ticketid');
        $sut->updateDelegate($delegate);
        self::assertEquals($delegate, $sut->getDelegate());
    }
}
