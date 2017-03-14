<?php


namespace OpenTickets\Tickets\Report;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DelegateInformationFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator->getServiceLocator();

        return new DelegateInformation($serviceLocator->get('doctrine.entitymanager.orm_default'));
    }
}