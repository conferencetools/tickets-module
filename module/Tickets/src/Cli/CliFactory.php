<?php

namespace OpenTickets\Tickets\Cli;

use OpenTickets\Tickets\Cli\Command\IssueFreeTicket;
use OpenTickets\Tickets\Cli\Command\TimeoutPurchases;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CliFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $cli = new Application;
        $cli->setName('Open Tickets Command Line Interface');
        $cli->setVersion('1');
        $cli->setHelperSet(new HelperSet);
        $cli->setCatchExceptions(true);
        $cli->setAutoExit(false);

        $cli->add($serviceLocator->get(TimeoutPurchases::class));
        $cli->add($serviceLocator->get(IssueFreeTicket::class));

        return $cli;
    }
}