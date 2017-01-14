<?php

namespace OpenTickets\Tickets\Cli\Command;

use Carnage\Cqrs\MessageBus\MessageBusInterface;
use Carnage\Cqrs\Service\EventCatcher;
use Doctrine\ORM\EntityManagerInterface;
use OpenTickets\Tickets\Domain\Command\Ticket\CompletePurchase;
use OpenTickets\Tickets\Domain\Command\Ticket\ReserveTickets;
use OpenTickets\Tickets\Domain\Command\Ticket\TimeoutPurchase;
use OpenTickets\Tickets\Domain\Event\Ticket\TicketPurchaseCreated;
use OpenTickets\Tickets\Domain\ReadModel\TicketRecord\PurchaseRecord;
use OpenTickets\Tickets\Domain\ReadModel\TicketRecord\TicketRecord;
use OpenTickets\Tickets\Domain\Service\Configuration;
use OpenTickets\Tickets\Domain\ValueObject\Delegate;
use OpenTickets\Tickets\Domain\ValueObject\TicketReservationRequest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class IssueFreeTicket extends Command
{
    /**
     * @var MessageBusInterface
     */
    private $commandBus;

    /**
     * @var Configuration
     */
    private $config;

    /**
     * @var EventCatcher
     */
    private $eventCatcher;

    public static function build(MessageBusInterface $commandBus, Configuration $config, EventCatcher $eventCatcher)
    {
        $instance = new static();
        $instance->commandBus = $commandBus;
        $instance->eventCatcher = $eventCatcher;
        $instance->config = $config;

        return $instance;
    }

    protected function configure()
    {
        $this->setName('opentickets:issue-free-ticket')
            ->setDescription('Creates a free ticket purchase record for a given email address')
            ->setDefinition([
                new InputArgument('ticketType', InputArgument::REQUIRED, 'Ticket type to issue'),
                new InputArgument('email', InputArgument::REQUIRED, 'Email address to send ticket to'),
                new InputOption('number', '', InputOption::VALUE_OPTIONAL, 'Number of tickets to add to purchase', 1)
        ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $issueTicketType = $input->getArgument('ticketType');
        $ticketTypes = $this->config->getTicketTypes();

        if (!isset($ticketTypes[$issueTicketType])) {
            throw new \Exception('Invalid ticket type');
        }

        $ticketType = $ticketTypes[$issueTicketType];
        $numberOfTickets = $input->getOption('number');
        $this->commandBus->dispatch(
            new ReserveTickets(new TicketReservationRequest($ticketType, $numberOfTickets))
        );
        /** @var TicketPurchaseCreated $event */
        $event = $this->eventCatcher->getEventsByType(TicketPurchaseCreated::class)[0];
        $delegateInfo = [];

        for ($i = 0; $i < $numberOfTickets; $i++) {
            $delegateInfo[] = Delegate::emptyObject();
        }

        $this->commandBus->dispatch(new CompletePurchase($event->getId(), $email, ...$delegateInfo));

        $output->writeln(sprintf('Tickets created. PurchaseId: %s', $event->getId()));
    }
}