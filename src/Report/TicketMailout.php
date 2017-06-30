<?php

namespace ConferenceTools\Tickets\Report;

use Doctrine\ORM\EntityManagerInterface;
use ConferenceTools\Tickets\Domain\ReadModel\TicketRecord\TicketRecord;

final class TicketMailout implements ReportInterface
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
            $email = $ticket->getDelegate()->getEmail();
            if (empty($email)) {
                $email = $ticket->getPurchase()->getPurchaserEmail();
            }
            $item = [
                'firstname' => $ticket->getDelegate()->getFirstname(),
                'lastname' => $ticket->getDelegate()->getLastname(),
                'ticket_id' => $ticket->getTicketId(),
                'email' => $email
            ];

            $report[] = $item;
        }

        return $report;
    }
}