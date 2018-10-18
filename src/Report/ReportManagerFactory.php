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

namespace ConferenceTools\Tickets\Report;

use Zend\Mvc\Service\AbstractPluginManagerFactory;
use Zend\ServiceManager\Config as ServiceManagerConfig;
use Zend\ServiceManager\ServiceLocatorInterface;

class ReportManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = ReportManager::class;

    public function createService(ServiceLocatorInterface $serviceLocator, $canonicalName = null, $requestedName = null)
    {
        $service = parent::createService($serviceLocator);
        $config = $serviceLocator->get('Config');

        $pluginManagerConfig = $config['reports'];

        $config = new ServiceManagerConfig($pluginManagerConfig);
        $config->configureServiceManager($service);

        return $service;
    }
}
