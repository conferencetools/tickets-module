<?php
/**
 * Created by PhpStorm.
 * User: imhotek
 * Date: 29/11/16
 * Time: 12:52
 */

namespace ConferenceTools\Tickets\Domain\Command\Ticket;


use Carnage\Cqrs\Command\CommandInterface;

class TimeoutPurchase implements CommandInterface
{
    /**
     * @var string
     */
    private $purchaseId;

    /**
     * TimeoutPurchase constructor.
     * @param $purchaseId
     */
    public function __construct(string $purchaseId)
    {
        $this->purchaseId = $purchaseId;
    }

    /**
     * @return string
     */
    public function getPurchaseId(): string
    {
        return $this->purchaseId;
    }
}