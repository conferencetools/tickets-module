<?php

namespace OpenTickets\Tickets\View\Helper;

use Zend\Console\Console;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\ServerUrl;

class ServerUrlFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator->getServiceLocator();
        $config = $serviceLocator->get('Config');

        $serverUrlHelper = new ServerUrl();
        if (Console::isConsole() && isset($config['website']['host'])) {
            $serverUrlHelper
                ->setHost($config['website']['host'])
                ->setScheme($config['website']['scheme']);

            if (isset($config['website']['port'])) {
                $serverUrlHelper->setPort($config['website']['port']);
            }

        }

        return $serverUrlHelper;
    }
}
