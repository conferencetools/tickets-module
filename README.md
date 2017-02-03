# opentickets
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/carnage/opentickets/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/carnage/opentickets/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/carnage/opentickets/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/carnage/opentickets/?branch=master)

Open source ticketing system

This is a ticketing system MVP I built for PHP Yorkshire. It is unlikely you will be able to make use of it in it's 
current state, however the plan for the long run is to do for ticketing what opencfp has done for cfp's.

# Install (dev)

copy the .dist files in config to files without the .dist in the name; edit the config to your liking 
(Doctrine shouldn't need changing if you use the docker env), do a composer install (requires php 7) 
then run `make run` from the root directory. 

# Features

- Ticket purchasing
- Stripe integration for payments
- Multiple different ticket types (can be mixed in a single purchase)
- Email reciept
- Purchase timeout (30 mins)
- Issue free tickets
- Vat/sales tax handling
- Collect delegate information
- Customers can update delegate information (eg to reassign a ticket)
