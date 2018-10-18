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

namespace ConferenceTools\Tickets\EventListener;

use Zend\Mail\Transport\Factory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EmailPurchaseFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator->getServiceLocator();
        $config = $serviceLocator->get('Config');
        $transport = Factory::create($config['mail']);

        return new EmailPurchase(
            $serviceLocator->get('doctrine.entitymanager.orm_default'),
            $serviceLocator->get('Zend\View\View'),
            $transport,
            $config['conferencetools']['mailconf']['purchase'] ?? []
        );
    }
}
