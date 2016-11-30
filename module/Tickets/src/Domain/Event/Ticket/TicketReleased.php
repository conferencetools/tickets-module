<?php
/**
 * Created by PhpStorm.
 * User: imhotek
 * Date: 29/11/16
 * Time: 15:45
 */

namespace OpenTickets\Tickets\Domain\Event\Ticket;


use Carnage\Cqrs\Event\EventInterface;
use OpenTickets\Tickets\Domain\ValueObject\TicketType;

class TicketReleased implements EventInterface
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
     * @var string
     */
    private $purchaseId;

    /**
     * TicketReleased constructor.
     * @param string $id
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