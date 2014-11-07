<?php
/**
 * ZF2 Authentication Adapter Radius
 *
 * @category   Authentication
 * @package    Fing
 * @copyright  Copyright (c) 2014 fzipi
 * @license    http://opensource.org/licenses/MIT The MIT License
 * @author Felipe Weckx <felipe@weckx.net>
 * @author Felipe Zipitria <fzipi@fing.edu.uy>
 */

namespace Fing\Authentication\Adapter;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Adapter\AbstractAdapter;

use Zend\Authentication;
use Zend\Authentication\Adapter\Exception\InvalidArgumentException;
use Zend\Authentication\Adapter\Exception\RuntimeException;

/**
 * Adapter to perform authentication on RADIUS servers. Uses the PECL radius
 * extension.
 *
 * @author Felipe Zipitria <fzipi@fing.edu.uy>
 */
class Radius extends AbstractAdapter implements AdapterInterface
{
    /**
     * Maximum number of servers that can be configured
     */
    const MAX_SERVER_COUNT = 10;

    /**
     * Default RADIUS authentication TCP port
     */
    const DEFAULT_PORT = 1812;

    /**
     * Default timeout period
     */
    const DEFAULT_TIMEOUT = 15;

    /**
     * Default maximum authentication attempts
     */
    const DEFAULT_MAXTRIES = 1;

    /**
     * Radius handle
     * @var resource
     */
    protected $radius = null;

    /**
     * Username
     * @var string
     */
    protected $username = null;

    /**
     * Password
     * @var string
     */
    protected $password = null;

    /**
     * Realm
     * @var string
     */
    protected $realm = null;

    /**
     * Configuration options
     * @var array
     */
    protected $options = array();

    /**
     * Constructor
     *
     * @param  array     $servers  Array of arrays containing the servers to be used. {@see addServer()}
     * @param  string    $username The username of the account
     * @param  string    $password The password of the account
     * @throws Exception If the radius extension is not loaded or there is an error
     *                            calling radius_auth_open
     */
    public function __construct($options = array(), $username = null, $password = null)
    {
        if (!extension_loaded('radius')) {
            throw new RuntimeException('The radius extension is not loaded');
        }

        $this->radius = radius_auth_open();
        if (!$this->radius) {
            throw new RuntimeException('Error creating RADIUS handle');
        }

        $this->loadOptions($options);

        if (isset($username)) {
            $this->setUsername($username);
        }

        if (isset($password)) {
            $this->setPassword($password);
        }
    }

    /**
     * Returns the username of the account or NULL if it is not set
     * @return string|null
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Sets the username to authenticate
     * @var string $username
     * @return Orbini_Auth_Adapter_Radius Provides fluent interface
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Returns the identity. For compatibility with other adapters. Proxies to {@see getUsername()}
     * @return string
     */
    public function getIdentity()
    {
        return $this->getUsername();
    }

    /**
     * Sets the identity. For compatibility with other adapters. Proxies to {@see setUsername()}
     * @var string $identity
     * @return self
     */
    public function setIdentity($identity)
    {
        return $this->setUsername($identity);
    }

    /**
     * Return the password being used to authenticate
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Sets the password to authenticate
     * @var string $password
     * @return self
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returns the credential. For compatibility with other adapters. Proxies to {@see getPassword()}
     * @return string
     */
    public function getCredential()
    {
        return $this->getPassword();
    }

    /**
     * Sets the credential. For compatibility with other adapters. Proxies to {@see setPassword()}
     * @var string $credential
     * @return self
     */
    public function setCredential($credential)
    {
        return $this->setPassword($credential);
    }

    /**
     * Returns the Radius realm.
     * @return realm
     */
    public function getRealm()
    {
        return $this->realm;
    }

    /**
     * Sets the radius handle. This basically overrides all configuration made on the object
     * @var resource $radius
     * @return self
     */
    public function setRealm($realm)
    {
        $this->realm = $realm;

        return $this;
    }

    /**
     * Returns the radius handle. Can be used on the radius_* functions
     * @return resource
     */
    public function getRadius()
    {
        return $this->radius;
    }

    /**
     * Sets the radius handle. This basically overrides all configuration made on the object
     * @var resource $radius
     * @return self
     */
    public function setRadius($radius)
    {
        $this->radius = $radius;

        return $this;
    }

