<?php

return [
    'root' => [
        'type'    => 'Segment',
        'options' => [
            'route'    => '/',
            'defaults' => [
                'controller'    => \OpenTickets\Tickets\Controller\TicketController::class,
                'action'        => 'index',
            ],
        ],
        'may_terminate' => true,
        'child_routes' => [
            'setup' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => 'setup',
                    'defaults' => [
                        'controller'    => \OpenTickets\Tickets\Controller\TicketController::class,
                        'action'        => 'setup',
                    ],
                ],
            ],
            'select-tickets' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => 'select-tickets',
                    'defaults' => [
                        'controller'    => \OpenTickets\Tickets\Controller\TicketController::class,
                        'action'        => 'select-tickets',
                    ],
                ],
            ],
            'purchase' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => 'purchase/:purchaseId',
                    'defaults' => [
                        'controller'    => \OpenTickets\Tickets\Controller\TicketController::class,
                        'action'        => 'purchase',
                    ],
                ],
            ],
            'complete' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => 'complete/:purchaseId',
                    'defaults' => [
                        'controller'    => \OpenTickets\Tickets\Controller\TicketController::class,
                        'action'        => 'complete',
                    ],
                ],
            ],
            'manage' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => 'manage/:purchaseId/:ticketId',
                    'defaults' => [
                        'controller'    => \OpenTickets\Tickets\Controller\TicketController::class,
                        'action'        => 'manage',
                    ],
                ],
            ]
        ],
    ],
];