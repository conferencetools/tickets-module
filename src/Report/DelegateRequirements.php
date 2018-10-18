<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace ConferenceTools\Tickets\Report;

use ConferenceTools\Tickets\Domain\ReadModel\TicketRecord\TicketRecord;
use Doctrine\ORM\EntityManagerInterface;

final class DelegateRequirements implements ReportInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * DelegateInformation constructor.
     *
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
        foreach ($tickets as $ticket) {
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
