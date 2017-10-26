<?php

namespace ConferenceTools\Tickets\Service\Factory\Projection;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TableProjectionFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return new $requestedName(
            $serviceLocator->getServiceLocator()->get('doctrine.entitymanager.orm_default')
        );
    }
}