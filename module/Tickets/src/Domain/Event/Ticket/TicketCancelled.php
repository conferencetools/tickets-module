<?php


namespace OpenTickets\Tickets\Domain\Event\Ticket;


use Carnage\Cqrs\Event\EventInterface;

class TicketCancelled implements EventInterface
{
    /**
     * @var string
     */
    private $id;
    /**
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
