<?php

namespace OpenTickets\Tickets\Domain\ReadModel\TicketRecord;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenTickets\Tickets\Domain\ValueObject\Money;
use OpenTickets\Tickets\Domain\ValueObject\TicketType;

/**
 * Class PurchaseRecord
 * @package OpenTickets\Tickets\Domain\ReadModel\TicketRecord
 * @ORM\Entity()
 */
class PurchaseRecord
{
    /**
     * @var integer
     * @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $purchaseId;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $purchaserEmail = '';

    /**
     * @var Money
     * @ORM\Embedded(class="OpenTickets\Tickets\Domain\ValueObject\Money")
     */
    private $totalCost;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="OpenTickets\Tickets\Domain\ReadModel\TicketRecord\TicketRecord", mappedBy="purchase", indexBy="ticketId", cascade={"persist", "remove"})
     */
    private $tickets;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $ticketCount = 0;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $paid = false;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * PurchaseRecord constructor.
     * @param string $purchaseId
     * @param Money $totalCost
     */
    public function __construct(string $purchaseId)
    {
        $this->purchaseId = $purchaseId;
        $this->totalCost = new Money(0, 'GBP');
        $this->createdAt = new \DateTime();
        $this->tickets = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPurchaseId(): string
    {
        return $this->purchaseId;
    }

    /**
     * @return Money
     */
    public function getTotalCost(): Money
    {
        return $this->totalCost;
    }

    /**
     * @return Collection
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    /**
     * @return int
     */
    public function getTicketCount(): int
    {
        return $this->ticketCount;
    }

    /**
     * @return boolean
     */
    public function isPaid(): bool
    {
        return $this->paid;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getTicketRecord($ticketId): TicketRecord
    {
        if (!isset($this->tickets[$ticketId])) {
            throw new \InvalidArgumentException("Invalid Ticket Id");
        }

        return $this->tickets[$ticketId];
    }

    public function getTicketSummary()
    {
        $tickets = [];
        foreach ($this->tickets as $ticket) {
            /** @var TicketRecord $ticket */
            $ticketTypeIdentifier = $ticket->getTicketType()->getIdentifier();

            if (!isset($tickets[$ticketTypeIdentifier])) {
                $tickets[$ticketTypeIdentifier]['quantity'] = 0;
                $tickets[$ticketTypeIdentifier]['lineTotal'] = new Money(0, 'GBP');
            }

            $tickets[$ticketTypeIdentifier]['ticketType'] = $ticket->getTicketType();
            $tickets[$ticketTypeIdentifier]['quantity']++;
            $tickets[$ticketTypeIdentifier]['lineTotal'] =
                $tickets[$ticketTypeIdentifier]['lineTotal']->add($ticket->getTicketType()->getPrice());
        }

        return $tickets;
    }

    public function addTicketRecord(TicketType $ticketType, string $ticketId)
    {
        $ticketRecord = new TicketRecord($ticketType, $this, $ticketId);
        $this->tickets->add($ticketRecord);
        $this->ticketCount++;
    }

    public function pay($email)
    {
        $this->purchaserEmail = $email;
        $this->paid = true;
    }

    /**
     * @param Money $totalCost
     */
    public function setTotalCost(Money $totalCost)
    {
        $this->totalCost = $totalCost;
    }
}