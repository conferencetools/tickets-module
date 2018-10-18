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
use ConferenceTools\Tickets\Domain\ValueObject\TicketType;
use JMS\Serializer\Annotation as Jms;

class TicketReleased implements EventInterface
{
    /**
     * @Jms\Type("string")
     *
     * @var string
     */
    private $id;

    /**
     * @Jms\Type("ConferenceTools\Tickets\Domain\ValueObject\TicketType")
     *
     * @var TicketType
     */
    private $ticketType;

    /**
     * @Jms\Type("string")
     *
     * @var string
     */
    private $purchaseId;

    /**
     * TicketReleased constructor.
     *
     * @param string     $id
     * @param TicketType $ticketType
     */
    public function __construct(string $id, string $purchaseId, TicketType $ticketType)
    {
        $this->id = $id;
        $this->ticketType = $ticketType;
        $this->purchaseId = $purchaseId;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return TicketType
     */
    public function getTicketType(): TicketType
    {
        return $this->ticketType;
    }

    /**
     * @return string
     */
    public function getPurchaseId(): string
    {
        return $this->purchaseId;
    }
}
