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

use ConferenceTools\Tickets\Domain\ValueObject\Delegate;
use ConferenceTools\Tickets\Domain\ValueObject\TicketType;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class TicketCounter.
 *
 * @ORM\Entity()
 */
class TicketRecord
{
    /**
     * @var int
     * @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var TicketType
     * @ORM\Embedded(class="ConferenceTools\Tickets\Domain\ValueObject\TicketType")
     */
    private $ticketType;

    /**
     * @var PurchaseRecord
     * @ORM\ManyToOne(targetEntity="ConferenceTools\Tickets\Domain\ReadModel\TicketRecord\PurchaseRecord", inversedBy="tickets")
     * @ORM\JoinColumn(name="purchase_id", referencedColumnName="id")
     */
    private $purchase;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $ticketId;

    /**
     * @var Delegate
     * @ORM\Embedded(class="ConferenceTools\Tickets\Domain\ValueObject\Delegate")
     */
    private $delegate;

    /**
     * TicketRecord constructor.
     *
     * @param TicketType     $ticketType
     * @param PurchaseRecord $purchase
     * @param string         $ticketId
     */
    public function __construct(TicketType $ticketType, PurchaseRecord $purchase, string $ticketId)
    {
        $this->ticketType = $ticketType;
        $this->ticketId = $ticketId;
        $this->delegate = Delegate::emptyObject();
        $this->purchase = $purchase;
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
    public function getTicketId(): string
    {
        return $this->ticketId;
    }

    /**
     * @return PurchaseRecord
     */
    public function getPurchase(): PurchaseRecord
    {
        return $this->purchase;
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

    public function cancel()
    {
        $this->purchase = null;
    }
}
