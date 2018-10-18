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

namespace ConferenceTools\Tickets\Service\Factory\CommandHandler;

use Carnage\Cqrs\Aggregate\Identity\YouTubeStyleIdentityGenerator;
use Carnage\Cqrs\Persistence\Repository\PluginManager;
use ConferenceTools\Tickets\Domain\CommandHandler\Ticket as TicketCommandHandler;
use ConferenceTools\Tickets\Domain\Model\Ticket\TicketPurchase;
use ConferenceTools\Tickets\Domain\Service\Basket\Factory;
use ConferenceTools\Tickets\Domain\Service\Basket\ValidateBasket;
use ConferenceTools\Tickets\Domain\Service\Configuration;
use ConferenceTools\Tickets\Service\Identity\TicketIdentityGenerator;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Ticket implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();
        $repositoryManager = $mainServiceLocator->get(PluginManager::class);

        return new TicketCommandHandler(
            new YouTubeStyleIdentityGenerator(),
            $repositoryManager->get(TicketPurchase::class),
            new Factory(
                new TicketIdentityGenerator(),
                $mainServiceLocator->get(Configuration::class),
                new ValidateBasket()
            )
        );
    }
}
