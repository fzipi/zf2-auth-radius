<?php

namespace FingTest\Authentication\Adapter;

use Fing\Authentication\Adapter;

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

        $this->radius = new Adapter\Radius($options);
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

        $will_throw_exception = new Adapter\Radius($bad_options);
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

        $this->assertTrue($result->isValid());
    }

}
