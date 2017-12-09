<?php
/**
 * Created by PhpStorm.
 * User: imhotek
 * Date: 28/11/16
 * Time: 21:37
 */

namespace ConferenceTools\Tickets\Domain\CommandHandler;

use Carnage\Cqrs\Aggregate\Identity\GeneratorInterface;
use Carnage\Cqrs\MessageHandler\AbstractMethodNameMessageHandler;
use Carnage\Cqrs\Persistence\Repository\RepositoryInterface;
use ConferenceTools\Tickets\Domain\Command\Ticket\AssignToDelegate;
use ConferenceTools\Tickets\Domain\Command\Ticket\CancelTicket;
use ConferenceTools\Tickets\Domain\Command\Ticket\CompletePurchase;
use ConferenceTools\Tickets\Domain\Command\Ticket\MakePayment;
use ConferenceTools\Tickets\Domain\Command\Ticket\ReserveTickets;
use ConferenceTools\Tickets\Domain\Command\Ticket\TimeoutPurchase;
use ConferenceTools\Tickets\Domain\Model\Ticket\TicketPurchase;
use ConferenceTools\Tickets\Domain\Service\Basket\Factory;
use ConferenceTools\Tickets\Domain\Service\Configuration;
use ConferenceTools\Tickets\Domain\ValueObject\Basket;
use ConferenceTools\Tickets\Domain\ValueObject\TicketReservation;

class Ticket extends AbstractMethodNameMessageHandler
{
    /**
     * @var GeneratorInterface
     */
    private $identityGenerator;

    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var Factory
     */
    private $basketFactory;

    public function __construct(
        GeneratorInterface $identityGenerator,
        RepositoryInterface $repository,
        Factory $basketFactory
    ) {

        $this->identityGenerator = $identityGenerator;
        $this->repository = $repository;
        $this->basketFactory = $basketFactory;
    }

    /**
     * @TODO can we refactor the command to pass a basket directly?
     * @param ReserveTickets $command
     */
    protected function handleReserveTickets(ReserveTickets $command)
    {
        if ($command->hasDiscountCode()) {
            $basket = $this->basketFactory->basketWithDiscount(
                $command->getDiscountCode(),
                ...$command->getReservationRequests()
            );
        } else {
            $basket = $this->basketFactory->basket(...$command->getReservationRequests());
        }

        $purchase = TicketPurchase::create($this->identityGenerator->generateIdentity(), $basket);

        $this->repository->save($purchase);
    }
    
    protected function handleMakePayment(MakePayment $command)
    {
        $purchase = $this->loadPurchase($command->getPurchaseId());
        $purchase->markAsPaid($command->getPurchaserEmail());
        $this->repository->save($purchase);
    }

    protected function handleTimeoutPurchase(TimeoutPurchase $command)
    {
        $purchase = $this->loadPurchase($command->getPurchaseId());
        $purchase->timeoutPurchase();
        $this->repository->save($purchase);
    }
    
    protected function handleAssignToDelegate(AssignToDelegate $command)
    {
        $purchase = $this->loadPurchase($command->getPurchaseId());
        $purchase->assignTicketToDelegate($command->getTicketId(), $command->getDelegate());
        $this->repository->save($purchase);
    }
    
    protected function handleCompletePurchase(CompletePurchase $command)
    {
        $purchase = $this->loadPurchase($command->getPurchaseId());
        $purchase->completePurchase($command->getPurchaseEmail(), ...$command->getDelegateInformation());
        $this->repository->save($purchase);
    }

    protected function handleCancelTicket(CancelTicket $command)
    {
        $purchase = $this->loadPurchase($command->getPurchaseId());
        $purchase->cancelTicket($command->getTicketId());
        $this->repository->save($purchase);
    }
    
    /**
     * @param string $purchaseId
     * @return TicketPurchase
     */
    private function loadPurchase(string $purchaseId): TicketPurchase
    {
        $purchase = $this->repository->load($purchaseId);
        return $purchase;
    }
}