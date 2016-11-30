<?php

namespace OpenTickets\Tickets\Domain\Projection;

use Carnage\Cqrs\MessageHandler\AbstractMethodNameMessageHandler;
use Doctrine\ORM\EntityManagerInterface;
use OpenTickets\Tickets\Domain\Event\Ticket\TicketReleased;
use OpenTickets\Tickets\Domain\Event\Ticket\TicketReserved;
use OpenTickets\Tickets\Domain\ReadModel\TicketCounts\TicketCounter;

class TicketCounts extends AbstractMethodNameMessageHandler
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    protected function handleTicketReserved(TicketReserved $event)
    {
        $counter = $this->em->getRepository(TicketCounter::class)->findOneBy(['ticketType' => $event->getTicketType()]);
        $counter->ticketsReserved(1);
        $this->em->flush();
    }
    
    protected function handleTicketReleased(TicketReleased $event)
    {
        $counter = $this->em->getRepository(TicketCounter::class)->findOneBy(['ticketType' => $event->getTicketType()]);
        $counter->ticketsReleased(1);
        $this->em->flush();
    }
}