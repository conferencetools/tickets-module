<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace ConferenceTools\Tickets\View\Helper;

use Zend\View\Helper\AbstractHelper;

class StripeKey extends AbstractHelper
{
    private $publishableKey;

    /**
     * StripeKey constructor.
     *
     * @param $publishableKey
     */
    public function __construct($publishableKey)
    {
        if (0 === strpos($publishableKey, 'sk')) {
            throw new \Exception('You appear to have set a secret key as your publishable key, please check config');
        }
        $this->publishableKey = $publishableKey;
    }

    public function __invoke()
    {
        return sprintf("Stripe.setPublishableKey('%s');", $this->publishableKey);
    }
}
