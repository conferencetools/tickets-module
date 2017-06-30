<?php


namespace ConferenceTools\Tickets\Cli\Command;


use ConferenceTools\Tickets\Report\ReportManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ReportToCsvFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator->getServiceLocator();
        return ReportToCsv::build($serviceLocator->get(ReportManager::class));
    }
}