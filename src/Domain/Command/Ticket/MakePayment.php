<?php
/**
 * Created by PhpStorm.
 * User: imhotek
 * Date: 29/11/16
 * Time: 12:43
 */

namespace ConferenceTools\Tickets\Domain\Command\Ticket;


use Carnage\Cqrs\Command\CommandInterface;

/**
 * Class MakePayment
 * @package ConferenceTools\Tickets\Domain\Command\Ticket
 *
 * This class exists mostly for manually marking a purchase as paid eg for free issues or offline payments.
 */
class MakePayment implements CommandInterface
{
    /**
     * @var string
     */
    private $purchaseId;
    /**
     * @var string
     */
    private $purchaserEmail;

    /**
     * MakePayment constructor.
     * @param string $purchaseId
     * @param string $purchaserEmail
     */
    public function __construct(string $purchaseId, string $purchaserEmail)
    {
        $this->purchaseId = $purchaseId;
        $this->purchaserEmail = $purchaserEmail;
    }

    /**
     * @return string
     */
    public function getPurchaseId(): string
    {
        return $this->purchaseId;
    }

    /**
     * @return string
     */
    public function getPurchaserEmail(): string
    {
        return $this->purchaserEmail;
    }
}