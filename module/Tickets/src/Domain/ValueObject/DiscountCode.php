<?php

namespace OpenTickets\Tickets\Domain\ValueObject;

use OpenTickets\Tickets\Domain\ValueObject\DiscountType\DiscountTypeInterface;
use JMS\Serializer\Annotation as Jms;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class DiscountCode
 * @package OpenTickets\Tickets\Domain\ValueObject
 * @ORM\Embeddable()
 */
class DiscountCode
{
    /**
     * @JMS\Type("string")
     * @ORM\Column(type="string")
     * @var string
     */
    private $code;

    /**
     * @JMS\Type("string")
     * @ORM\Column(type="string")
     * @var string
     */
    private $displayName;

    /**
     * @TODO when more than one type figure out how to handle this with jms
     * @JMS\Type("OpenTickets\Tickets\Domain\ValueObject\DiscountType\Percentage")
     * @ORM\Column(type="json_object")
     * @var DiscountTypeInterface
     */
    private $discountType;

    /**
     * DiscountCode constructor.
     * @param $code
     * @param $displayName
     * @param $discountType
     */
    public function __construct(string $code, string $displayName, DiscountTypeInterface $discountType)
    {
        $this->code = $code;
        $this->displayName = $displayName;
        $this->discountType = $discountType;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @return DiscountTypeInterface
     */
    public function getDiscountType(): DiscountTypeInterface
    {
        return $this->discountType;
    }

    /**
     * @param Basket $to
     * @return Price
     */
    public function apply(Basket $to): Price
    {
        return $this->discountType->apply($to);
    }
}