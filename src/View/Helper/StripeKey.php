<?php

namespace ConferenceTools\Tickets\View\Helper;

use Zend\View\Helper\AbstractHelper;

class StripeKey extends AbstractHelper
{
    private $publishableKey;

    /**
     * StripeKey constructor.
     * @param $publishableKey
     */
    public function __construct($publishableKey)
    {
        if (strpos($publishableKey, 'sk') === 0) {
            throw new \Exception('You appear to have set a secret key as your publishable key, please check config');
        }
        $this->publishableKey = $publishableKey;
    }

    public function __invoke()
    {
        return sprintf("Stripe.setPublishableKey('%s');", $this->publishableKey);
    }
}