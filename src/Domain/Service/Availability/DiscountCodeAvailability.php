<?php

namespace ConferenceTools\Tickets\Domain\Service\Availability;

use Carnage\Cqrs\Persistence\ReadModel\RepositoryInterface;
use ConferenceTools\Tickets\Domain\Service\Configuration;
use ConferenceTools\Tickets\Domain\ValueObject\DiscountCode;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use ConferenceTools\Tickets\Domain\ReadModel\Counts\TicketCounter;
use ConferenceTools\Tickets\Domain\ValueObject\TicketType;
use ConferenceTools\Tickets\Domain\Service\Availability\Filters\FilterInterface;

class DiscountCodeAvailability
{
    /**
     * @var FilterInterface[]
     */
    private $filters;
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * TicketAvailability constructor.
     * @param RepositoryInterface $repository
     * @param FilterInterface[] $filters
     */
    public function __construct(RepositoryInterface $repository, FilterInterface ...$filters)
    {
        $this->filters = $filters;
        $this->repository = $repository;
    }

    /**
     * @return TicketCounter[]|Collection
     */
    public function fetchAllAvailableDiscountCodes()
    {
        $tickets = $this->repository->matching(new Criteria());

        return $this->reindex($this->filterSet($tickets));
    }

    private function filterSet(Collection $tickets): Collection
    {
        foreach ($this->filters as $filter) {
            /** @var FilterInterface $tickets */
            $tickets = $filter->filter($tickets);
        }

        return $tickets;
    }

    private function reindex(Collection $discountCodes): Collection
    {
        $result = [];
        foreach ($discountCodes as $discountCode) {
            /** @var DiscountCode $discountCode */
            $result[$discountCode->getCode()] = $discountCode;
        }

        return new ArrayCollection($result);
    }

    public function isAvailable(DiscountCode $discountCode)
    {
        $discountCodes = $this->fetchAllAvailableDiscountCodes();
        return isset($discountCodes[$discountCode->getCode()]);
    }
}