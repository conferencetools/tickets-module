<?php
///////////test
namespace ConferenceTools\Tickets\View\Helper;

use ConferenceTools\Tickets\Domain\Service\Configuration as TicketsConfiguration;
use Zend\View\Helper\AbstractHelper;

class Configuration extends AbstractHelper
{
    private $config;

    /**
     * Configuration constructor.
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