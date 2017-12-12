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

    /**
     * @var BasketValidator
     */
    private $basketValidator;

    public function __construct(
        GeneratorInterface $ticketIdGenerator,
        Configuration $configuration,
        BasketValidator $basketValidator
    ) {
        $this->ticketIdGenerator = $ticketIdGenerator;
        $this->configuration = $configuration;
        $this->basketValidator = $basketValidator;
    }

    public function basket(TicketReservationRequest ...$reservationRequests): Basket
    {
        $tickets = $this->createTicketReservations(...$reservationRequests);

        return Basket::fromReservations(
            $this->configuration,
            $this->basketValidator,
            ...$tickets
        );
    }

    public function basketWithDiscount(
        DiscountCode $discountCode,
        TicketReservationRequest ...$reservationRequests
    ): Basket {
        $tickets = $this->createTicketReservations(...$reservationRequests);

        return Basket::fromReservationsWithDiscount(
            $this->configuration,
            $this->basketValidator,
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

        return $tickets;
    }
}
