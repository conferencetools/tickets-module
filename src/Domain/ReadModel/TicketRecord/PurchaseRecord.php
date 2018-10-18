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

namespace ConferenceTools\Tickets\Domain\ReadModel\TicketRecord;

use ConferenceTools\Tickets\Domain\ValueObject\DiscountCode;
use ConferenceTools\Tickets\Domain\ValueObject\Money;
use ConferenceTools\Tickets\Domain\ValueObject\Price;
use ConferenceTools\Tickets\Domain\ValueObject\TaxRate;
use ConferenceTools\Tickets\Domain\ValueObject\TicketType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class PurchaseRecord.
 *
 * @ORM\Entity()
 */
class PurchaseRecord
{
    /**
     * @var int
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
     * @var Price
     * @ORM\Embedded(class="ConferenceTools\Tickets\Domain\ValueObject\Price")
     */
    private $totalCost;

    /**
     * @var Collection
     * @ORM\OneToMany(
     *     targetEntity="ConferenceTools\Tickets\Domain\ReadModel\TicketRecord\TicketRecord",
     *     mappedBy="purchase",
     *     indexBy="ticketId",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     */
    private $tickets;

    /**
     * @var int
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
     * @ORM\Column(type="json_object", nullable=true)
     *
     * @var DiscountCode
     */
    private $discountCode;

    /**
     * PurchaseRecord constructor.
     *
     * @param string $purchaseId
     */
    public function __construct(string $purchaseId)
    {
        $this->purchaseId = $purchaseId;
        $this->totalCost = Price::fromNetCost(new Money(0, 'GBP'), new TaxRate(0));
        $this->createdAt = new \DateTime();
        $this->tickets = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getPurchaseId(): string
    {
        return $this->purchaseId;
    }

    /**
     * @return Price
     */
    public function getTotalCost(): Price
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
     * @return string
     */
    public function getPurchaserEmail(): string
    {
        return $this->purchaserEmail;
    }

    /**
     * @return bool
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

    public function hasDiscountCode(): bool
    {
        return !(null === $this->discountCode);
    }

    /**
     * @return DiscountCode
     */
    public function getDiscountCode(): DiscountCode
    {
        return $this->discountCode;
    }

    public function getTicketRecord($ticketId): TicketRecord
    {
        if (!isset($this->tickets[$ticketId])) {
            throw new \InvalidArgumentException('Invalid Ticket Id');
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
                $tickets[$ticketTypeIdentifier]['quantity'] = 1;
                $tickets[$ticketTypeIdentifier]['lineTotal'] = $ticket->getTicketType()->getPrice();
                $tickets[$ticketTypeIdentifier]['ticketType'] = $ticket->getTicketType();
            } else {
                ++$tickets[$ticketTypeIdentifier]['quantity'];
                $tickets[$ticketTypeIdentifier]['lineTotal'] =
                    $tickets[$ticketTypeIdentifier]['lineTotal']->add($ticket->getTicketType()->getPrice());
            }
        }

        return $tickets;
    }

    public function addTicketRecord(TicketType $ticketType, string $ticketId)
    {
        $ticketRecord = new TicketRecord($ticketType, $this, $ticketId);
        $this->tickets->set($ticketId, $ticketRecord);
        ++$this->ticketCount;
    }

    /**
     * @param string $email
     */
    public function pay($email)
    {
        $this->purchaserEmail = $email;
        $this->paid = true;
    }

    /**
     * @param Price $totalCost
     */
    public function setTotalCost(Price $totalCost)
    {
        $this->totalCost = $totalCost;
    }

    public function hasTimedout()
    {
        return $this->createdAt < new \DateTime('-30 minutes');
    }

    public function applyDiscountCode(DiscountCode $discountCode)
    {
        $this->discountCode = $discountCode;
    }

    public function cancelTicket(string $ticketId)
    {
        $ticket = $this->getTicketRecord($ticketId);
        $ticket->cancel();
        $this->tickets->remove($ticketId);
        --$this->ticketCount;
    }

    public function shouldBeCancelled(): bool
    {
        return $this->ticketCount <= 0;
    }
}
