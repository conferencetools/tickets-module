<?php
/**
 * Created by PhpStorm.
 * User: imhotek
 * Date: 28/11/16
 * Time: 16:22
 */

namespace OpenTickets\Tickets\Domain\Event\Ticket;

use Carnage\Cqrs\Event\EventInterface;

class TicketPurchaseTimedout implements EventInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * TicketPurchaseTimedout constructor.
     * @param $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}