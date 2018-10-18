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
use ConferenceTools\Tickets\Domain\ValueObject\Delegate;

/**
 * Class AssignToDelegate.
 */
class AssignToDelegate implements CommandInterface
{
    /**
     * @var Delegate
     */
    private $delegate;

    /**
     * @var string
     */
    private $ticketId;

    /**
     * @var string
     */
    private $purchaseId;

    /**
     * AssignToDelegate constructor.
     *
     * @param $delegate
     * @param $ticketId
     * @param $purchaseId
     */
    public function __construct(Delegate $delegate, string $ticketId, string $purchaseId)
    {
        $this->delegate = $delegate;
        $this->ticketId = $ticketId;
        $this->purchaseId = $purchaseId;
    }

    /**
     * @return Delegate
     */
    public function getDelegate(): Delegate
    {
        return $this->delegate;
    }

    /**
     * @return string
     */
    public function getTicketId(): string
    {
        return $this->ticketId;
    }

    /**
     * @return string
     */
    public function getPurchaseId(): string
    {
        return $this->purchaseId;
    }
}
