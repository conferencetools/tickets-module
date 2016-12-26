<?php


return [
    'router' => [
        'routes' => require __DIR__ . '/routes.config.php'
    ],
    
    'navigation' => [
        'default' => require __DIR__ . '/navigation.config.php'
    ],
    'service_manager' => [
        'factories' => [
            'opentickets.cli' => \OpenTickets\Tickets\Cli\CliFactory::class,
            \OpenTickets\Tickets\Cli\Command\TimeoutPurchases::class => \OpenTickets\Tickets\Cli\Command\TimeoutPurchasesFactory::class
        ],
        'abstract_factories' => [
            \Zend\Log\LoggerAbstractServiceFactory::class
        ]
    ],
    'command_handlers' => [
        'factories' => [
            \OpenTickets\Tickets\Domain\CommandHandler\Ticket::class => \OpenTickets\Tickets\Service\Factory\CommandHandler\Ticket::class
        ],
    ],
    'process_managers' => [],
    'command_subscriptions' => [
        \OpenTickets\Tickets\Domain\Command\Ticket\ReserveTickets::class => \OpenTickets\Tickets\Domain\CommandHandler\Ticket::class,
        \OpenTickets\Tickets\Domain\Command\Ticket\AssignToDelegate::class => \OpenTickets\Tickets\Domain\CommandHandler\Ticket::class,
        \OpenTickets\Tickets\Domain\Command\Ticket\CompletePurchase::class => \OpenTickets\Tickets\Domain\CommandHandler\Ticket::class,
        \OpenTickets\Tickets\Domain\Command\Ticket\MakePayment::class => \OpenTickets\Tickets\Domain\CommandHandler\Ticket::class,
        \OpenTickets\Tickets\Domain\Command\Ticket\TimeoutPurchase::class => \OpenTickets\Tickets\Domain\CommandHandler\Ticket::class,
    ],
    'event_listeners' => [
        'factories' => [
            \OpenTickets\Tickets\EventListener\EmailPurchase::class => \OpenTickets\Tickets\EventListener\EmailPurchaseFactory::class
        ]
    ],
    'projections' => [
        'factories' => [
            \OpenTickets\Tickets\Domain\Projection\TicketCounts::class => \OpenTickets\Tickets\Service\Factory\Projection\TicketCountsFactory::class,
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
        \OpenTickets\Tickets\Domain\Event\Ticket\TicketPurchasePaid::class => [
            \OpenTickets\Tickets\Domain\Projection\TicketRecord::class,
            \OpenTickets\Tickets\EventListener\EmailPurchase::class,
        ],
        \OpenTickets\Tickets\Domain\Event\Ticket\TicketPurchaseTimedout::class => \OpenTickets\Tickets\Domain\Projection\TicketRecord::class,
        \OpenTickets\Tickets\Domain\Event\Ticket\TicketPurchaseTotalPriceCalculated::class => \OpenTickets\Tickets\Domain\Projection\TicketRecord::class,
        \OpenTickets\Tickets\Domain\Event\Ticket\TicketPurchaseCreated::class => \OpenTickets\Tickets\Domain\Projection\TicketRecord::class,

    ],
    'controllers' => [
        'factories' => [
            \OpenTickets\Tickets\Controller\TicketController::class => \OpenTickets\Tickets\Service\Factory\ControllerFactory::class
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'flashMessenger' => \OpenTickets\Tickets\View\Helper\FlashMessenger::class,
            'moneyFormat' => \OpenTickets\Tickets\View\Helper\MoneyFormat::class,
        ],
        'factories' => [
            'stripeKey' => \OpenTickets\Tickets\View\Helper\StripeKeyFactory::class
        ]
    ],
    'view_manager' => [
        'display_not_found_reason' => false,
        'display_exceptions'       => false,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
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
                    'OpenTickets\Tickets\Domain\ReadModel' => 'opentickets_tickets_read_orm_driver',
                    'OpenTickets\Tickets\Domain\ValueObject' => 'opentickets_tickets_read_orm_driver'
                ]
            ],

        ],
    ],
    'log' => [
        'Log\\Application' => [
            'writers' => [
                [
                    'name' => 'syslog',
                ],
            ],
        ],
        'Log\\CommandBusLog'  => [
            'writers' => [
                [
                    'name' => 'syslog',
                ],
            ],
        ],
        'Log\\EventManagerLog'  => [
            'writers' => [
                [
                    'name' => 'syslog',
                ],
            ],
        ],
    ],

    'message_handlers' => [
        'CommandHandlerManager' => [
            'logger' => 'Log\\Application',
        ],
        'ProjectionManager' => [
            'logger' => 'Log\\Application',
        ],
        'EventListenerManager' => [
            'logger' => 'Log\\Application',
        ],
        'EventSubscriberManager' => [
            'logger' => 'Log\\Application',
        ]
    ],
];
