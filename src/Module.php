<?php

namespace ConferenceTools\Tickets;

use Zend\ModuleManager\Feature\DependencyIndicatorInterface;

class Module implements DependencyIndicatorInterface
{
    public function getModuleDependencies()
    {
        return ['ConferenceTools\GoogleAnalytics'];
    }

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
