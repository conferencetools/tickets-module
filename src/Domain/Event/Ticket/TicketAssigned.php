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

namespace ConferenceTools\Tickets\Domain\Event\Ticket;

use Carnage\Cqrs\Event\EventInterface;
use ConferenceTools\Tickets\Domain\ValueObject\Delegate;
use JMS\Serializer\Annotation as Jms;

class TicketAssigned implements EventInterface
{
    /**
     * @Jms\Type("string")
     *
     * @var string
     */
    private $ticketId;

    /**
     * @Jms\Type("ConferenceTools\Tickets\Domain\ValueObject\Delegate")
     *
     * @var Delegate
     */
    private $delegate;

    /**
     * @Jms\Type("string")
     *
     * @var string
     */
    private $purchaseId;

    /**
     * TicketAssigned constructor.
     *
     * @param $ticketId
     * @param Delegate $delegate
     */
    public function __construct(string $ticketId, string $purchaseId, Delegate $delegate)
    {
        $this->ticketId = $ticketId;
        $this->delegate = $delegate;
        $this->purchaseId = $purchaseId;
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

    /**
     * @return string
     */
    public function getPurchaseId(): string
    {
        return $this->purchaseId;
    }
}
