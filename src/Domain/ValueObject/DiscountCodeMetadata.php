<?php

namespace ConferenceTools\Tickets\Domain\ValueObject;

class DiscountCodeMetadata
{
    private $discountCode;
    private $availableFrom;
    private $availableTo;

    public function __construct(
        DiscountCode $discountCode,
        \DateTime $availableFrom,
        \DateTime $availableTo
    ) {
        $this->discountCode = $discountCode;
        $this->availableFrom = $availableFrom;
        $this->availableTo = $availableTo;
    }

    public static function fromArray(DiscountCode $discountCode, array $metadata)
    {
        $instance = new static(
            $discountCode,
            $metadata['availableFrom'] ?? (new \DateTime())->sub(new \DateInterval('P1D')),
            $metadata['availableTo'] ?? (new \DateTime())->add(new \DateInterval('P1D'))
        );

        return $instance;
    }

    /**
     * @return DiscountCode
     */
    public function getDiscountCode(): DiscountCode
    {
        return $this->discountCode;
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