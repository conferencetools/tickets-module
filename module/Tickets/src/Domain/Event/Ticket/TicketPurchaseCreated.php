<?php
/**
 * Created by PhpStorm.
 * User: imhotek
 * Date: 28/11/16
 * Time: 18:21
 */

namespace OpenTickets\Tickets\Domain\Event\Ticket;

use JMS\Serializer\Annotation as Jms;
use Carnage\Cqrs\Event\EventInterface;
use OpenTickets\Tickets\Domain\ValueObject\Money;

class TicketPurchaseCreated implements EventInterface
{
    /**
     * @Jms\Type("string")
     * @var string
     */
    private $id;

    /**
     * TicketPurchaseTotalPriceCalculated constructor.
     * @param string $id
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