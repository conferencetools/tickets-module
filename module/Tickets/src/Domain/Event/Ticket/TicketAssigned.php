<?php
/**
 * Created by PhpStorm.
 * User: imhotek
 * Date: 28/11/16
 * Time: 16:15
 */

namespace OpenTickets\Tickets\Domain\Event\Ticket;

use Carnage\Cqrs\Event\EventInterface;
use OpenTickets\Tickets\Domain\ValueObject\Delegate;
use JMS\Serializer\Annotation as Jms;

class TicketAssigned implements EventInterface
{
    /**
     * @Jms\Type("string")
     * @var string
     */
    private $ticketId;

    /**
     * @Jms\Type("OpenTickets\Tickets\Domain\ValueObject\Delegate")
     * @var Delegate
     */
    private $delegate;

    /**
     * @Jms\Type("string")
     * @var string
     */
    private $purchaseId;

    /**
     * TicketAssigned constructor.
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