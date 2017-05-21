# Configuration

Example configuration files for all the following can be found in the `module\Tickets\example-config`
directory. You should copy them to the `config\autoload` directory of the main Zend application and 
configure as below.

## Database

To configure the database make a copy of `doctrine.local.php.dist` as 
`doctrine.local.php` If you are using the provided docker environment, you 
don't need to modify this file, the settings will be populated automatically for you. For a 
production setup, you will need to provide the appropriate settings.
 
## Stripe

To collect payments you will need to enter stripe details. For development ensure you use the 
test credentials provided by stripe. Make a copy of `zfr_stripe.local.php.dist`
as `zfr_stripe.local.php` and enter your secret and publishable keys.

## Email

To configure the database make a copy of `mail.local.php.dist` as 
`mail.local.php` Configure your SMTP settings under the mail array key, the
details under the website key are used to ensure that the correct urls are put in emails 
sent out by the app. 

### Email customisation

To customise the email subject and from addresses for purchase receipts you can use the `mailconf`
configuration key. Each email sent by the app has it's own subkey in this array. The following keys
are valid:

- `purchase` For the email sent out when a purchase is completed

You can use the following sub keys in each array to configure the email properties

#### Subject

Set the `subject` key to customise the subject for the email. Every email will have it's own default
which is used if this key is missed.

#### From

Set the `from` key to set the email address emails are sent from. It is highly recommended that you set
this key to help the email be recognised by spam filters as legitimate email.

## Google Analytics

To configure Google Analytics make a copy of `module/GoogleAnalytics/config/google-analytics.local.php` as 
`config/autoload/google-analytics.local.php` and set your own Google Analytics tracking ID.

By default, the tracking ID is an empty key, which disables the Google Analytics integration.

When overriding the `layout.phtml` view with your own do no forget to call the view helper
(`<?= $this->googleAnalytics() ?>`) in the `<head>` section.

## Open tickets

The final config file to look at is `opentickets.local.php.dist` copy this 
as `opentickets.local.php` This file contains all the settings for open 
tickets itself. It is divided into three keys

### Financial

This key contains all the financial settings for the app controlling the handling of VAT and
the currency used by the app.

#### Currency

Defaults to GBP, should be a proper currency code otherwise you will have issues with 
display of currency values and creating stripe charges.

#### Tax Rate

Defaults to 0% Will be added to all cost values for tickets. The app assumes a single 
tax rate for all tickets and also assumes that tickets are for a physical event. As such 
the EU VATMOSS rules do not apply. If you are selling tickets for a webinar or online 
conference, you should check with legal advisers if this is appropriate.

#### Display Tax

Should VAT/sales tax be displayed in the app.

Defaults to false. If enabled, this will display sales tax (VAT) in various points in 
the purchase process. You should also update the layout template to include the relevant 
legal information such as VAT number. There are three ways this app can deal with tax:

- If you are not vat registered or do not need to charge VAT, you can set this to false 
and the tax rate to 0; the app will not track any tax for you.
- if you set a tax rate but disable this flag, VAT will be added to purchases and tracked 
by the app but not made visible to customers. The main purpose of this is for when you have 
a pending VAT registration; the app will still track the tax and you can turn on the display 
of this tracking when the registration completes.
Another use for this, if you don't need to track VAT would be to add a percentage based 
handling/processing fee to tickets, in this case you should update the template to indicate 
that it is charging a handling fee, not a tax.
- If you set a tax rate and enable this flag, VAT will be tracked and displayed to your 
customers at purchase time.

Regardless of this setting; the rate set in tax rate will be added to all purchases and 
charged to the customer. Set tax rate to 0 if you do not want to add a charge.
 
### Tickets

The tickets configuration lists all the types of tickets you have available and their metadata,
the array keys in this configuration should not be changed once your app is running in production
as they are used to track ticket types in the app. You can add new tickets or edit the 
configuration of existing tickets at any point. Changing the config for an existing ticket will 
not affect tickets which have already been purchased so it is recommended that you add additional
types instead of altering them if you want to change the name or prices of existing tickets. 

If you make any changes to this array, you will need to rebuild the ticket counters projection 
in order to see them in the public app. 

#### Name

