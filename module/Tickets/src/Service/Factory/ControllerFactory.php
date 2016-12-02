<?php
namespace OpenTickets\Tickets\Service\Factory;

use Carnage\Cqrs\Command\CommandBusInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        $serviceLocator = $serviceLocator->getServiceLocator();
        return new $requestedName(
            $serviceLocator->get(CommandBusInterface::class),
            $serviceLocator->get('FormElementManager'),
            $serviceLocator->get('doctrine.entitymanager.orm_default')
        );
    }
}