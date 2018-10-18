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
