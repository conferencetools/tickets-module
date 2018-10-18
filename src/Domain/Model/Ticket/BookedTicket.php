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

namespace ConferenceTools\Tickets\Domain\Model\Ticket;

use ConferenceTools\Tickets\Domain\Event\Ticket\TicketAssigned;
use ConferenceTools\Tickets\Domain\Event\Ticket\TicketReserved;
use ConferenceTools\Tickets\Domain\ValueObject\Delegate;
use ConferenceTools\Tickets\Domain\ValueObject\TicketType;

class BookedTicket
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var TicketType
     */
    private $ticketType;

    /**
     * @var Delegate
     */
    private $delegate;

    public function getId()
    {
        return $this->id;
    }

    public static function reserve(TicketReserved $event)
    {
        $instance = new static();
        $instance->id = $event->getId();
        $instance->ticketType = $event->getTicketType();

        return $instance;
    }

    public function assignToDelegate(TicketAssigned $event)
    {
        $this->delegate = $event->getDelegate();
    }

    public function getTicketType(): TicketType
    {
        return $this->ticketType;
    }
}
