<?php

namespace OpenTickets\Tickets\Form;

use OpenTickets\Tickets\Form\Fieldset\DelegateInformation;
use Zend\Form\Element\Csrf;
use Zend\Form\Form;

class ManageTicket extends Form
{
    public function __construct()
    {
        parent::__construct('manage-ticket-form');

        $this->add(['type' => DelegateInformation::class, 'name' => 'delegate']);

        $this->add(new Csrf('security'));
    }
}