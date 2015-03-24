# domain-context
This is a simple Laravel utility to facilitate mapping parts of a route hierarchy
to separate domains. 

As an example, let's say that your company has a site called coolmarketplace.com which lets other companies
sign up to list their products. In addition to any pages you provide to highlight interesting or popular products, 
each company also has its own page, identified by a short-code that they pick. For example, Golly Gee Widgets has
the page http://coolmarketplace.com/golly. 

However, they also want that mini-site to show up under their own domain name, http://market.gollygee.com, probably
with different styling, links, etc. You will need to have different application logic or presentation for the same
mini-site depending on whether it's accessed as coolmarketplace.com/golly or market.gollygee.com.

The utility creates a singleton DomainContext object that can be used to test whether the current request is
coming in via a domain that should be
mapped differently from the default and to get the identifier for the internal object that is associated with that domain.

## Instructions For Use

### Installation

Begin by installing this package through Composer.

    {
        "require": {
            "democracyapps/domain-context": "dev-master"
        }
    }

Add the service provider to app.php


    // app/config/app.php
    
    'providers' => [
        '...',
        'DemocracyApps\DomainContext\DomainContextServiceProvider',
    ];

For convenience, you may also add the Facade to app.php

    	'aliases' => [
            '...',
        'DomainContext' => 'DemocracyApps\DomainContext\DomainContextFacade',
    ];

### Configuration Parameters

Next, publish the configuration file by running

    php artisan vendor:publish

and edit 'config/domain-context.php'. There are currently four configuration settings in use in the package.

#### mapped_domain_storage (default: 'config')

You may specify domains that are to be mapped in either the configuration file or in a database 
table. This parameter determines which is to be used. By default, mapped domains are simply added to the
configuration file itself (see _mapped_domains_ parameter). If _mapped_domain_storage_ is set to 'database', then
it will look in the table specified by the _mapped_domain_table_name_ parameter.

#### mapped_domains (default: empty array)

If _mapped_domain_storage_ is set to 'config', then this array provides a list of the domains being mapped and the identifiers
associated with them. The identifiers may be of any type. In the example below, http://market.gollygee.com is a mapped domain and
is associated internally with a short code of 'golly'. A more common use might be to associate it with an ID of a database
model, as in the second line. 

    'mapped_domains' => [
        'market.gollygee.com' => 'golly',
        'stdexample.com' => 133
    ]

#### mapped_domain_table_name (default: 'mapped_domains')

If _mapped_domain_storage_ is set to 'database', information on mapped domains is searched in the table identified
by this parameter. It expects the table to have a  'domain_name' column and an 'identifier' column. It doesn't do
anything with them - simply returns them when requested (see below) and interprets a match of the current domain with
a table entry as indicating that the domain is mapped.

#### view_variable_name (default: 'domainContext')

The DomainContext object is automatically made available to all Laravel views via, by default, the $domainContext variable.
This parameter can be used to specify a different variable name.

### How To Use

Let's take the example at the top of this readme.

There are three common places where you'll actually implement variations in the logic or presentation in response to
the incoming domain: the _routes.php_ file,
controllers, and views. The logic is your own, of course. You just need an easy way to tell whether you're in a mapped
domain or not and, if so, what the associated client object should be.

The DomainContext object provides this via three methods: DomainContext::isMapped(), DomainContext::getDomain(), 
and DomainContext::getDomainIdentifier() (of course, you can also get the object via app()->make).

The first is a boolean that indicates whether the current domain is one that has been mapped. The second tells you what the 
domain is (in the example above, it would return 'market.gollygee.com'). The last returns the identifier to be used, e.g.,
to load client information from a table ('golly' in first example above). 

To use them in the routes file, the simplest thing to do is use the facade. For example:

    if (\DomainContext::isMapped()) {
        require app_path().'/Http/Routes/mapped.php'; // all the routes associated with mapped domains
    }
    else {
        require app_path().'/Http/Routes/market.php'; // all the routes associated with the platform
    }

The facade can, of course, also be used in a controller or anywhere else, however, you can also simply inject the DomainContext
object into your controller method, e.g.,

    public function show($id, DomainContext $context)
    {
        $short = $context->getDomainIdentifier();
        $tc = Company::where('short_name', '=', $short)->first();
        ...
            
Finally, within views, the DomainContext is always available as $domainContext (or the variable name you set in the 
configuration file). For example, you might have part of a menu depend on whether domain is mapped or not:

    <li class="dropdown">
        @if ($domainContext->isMapped())
            <a href="/products" class="dropdown-toggle" >All Products</a>
        @else
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Market</a>
            <ul class="dropdown-menu">
                <li><a href="/market/products">Products</a></li>
                <li><a href="/market/companies">Companies</a></li>
            </ul>
        @endif
    </li>

## Problems and Plans
 
This module is being used for a couple products in active development and will probably evolve. If you find bugs or have
requests for features, create an issue here, find me on Twitter (@ejaxon) or submit a pull request.
