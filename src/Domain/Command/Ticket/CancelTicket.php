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

namespace ConferenceTools\Tickets\Domain\Command\Ticket;

use Carnage\Cqrs\Event\EventInterface;

class CancelTicket implements EventInterface
{
    /**
     * @var string
     */
    private $purchaseId;
    /**
     * @var string
     */
    private $ticketId;

    /**
     * TicketCancelled constructor.
     *
     * @param string $purchaseId
     * @param string $ticketId
     */
    public function __construct(string $purchaseId, string $ticketId)
    {
        $this->purchaseId = $purchaseId;
        $this->ticketId = $ticketId;
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
}
