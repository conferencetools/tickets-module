<?php

namespace OpenTickets\Tickets\View\Helper;

use OpenTickets\Tickets\Domain\Service\Configuration as OpenTicketsConfiguration;
use Zend\View\Helper\AbstractHelper;

class Configuration extends AbstractHelper
{
    private $config;

    /**
     * Configuration constructor.
     * @param OpenTicketsConfiguration $config
     */
    public function __construct(OpenTicketsConfiguration $config)
    {
        $this->config = $config;
    }

    public function __invoke()
    {
        return $this->config;
    }
}