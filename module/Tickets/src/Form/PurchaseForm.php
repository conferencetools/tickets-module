<?php

namespace OpenTickets\Tickets\Form;

use OpenTickets\Tickets\Domain\ValueObject\Delegate;
use OpenTickets\Tickets\Form\Fieldset\DelegateInformation;
use OpenTickets\Tickets\Hydrator\DelegateHydrator;
use Zend\Form\Element\Collection;
use Zend\Form\Element\Csrf;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\Validator\EmailAddress;
use Zend\Validator\NotEmpty;

class PurchaseForm extends Form
{
    public function __construct($tickets)
    {
        parent::__construct('delegate-form');
        $this->add(['type' => Hidden::class, 'name' => 'stripe_token']);
        $this->add([
            'type' => Text::class,
            'name' => 'purchase_email',
            'options' => [
                'label' => 'Email',
                'help-block' => 'Your receipt will be emailed to this address'
            ]
        ]);

        for ($i = 0; $i < $tickets; $i++ ) {
            $this->add(['type' => DelegateInformation::class, 'name' => 'delegates_' . $i]);
        }

        $this->add(new Csrf('security'));

        $this->getInputFilter()
            ->get('purchase_email')
            ->setAllowEmpty(false)
            ->setRequired(true)
            ->getValidatorChain()
            ->attach(new NotEmpty())
            ->attach(new EmailAddress());

    }
}