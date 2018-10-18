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
        return include __DIR__.'/../config/module.config.php';
    }
}
