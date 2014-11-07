<?php

namespace FingTest\Authentication\Adapter;

use Fing\Authentication\Adapter;

/**
* @requires extension radius
*/
class RadiusTest extends \PHPUnit_Framework_TestCase
{
  /**
   * RADIUS connection options
   *
   * @var array
   */
    protected $options = array();

    /**
     * @requires extension radius
     */
    public function setUp()
    {
        if (!constant('TESTS_FING_AUTH_ADAPTER_RADIUS_ONLINE_ENABLED')) {
            $this->markTestSkipped('RADIUS online tests are not enabled');
        }
        
        $this->options = array(
            'servers' => array(
                array(
                    'hostname' => TESTS_FING_RADIUS_HOST,
                    'secret' => TESTS_FING_RADIUS_SECRET,
                ),
            )
        );
                        
        if (defined('TESTS_FING_RADIUS_REALM')) {
            $this->radius->setRealm(TESTS_FING_RADIUS_REALM);
        }

    }

    /**
     * @requires extension radius
     * @expectedException Zend\Authentication\Adapter\Exception\InvalidArgumentException
     */
    public function testInvalidArgument()
    {
        $bad_options = array(
            'bad_options' => array(
            )
        );

        $will_throw_exception = new Adapter\Radius($bad_options);
    }

    public function testSimpleAuth()
    {
        $adapter = new Adapter\Radius(
            array($this->options),
            TESTS_FING_RADIUS_USERNAME,
            TESTS_FING_RADIUS_PASSWORD
        );
        $result = $adapter->authenticate();
        $this->assertTrue($result instanceof Authentication\Result);
        $this->assertTrue($result->isValid());
        $this->assertTrue($result->getCode() == Authentication\Result::SUCCESS);
    }

    public function testInvalidPassAuth()
    {
        $adapter = new Adapter\Radius(
            array($this->options),
            TESTS_FING_RADIUS_USERNAME,
            'invalid'
        );
        $result = $adapter->authenticate();
        $this->assertTrue($result instanceof Authentication\Result);
        $this->assertTrue($result->isValid() === false);
        $this->assertTrue($result->getCode() == Authentication\Result::FAILURE_CREDENTIAL_INVALID);
    }

    public function testInvalidUserAuth()
    {
        $adapter = new Adapter\Radius(
            array($this->options),
            'invalid',
            'doesntmatter'
        );
        $result = $adapter->authenticate();
        $this->assertTrue($result instanceof Authentication\Result);
        $this->assertTrue($result->isValid() === false);
        $this->assertTrue(
            $result->getCode() == Authentication\Result::FAILURE_IDENTITY_NOT_FOUND ||
            $result->getCode() == Authentication\Result::FAILURE_CREDENTIAL_INVALID
        );
    }
}
