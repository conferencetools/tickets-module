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
use Carnage\Cqrs\Service\EventCatcher;
use ConferenceTools\Tickets\Domain\Command\Ticket\CompletePurchase;
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
        $this->setName('tickets:issue-free-ticket')
            ->setDescription('Creates a free ticket purchase record for a given email address')
            ->setDefinition([
                new InputArgument('ticketType', InputArgument::REQUIRED, 'Ticket type to issue'),
                new InputArgument('email', InputArgument::REQUIRED, 'Email address to send ticket to'),
                new InputOption('number', '', InputOption::VALUE_OPTIONAL, 'Number of tickets to add to purchase', 1),
                new InputOption('discountCode', '', InputOption::VALUE_OPTIONAL, 'Discount code to apply to the purchase'),
            ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $numberOfTickets = $input->getOption('number');
        $ticketType = $this->getTicketType($input->getArgument('ticketType'));
        $discountCode = $this->getDiscountcode($input->getOption('discountCode'));

        $purchaseId = $this->reserveTickets($ticketType, $numberOfTickets, $discountCode);
        $delegateInfo = $this->createDelegates($numberOfTickets);

        $this->commandBus->dispatch(new CompletePurchase($purchaseId, $email, ...$delegateInfo));

        $output->writeln(sprintf('Tickets created. PurchaseId: %s', $purchaseId));
    }

    /**
     * @param $numberOfTickets
     *
     * @return Delegate[]
     */
    private function createDelegates($numberOfTickets): array
    {
        $delegateInfo = [];

        for ($i = 0; $i < $numberOfTickets; ++$i) {
            $delegateInfo[] = Delegate::emptyObject();
        }

        return $delegateInfo;
    }

    /**
     * @param $ticketType
     * @param $numberOfTickets
     * @param mixed $discountCode
     *
     * @return string
     */
    private function reserveTickets($ticketType, $numberOfTickets, $discountCode): string
    {
        if (null === $discountCode) {
            $command = ReserveTickets::withoutDiscountCode(
                new TicketReservationRequest($ticketType, $numberOfTickets)
            );
        } else {
            $command = ReserveTickets::withDiscountCode(
                $discountCode,
                new TicketReservationRequest($ticketType, $numberOfTickets)
            );
        }

        $this->commandBus->dispatch($command);

        /** @var TicketPurchaseCreated $event */
        $event = $this->eventCatcher->getEventsByType(TicketPurchaseCreated::class)[0];

        return $event->getId();
    }

    /**
     * @param $issueTicketType
     *
     * @throws \Exception
     *
     * @return TicketType
     */
    private function getTicketType($issueTicketType): TicketType
    {
        $ticketTypes = $this->config->getTicketTypes();

        if (!isset($ticketTypes[$issueTicketType])) {
            throw new \Exception('Invalid ticket type');
        }

        return $ticketTypes[$issueTicketType];
    }

    private function getDiscountCode($type) // : ?DiscountCode
    {
        if (empty($type)) {
            return null;
        }

        $discountCodes = $this->config->getDiscountCodes();

        if (!isset($discountCodes[$type])) {
            throw new \Exception('Invalid discount code');
        }

        return $discountCodes[$type];
    }
}
