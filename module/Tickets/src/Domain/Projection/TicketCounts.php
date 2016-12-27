<?php

namespace OpenTickets\Tickets\Domain\Projection;

use Carnage\Cqrs\Event\Projection\ResettableInterface;
use Carnage\Cqrs\MessageHandler\AbstractMethodNameMessageHandler;
use Doctrine\ORM\EntityManagerInterface;
use OpenTickets\Tickets\Domain\Event\Ticket\TicketReleased;
use OpenTickets\Tickets\Domain\Event\Ticket\TicketReserved;
use OpenTickets\Tickets\Domain\ReadModel\TicketCounts\TicketCounter;
use OpenTickets\Tickets\Domain\ValueObject\Money;
use OpenTickets\Tickets\Domain\ValueObject\Price;
use OpenTickets\Tickets\Domain\ValueObject\TaxRate;
use OpenTickets\Tickets\Domain\ValueObject\TicketType;

class TicketCounts extends AbstractMethodNameMessageHandler implements ResettableInterface
{
    private $em;
    /**
     * @var array
     */
    private $ticketConfig;

    public function __construct(EntityManagerInterface $em, array $ticketConfig)
    {
        $this->em = $em;
        $this->ticketConfig = $ticketConfig;
    }

    public function reset()
    {
        $em = $this->em;

        $q = $em->createQuery(sprintf('delete from %s', TicketCounter::class));
        $q->execute();

        foreach ($this->ticketConfig as $handle => $ticket) {
            $cost = new Money($ticket['cost'], $ticket['currency']);
            $tax = new TaxRate($ticket['tax']);
            $price = Price::fromNetCost($cost, $tax);
            $ticketType = new TicketType($handle, $price, $ticket['name']);
            $entity = new TicketCounter(
                $ticketType,
                $ticket['available']
            );
            $em->persist($entity);
        }

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