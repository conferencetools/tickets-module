<?php
/**
 * Created by PhpStorm.
 * User: imhotek
 * Date: 29/11/16
 * Time: 15:45
 */

namespace ConferenceTools\Tickets\Domain\Event\Ticket;

use JMS\Serializer\Annotation as Jms;
use Carnage\Cqrs\Event\EventInterface;
use ConferenceTools\Tickets\Domain\ValueObject\TicketType;

class TicketReleased implements EventInterface
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