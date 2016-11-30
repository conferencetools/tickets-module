<?php

namespace OpenTickets\Tickets\Domain\Command\Ticket;

use Carnage\Cqrs\Command\CommandInterface;
use OpenTickets\Tickets\Domain\ValueObject\Delegate;

/**
 * Class AssignToDelegate
 * @package OpenTickets\Tickets\Domain\Command\Ticket
 *
 * This command is intended for updating tickets at a later date after they have been purchased
 */
class AssignToDelegate implements CommandInterface
{
    /**
     * @var Delegate
     */
    private $delegate;

    /**
     * @var string
     */
    private $ticketId;

    /**
     * @var string
     */
    private $purchaseId;

    /**
     * AssignToDelegate constructor.
     * @param $delegate
     * @param $ticketId
     * @param $purchaseId
     */
    public function __construct(Delegate $delegate, string $ticketId, string $purchaseId)
    {
        $this->delegate = $delegate;
        $this->ticketId = $ticketId;
        $this->purchaseId = $purchaseId;
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
    public function getTicketId(): string
    {
        return $this->ticketId;
    }

    /**
     * @return string
     */
    public function getPurchaseId(): string
    {
        return $this->purchaseId;
    }
}