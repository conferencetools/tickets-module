<?php

namespace ConferenceTools\Tickets\Service\Factory\CommandHandler;

use Carnage\Cqrs\Aggregate\Identity\YouTubeStyleIdentityGenerator;
use ConferenceTools\Tickets\Domain\CommandHandler\Ticket as TicketCommandHandler;
use ConferenceTools\Tickets\Domain\Model\Ticket\TicketPurchase;
use ConferenceTools\Tickets\Domain\Service\Basket\Factory;
use ConferenceTools\Tickets\Domain\Service\Configuration;
use ConferenceTools\Tickets\Service\Identity\TicketIdentityGenerator;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Carnage\Cqrs\Persistence\Repository\PluginManager;

class Ticket implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();
        $repositoryManager = $mainServiceLocator->get(PluginManager::class);

        return new TicketCommandHandler(
            new YouTubeStyleIdentityGenerator(),
            $repositoryManager->get(TicketPurchase::class),
            new Factory(new TicketIdentityGenerator(), $mainServiceLocator->get(Configuration::class))
        );
    }
}