    /**
     * Return current options
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Adds a RADIUS server to try to authenticate. Up to 10 servers can be specified.
     * @param  string                      $hostname The hostname or IP address of the server.
     * @param  int                         $port     The port on which authentication is listening. Usually 1812.
     * @param  string                      $secret   The shared secret for the server host.
     * @param  integer                     $timeout  Timeout in seconds to wait for a server reply
     * @param  integer                     $maxTries Maximum number of repeated requests before giving up
     * @throws Zend_Auth_Adapter_Exception If the server cannot be added
     */
    public function addServer(
        $hostname,
        $port = self::DEFAULT_PORT,
        $secret = null,
        $timeout = self::DEFAULT_TIMEOUT,
        $maxTries = self::DEFAULT_MAXTRIES
    ) {
        if (count($this->options['servers']) == self::MAX_SERVER_COUNT) {
            throw new InvalidArgumentException('A maximum of ' . self::MAX_SERVER_COUNT . ' can be added.');
        }

        if (!radius_add_server($this->radius, $hostname, $port, $secret, $timeout, $maxTries)) {
            throw new InvalidArgumentException('Error adding RADIUS server: ' . radius_strerror($this->radius));
        }

        $this->options['servers'][] = array(
            'hostname' => $hostname,
            'port'     => $port,
            'secret'   => $secret,
            'timeout'  => $timeout,
            'maxTries' => $maxTries
        );

        return $this;
    }

     /**
     * Gets RADIUS Attributes from response
     * @param  integer                     $maxTries Maximum number of repeated requests before giving up
     * @return array                       associative array of attribute name and its value
     * @throws InvalidArgumentException    if attributes cannot be readed
     */
    public function getRadiusResponseAttributes()
    {
        $response = array();
        $attributes = new Radius\Attributes();
        
        while ($resa = radius_get_attr($this->radius)) {
            if (!is_array($resa)) {
                throw
                  new InvalidArgumentException('Error getting RADIUS attributes: ' . radius_strerror($this->radius));
            }

            $key = $resa['attr'];
            $value = $resa['data'];
            $response[$attributes->attributeToString($key)] = $value;
        }
        return $response;
    }

    /**
     * Authenticate the configured user
     *
     * @return Zend\Authentication\Result
     */
    public function authenticate()
    {
        //Create RADIUS request
        radius_create_request($this->radius, RADIUS_ACCESS_REQUEST);

        if ($this->getUsername()) {
            radius_put_attr($this->radius, RADIUS_USER_NAME, $this->getUsername() . $this->getAuthenticationRealm());
        }

        if ($this->getPassword()) {
            radius_put_attr($this->radius, RADIUS_USER_PASSWORD, $this->getPassword());
        }

        //Send
        $result = radius_send_request($this->radius);

        switch ($result) {
            case RADIUS_ACCESS_ACCEPT:
                return new Authentication\Result(Authentication\Result::SUCCESS, $this->getUsername());
            case RADIUS_ACCESS_REJECT:
                return new Authentication\Result(
                    Authentication\Result::FAILURE_CREDENTIAL_INVALID,
                    $this->getUsername(),
                    array(radius_strerror($this->radius))
                );
            default:
                var_dump($result); # don't do this!

                return new Authentication\Result(
                    Authentication\Result::FAILURE_UNCATEGORIZED,
                    $this->getUsername(),
                    array(radius_strerror($this->radius))
                );
        }
    }

    /**
     * Loads an array of options
     * @param  array $options The array of options in the format:
     *                        array(
     *                        'realm' => 'somerealm',
     *                        'servers' => array(
     *                        array(
     *                        'hostname' => '127.0.0.1',
     *                        'port' => 1812,
     *                        'secret' => 'mysecret',
     *                        'timeout' => 10,
     *                        'maxTries' => 2
     *                        )
     *                        ),
     *                        'attribs' => array(
     *                        RADIUS_CHAP_PASSWORD => pack('C', $ident),
     *                        RADIUS_CHAP_CHALLENGE => 'challenge'
     *                        )
     *                        )
     * @return void
     */
    protected function loadOptions(array $options)
    {
        $this->options = array(
            'realm'  => null,
            'servers' => array(),
            'attribs' => array()
        );

        if (isset($options['servers'])) {
            foreach ($options['servers'] as $server) {
                if (!is_array($server) || !isset($server['hostname'])) {
                    throw new InvalidArgumentException('Invalid format on servers configuration');
                }
                $port = isset($server['port']) ? $server['port'] : self::DEFAULT_PORT;
                $secret = isset($server['secret']) ? $server['secret'] : '';
                $timeout = isset($server['timeout']) ? $server['timeout'] : self::DEFAULT_TIMEOUT;
                $maxTries = isset($server['maxTries']) ? $server['maxTries'] : self::DEFAULT_MAXTRIES;

                $this->addServer($server['hostname'], $port, $secret, $timeout, $maxTries);
            }
        }

        if (isset($options['realm'])) {
            $this->setRealm($options['realm']);
        }

        if (isset($options['attribs']) && is_array($options['attribs'])) {
            $this->options['attribs'] = $options['attribs'];
        }
    }

    protected function getAuthenticationRealm()
    {
        return isset($this->realm) ? "@" . $this->realm : "";
    }
}
