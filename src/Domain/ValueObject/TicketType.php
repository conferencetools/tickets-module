<?php

namespace ConferenceTools\Tickets\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Jms;

/**
 * Class TicketType
 * @package ConferenceTools\Tickets\Domain\ValueObject
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
     * @ORM\Embedded(class="ConferenceTools\Tickets\Domain\ValueObject\Price")
     * @Jms\Type("ConferenceTools\Tickets\Domain\ValueObject\Price")
     */
    private $price;

    /**
     * @var string
     * @Jms\Type("string")
     * @ORM\Column(type="string")
     */
    private $displayName;

    /**
     * @var string
     * @Jms\Type("string")
     * @ORM\Column(type="string")
     */
    private $description;

    public function __construct(string $identifier, Price $price, string $displayName, string $description = '')
    {
        $this->identifier = $identifier;
        $this->price = $price;
        $this->displayName = $displayName;
        $this->description = $description;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}