<?php

namespace ConferenceTools\Tickets\View\Helper;

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
        $websiteConfig = $config['conferencetools']['website'];
        if (Console::isConsole() && isset($websiteConfig['host'])) {
            $serverUrlHelper
                ->setHost($websiteConfig['host'])
                ->setScheme($websiteConfig['scheme']);

            if (isset($websiteConfig['port'])) {
                $serverUrlHelper->setPort($websiteConfig['port']);
            }

        }

        return $serverUrlHelper;
    }
}
