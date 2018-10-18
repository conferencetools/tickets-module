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

namespace ConferenceTools\Tickets\Cli\Command;

use Carnage\Cqrs\MessageBus\MessageBusInterface;
use ConferenceTools\Tickets\Domain\Command\Ticket\TimeoutPurchase;
use ConferenceTools\Tickets\Domain\ReadModel\TicketRecord\PurchaseRecord;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TimeoutPurchases extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var MessageBusInterface
     */
    private $commandBus;

    public static function build(EntityManagerInterface $em, MessageBusInterface $commandBus)
    {
        $instance = new static();
        $instance->em = $em;
        $instance->commandBus = $commandBus;

        return $instance;
    }

    protected function configure()
    {
        $this->setName('tickets:timeout-purchases')
            ->setDescription('Times out all purchases over 30 mins old.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $qb = $this->em->getRepository(PurchaseRecord::class)->createQueryBuilder('pr');
        /** @var PurchaseRecord[] $timedout */
        $timedout = $qb->where('pr.paid = false')
            ->getQuery()
            ->getResult();

        foreach ($timedout as $ticketRecord) {
            if ($ticketRecord->hasTimedout()) {
                $command = new TimeoutPurchase($ticketRecord->getPurchaseId());
                $this->commandBus->dispatch($command);
            }
        }
    }
}
