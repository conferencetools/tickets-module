<?php

namespace ConferenceTools\Tickets\Domain\Projection;

use Carnage\Cqrs\Event\Projection\ResettableInterface;
use Carnage\Cqrs\MessageHandler\AbstractMethodNameMessageHandler;
use Doctrine\ORM\EntityManagerInterface;
use ConferenceTools\Tickets\Domain\Event\Ticket\TicketReleased;
use ConferenceTools\Tickets\Domain\Event\Ticket\TicketReserved;
use ConferenceTools\Tickets\Domain\ReadModel\TicketCounts\TicketCounter;
use ConferenceTools\Tickets\Domain\Service\Configuration;
use ConferenceTools\Tickets\Domain\ValueObject\Money;
use ConferenceTools\Tickets\Domain\ValueObject\Price;
use ConferenceTools\Tickets\Domain\ValueObject\TaxRate;
use ConferenceTools\Tickets\Domain\ValueObject\TicketType;

class TicketCounts extends AbstractMethodNameMessageHandler implements ResettableInterface
{
    private $em;

    /**
     * @var Configuration
     */
    private $ticketConfig;

    public function __construct(EntityManagerInterface $em, Configuration $ticketConfig)
    {
        $this->em = $em;
        $this->ticketConfig = $ticketConfig;
    }

    public function reset()
    {
        $em = $this->em;

        $q = $em->createQuery(sprintf('delete from %s tc', TicketCounter::class));
        $q->execute();

        foreach ($this->ticketConfig->getTicketTypes() as $handle => $ticketType) {
            $entity = new TicketCounter(
                $ticketType,
                $this->ticketConfig->getAvailableTickets($handle)
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