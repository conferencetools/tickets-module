<?php

namespace ConferenceTools\Tickets\Cli\Command;

use Carnage\Cqrs\MessageBus\MessageBusInterface;
use ConferenceTools\Tickets\Domain\Command\Ticket\CancelTicket as CancelTicketCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CancelTicket extends Command
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
        $this->setName('tickets:cancel-ticket')
            ->setDescription('Cancels a ticket - does not handle refund.')
            ->setDefinition([
                new InputArgument('purchaseId', InputArgument::REQUIRED, 'Purchase id'),
                new InputArgument('ticketId', InputArgument::REQUIRED, 'Ticket id'),
        ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $purchaseId = $input->getArgument('purchaseId');
        $ticketId = $input->getArgument('ticketId');

        $this->commandBus->dispatch(new CancelTicketCommand($purchaseId, $ticketId));

        $output->writeln(sprintf('Tickets cancelled. PurchaseId: %s', $purchaseId));
    }
}
