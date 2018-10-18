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
use ConferenceTools\Tickets\Domain\Command\Ticket\MakePayment;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MarkPurchasePaid extends Command
{
    /**
     * @var MessageBusInterface
     */
    private $commandBus;

    public static function build(MessageBusInterface $commandBus)
    {
        $instance = new static();
        $instance->commandBus = $commandBus;

        return $instance;
    }

    protected function configure()
    {
        $this->setName('tickets:mark-purchase-paid')
            ->setDescription('Marks a purchase made via the web as paid')
            ->setDefinition([
                new InputArgument('purchaseId', InputArgument::REQUIRED, 'Id of purchase to mark paid'),
                new InputArgument('email', InputArgument::REQUIRED, 'Email address for purchaser'),
            ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $purchaseId = $input->getArgument('purchaseId');

        $this->commandBus->dispatch(new MakePayment($purchaseId, $email));

        $output->writeln(sprintf('Marked paid. PurchaseId: %s', $purchaseId));
    }
}
