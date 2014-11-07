<?php

namespace Fing\Authentication\Adapter\Radius\AttributesTest;

use Fing\Authentication\Adapter\Radius\Attributes;

class AttributesTest extends \PHPUnit_Framework_TestCase
{
    protected $attributes;

    public function setUp()
    {
        $this->attributes = new Attributes();
    }

    public function testAttributeToStringArgument()
    {
        $reply_message = $this->attributes->attributeToString(18);
        $this->assertEquals($reply_message, "Reply-Message");
    }

    public function testInexixtentAttributeToStringArgument()
    {
        $not_found = $this->attributes->attributeToString(1000);
        $this->assertFalse($not_found);
    }
}
