<?php

namespace ConferenceTools\Tickets\Cli\Command;

use Carnage\Cqrs\MessageBus\MessageBusInterface;
use Carnage\Cqrs\Service\EventCatcher;
use ConferenceTools\Tickets\Domain\Command\Ticket\CompletePurchase;
use ConferenceTools\Tickets\Domain\Command\Ticket\MakePayment;
use ConferenceTools\Tickets\Domain\Command\Ticket\ReserveTickets;
use ConferenceTools\Tickets\Domain\Event\Ticket\TicketPurchaseCreated;
use ConferenceTools\Tickets\Domain\Service\Configuration;
use ConferenceTools\Tickets\Domain\ValueObject\Delegate;
use ConferenceTools\Tickets\Domain\ValueObject\DiscountCode;
use ConferenceTools\Tickets\Domain\ValueObject\TicketReservationRequest;
use ConferenceTools\Tickets\Domain\ValueObject\TicketType;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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