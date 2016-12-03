<?php

namespace OpenTickets\Tickets\Domain\Projection;

use Carnage\Cqrs\MessageHandler\AbstractMethodNameMessageHandler;
use Doctrine\ORM\EntityManagerInterface;
use OpenTickets\Tickets\Domain\Event\Ticket\TicketAssigned;
use OpenTickets\Tickets\Domain\Event\Ticket\TicketPurchaseCreated;
use OpenTickets\Tickets\Domain\Event\Ticket\TicketPurchasePaid;
use OpenTickets\Tickets\Domain\Event\Ticket\TicketPurchaseTimedout;
use OpenTickets\Tickets\Domain\Event\Ticket\TicketPurchaseTotalPriceCalculated;
use OpenTickets\Tickets\Domain\Event\Ticket\TicketReleased;
use OpenTickets\Tickets\Domain\Event\Ticket\TicketReserved;
use OpenTickets\Tickets\Domain\ReadModel\TicketRecord\PurchaseRecord;
use OpenTickets\Tickets\Domain\ReadModel\TicketRecord\TicketRecord as TicketRecordReadModel;

class TicketRecord extends AbstractMethodNameMessageHandler
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    protected function handleTicketPurchaseCreated(TicketPurchaseCreated $event)
    {
        $entity = new PurchaseRecord($event->getId());
        $this->em->persist($entity);
        $this->em->flush();
    }

    protected function handleTicketPurchaseTotalPriceCalculated(TicketPurchaseTotalPriceCalculated $event)
    {
        $purchase = $this->fetchPurchaseRecord($event->getId());
        $purchase->setTotalCost($event->getTotal());
        $this->em->flush();
    }

    protected function handleTicketPurchasePaid(TicketPurchasePaid $event)
    {
        $purchase = $this->fetchPurchaseRecord($event->getId());

        $purchase->pay($event->getPurchaserEmail());
        $this->em->flush();
    }

    protected function handleTicketPurchaseTimedout(TicketPurchaseTimedout $event)
    {
        $purchase = $this->fetchPurchaseRecord($event->getId());
        $this->em->remove($purchase);
        $this->em->flush();
    }

    protected function handleTicketReserved(TicketReserved $event)
    {
        $purchase = $this->fetchPurchaseRecord($event->getPurchaseId());

        $purchase->addTicketRecord($event->getTicketType(), $event->getId());
        $this->em->flush();
    }

    protected function handleTicketReleased(TicketReleased $event)
    {
        //not bothering to reduce ticket count as this will (should) be followed by a kill purchase event anyway
        $ticket = $this->fetchTicketRecord($event->getPurchaseId(), $event->getId());

        $this->em->remove($ticket);
        $this->em->flush();
    }

    protected function handleTicketAssigned(TicketAssigned $event)
    {
        $ticket = $this->fetchTicketRecord($event->getPurchaseId(), $event->getTicketId());

        $ticket->updateDelegate($event->getDelegate());
        $this->em->flush();
    }

    /**
     * @param string $purchaseId
     * @return PurchaseRecord
     */
    private function fetchPurchaseRecord(string $purchaseId): PurchaseRecord
    {
        $purchase = $this->em->getRepository(PurchaseRecord::class)->findOneBy([
            'purchaseId' => $purchaseId
        ]);
        return $purchase;
    }

    /**
     * @param string $purchaseId
     * @param string $ticketId
     * @return TicketRecordReadModel
     */
    private function fetchTicketRecord(string $purchaseId, string $ticketId): TicketRecordReadModel
    {
        $purchase = $this->fetchPurchaseRecord($purchaseId);
        $ticket = $purchase->getTicketRecord($ticketId);
        return $ticket;
    }
}