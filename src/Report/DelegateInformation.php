<?php

namespace ConferenceTools\Tickets\Report;

use Doctrine\ORM\EntityManagerInterface;
use ConferenceTools\Tickets\Domain\ReadModel\TicketRecord\TicketRecord;

final class DelegateInformation implements ReportInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * DelegateInformation constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function produceReport(): array
    {
        $report = [];
        $tickets = $this->em->getRepository(TicketRecord::class)->findAll();
        foreach ($tickets as $ticket)
        {
            /** @var TicketRecord $ticket */
            $item = [
                'firstname' => $ticket->getDelegate()->getFirstname(),
                'lastname' => $ticket->getDelegate()->getLastname(),
                'company' => $ticket->getDelegate()->getCompany(),
                'twitter' => $ticket->getDelegate()->getTwitter(),
                'type' => $ticket->getTicketType()->getDisplayName(),
            ];

            $report[] = $item;
        }

        return $report;
    }
}