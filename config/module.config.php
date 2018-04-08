<?php

return [
    'router' => [
        'routes' => require __DIR__ . '/routes.config.php',
    ],
    'navigation' => [
        'default' => require __DIR__ . '/navigation.config.php',
    ],
    'asset_manager' => require __DIR__ . '/asset.config.php',
    'service_manager' => [
        'factories' => [
            \ConferenceTools\Tickets\Domain\Service\Configuration::class => \ConferenceTools\Tickets\Service\Factory\ConfigurationFactory::class,
            \ConferenceTools\Tickets\Domain\Service\Availability\TicketAvailability::class => \ConferenceTools\Tickets\Service\Factory\Service\TicketAvailability::class,
            \ConferenceTools\Tickets\Domain\Service\Availability\DiscountCodeAvailability::class => \ConferenceTools\Tickets\Service\Factory\Service\DiscountCodeAvailability::class,
            \ConferenceTools\Tickets\Report\ReportManager::class => \ConferenceTools\Tickets\Report\ReportManagerFactory::class,
        ],
        'abstract_factories' => [
            \Zend\Log\LoggerAbstractServiceFactory::class,
        ],
    ],
    'cli_commands' => [
        'factories' => [
            \ConferenceTools\Tickets\Cli\Command\TimeoutPurchases::class => \ConferenceTools\Tickets\Cli\Command\TimeoutPurchasesFactory::class,
            \ConferenceTools\Tickets\Cli\Command\IssueFreeTicket::class => \ConferenceTools\Tickets\Cli\Command\IssueFreeTicketFactory::class,
            \ConferenceTools\Tickets\Cli\Command\ReportToCsv::class => \ConferenceTools\Tickets\Cli\Command\ReportToCsvFactory::class,
            \ConferenceTools\Tickets\Cli\Command\CancelTicket::class => \ConferenceTools\Tickets\Cli\Command\CancelTicketFactory::class,
            \ConferenceTools\Tickets\Cli\Command\MarkPurchasePaid::class => \ConferenceTools\Tickets\Cli\Command\MarkPurchasePaidFactory::class,
        ],
    ],
    'command_handlers' => [
        'factories' => [
            \ConferenceTools\Tickets\Domain\CommandHandler\Ticket::class => \ConferenceTools\Tickets\Service\Factory\CommandHandler\Ticket::class,
        ],
    ],
    'process_managers' => [],
    'command_subscriptions' => [
        \ConferenceTools\Tickets\Domain\Command\Ticket\ReserveTickets::class => \ConferenceTools\Tickets\Domain\CommandHandler\Ticket::class,
        \ConferenceTools\Tickets\Domain\Command\Ticket\AssignToDelegate::class => \ConferenceTools\Tickets\Domain\CommandHandler\Ticket::class,
        \ConferenceTools\Tickets\Domain\Command\Ticket\CompletePurchase::class => \ConferenceTools\Tickets\Domain\CommandHandler\Ticket::class,
        \ConferenceTools\Tickets\Domain\Command\Ticket\MakePayment::class => \ConferenceTools\Tickets\Domain\CommandHandler\Ticket::class,
        \ConferenceTools\Tickets\Domain\Command\Ticket\TimeoutPurchase::class => \ConferenceTools\Tickets\Domain\CommandHandler\Ticket::class,
        \ConferenceTools\Tickets\Domain\Command\Ticket\CancelTicket::class => \ConferenceTools\Tickets\Domain\CommandHandler\Ticket::class,
    ],
    'event_listeners' => [
        'factories' => [
            \ConferenceTools\Tickets\EventListener\EmailPurchase::class => \ConferenceTools\Tickets\EventListener\EmailPurchaseFactory::class,
        ],
    ],
    'projections' => [
        'factories' => [
            \ConferenceTools\Tickets\Domain\Projection\TicketCounts::class => \ConferenceTools\Tickets\Service\Factory\Projection\TicketCountsFactory::class,
            \ConferenceTools\Tickets\Domain\Projection\TicketRecord::class => \ConferenceTools\Tickets\Service\Factory\Projection\TableProjectionFactory::class,
        ],
    ],
    'domain_event_subscriptions' => [
        \ConferenceTools\Tickets\Domain\Event\Ticket\TicketReserved::class => [
            \ConferenceTools\Tickets\Domain\Projection\TicketCounts::class,
            \ConferenceTools\Tickets\Domain\Projection\TicketRecord::class,
        ],
        \ConferenceTools\Tickets\Domain\Event\Ticket\TicketReleased::class => [
            \ConferenceTools\Tickets\Domain\Projection\TicketCounts::class,
            \ConferenceTools\Tickets\Domain\Projection\TicketRecord::class,
        ],
        \ConferenceTools\Tickets\Domain\Event\Ticket\TicketAssigned::class => [
            \ConferenceTools\Tickets\Domain\Projection\TicketRecord::class,
        ],
        \ConferenceTools\Tickets\Domain\Event\Ticket\TicketPurchasePaid::class => [
            \ConferenceTools\Tickets\Domain\Projection\TicketRecord::class,
            \ConferenceTools\Tickets\EventListener\EmailPurchase::class,
        ],
        \ConferenceTools\Tickets\Domain\Event\Ticket\DiscountCodeApplied::class => [
            \ConferenceTools\Tickets\Domain\Projection\TicketRecord::class,
        ],
        \ConferenceTools\Tickets\Domain\Event\Ticket\TicketPurchaseTimedout::class => [
            \ConferenceTools\Tickets\Domain\Projection\TicketRecord::class,
        ],
        \ConferenceTools\Tickets\Domain\Event\Ticket\TicketPurchaseTotalPriceCalculated::class => [
            \ConferenceTools\Tickets\Domain\Projection\TicketRecord::class,
        ],
        \ConferenceTools\Tickets\Domain\Event\Ticket\TicketPurchaseCreated::class => [
            \ConferenceTools\Tickets\Domain\Projection\TicketRecord::class,
        ],
        \ConferenceTools\Tickets\Domain\Event\Ticket\TicketCancelled::class => [
            \ConferenceTools\Tickets\Domain\Projection\TicketRecord::class,
        ],
    ],
    'controllers' => [
        'factories' => [
            \ConferenceTools\Tickets\Controller\TicketController::class => \ConferenceTools\Tickets\Service\Factory\ControllerFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            \ConferenceTools\Tickets\Form\ManageTicket::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
        ],
    ],
    'input_filters' => [
        'abstract_factories' => [
            \Zend\InputFilter\InputFilterAbstractServiceFactory::class,
        ],
    ],
    'input_filter_specs' => [
        \ConferenceTools\Tickets\Form\ManageTicket::class => include __DIR__ . '/input-filters/manage-ticket.config.php',
    ],
    'reports' => [
        'aliases' => [
            'delegate_information' => \ConferenceTools\Tickets\Report\DelegateInformation::class,
            'delegate_requirements' => \ConferenceTools\Tickets\Report\DelegateRequirements::class,
            'missing_delegate_information' => \ConferenceTools\Tickets\Report\MissingDelegateInformation::class,
            'ticket_mailout' => \ConferenceTools\Tickets\Report\TicketMailout::class,
            'ticket_sales' => \ConferenceTools\Tickets\Report\TicketSales::class,
        ],
        'factories' => [
            \ConferenceTools\Tickets\Report\DelegateInformation::class => \ConferenceTools\Tickets\Report\ReportFactory::class,
            \ConferenceTools\Tickets\Report\DelegateRequirements::class => \ConferenceTools\Tickets\Report\ReportFactory::class,
            \ConferenceTools\Tickets\Report\MissingDelegateInformation::class => \ConferenceTools\Tickets\Report\ReportFactory::class,
            \ConferenceTools\Tickets\Report\TicketMailout::class => \ConferenceTools\Tickets\Report\ReportFactory::class,
            \ConferenceTools\Tickets\Report\TicketSales::class => \ConferenceTools\Tickets\Report\ReportFactory::class,
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'flashMessenger' => \ConferenceTools\Tickets\View\Helper\FlashMessenger::class,
            'moneyFormat' => \ConferenceTools\Tickets\View\Helper\MoneyFormat::class,
        ],
        'factories' => [
            'stripeKey' => \ConferenceTools\Tickets\View\Helper\StripeKeyFactory::class,
            'ticketsConfig' => \ConferenceTools\Tickets\View\Helper\ConfigurationFactory::class,
            'serverUrl' => \ConferenceTools\Tickets\View\Helper\ServerUrlFactory::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => false,
        'display_exceptions' => false,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => [
            'tickets/layout' => __DIR__ . '/../view/layout/layout.phtml',
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
            'ConferenceTools\Tickets\Controller' => 'tickets',
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
            'conferencetools_tickets_read_orm_driver' => [
                'class' =>'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Domain/ReadModel']
            ],
            'orm_default' => [
                'drivers' => [
                    'ConferenceTools\Tickets\Domain\ReadModel' => 'conferencetools_tickets_read_orm_driver',
                    'ConferenceTools\Tickets\Domain\ValueObject' => 'conferencetools_tickets_read_orm_driver',
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
