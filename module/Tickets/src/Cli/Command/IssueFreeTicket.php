<?php

namespace OpenTickets\Tickets\Cli\Command;

use Carnage\Cqrs\MessageBus\MessageBusInterface;
use Doctrine\ORM\EntityManagerInterface;
use OpenTickets\Tickets\Domain\Command\Ticket\CompletePurchase;
use OpenTickets\Tickets\Domain\Command\Ticket\ReserveTickets;
use OpenTickets\Tickets\Domain\Command\Ticket\TimeoutPurchase;
use OpenTickets\Tickets\Domain\ReadModel\TicketRecord\PurchaseRecord;
use OpenTickets\Tickets\Domain\ReadModel\TicketRecord\TicketRecord;
use OpenTickets\Tickets\Domain\Service\Configuration;
use OpenTickets\Tickets\Domain\ValueObject\Delegate;
use OpenTickets\Tickets\Domain\ValueObject\TicketReservationRequest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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

    public static function build(MessageBusInterface $commandBus, Configuration $config)
    {
        $instance = new static();
        $instance->commandBus = $commandBus;
        $instance->config = $config;

        return $instance;
    }

    protected function configure()
    {
        $this->setName('opentickets:issue-free-ticket')
            ->setDescription('Creates a free ticket purchase record for a given email address')
            ->setDefinition([
                new InputArgument('email', InputArgument::OPTIONAL, 'Email address to send ticket to')
        ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('projection');
        $this->commandBus->dispatch(
            new ReserveTickets(new TicketReservationRequest($this->config->getFreeTicketType(), 1))
        );
//how to get purchase id out?
        $this->commandBus->dispatch(new CompletePurchase('', $email, Delegate::emptyObject()));
    }
}