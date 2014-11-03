zf2-auth-radius
===============

Zend Framework 2 adapter to authenticate on RADIUS servers

Installation
------------

To install use composer

Usage
-----

Simply instantiate the ZF_Auth_Adapter_Radius class specifying the desired servers and pass it to Zend_Auth:

```php
//Create our adapter passing one server (up to 10 can be passed)
$adapter = new ZF2_Auth_Adapter_Radius(
    array('servers' => array(
        array(
            'hostname' => 'localhost',
            'port'     => 1812,
            'secret'   => 'mysecret',
            'timeout'  => 15,
            'maxTries' => 1
        )
    )),
    $username,
    $password
);

//Get Zend_Auth Singleton instance
$auth = Zend_Auth::getInstance()

//Authenticate
$result = $auth->authenticate($adapter);
```



