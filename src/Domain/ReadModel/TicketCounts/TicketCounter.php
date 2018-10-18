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

namespace ConferenceTools\Tickets\Domain\ReadModel\TicketCounts;

use ConferenceTools\Tickets\Domain\ValueObject\TicketType;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class TicketCounter.
 *
 * @ORM\Entity()
 */
class TicketCounter
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
     * @var int
     * @ORM\Column(type="integer")
     */
    private $remaining;

    /**
     * TicketCounter constructor.
     *
     * @param TicketType $ticketType
     * @param int        $remaining
     */
    public function __construct(TicketType $ticketType, int $remaining)
    {
        $this->ticketType = $ticketType;
        $this->remaining = $remaining;
    }

    /**
     * @return int
     */
    public function getRemaining()
    {
        return $this->remaining;
    }

    /**
     * @return TicketType
     */
    public function getTicketType()
    {
        return $this->ticketType;
    }

    public function ticketsReserved(int $number)
    {
        $this->remaining -= $number;
    }

    public function ticketsReleased(int $number)
    {
        $this->remaining += $number;
    }
}
