<?php
/**
 * Created by PhpStorm.
 * User: imhotek
 * Date: 28/11/16
 * Time: 18:20
 */

namespace OpenTickets\Tickets\Domain\Event\Ticket;


use Carnage\Cqrs\Event\EventInterface;

class TicketPurchasePaid implements EventInterface
{
    private $id;

    /**
     * TicketPurchasePaid constructor.
     * @param $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}