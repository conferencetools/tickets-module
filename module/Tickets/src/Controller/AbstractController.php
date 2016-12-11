<?php

namespace OpenTickets\Tickets\Controller;

use Carnage\Cqrs\MessageBus\MessageBusInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManager;
use Zend\Form\FormElementManager;
use Zend\Mvc\Controller\AbstractActionController;
use ZfrStripe\Client\StripeClient;

/**
 * Class AbstractController
 * @package HirePower\Common\Mvc\Controller
 * @method \Zend\Form\Form commandForm(string $commandClass, array $additionalData = [], array $defaults = [])
 * @method \Carnage\Cqrs\Mvc\Controller\Plugin\Events events()
 */
abstract class AbstractController extends AbstractActionController
{
    /**
     * @var MessageBusInterface
     */
    private $commandBus;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var StripeClient
     */
    private $stripeClient;

    /**
     * AbstractController constructor.
     * @param MessageBusInterface $commandBus
     * @param EntityManager $entityManager
     * @param StripeClient $stripeClient
     */
    public function __construct(
        MessageBusInterface $commandBus,
        EntityManager $entityManager,
        StripeClient $stripeClient
    ) {
        $this->commandBus = $commandBus;
        $this->entityManager = $entityManager;
        $this->stripeClient = $stripeClient;
    }

    /**
     * @return MessageBusInterface
     */
    public function getCommandBus()
    {
        return $this->commandBus;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @return StripeClient
     */
    public function getStripeClient(): StripeClient
    {
        return $this->stripeClient;
    }
}