<?php

namespace OpenTickets\Tickets\Report;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use OpenTickets\Tickets\Domain\ReadModel\TicketRecord\PurchaseRecord;
use OpenTickets\Tickets\Domain\ReadModel\TicketRecord\TicketRecord;
use OpenTickets\Tickets\Domain\ValueObject\DiscountType\Fixed;
use OpenTickets\Tickets\Domain\ValueObject\DiscountType\FixedPerTicket;
use OpenTickets\Tickets\Domain\ValueObject\DiscountType\Percentage;

final class TicketSales implements ReportInterface
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
        $repo = $this->em->getRepository(PurchaseRecord::class);
        /** @var EntityRepository $repo */
        $qb = $repo->createQueryBuilder('p');
        $qb->join('p.tickets', 't');
        $purchases = $qb->getQuery()->execute();
        foreach ($purchases as $purchase)
        {
            /** @var PurchaseRecord $purchase */
            $discountUsed = false;

            foreach ($purchase->getTickets() as $ticket) {
                /** @var TicketRecord $ticket */
                switch (true) {
                    case !$purchase->hasDiscountCode():
                        $report = $this->recordTicketPurchase(
                            $report,
                            $ticket->getTicketType()->getIdentifier(),
                            '-'
                        );
                        break;
                    case !$discountUsed && ($purchase->getDiscountCode()->getDiscountType() instanceof Fixed):
                        $report = $this->recordTicketPurchase(
                            $report,
                            $ticket->getTicketType()->getIdentifier(),
                            $purchase->getDiscountCode()->getCode()
                        );
                        $discountUsed = true;
                        break;
                    case ($purchase->getDiscountCode()->getDiscountType() instanceof FixedPerTicket):
                        $report = $this->recordTicketPurchase(
                            $report,
                            $ticket->getTicketType()->getIdentifier(),
                            $purchase->getDiscountCode()->getCode()
                        );
                        break;
                    case ($purchase->getDiscountCode()->getDiscountType() instanceof Percentage):
                        $report = $this->recordTicketPurchase(
                            $report,
                            $ticket->getTicketType()->getIdentifier(),
                            $purchase->getDiscountCode()->getCode()
                        );
                        break;
                }
            }
        }

        $flattened = [];

        foreach ($report as $ticketType => $codes) {
            foreach ($codes as $code => $count) {
                $flattened[] = [
                    'ticket_type' => $ticketType,
                    'discount_code' => $code,
                    'count' => $count
                ];
            }
        }

        return $flattened;
    }

    /**
     * @param $report
     * @param $ticket
     */
    private function recordTicketPurchase($report, $ticket, $discount)
    {
        if (!isset($report[$ticket])) {
            $report[$ticket] = [];
        }
        if (!isset($report[$ticket][$discount])) {
            $report[$ticket][$discount] = 0;
        }
        $report[$ticket][$discount]++;
        return $report;
    }
}