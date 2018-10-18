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

use ConferenceTools\Tickets\Domain\ValueObject\Delegate;
use ConferenceTools\Tickets\Domain\ValueObject\Money;
use ConferenceTools\Tickets\Domain\ValueObject\Price;
use ConferenceTools\Tickets\Domain\ValueObject\TaxRate;
use ConferenceTools\Tickets\Domain\ValueObject\TicketType;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class TicketRecordTest extends TestCase
{
    public function testCreate()
    {
        $price = Price::fromNetCost(new Money(100, 'GBP'), new TaxRate(20));
        $ticketType = new TicketType('test', $price, 'Test ticket');
        $purchaseRecord = new PurchaseRecord('randomid');

        $sut = new TicketRecord($ticketType, $purchaseRecord, 'ticketid');

        $this->assertSame($ticketType, $sut->getTicketType());
        $this->assertSame('ticketid', $sut->getTicketId());
        $this->assertSame($purchaseRecord, $sut->getPurchase());
        $this->assertSame(Delegate::emptyObject(), $sut->getDelegate());
    }

    public function testUpdateDelegate()
    {
        $data['firstname'] = 'Ed';
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
        $this->assertSame($delegate, $sut->getDelegate());
    }
}
