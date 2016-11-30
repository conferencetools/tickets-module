<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace OpenTickets\Tickets;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        /*$eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $t = $e->getTarget();

        $t->getEventManager()->attach(
            $t->getServiceManager()->get('ZfcRbac\View\Strategy\RedirectStrategy')
        );*/
    }

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

}
