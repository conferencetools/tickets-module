<?php
/**
 * Created by PhpStorm.
 * User: imhotek
 * Date: 28/11/16
 * Time: 16:22
 */

namespace ConferenceTools\Tickets\Domain\Event\Ticket;

use Carnage\Cqrs\Event\EventInterface;
use JMS\Serializer\Annotation as Jms;

class TicketPurchaseTimedout implements EventInterface
{
    /**
     * @Jms\Type("string")
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