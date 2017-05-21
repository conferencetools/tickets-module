<?php

declare(strict_types=1);

namespace OpenTickets\GoogleAnalytics\View\Helper;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorInterface;

final class GoogleAnalyticsFactory
{
    public function __invoke(AbstractPluginManager $pluginManager)
    {
        $trackingId = $this->getTrackingId($pluginManager->getServiceLocator());

        return new GoogleAnalytics($trackingId);
    }

    private function getTrackingId(ServiceLocatorInterface $serviceLocator): string
    {
        if (!$serviceLocator->has('config')) {
            return '';
        }
        $config = $serviceLocator->get('Config');
        if (!isset($config['google_analytics']['tracking_id']) || !is_string($config['google_analytics']['tracking_id'])) {
            return '';
        }
        return $config['google_analytics']['tracking_id'];
    }
}
