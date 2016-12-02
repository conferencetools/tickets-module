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

class TicketPurchaseTotalPriceCalculated implements EventInterface
{
    /**
     * @Jms\Type("string")
     * @var string
     */
    private $id;

    /**
     * @var Money
     * @Jms\Type("OpenTickets\Tickets\Domain\ValueObject\Money")
     */
    private $total;

    /**
     * TicketPurchaseTotalPriceCalculated constructor.
     * @param string $id
     * @param Money $total
     */
    public function __construct(string $id, Money $total)
    {
        $this->id = $id;
        $this->total = $total;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Money
     */
    public function getTotal()
    {
        return $this->total;
    }
}