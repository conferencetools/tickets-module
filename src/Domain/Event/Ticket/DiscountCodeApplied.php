<?php

namespace ConferenceTools\Tickets\Domain\Event\Ticket;

use Carnage\Cqrs\Event\EventInterface;
use ConferenceTools\Tickets\Domain\ValueObject\DiscountCode;
use JMS\Serializer\Annotation as Jms;

class DiscountCodeApplied implements EventInterface
{
    /**
     * @JMS\Type("string")
     * @var string
     */
    private $id;

    /**
     * @Jms\Type("ConferenceTools\Tickets\Domain\ValueObject\DiscountCode")
     * @var DiscountCode
     */
    private $discountCode;

    /**
     * DiscountCodeApplied constructor.
     * @param string $id
     * @param DiscountCode $discountCode
     */
    public function __construct(string $id, DiscountCode $discountCode)
    {
        $this->id = $id;
        $this->discountCode = $discountCode;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return DiscountCode
     */
    public function getDiscountCode(): DiscountCode
    {
        return $this->discountCode;
    }
}
