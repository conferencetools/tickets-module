<?php

namespace ConferenceTools\Tickets\Domain\ValueObject\DiscountType;

use ConferenceTools\Tickets\Domain\Service\Configuration;
use ConferenceTools\Tickets\Domain\ValueObject\Basket;
use ConferenceTools\Tickets\Domain\ValueObject\Money;
use ConferenceTools\Tickets\Domain\ValueObject\Price;
use ConferenceTools\Tickets\Domain\ValueObject\TicketType;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as Jms;

class RestrictedToTicketType implements DiscountTypeInterface
{
    /**
     * @JMS\Type("Object")
     * @var DiscountTypeInterface
     */
    private $discountType;

    /**
     * @JMS\Type("array<ConferenceTools\Tickets\Domain\ValueObject\TicketType>")
     * @var TicketType[]
     */
    private $ticketTypes;

    /**
     * Percentage constructor.
     * @param $discount
     */
    public function __construct(DiscountTypeInterface $discount, TicketType ...$ticketTypes)
    {
        $this->discountType = $discount;
        $this->ticketTypes = $ticketTypes;
    }

    public function apply(Basket $to): Price
    {
        try {
            return $this->discountType->apply($to->containingOnly(...$this->ticketTypes));
        } catch (\InvalidArgumentException $e) {
            return Price::fromNetCost(
                new Money(0, $to->getPreDiscountTotal()->getNet()->getCurrency()),
                $to->getPreDiscountTotal()->getTaxRate()
            );
        }
    }

    public function getDiscountType(): DiscountTypeInterface
    {
        return $this->discountType;
    }

    public static function fromArray(array $data, Configuration $configuration): DiscountTypeInterface
    {
        $delegated = call_user_func([$data['discountType'], 'fromArray'], $data['options'], $configuration);
        $ticketTypes = [];

        foreach ((array) $data['allowedTicketTypes'] as $ticketType) {
            $ticketTypes[] = $configuration->getTicketType($ticketType);
        }

        return new static($delegated, ...$ticketTypes);
    }
}
