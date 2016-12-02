<?php

namespace OpenTickets\Tickets\Controller;

use Carnage\Cqrs\MessageBus\MessageBusInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManager;
use Zend\Form\FormElementManager;
use Zend\Mvc\Controller\AbstractActionController;

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
     * @var FormElementManager
     */
    private $formElementManager;

    /**
     * AbstractController constructor.
     * @param MessageBusInterface $commandBus
     * @param FormElementManager $formElementManager
     * @param EntityManager $entityManager
     */
    public function __construct(
        MessageBusInterface $commandBus,
        FormElementManager $formElementManager,
        EntityManager $entityManager
    ) {
        $this->commandBus = $commandBus;
        $this->entityManager = $entityManager;
        $this->formElementManager = $formElementManager;
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
     * @return FormElementManager
     */
    public function getFormElementManager()
    {
        return $this->formElementManager;
    }
}