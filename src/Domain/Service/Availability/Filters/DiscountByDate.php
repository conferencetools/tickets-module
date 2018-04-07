<?php
/**
 * Created by PhpStorm.
 * User: imhotek
 * Date: 15/11/17
 * Time: 16:57
 */

namespace ConferenceTools\Tickets\Domain\Service\Availability\Filters;

use ConferenceTools\Tickets\Domain\ReadModel\Counts\TicketCounter;
use ConferenceTools\Tickets\Domain\Service\Configuration;
use ConferenceTools\Tickets\Domain\ValueObject\DiscountCode;
use Doctrine\Common\Collections\Collection;

/**
 * @TODO refactor this to use a single filter
 */
class DiscountByDate implements FilterInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function filter(Collection $tickets): Collection
    {
        $configuration = $this->configuration;
        $today = new \DateTime();

        $p = function(DiscountCode $discountCode) use ($configuration, $today) {
            $metadata = $configuration->getDiscountCodeMetadata($discountCode->getCode());
            return $metadata->isAvailableOn($today);
        };

        return $tickets->filter($p);
    }
}