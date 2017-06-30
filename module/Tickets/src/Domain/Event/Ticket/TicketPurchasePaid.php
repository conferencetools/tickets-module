<?php
/**
 * Created by PhpStorm.
 * User: imhotek
 * Date: 28/11/16
 * Time: 18:20
 */

namespace ConferenceTools\Tickets\Domain\Event\Ticket;

use JMS\Serializer\Annotation as Jms;
use Carnage\Cqrs\Event\EventInterface;

class TicketPurchasePaid implements EventInterface
{
    /**
     * @Jms\Type("string")
     * @var string
     */
    private $id;

    /**
     * @Jms\Type("string")
     * @var string
     */
    private $purchaserEmail;

    /**
     * TicketPurchasePaid constructor.
     * @param string $id
     * @param string $purchaserEmail
     */
    public function __construct(string $id, string $purchaserEmail)
    {
        $this->id = $id;
        $this->purchaserEmail = $purchaserEmail;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPurchaserEmail(): string
    {
        return $this->purchaserEmail;
    }
}