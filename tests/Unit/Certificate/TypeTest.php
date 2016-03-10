<?php
namespace Genkgo\Push\Unit\Certificate;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Certificate\Apple\Type;
use InvalidArgumentException;

class TypeTest extends AbstractTestCase
{
    public function testValidConstructor()
    {
        $type = new Type(Type::DEVELOPMENT);
        $this->assertEquals('Development', $type->getHumanReadable());
        $this->assertEquals(Type::DEVELOPMENT, (string)$type);
    }

    public function testInvalidConstructor()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        new Type('test');
    }
}
