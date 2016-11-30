<?php

namespace OpenTickets\Tickets\Domain\ReadModel\TicketRecord;

use Doctrine\ORM\Mapping as ORM;
use OpenTickets\Tickets\Domain\ValueObject\Delegate;
use OpenTickets\Tickets\Domain\ValueObject\TicketType;

/**
 * Class TicketCounter
 * @package OpenTickets\Tickets\Domain\ReadModel\TicketCounts
 * @ORM\Entity()
 */
class TicketRecord
{
    /**
     * @var integer
     * @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var TicketType
     * @ORM\Embedded(class="OpenTickets\Tickets\Domain\ValueObject\TicketType")
     */
    private $ticketType;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $purchaseId;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $ticketId;

    /**
     * @var Delegate
     * @ORM\Embedded(class="OpenTickets\Tickets\Domain\ValueObject\Delegate")
     */
    private $delegate;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * TicketRecord constructor.
     * @param TicketType $ticketType
     * @param string $purchaseId
     * @param string $ticketId
     * @param Delegate $delegate
     */
    public function __construct(TicketType $ticketType, string $purchaseId, string $ticketId)
    {
        $this->ticketType = $ticketType;
        $this->purchaseId = $purchaseId;
        $this->ticketId = $ticketId;
        $this->delegate = Delegate::emptyObject();
        $this->createdAt = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId(): int
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

    /**
     * @return string
     */
    public function getTicketId(): string
    {
        return $this->ticketId;
    }

    /**
     * @return Delegate
     */
    public function getDelegate(): Delegate
    {
        return $this->delegate;
    }

    public function updateDelegate(Delegate $delegate)
    {
        $this->delegate = $delegate;
    }
}