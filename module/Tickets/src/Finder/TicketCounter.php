<?php


namespace ConferenceTools\Tickets\Finder;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use ConferenceTools\Tickets\Domain\Finder\TicketCounterInterface;
use ConferenceTools\Tickets\Domain\ReadModel\TicketCounts\TicketCounter as TicketCounterReadModel;

class TicketCounter implements TicketCounterInterface
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param \string[] ...$identifiers
     * @return TicketCounterReadModel[]
     */
    public function byTicketTypeIdentifiers(string ...$identifiers): Collection
    {
        $qb = $this->em->getRepository(TicketCounterReadModel::class)->createQueryBuilder('t', 't.ticketType.identifier');
        /** @var TicketCounter[] $tickets */
        return new ArrayCollection($qb->where($qb->expr()->in('t.ticketType.identifier', $identifiers))
            ->getQuery()->getResult());
    }
}