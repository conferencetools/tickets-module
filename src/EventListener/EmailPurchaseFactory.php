<?php

namespace ConferenceTools\Tickets\EventListener;

use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EmailPurchaseFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator->getServiceLocator();
        $config = $serviceLocator->get('Config');
        $options = new SmtpOptions($config['mail']);
        $transport = new Smtp($options);

        return new EmailPurchase(
            $serviceLocator->get('doctrine.entitymanager.orm_default'),
            $serviceLocator->get('Zend\View\View'),
            $transport,
            $config['conferencetools']['mailconf']['purchase'] ?? []
        );
    }
}
