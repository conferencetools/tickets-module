<?php
/**
 * Created by PhpStorm.
 * User: imhotek
 * Date: 29/11/16
 * Time: 14:10
 */

namespace ConferenceTools\Tickets\Domain\Command\Ticket;


use Carnage\Cqrs\Command\CommandInterface;
use ConferenceTools\Tickets\Domain\ValueObject\Delegate;

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
     * @var string
     */
    private $purchaseEmail;

    /**
     * CompletePurchase constructor.
     * @param string $purchaseId
     * @param string $purchaseEmail
     * @param Delegate[] ...$delegateInformation
     */
    public function __construct(string $purchaseId, string $purchaseEmail, Delegate ...$delegateInformation)
    {
        $this->purchaseId = $purchaseId;
        $this->delegateInformation = $delegateInformation;
        $this->purchaseEmail = $purchaseEmail;
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
    public function getPurchaseEmail(): string
    {
        return $this->purchaseEmail;
    }

    /**
     * @return Delegate[]
     */
    public function getDelegateInformation(): array
    {
        return $this->delegateInformation;
    }
}