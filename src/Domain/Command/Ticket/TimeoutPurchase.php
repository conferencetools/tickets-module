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

namespace ConferenceTools\Tickets\Domain\Command\Ticket;

use Carnage\Cqrs\Command\CommandInterface;

class TimeoutPurchase implements CommandInterface
{
    /**
     * @var string
     */
    private $purchaseId;

    /**
     * TimeoutPurchase constructor.
     *
     * @param $purchaseId
     */
    public function __construct(string $purchaseId)
    {
        $this->purchaseId = $purchaseId;
    }

    /**
     * @return string
     */
    public function getPurchaseId(): string
    {
        return $this->purchaseId;
    }
}
