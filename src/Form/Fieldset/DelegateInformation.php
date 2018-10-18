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

namespace ConferenceTools\Tickets\Form\Fieldset;

use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class DelegateInformation extends Fieldset implements InputFilterProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->add([
            'type' => Text::class,
            'name' => 'firstname',
            'options' => [
                'label' => 'First name',
            ],
        ]);
        $this->add([
            'type' => Text::class,
            'name' => 'lastname',
            'options' => [
                'label' => 'Family name',
            ],
        ]);
        $this->add([
            'type' => Text::class,
            'name' => 'email',
            'options' => [
                'label' => 'Email',
                'help-block' => 'We\'ll add this email to our attendees mailing list to keep you up to date',
            ],
        ]);
        $this->add([
            'type' => Text::class,
            'name' => 'company',
            'options' => [
                'label' => 'Company',
            ],
        ]);
        $this->add([
            'type' => Text::class,
            'name' => 'twitter',
            'options' => [
                'label' => 'Twitter handle',
            ],
        ]);
        $this->add([
            'type' => Textarea::class,
            'name' => 'requirements',
            'options' => [
                'label' => 'Any Requirements',
                'help-block' => 'eg dietary needs, accessibility needs etc',
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
