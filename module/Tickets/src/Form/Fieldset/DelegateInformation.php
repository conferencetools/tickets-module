<?php

namespace OpenTickets\Tickets\Form\Fieldset;

use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class DelegateInformation extends Fieldset implements InputFilterProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function init()
    {
        $this->add([
            'type' => Text::class,
            'name' => 'firstname',
            'options' => [
                'label' => 'First name'
            ],
        ]);
        $this->add([
            'type' => Text::class,
            'name' => 'lastname',
            'options' => [
                'label' => 'Family name'
            ],
        ]);
        $this->add([
            'type' => Text::class,
            'name' => 'email',
            'options' => [
                'label' => 'Email',
                'help-block' => 'We\'ll add this email to our attendees mailing list to keep you up to date'
            ],
        ]);
        $this->add([
            'type' => Text::class,
            'name' => 'company',
            'options' => [
                'label' => 'Company'
            ],
        ]);
        $this->add([
            'type' => Text::class,
            'name' => 'twitter',
            'options' => [
                'label' => 'Twitter handle'
            ],
        ]);
        $this->add([
            'type' => Textarea::class,
            'name' => 'requirements',
            'options' => [
                'label' => 'Any Requirements',
                'help-block' => 'eg dietary needs, accessibility needs etc'
            ],
        ]);
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [];
    }
}