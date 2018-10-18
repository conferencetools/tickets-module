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

namespace ConferenceTools\Tickets\Domain\Event\Ticket;

use Carnage\Cqrs\Event\EventInterface;
use JMS\Serializer\Annotation as Jms;

class TicketPurchasePaid implements EventInterface
{
    /**
     * @Jms\Type("string")
     *
     * @var string
     */
    private $id;

    /**
     * @Jms\Type("string")
     *
     * @var string
     */
    private $purchaserEmail;

    /**
     * TicketPurchasePaid constructor.
     *
     * @param string $id
     * @param string $purchaserEmail
     */
    public function __construct(string $id, string $purchaserEmail)
    {
        $this->id = $id;
        $this->purchaserEmail = $purchaserEmail;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPurchaserEmail(): string
    {
        return $this->purchaserEmail;
    }
}
