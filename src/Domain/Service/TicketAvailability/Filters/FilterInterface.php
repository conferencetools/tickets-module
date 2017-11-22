<?php
/**
 * Created by PhpStorm.
 * User: imhotek
 * Date: 15/11/17
 * Time: 16:34
 */

namespace ConferenceTools\Tickets\Domain\Service\TicketAvailability\Filters;


use Doctrine\Common\Collections\Collection;

interface FilterInterface
{
    public function filter(Collection $tickets): Collection;
}