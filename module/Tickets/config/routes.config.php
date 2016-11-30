<?php

return [
    'root' => [
        'type'    => 'Literal',
        'options' => [
            'route'    => '/',
            'defaults' => [
                '__NAMESPACE__' => 'OpenTickets\Tickets\Controller',
                'controller'    => 'Index',
                'action'        => 'index',
            ],
        ],
        'may_terminate' => true,
        'child_routes' => [

        ],
    ],
];