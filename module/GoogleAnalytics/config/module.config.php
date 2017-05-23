<?php

use OpenTickets\GoogleAnalytics\View\Helper\GoogleAnalyticsFactory;

return [
    'view_helpers' => [
        'factories' => [
            'googleAnalytics' => GoogleAnalyticsFactory::class,
        ],
    ],
    'view_manager' => [
        'template_map' => [
            'layout/google-analytics' => __DIR__ . '/../view/layout/google-analytics.phtml',
        ],
    ],
    'google_analytics' => [
        'tracking_id' => '',
    ],
];
