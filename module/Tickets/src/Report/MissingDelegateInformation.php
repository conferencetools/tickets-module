<?php

namespace OpenTickets\Tickets\Report;

use Doctrine\ORM\EntityManagerInterface;
use OpenTickets\Tickets\Domain\ReadModel\TicketRecord\TicketRecord;

final class MissingDelegateInformation implements ReportInterface
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
            if (
                empty($ticket->getDelegate()->getEmail()) &&
                empty($ticket->getDelegate()->getFirstname()) &&
                empty($ticket->getDelegate()->getLastname())
            ) {

                $item = [
                    'purchase_id' => $ticket->getPurchase()->getPurchaseId(),
                    'ticket_id' => $ticket->getTicketId(),
                    'email' => $ticket->getPurchase()->getPurchaserEmail()
                ];

                $report[] = $item;
            }
        }

        return $report;
    }
}