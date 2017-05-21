<?php

declare(strict_types=1);

namespace OpenTickets\GoogleAnalytics\View\Helper;

use Zend\View\Helper\AbstractHelper;

final class GoogleAnalytics extends AbstractHelper
{
    /**
     * @var string
     */
    private $googleAnalyticsTrackingId;

    public function __construct(string $googleAnalyticsTrackingId)
    {
        $this->googleAnalyticsTrackingId = $googleAnalyticsTrackingId;
    }

    public function __invoke(): string
    {
        if ($this->googleAnalyticsTrackingId === '') {
            return '';
        }
        return $this->getView()->render('layout/google-analytics', [
            'trackingId' => $this->googleAnalyticsTrackingId,
        ]);
    }
}
