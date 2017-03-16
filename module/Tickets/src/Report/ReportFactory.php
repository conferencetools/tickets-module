<?php


namespace OpenTickets\Tickets\Report;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ReportFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator, $cName = null, $rName = null)
    {
        $serviceLocator = $serviceLocator->getServiceLocator();

        return new $rName($serviceLocator->get('doctrine.entitymanager.orm_default'));
    }
}