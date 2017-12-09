<?php

namespace ConferenceTools\Tickets\Domain\Service\Basket;

use Carnage\Cqrs\Aggregate\Identity\GeneratorInterface;
use ConferenceTools\Tickets\Domain\Service\Configuration;
use ConferenceTools\Tickets\Domain\ValueObject\Basket;
use ConferenceTools\Tickets\Domain\ValueObject\DiscountCode;
use ConferenceTools\Tickets\Domain\ValueObject\TicketReservation;
use ConferenceTools\Tickets\Domain\ValueObject\TicketReservationRequest;

class Factory
{
    /**
     * @var GeneratorInterface
     */
    private $ticketIdGenerator;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(GeneratorInterface $ticketIdGenerator, Configuration $configuration)
    {
        $this->ticketIdGenerator = $ticketIdGenerator;
        $this->configuration = $configuration;
    }

    public function basket(TicketReservationRequest ...$reservationRequests): Basket
    {
        $tickets = $this->createTicketReservations($reservationRequests);

        return Basket::fromReservations($this->configuration, ...$tickets);
    }

    public function basketWithDiscount(
        DiscountCode $discountCode,
        TicketReservationRequest ...$reservationRequests
    ): Basket {
        $tickets = $this->createTicketReservations($reservationRequests);

        return Basket::fromReservationsWithDiscount(
            $this->configuration,
            $discountCode,
            ...$tickets
        );
    }

    /**
     * @return TicketReservation[]
     */
    private function createTicketReservations(TicketReservationRequest ...$reservationRequests): array
    {
        $tickets = [];
        foreach ($reservationRequests as $reservationRequest) {
            for ($i = 0; $i < $reservationRequest->getQuantity(); $i++) {
                $tickets[] = new TicketReservation($reservationRequest->getTicketType(),
                    $this->ticketIdGenerator->generateIdentity());
            }
        }

        if (count($tickets) === 0) {
            throw new \RuntimeException('Must specify at least 1 ticket for purchase');
        }

        return $tickets;
    }
}
