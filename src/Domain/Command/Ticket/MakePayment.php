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

/**
 * Class MakePayment.
 */
class MakePayment implements CommandInterface
{
    /**
     * @var string
     */
    private $purchaseId;
    /**
     * @var string
     */
    private $purchaserEmail;

    /**
     * MakePayment constructor.
     *
     * @param string $purchaseId
     * @param string $purchaserEmail
     */
    public function __construct(string $purchaseId, string $purchaserEmail)
    {
        $this->purchaseId = $purchaseId;
        $this->purchaserEmail = $purchaserEmail;
    }

    /**
     * @return string
     */
    public function getPurchaseId(): string
    {
        return $this->purchaseId;
    }

    /**
     * @return string
     */
    public function getPurchaserEmail(): string
    {
        return $this->purchaserEmail;
    }
}
