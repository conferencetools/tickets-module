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

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Jms;

/**
 * Class TicketType.
 *
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
    private $description = '';

    /**
     * @var bool
     * @Jms\Type("boolean")
     * @ORM\Column(type="boolean")
     */
    private $supplementary = false;

    public function __construct(
        string $identifier,
        Price $price,
        string $displayName,
        string $description = '',
        bool $supplementary = false
    ) {
        $this->identifier = $identifier;
        $this->price = $price;
        $this->displayName = $displayName;
        $this->description = $description;
        $this->supplementary = $supplementary;
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

    public function isSupplementary(): bool
    {
        return $this->supplementary;
    }
}
