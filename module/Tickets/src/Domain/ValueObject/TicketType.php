<?php

namespace OpenTickets\Tickets\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class TicketType
 * @package OpenTickets\Tickets\Domain\ValueObject
 * @ORM\Embeddable
 */
final class TicketType
{
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $identifier;

    /**
     * @var Money
     * @ORM\Embedded(class="OpenTickets\Tickets\Domain\ValueObject\Money")
     */
    private $price;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $displayName;

    /**
     * TicketType constructor.
     * @param $identifier
     * @param $price
     * @param $displayName
     */
    public function __construct(string $identifier, Money $price, string $displayName)
    {
        $this->identifier = $identifier;
        $this->price = $price;
        $this->displayName = $displayName;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return Money
     */
    public function getPrice(): Money
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string 
    {
        return $this->displayName;
    }
}