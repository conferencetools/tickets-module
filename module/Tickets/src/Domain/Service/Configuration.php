<?php

namespace OpenTickets\Tickets\Domain\Service;

use OpenTickets\Tickets\Domain\ValueObject\DiscountCode;
use OpenTickets\Tickets\Domain\ValueObject\Money;
use OpenTickets\Tickets\Domain\ValueObject\Price;
use OpenTickets\Tickets\Domain\ValueObject\TaxRate;
use OpenTickets\Tickets\Domain\ValueObject\TicketType;
use Zend\Stdlib\ArrayUtils;

class Configuration
{
    //@TODO change to private const
    private static $defaults = [
        'tickets' => [],
        'discountCodes' => [],
        'financial' => [
            'currency' => 'GBP',
            'taxRate' => 0,
            'displayTax' => false
        ]
    ];

    /**
     * Defines the currency in use across the app.
     *
     * Defaults to GBP, should be a proper currency code otherwise you will have issues with display of
     * currency values and creating stripe charges.
     *
     * config key: financial->currency
     *
     * @var string
     */
    private $currency;

    /**
     * The tax rate in use across the app.
     *
     * Defaults to 0% Will be added to all cost values for tickets. The app assumes a single tax rate for all tickets
     * and also assumes that tickets are for a physical event. As such the EU VATMOSS rules do not apply. If you are
     * selling tickets for a webinar or online conference, you should check with legal advisers if this is appropriate.
     *
     * config key: financial->taxRate
     *
     * @var TaxRate
     */
    private $taxRate;

    /**
     * Should VAT/sales tax be displayed in the app.
     *
     * Defaults to false. If enabled, this will display sales tax (VAT) in various points in the purchase process. You
     * should also update the layout template to include the relevent legal information. There are three ways this app
     * can deal with tax:
     * - if you are not vat registered or do not need to charge VAT, you can set this to false and the tax rate to 0;
     *   the app will not track any tax for you.
     * - if you set a tax rate but disable this flag, VAT will be added to purchases and tracked by the app but not
     *   made visible to customers. The main purpose of this is for when you have a pending VAT registration; the app
     *   will still track the tax and you can turn on the display of this tracking when the registration completes.
     *   Another use for this, if you don't need to track VAT would be to add a handling/processing fee to tickets.
     * - If you set a tax rate and enable this flag, VAT will be tracked and displayed to your customers at purchase
     *   time.
     *
     *
     * config key: financial->displayTax
     *
     * @var bool
     */
    private $displayTax;

    /**
     * An array of available ticket types. The app uses this for determining how many tickets are available and their
     * prices.
     *
     * If you change this config, you will need to rebuild the ticket counters projection to get the updated types in
     * your app.
     *
     * config key: tickets
     * structure: identifier => [
     *      'cost' => Net cost in pence/cents (eg before tax price),
     *      'name' => display name shown to customers,
     *      'available' => Number available for purchase
     * ]
     *
     * @var TicketType[]
     */
    private $ticketTypes;

    /**
     * Holds a count of avaliable tickets by type.
     *
     * @see ticketTypes for how to configure
     *
     * @var int[]
     */
    private $avaliableTickets;

    /**
     * An array of discount codes. The app uses this for validating and applying different codes.
     *
     * configkey: discountCodes
     * structure: identifier => [
     *      'type' => The class name of the discount type eg Percentage::class,
     *      'name' => User friendly name for the code
     *      'options' => An array of options for the type you are using
     * ]
     *
     * @var DiscountCode[]
     */
    private $discountCodes;

    private function __construct() {}

    public static function fromArray(array $settings)
    {
        /** Ensures that all the keys exist @TODO remove dependency on Zend Array Utils for this */
        $settings = ArrayUtils::merge(self::$defaults, $settings);
        $instance = new static();

        $instance->currency = (string) $settings['financial']['currency'];
        $instance->displayTax = (string) $settings['financial']['displayTax'];
        $instance->taxRate = new TaxRate($settings['financial']['taxRate']);

        foreach ($settings['tickets'] as $identifier => $ticket) {
            $price = Price::fromNetCost(
                new Money($ticket['cost'], $instance->currency),
                $instance->taxRate
            );

            $instance->ticketTypes[$identifier] = new TicketType(
                $identifier,
                $price,
                $ticket['name']
            );

            $instance->avaliableTickets[$identifier] = $ticket['available'];
        }

        foreach ($settings['discountCodes'] as $identifier => $code) {
            $discountType = call_user_func([$code['type'], 'fromArray'], $code['options']);
            $instance->discountCodes[$identifier] = new DiscountCode(
                $identifier,
                $code['name'],
                $discountType
            );
        }

        return $instance;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return TaxRate
     */
    public function getTaxRate(): TaxRate
    {
        return $this->taxRate;
    }

    /**
     * @return boolean
     */
    public function displayTax(): bool
    {
        return $this->displayTax;
    }

    /**
     * @return TicketType[]
     */
    public function getTicketTypes(): array
    {
        return $this->ticketTypes;
    }

    /**
     * @return TicketType
     */
    public function getTicketType(string $identifier): TicketType
    {
        return $this->ticketTypes[$identifier];
    }

    /**
     * @param string $identifier
     * @return int
     */
    public function getAvaliableTickets(string $identifier): int
    {
        return $this->avaliableTickets[$identifier];
    }

    public function getDiscountCodes(): array
    {
        return $this->discountCodes;
    }
}