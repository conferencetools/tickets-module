<?php

namespace OpenTickets\Tickets\View\Helper;

use OpenTickets\Tickets\Domain\ValueObject\Money;
use Zend\View\Helper\AbstractHelper;

class MoneyFormat extends AbstractHelper
{
    /**
     * @param Money $money
     * @return string
     */
    public function __invoke(Money $money)
    {
        $currencyFormat = $this->getView()->plugin('currencyFormat');

        return $currencyFormat($money->getAmount() / 100, $money->getCurrency());
    }
}
