<?php

namespace OpenTickets\Tickets\Domain\Projection;

use Carnage\Cqrs\Event\Projection\ResettableInterface;
use Carnage\Cqrs\MessageHandler\AbstractMethodNameMessageHandler;
use Doctrine\ORM\EntityManagerInterface;
use OpenTickets\Tickets\Domain\Event\Ticket\TicketReleased;
use OpenTickets\Tickets\Domain\Event\Ticket\TicketReserved;
use OpenTickets\Tickets\Domain\ReadModel\TicketCounts\TicketCounter;
use OpenTickets\Tickets\Domain\ValueObject\Money;
use OpenTickets\Tickets\Domain\ValueObject\TicketType;

class TicketCounts extends AbstractMethodNameMessageHandler implements ResettableInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function reset()
    {
        $em = $this->em;

        $q = $em->createQuery(sprintf('delete from %s', TicketCounter::class));
        $q->execute();
        //** @TODO move this to config */
        $x = new TicketCounter(new TicketType('sup_early', new Money(70*1.2, 'GBP'), 'Super Early Bird'), 25);
        $y = new TicketCounter(new TicketType('early', new Money(85*1.2, 'GBP'), 'Early Bird'), 75);
        $z = new TicketCounter(new TicketType('std', new Money(100*1.2, 'GBP'), 'Standard'), 150);

        $em->persist($x);
        $em->persist($y);
        $em->persist($z);
        $em->flush();
    }

    protected function handleTicketReserved(TicketReserved $event)
    {
        $counter = $this->em->getRepository(TicketCounter::class)->findOneBy(
            ['ticketType.identifier' => $event->getTicketType()->getIdentifier()]
        );
        $counter->ticketsReserved(1);
        $this->em->flush();
    }
    
    protected function handleTicketReleased(TicketReleased $event)
    {
        $counter = $this->em->getRepository(TicketCounter::class)->findOneBy(
            ['ticketType.identifier' => $event->getTicketType()->getIdentifier()]
        );

        $counter->ticketsReleased(1);
        $this->em->flush();
    }
}