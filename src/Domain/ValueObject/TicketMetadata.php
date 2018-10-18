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

namespace ConferenceTools\Tickets\Domain\ValueObject;

class TicketMetadata
{
    private $ticketType;
    private $availableFrom;
    private $availableTo;
    private $privateTicket;
    private $afterSoldOut;

    /**
     * TicketMetadata constructor.
     *
     * @param TicketType $ticketType
     * @param \DateTime  $availableFrom
     * @param \DateTime  $availableTo
     * @param bool       $privateTicket
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

    public static function fromArray(TicketType $ticketType, array $metadata)
    {
        $instance = new static(
            $ticketType,
            $metadata['availableFrom'] ?? (new \DateTime())->sub(new \DateInterval('P1D')),
            $metadata['availableTo'] ?? (new \DateTime())->add(new \DateInterval('P1D')),
            $metadata['private'] ?? false
        );

        $instance->afterSoldOut = $metadata['after'] ?? [];

        return $instance;
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
        return $this->availableFrom < $date && $date < $this->availableTo;
    }

    /**
     * @return bool
     */
    public function isPrivateTicket(): bool
    {
        return $this->privateTicket;
    }

    public function getAfterSoldOut(): array
    {
        return $this->afterSoldOut;
    }

    public function expiredOn(\DateTime $date)
    {
        return !($date < $this->availableTo);
    }
}
