<?php

declare(strict_types=1);

namespace OpenTickets\Tickets\Form;

use OpenTickets\Tickets\Form\Fieldset\DelegateInformation;
use Zend\Form\Element\Csrf;
use Zend\Form\Form;

final class ManageTicket extends Form
{
    public function __construct()
    {
        parent::__construct('manage-ticket-form');
    }

    public function init()
    {
        $this->add([
            'type' => DelegateInformation::class,
            'name' => 'delegate',
        ]);

        $this->add([
            'type' => Csrf::class,
            'name' => 'security',
        ]);
    }
}
