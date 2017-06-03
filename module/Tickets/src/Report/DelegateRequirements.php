<?php

namespace ConferenceTools\Tickets\Report;

use Doctrine\ORM\EntityManagerInterface;
use ConferenceTools\Tickets\Domain\ReadModel\TicketRecord\TicketRecord;

final class DelegateRequirements implements ReportInterface
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
            if (!empty($ticket->getDelegate()->getRequirements())) {
                $item = [
                    'firstname' => $ticket->getDelegate()->getFirstname(),
                    'lastname' => $ticket->getDelegate()->getLastname(),
                    'requirements' => $ticket->getDelegate()->getRequirements(),
                ];

                $report[] = $item;
            }
        }

        return $report;
    }
}