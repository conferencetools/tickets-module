<?php
/**
 * Created by PhpStorm.
 * User: imhotek
 * Date: 28/11/16
 * Time: 18:21
 */

namespace ConferenceTools\Tickets\Domain\Event\Ticket;

use JMS\Serializer\Annotation as Jms;
use Carnage\Cqrs\Event\EventInterface;
use ConferenceTools\Tickets\Domain\ValueObject\Money;
use ConferenceTools\Tickets\Domain\ValueObject\Price;

class TicketPurchaseTotalPriceCalculated implements EventInterface
{
    /**
     * @Jms\Type("string")
     * @var string
     */
    private $id;

    /**
     * @var Price
     * @Jms\Type("ConferenceTools\Tickets\Domain\ValueObject\Price")
     */
    private $total;

    /**
     * TicketPurchaseTotalPriceCalculated constructor.
     * @param string $id
     * @param Money $total
     */
    public function __construct(string $id, Price $total)
    {
        $this->id = $id;
        $this->total = $total;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return Price
     */
    public function getTotal(): Price
    {
        return $this->total;
    }
}