<?php

namespace FingTest\Authentication\Adapter;

use Fing\Authentication\Adapter\Radius;

/**
* @requires extension radius
*/
class RadiusTest extends \PHPUnit_Framework_TestCase
{
    protected $radius;

    /**
     * @requires extension radius
     */
    public function setUp()
    {
        $options = array(
            'servers' => array(
                array(
                    'hostname' => 'radius01.example.com',
                    'secret' => 'mysecret',
                ),
                array(
                    'hostname' => 'radius02.example.com',
                    'secret' => 'myothersecret',
                )
            )
        );

        $this->radius = new Radius($options);
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

        $will_throw_exception = new Radius($bad_options);
    }

    /**
     * @requires extension radius
     */
    public function testValidateResponseWithBadUser()
    {
        $this->radius->setUsername("test");
        $this->radius->setPassword("test");

        $result = $this->radius->authenticate();

        $this->assertFalse($result->isValid());
    }

    /**
     * @requires extension radius
     */
    public function testValidateResponseWithValidUser()
    {
        $this->radius->setUsername("good");
        $this->radius->setRealm("realm");
        $this->radius->setPassword("password");

        $result = $this->radius->authenticate();

        // Stop here and mark this test as incomplete. you have to provide your
        // own user and password for it to work!
        $this->markTestIncomplete('You have to change user and password for this test to work!');

        $this->assertTrue($result->isValid());
    }
}
