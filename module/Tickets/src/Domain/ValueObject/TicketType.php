<?php

namespace OpenTickets\Tickets\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Jms;

/**
 * Class TicketType
 * @package OpenTickets\Tickets\Domain\ValueObject
 * @ORM\Embeddable
 */
final class TicketType
{
    /**
     * @var string
     * @Jms\Type("string")
     * @ORM\Column(type="string")
     */
    private $identifier;

    /**
     * @var Money
     * @ORM\Embedded(class="OpenTickets\Tickets\Domain\ValueObject\Price")
     * @Jms\Type("OpenTickets\Tickets\Domain\ValueObject\Price")
     */
    private $price;

    /**
     * @var string
     * @Jms\Type("string")
     * @ORM\Column(type="string")
     */
    private $displayName;

    /**
     * TicketType constructor.
     * @param string $identifier
     * @param Price $price
     * @param string $displayName
     * @param TaxRate $taxRate
     */
    public function __construct(string $identifier, Price $price, string $displayName)
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
     * @return Price
     */
    public function getPrice(): Price
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