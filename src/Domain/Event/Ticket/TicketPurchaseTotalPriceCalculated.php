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

namespace ConferenceTools\Tickets\Domain\Event\Ticket;

use Carnage\Cqrs\Event\EventInterface;
use ConferenceTools\Tickets\Domain\ValueObject\Money;
use ConferenceTools\Tickets\Domain\ValueObject\Price;
use JMS\Serializer\Annotation as Jms;

class TicketPurchaseTotalPriceCalculated implements EventInterface
{
    /**
     * @Jms\Type("string")
     *
     * @var string
     */
    private $id;

    /**
     * @var Price
     * @Jms\Type("ConferenceTools\Tickets\Domain\ValueObject\Price")
     */
    private $total;

    /**
     * TicketPurchaseTotalPriceCalculated constructor.
     *
     * @param string $id
     * @param Money  $total
     */
    public function __construct(string $id, Price $total)
    {
        $this->id = $id;
        $this->total = $total;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return Price
     */
    public function getTotal(): Price
    {
        return $this->total;
    }
}
