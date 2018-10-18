<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace ConferenceTools\Tickets\Form;

use Interop\Container\ContainerInterface;
use Zend\Form\FormElementManager\FormElementManagerV2Polyfill;
use Zend\Form\FormInterface;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

final class FormInputFilterDelegator implements DelegatorFactoryInterface
{
    public function __invoke(
        ContainerInterface $serviceLocator,
        string $name,
        callable $callback,
        array $options = null
    ) {
        /** @var FormInterface $form */
        $form = $callback();
        $inputFilterManager = $serviceLocator->get('InputFilterManager');
        if ($inputFilterManager->has(\get_class($form))) {
            /** @var InputFilter $inputFilter */
            $inputFilter = $form->getInputFilter();
            $inputFilter->merge($inputFilterManager->get(\get_class($form)));
            $form->setInputFilter($inputFilter);
        }

        return $form;
    }

    /**
     * @param FormElementManagerV2Polyfill|ServiceLocatorInterface $serviceLocator
     * @param string                                               $name
     * @param string                                               $requestedName
     * @param callable                                             $callback
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
}
