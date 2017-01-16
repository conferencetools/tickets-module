<?php
/**
 * Created by PhpStorm.
 * User: imhotek
 * Date: 28/11/16
 * Time: 21:37
 */

namespace OpenTickets\Tickets\Domain\CommandHandler;

use Carnage\Cqrs\Aggregate\Identity\GeneratorInterface;
use Carnage\Cqrs\MessageHandler\AbstractMethodNameMessageHandler;
use Carnage\Cqrs\Persistence\Repository\RepositoryInterface;
use OpenTickets\Tickets\Domain\Command\Ticket\AssignToDelegate;
use OpenTickets\Tickets\Domain\Command\Ticket\CompletePurchase;
use OpenTickets\Tickets\Domain\Command\Ticket\MakePayment;
use OpenTickets\Tickets\Domain\Command\Ticket\ReserveTickets;
use OpenTickets\Tickets\Domain\Command\Ticket\TimeoutPurchase;
use OpenTickets\Tickets\Domain\Model\Ticket\TicketPurchase;
use OpenTickets\Tickets\Domain\Service\Configuration;
use OpenTickets\Tickets\Domain\ValueObject\Basket;
use OpenTickets\Tickets\Domain\ValueObject\TicketReservation;

class Ticket extends AbstractMethodNameMessageHandler
{
    /**
     * @var GeneratorInterface
     */
    private $identityGenerator;
    /**
     * @var GeneratorInterface
     */
    private $ticketIdGenerator;

    /**
     * @var RepositoryInterface
     */
    private $repository;
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(
        GeneratorInterface $identityGenerator,
        GeneratorInterface $ticketIdGenerator,
        RepositoryInterface $repository,
        Configuration $configuration
    ) {

        $this->identityGenerator = $identityGenerator;
        $this->ticketIdGenerator = $ticketIdGenerator;
        $this->repository = $repository;
        $this->configuration = $configuration;
    }

    protected function handleReserveTickets(ReserveTickets $command)
    {
        $tickets = [];
        foreach ($command->getReservationRequests() as $reservationRequest) {
            for ($i = 0; $i < $reservationRequest->getQuantity(); $i++) {
                $tickets[] = new TicketReservation($reservationRequest->getTicketType(), $this->ticketIdGenerator->generateIdentity());
            }
        }

        if (count($tickets) === 0) {
            throw new \RuntimeException('Must specify at least 1 ticket for purchase');
        }

        $basket = Basket::fromReservations($this->configuration, ...$tickets);
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