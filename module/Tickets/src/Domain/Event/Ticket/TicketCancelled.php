<?php

namespace OpenTickets\Tickets\Domain\Event\Ticket;

use Carnage\Cqrs\Event\EventInterface;
use JMS\Serializer\Annotation as Jms;

class TicketCancelled implements EventInterface
{
    /**
     * @Jms\Type("string")
     * @var string
     */
    private $id;
    /**
     * @Jms\Type("string")
     * @var string
     */
    private $ticketId;

    /**
     * TicketCancelled constructor.
     * @param string $id
     * @param string $ticketId
     */
    public function __construct(string $id, string $ticketId)
    {
        $this->id = $id;
        $this->ticketId = $ticketId;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTicketId(): string
    {
        return $this->ticketId;
    }
}
