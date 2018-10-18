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
use JMS\Serializer\Annotation as Jms;

class TicketCancelled implements EventInterface
{
    /**
     * @Jms\Type("string")
     *
     * @var string
     */
    private $id;
    /**
     * @Jms\Type("string")
     *
     * @var string
     */
    private $ticketId;

    /**
     * TicketCancelled constructor.
     *
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
