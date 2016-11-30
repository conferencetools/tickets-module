<?php
/**
 * Created by PhpStorm.
 * User: imhotek
 * Date: 29/11/16
 * Time: 14:10
 */

namespace OpenTickets\Tickets\Domain\Command\Ticket;


use Carnage\Cqrs\Command\CommandInterface;
use OpenTickets\Tickets\Domain\ValueObject\Delegate;

class CompletePurchase implements CommandInterface
{
    /**
     * @var string
     */
    private $purchaseId;

    /**
     * @var Delegate[]
     */
    private $delegateInformation;

    /**
     * CompletePurchase constructor.
     * @param $purchaseId
     * @param $delegateInformation
     */
    public function __construct(string $purchaseId, Delegate ...$delegateInformation)
    {
        $this->purchaseId = $purchaseId;
        $this->delegateInformation = $delegateInformation;
    }

    /**
     * @return string
     */
    public function getPurchaseId(): string
    {
        return $this->purchaseId;
    }

    /**
     * @return Delegate[]
     */
    public function getDelegateInformation(): array
    {
        return $this->delegateInformation;
    }
}