<?php

namespace OpenTickets\Tickets\Domain\ValueObject;

class TicketMetadata
{
    private $ticketType;
    private $availableFrom;
    private $availableTo;

    /**
     * TicketMetadata constructor.
     * @param $ticketType
     * @param $availableFrom
     * @param $availableTo
     * @param $adminOnly
     */
    public function __construct(
        TicketType $ticketType,
        \DateTime $availableFrom,
        \DateTime $availableTo
    ) {
        $this->ticketType = $ticketType;
        $this->availableFrom = $availableFrom;
        $this->availableTo = $availableTo;
    }

    public static function createWithoutDates(TicketType $ticketType): TicketMetadata
    {
        return new self(
            $ticketType,
            (new \DateTime())->sub(new \DateInterval('P1D')),
            (new \DateTime())->add(new \DateInterval('P1D'))
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
}