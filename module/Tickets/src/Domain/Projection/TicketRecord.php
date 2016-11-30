<?php

namespace OpenTickets\Tickets\Domain\Projection;

use Carnage\Cqrs\MessageHandler\AbstractMethodNameMessageHandler;
use Doctrine\ORM\EntityManagerInterface;
use OpenTickets\Tickets\Domain\Event\Ticket\TicketAssigned;
use OpenTickets\Tickets\Domain\Event\Ticket\TicketReleased;
use OpenTickets\Tickets\Domain\Event\Ticket\TicketReserved;
use OpenTickets\Tickets\Domain\ReadModel\TicketRecord\TicketRecord as TicketRecordReadModel;

class TicketRecord extends AbstractMethodNameMessageHandler
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    protected function handleTicketReserved(TicketReserved $event)
    {
        $entity = new TicketRecordReadModel($event->getTicketType(), $event->getPurchaseId(), $event->getId());
        $this->em->persist($entity);
        $this->em->flush();
    }

    protected function handleTicketReleased(TicketReleased $event)
    {
        $ticket = $this->em->getRepository(TicketRecordReadModel::class)->findOneBy([
            'ticketId' => $event->getId(),
            'purchaseId' => $event->getPurchaseId()
        ]);
        $this->em->remove($ticket);
        $this->em->flush();
    }

    protected function handleTicketAssigned(TicketAssigned $event)
    {
        $ticket = $this->em->getRepository(TicketRecordReadModel::class)->findOneBy([
            'ticketId' => $event->getTicketId(),
            'purchaseId' => $event->getPurchaseId()
        ]);

        $ticket->updateDelegate($event->getDelegate());
        $this->em->flush();
    }
}