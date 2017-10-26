<?php

declare(strict_types=1);

namespace ConferenceTools\Tickets\Form;

use Interop\Container\ContainerInterface;
use Zend\Form\FormElementManager\FormElementManagerV2Polyfill;
use Zend\Form\FormInterface;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

final class FormInputFilterDelegator implements DelegatorFactoryInterface
{
    /**
     * @param ServiceLocatorInterface|FormElementManagerV2Polyfill $serviceLocator
     * @param string $name
     * @param string $requestedName
     * @param callable $callback
     *
     * @return FormInterface
     */
    public function createDelegatorWithName(
        ServiceLocatorInterface $serviceLocator,
        $name,
        $requestedName,
        $callback
    ) {
        return $this($serviceLocator->getServiceLocator(), $requestedName, $callback);
    }


    public function __invoke(
        ContainerInterface $serviceLocator,
        string $name,
        callable $callback,
        array $options = null
    ) {
        /* @var FormInterface $form */
        $form = $callback();
        $inputFilterManager = $serviceLocator->get('InputFilterManager');
        if ($inputFilterManager->has(get_class($form))) {
            /* @var InputFilter $inputFilter */
            $inputFilter = $form->getInputFilter();
            $inputFilter->merge($inputFilterManager->get(get_class($form)));
            $form->setInputFilter($inputFilter);
        }
        return $form;
    }
}
