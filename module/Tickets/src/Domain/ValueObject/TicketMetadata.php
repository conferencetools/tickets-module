<?php

namespace OpenTickets\Tickets\Domain\ValueObject;

class TicketMetadata
{
    private $ticketType;
    private $availableFrom;
    private $availableTo;
    private $privateTicket;

    /**
     * TicketMetadata constructor.
     * @param TicketType $ticketType
     * @param \DateTime $availableFrom
     * @param \DateTime $availableTo
     * @param bool $privateTicket
     */
    public function __construct(
        TicketType $ticketType,
        \DateTime $availableFrom,
        \DateTime $availableTo,
        bool $privateTicket
    ) {
        $this->ticketType = $ticketType;
        $this->availableFrom = $availableFrom;
        $this->availableTo = $availableTo;
        $this->privateTicket = $privateTicket;
    }

    public static function createWithoutDates(TicketType $ticketType, $privateTicket): TicketMetadata
    {
        return new self(
            $ticketType,
            (new \DateTime())->sub(new \DateInterval('P1D')),
            (new \DateTime())->add(new \DateInterval('P1D')),
            $privateTicket
        );
    }

    /**
     * @return TicketType
     */
    public function getTicketType(): TicketType
    {
        return $this->ticketType;
    }

    /**
     * @return \DateTime
     */
    public function getAvailableFrom(): \DateTime
    {
        return $this->availableFrom;
    }

    /**
     * @return \DateTime
     */
    public function getAvailableTo(): \DateTime
    {
        return $this->availableTo;
    }

    public function isAvailableOn(\DateTime $date)
    {
        return ($this->availableFrom < $date && $date < $this->availableTo);
    }

    /**
     * @return boolean
     */
    public function isPrivateTicket(): bool
    {
        return $this->privateTicket;
    }
}