This is the friendly name for the ticket type (the array key is used as the system name) and is 
what will be displayed to customers.

#### Cost

Cost is the net price in the lowest denomination of your currency. (Eg if you are using GBP, 
state the price in pence) This is the ticket price without any added VAT  

#### Available

The number of tickets initially available. As customers purchase tickets the number will count 
down, once it reaches 0 the ticket will not be shown in the public app. This limit is ignored 
by the admin tools for issuing tickets, however using this tool will consume available tickets.

Setting this to 0 has the effect of creating an admin only ticket which can only be issued by
using the open tickets cli.

#### Metadata

Metadata is a new feature which allows for more fine grained control over which tickets are 
shown as being available to the general public. This is an area under active development and
new options will be added over time. Currently the supported options are.

##### Available From

The `availableFrom` key can be set to a PHP DateTime object representing the data from which a ticket 
becomes available. Leaving it blank will not restrict the date a ticket can be purchased from.

Currently, if you set this field you also need to set the Available To field 

To make it available indefinitely while using the available to field you can use the value 
`(new \DateTime())->sub(new \DateInterval('P1D'))`

##### Available To

The `availableTo` key can be set to a PHP DateTime object representing the data from which a ticket 
ceases to be available. Leaving it blank will not restrict the date a ticket can be purchased
on.

Currently, if you set this field you also need to set the Available From field 

To make it available indefinitely while using the available from field you can use the value 
`(new \DateTime())->add(new \DateInterval('P1D'))`

##### Private

The `private` key can be set to hide a ticket type from the user purchase page. The web UI will
disallow purchases of this ticket type. This is intended to be used for Free ticket types which
you might need eg Sponsor tickets, Speaker tickets or Prize give aways.

### Discount codes

The discount codes configuration array lists all the discount codes available in the app. The
array key acts as a system identifier for the code and shouldn't be changed, it is also the
code customers can type into the discount code box or append to the url to activate the code.

Unlike the ticket config, changes to this array take effect immediately.

Each code has the following config available
 
#### Name

The name of the discount code: this is displayed to customers in the application and in emails

#### Type

The type of discount code, currently there are three types available:

##### Percentage 

This type applies a percentage discount to the entire order.

Set to `\OpenTickets\Tickets\Domain\ValueObject\DiscountType\Percentage::class`

##### Fixed

This applies a fixed discount to the whole order, regardless of how many tickets are purchased.

Set to `\OpenTickets\Tickets\Domain\ValueObject\DiscountType\Fixed::class`

##### Fixed Per Ticket

This applies a fixed discount to each ticket a customer purchases.

Set to `\OpenTickets\Tickets\Domain\ValueObject\DiscountType\FixedPerTicket::class`

#### Options

The options key is used to configure the discount type; each type has different options available.

##### Percentage

This option is available only for the Percentage discount type and represents the discount to apply
a value of 10 applies a 10% discount to the basket

##### Gross

This option is available for both Fixed price and fixed price per ticket discount types. It represents
the gross amount to take off the ticket price in the lowest denomination of your currency. If a 
ticket costs £120 including VAT, a value of 1000 will result in the customer being charged £110 in
total.

##### Net

This option is available for both Fixed price and fixed price per ticket discount types. It represents
the net amount to take off the ticket price in the lowest denomination of your currency. If a 
ticket costs £120 including VAT, a value of 1000 will result in the customer being charged £108 in
total.

To clarify the last two options: Gross applies a discount to the price after tax, Net applies it
before tax, resulting in a larger overall discount. 

## Advanced config

To override other default config in the app you can create a file `config/autoload.local.php` add 
configuration to it. Take a look at the config files inside the module directory for default config 
you can override.

### Overriding views

@TODO

### Overriding routes

You can change the base uri for the app with the following piece of config.

`'router' => ['routes' => ['root' => ['options' => ['route' => '/tickets/' ] ] ] ]`

This example would prefix all application urls with `/tickets/`

### Overriding assets

! Warning: This area is under development and some of the required configutation may change
in subsequent releases!

To override individual assets, add a `map` key to the asset manager eg

`'asset_manager' => ['resolver_configs' => ['map' => ['css/card.css' => 'my.new.css'] ] ]`


