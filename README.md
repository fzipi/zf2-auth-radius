# Radius authentication adapter for Zend Framework 2

[![Build Status](https://travis-ci.org/fzipi/zf2-radius-authentication-adapter.svg)](https://travis-ci.org/fzipi/zf2-radius-authentication-adapter)

Forked originally from MT4SoftwareStudio/orbini-auth-radius (for zf1). Added functionality for Radius realms.


## Requirements

* Zend Framework 2 Authentication framework
* PHP PECL Radius extension

## Installation

Add this repository to your `composer.json`:

```json
{
    "require": {
        "fzipi/zf2-radius-authentication-adapter": "~0.0"
    }
}
```

then `composer update`.

## Usage

```php
<?php

use Fing\Authentication\Adapter\Radius as RadiusAdapter;
use Zend\Authentication;

$servers = array(
			'realm' => "myrealm",
            'servers' => array(
                array(
                    'hostname' => 'radius01.example.com',
					'port' => 1812,
                    'secret' => '<verysecretstringforthisserver>',
                ),
                array(
                    'hostname' => 'radius02.example.com',
					'port' => 18120, // not default port
                    'secret' => '<anotherverysecretstring>',
					'timeout' => 10,
					'maxTries' => 2
                )
            )
        );

//Create our adapter passing one server (up to 10 can be passed)
$adapter =  new RadiusAdapter($options, $username, $password);

//Authenticate
$result = $adapter->authenticate();

//Using Radius REALMS
$adapter->setRealm("routers"); // if not set in options config previously

$access = $adapter->authenticate()

/** Plugged in to Zend\Authentication **/

// Assuming we're still using the $adapter constructed above:

$authService = new Authentication\AuthenticationService(
    new Authentication\Storage\NonPersistent(),
    $adapter
);

$result = $authService->authenticate();

```
## Troubleshooting

Did you remember to set your RADIUS secret accordingly?
