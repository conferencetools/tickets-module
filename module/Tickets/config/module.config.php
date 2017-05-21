<?php

return [
    'router' => [
        'routes' => require __DIR__ . '/routes.config.php',
    ],
    'navigation' => [
        'default' => require __DIR__ . '/navigation.config.php',
    ],
    'service_manager' => [
        'factories' => [
            \OpenTickets\Tickets\Domain\Service\Configuration::class => \OpenTickets\Tickets\Service\Factory\ConfigurationFactory::class,
            \OpenTickets\Tickets\Domain\Service\TicketAvailability\TicketAvailability::class => \OpenTickets\Tickets\Service\Factory\Service\TicketAvailability::class,
            \OpenTickets\Tickets\Report\ReportManager::class => \OpenTickets\Tickets\Report\ReportManagerFactory::class,
        ],
        'abstract_factories' => [
            \Zend\Log\LoggerAbstractServiceFactory::class,
        ],
    ],
    'cli_commands' => [
        'factories' => [
            \OpenTickets\Tickets\Cli\Command\TimeoutPurchases::class => \OpenTickets\Tickets\Cli\Command\TimeoutPurchasesFactory::class,
            \OpenTickets\Tickets\Cli\Command\IssueFreeTicket::class => \OpenTickets\Tickets\Cli\Command\IssueFreeTicketFactory::class,
            \OpenTickets\Tickets\Cli\Command\ReportToCsv::class => \OpenTickets\Tickets\Cli\Command\ReportToCsvFactory::class,
            \OpenTickets\Tickets\Cli\Command\CancelTicket::class => \OpenTickets\Tickets\Cli\Command\CancelTicketFactory::class,
        ],
    ],
    'command_handlers' => [
        'factories' => [
            \OpenTickets\Tickets\Domain\CommandHandler\Ticket::class => \OpenTickets\Tickets\Service\Factory\CommandHandler\Ticket::class,
        ],
    ],
    'process_managers' => [],
    'command_subscriptions' => [
        \OpenTickets\Tickets\Domain\Command\Ticket\ReserveTickets::class => \OpenTickets\Tickets\Domain\CommandHandler\Ticket::class,
        \OpenTickets\Tickets\Domain\Command\Ticket\AssignToDelegate::class => \OpenTickets\Tickets\Domain\CommandHandler\Ticket::class,
        \OpenTickets\Tickets\Domain\Command\Ticket\CompletePurchase::class => \OpenTickets\Tickets\Domain\CommandHandler\Ticket::class,
        \OpenTickets\Tickets\Domain\Command\Ticket\MakePayment::class => \OpenTickets\Tickets\Domain\CommandHandler\Ticket::class,
        \OpenTickets\Tickets\Domain\Command\Ticket\TimeoutPurchase::class => \OpenTickets\Tickets\Domain\CommandHandler\Ticket::class,
        \OpenTickets\Tickets\Domain\Command\Ticket\CancelTicket::class => \OpenTickets\Tickets\Domain\CommandHandler\Ticket::class,
    ],
    'event_listeners' => [
        'factories' => [
            \OpenTickets\Tickets\EventListener\EmailPurchase::class => \OpenTickets\Tickets\EventListener\EmailPurchaseFactory::class,
        ],
    ],
    'projections' => [
        'factories' => [
            \OpenTickets\Tickets\Domain\Projection\TicketCounts::class => \OpenTickets\Tickets\Service\Factory\Projection\TicketCountsFactory::class,
            \OpenTickets\Tickets\Domain\Projection\TicketRecord::class => \OpenTickets\Tickets\Service\Factory\Projection\TableProjectionFactory::class,
        ],
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
        \OpenTickets\Tickets\Domain\Event\Ticket\DiscountCodeApplied::class => [
            \OpenTickets\Tickets\Domain\Projection\TicketRecord::class,
        ],
        \OpenTickets\Tickets\Domain\Event\Ticket\TicketPurchaseTimedout::class => \OpenTickets\Tickets\Domain\Projection\TicketRecord::class,
        \OpenTickets\Tickets\Domain\Event\Ticket\TicketPurchaseTotalPriceCalculated::class => \OpenTickets\Tickets\Domain\Projection\TicketRecord::class,
        \OpenTickets\Tickets\Domain\Event\Ticket\TicketPurchaseCreated::class => \OpenTickets\Tickets\Domain\Projection\TicketRecord::class,
        \OpenTickets\Tickets\Domain\Event\Ticket\TicketCancelled::class => \OpenTickets\Tickets\Domain\Projection\TicketRecord::class,
    ],
    'controllers' => [
        'factories' => [
            \OpenTickets\Tickets\Controller\TicketController::class => \OpenTickets\Tickets\Service\Factory\ControllerFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            \OpenTickets\Tickets\Form\ManageTicket::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
        ],
    ],
    'input_filters' => [
        'abstract_factories' => [
            \Zend\InputFilter\InputFilterAbstractServiceFactory::class,
        ],
    ],
    'input_filter_specs' => [
        \OpenTickets\Tickets\Form\ManageTicket::class => include __DIR__ . '/input-filters/manage-ticket.config.php',
    ],
    'reports' => [
        'aliases' => [
            'delegate_information' => \OpenTickets\Tickets\Report\DelegateInformation::class,
            'delegate_requirements' => \OpenTickets\Tickets\Report\DelegateRequirements::class,
            'missing_delegate_information' => \OpenTickets\Tickets\Report\MissingDelegateInformation::class,
            'ticket_mailout' => \OpenTickets\Tickets\Report\TicketMailout::class,
            'ticket_sales' => \OpenTickets\Tickets\Report\TicketSales::class,
        ],
        'factories' => [
            \OpenTickets\Tickets\Report\DelegateInformation::class => \OpenTickets\Tickets\Report\ReportFactory::class,
            \OpenTickets\Tickets\Report\DelegateRequirements::class => \OpenTickets\Tickets\Report\ReportFactory::class,
            \OpenTickets\Tickets\Report\MissingDelegateInformation::class => \OpenTickets\Tickets\Report\ReportFactory::class,
            \OpenTickets\Tickets\Report\TicketMailout::class => \OpenTickets\Tickets\Report\ReportFactory::class,
            \OpenTickets\Tickets\Report\TicketSales::class => \OpenTickets\Tickets\Report\ReportFactory::class,
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'flashMessenger' => \OpenTickets\Tickets\View\Helper\FlashMessenger::class,
            'moneyFormat' => \OpenTickets\Tickets\View\Helper\MoneyFormat::class,
        ],
        'factories' => [
            'stripeKey' => \OpenTickets\Tickets\View\Helper\StripeKeyFactory::class,
            'openTicketsConfig' => \OpenTickets\Tickets\View\Helper\ConfigurationFactory::class,
            'serverUrl' => \OpenTickets\Tickets\View\Helper\ServerUrlFactory::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => false,
        'display_exceptions' => false,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => [
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
            'email/receipt' => __DIR__ . '/../view/email/receipt.phtml',
            'tickets/ticket/complete' => __DIR__ . '/../view/tickets/ticket/complete.phtml',
            'tickets/ticket/manage' => __DIR__ . '/../view/tickets/ticket/manage.phtml',
            'tickets/ticket/purchase' => __DIR__ . '/../view/tickets/ticket/purchase.phtml',
            'tickets/ticket/select-tickets' => __DIR__ . '/../view/tickets/ticket/select-tickets.phtml',
            'tickets/ticket/_orderInformation' => __DIR__ . '/../view/tickets/ticket/_orderInformation.phtml',
        ],
        'controller_map' => [
            'OpenTickets\Tickets\Controller' => 'tickets',
        ],
    ],
    'zfc_rbac' => [
        'guards' => [
            'ZfcRbac\Guard\RouteGuard' => [
                'admin/*' => ['admin'],
            ],
        ],
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
                    'OpenTickets\Tickets\Domain\ValueObject' => 'opentickets_tickets_read_orm_driver',
                ],
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
        ],
    ],
];
