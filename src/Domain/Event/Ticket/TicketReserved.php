<?php

namespace ConferenceTools\Tickets\Domain\Event\Ticket;

use Carnage\Cqrs\Event\EventInterface;
use ConferenceTools\Tickets\Domain\ValueObject\TicketType;
use JMS\Serializer\Annotation as Jms;

class TicketReserved implements EventInterface
{
    /**
     * @Jms\Type("string")
     * @var string
     */
    private $id;

    /**
     * @Jms\Type("ConferenceTools\Tickets\Domain\ValueObject\TicketType")
     * @var TicketType
     */
    private $ticketType;

    /**
     * @Jms\Type("string")
     * @var string
     */
    private $purchaseId;

    /**
     * TicketReserved constructor.
     * @param string $id
     * @param TicketType $ticketType
     * @param string $purchaseId
     */
    public function __construct(string $id, TicketType $ticketType, string $purchaseId)
    {
        $this->id = $id;
        $this->ticketType = $ticketType;
        $this->purchaseId = $purchaseId;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return TicketType
     */
    public function getTicketType()
    {
        return $this->ticketType;
    }

    /**
     * @return string
     */
    public function getPurchaseId()
    {
        return $this->purchaseId;
    }
}