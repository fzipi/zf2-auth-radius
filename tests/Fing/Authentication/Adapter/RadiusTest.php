<?php

namespace FingTest\Authentication\Adapter;

use Fing\Authentication\Adapter\Radius;
use Fing\Authentication\Adapter\Radius\Attributes;

class RadiusTest extends \PHPUnit_Framework_TestCase
{
    protected $radius;

    public function setUp()
    {
        $options = array(
            'servers' => array(
                array(
                    'hostname' => 'radius01.fing.edu.uy',
                    'secret' => 'picard',
                ),
                array(
                    'hostname' => 'radius02.fing.edu.uy',
                    'secret' => 'picard',
                )
            )
        );

        $this->radius = new Radius($options);
    }

    /**
     * //expectedException Zend\Authentication\Adapter\Exception\InvalidArgumentException
     */
    public function testInvalidArgument()
    {
        $bad_options = array(
            'bad_options' => array(
            )
        );

        $will_throw_exception = new Radius($bad_options);
    }

    public function testValidateResponseWithBadUser()
    {
        $this->radius->setUsername("test");
        $this->radius->setPassword("test");

        $result = $this->radius->authenticate();

        $this->assertFalse($result->isValid());
    }

    public function testValidateResponseWithValidUser()
    {
        $this->radius->setUsername("good");
        $this->radius->setRealm("realm");
        $this->radius->setPassword("password");

        $result = $this->radius->authenticate();

        // Stop here and mark this test as incomplete. you have to provide your
        // own user and password for it to work!
        $this->markTestIncomplete(
          'You have to change user and password for this test to work!'
        );

        $this->assertTrue($result->isValid());
    }

    public function testGetRadiusResponseAttributes()
    {
        $this->radius->setUsername("9555555");
        $this->radius->setRealm("fing");
        $this->radius->setPassword("eNsf1Ng");

        $result = $this->radius->authenticate();

        $response = $this->radius->getRadiusResponseAttributes();

        $attrib = new Attributes();

        $this->assertEquals($response["Reply-Message"], "My Reply Message");

        $this->assertTrue($result->isValid());
    }
}
