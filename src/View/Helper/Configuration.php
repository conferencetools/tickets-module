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

namespace ConferenceTools\Tickets\View\Helper;

use ConferenceTools\Tickets\Domain\Service\Configuration as TicketsConfiguration;
use Zend\View\Helper\AbstractHelper;

class Configuration extends AbstractHelper
{
    private $config;

    /**
     * Configuration constructor.
     *
     * @param TicketsConfiguration $config
     */
    public function __construct(TicketsConfiguration $config)
    {
        $this->config = $config;
    }

    public function __invoke()
    {
        return $this->config;
    }
}
