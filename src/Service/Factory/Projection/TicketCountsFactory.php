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

namespace ConferenceTools\Tickets\Service\Factory\Projection;

use ConferenceTools\Tickets\Domain\Service\Configuration as TicketsConfiguration;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TicketCountsFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        $serviceLocator = $serviceLocator->getServiceLocator();

        return new $requestedName(
            $serviceLocator->get('doctrine.entitymanager.orm_default'),
            $serviceLocator->get(TicketsConfiguration::class)
        );
    }
}
