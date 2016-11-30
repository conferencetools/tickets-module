<?php

namespace OpenTickets\Tickets\Service\Factory\CommandHandler;

use Carnage\Cqrs\Aggregate\Identity\YouTubeStyleIdentityGenerator;
use OpenTickets\Tickets\Domain\CommandHandler\Ticket as TicketCommandHandler;
use OpenTickets\Tickets\Domain\Model\Ticket\TicketPurchase;
use OpenTickets\Tickets\Service\Identity\TicketIdentityGenerator;
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
            new TicketIdentityGenerator(),
            $repositoryManager->get(TicketPurchase::class)
        );
    }
}