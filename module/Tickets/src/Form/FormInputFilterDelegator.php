<?php

declare(strict_types=1);

namespace OpenTickets\Tickets\Form;

use Zend\Form\FormElementManager\FormElementManagerV2Polyfill;

final class FormInputFilterDelegator
{
    public function __invoke(
        FormElementManagerV2Polyfill $serviceLocator,
        string $name,
        string $requestedName,
        callable $callback
    ) {
        /* @var $form \Zend\Form\FormInterface */
        $form = call_user_func($callback);
        $inputFilterManager = $serviceLocator->getServiceLocator()->get('InputFilterManager');
        if ($inputFilterManager->has(get_class($form))) {
            /* @var $inputFilter \Zend\InputFilter\InputFilter */
            $inputFilter = $form->getInputFilter();
            $inputFilter->merge($inputFilterManager->get(get_class($form)));
            $form->setInputFilter($inputFilter);
        }
        return $form;
    }
}
