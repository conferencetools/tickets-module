<?php


return [
    'router' => [
        'routes' => require __DIR__ . '/routes.config.php'
    ],
    
    'navigation' => [
        'default' => require __DIR__ . '/navigation.config.php'
    ],
    'service_manager' => [
    ],
    'command_handlers' => [
        'factories' => [
            \OpenTickets\Tickets\Domain\CommandHandler\Ticket::class => \OpenTickets\Tickets\Service\Factory\CommandHandler\Ticket::class
        ],
    ],
    'command_subscriptions' => [
        \OpenTickets\Tickets\Domain\Command\Ticket\ReserveTickets::class => \OpenTickets\Tickets\Domain\CommandHandler\Ticket::class,
        \OpenTickets\Tickets\Domain\Command\Ticket\AssignToDelegate::class => \OpenTickets\Tickets\Domain\CommandHandler\Ticket::class,
        \OpenTickets\Tickets\Domain\Command\Ticket\CompletePurchase::class => \OpenTickets\Tickets\Domain\CommandHandler\Ticket::class,
        \OpenTickets\Tickets\Domain\Command\Ticket\MakePayment::class => \OpenTickets\Tickets\Domain\CommandHandler\Ticket::class,
        \OpenTickets\Tickets\Domain\Command\Ticket\TimeoutPurchase::class => \OpenTickets\Tickets\Domain\CommandHandler\Ticket::class,
    ],
    'event_listeners' => [
        'factories' => [

        ]
    ],
    'projections' => [
        'factories' => [
            \OpenTickets\Tickets\Domain\Projection\TicketCounts::class => \OpenTickets\Tickets\Service\Factory\Projection\TableProjectionFactory::class,
            \OpenTickets\Tickets\Domain\Projection\TicketRecord::class => \OpenTickets\Tickets\Service\Factory\Projection\TableProjectionFactory::class,
        ]
    ],
    'domain_event_subscriptions' => [
        \OpenTickets\Tickets\Domain\Event\Ticket\TicketReserved::class => [
            \OpenTickets\Tickets\Domain\Projection\TicketCounts::class,
            \OpenTickets\Tickets\Domain\Projection\TicketRecord::class,
        ],
        \OpenTickets\Tickets\Domain\Event\Ticket\TicketReleased::class => [
            \OpenTickets\Tickets\Domain\Projection\TicketCounts::class,
            \OpenTickets\Tickets\Domain\Projection\TicketRecord::class,
        ],
        \OpenTickets\Tickets\Domain\Event\Ticket\TicketAssigned::class => \OpenTickets\Tickets\Domain\Projection\TicketRecord::class,
    ],
    'controllers' => [
        'factories' => [
            
        ],
    ],
    'view_manager' => [
        'controller_map' => [
            'OpenTickets\Tickets\Controller' => 'tickets',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'zfc_rbac' => [
        'guards' => [
            'ZfcRbac\Guard\RouteGuard' => [
                'admin/*' => ['admin']
            ]
        ]
    ],
    'doctrine' => [
        'driver' => [
           'opentickets_tickets_read_orm_driver' => [
                'class' =>'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Domain/ReadModel']
            ],
            'orm_default' => [
                'drivers' => [
                    'OpenTickets\Tickets\Domain\ReadModel' => 'opentickets_tickets_read_orm_driver'
                ]
            ],

        ],
    ],
];
