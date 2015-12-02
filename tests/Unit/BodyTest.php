<?php
namespace Genkgo\Push\Unit;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Body;

class BodyTest extends AbstractTestCase
{
    public function testToString()
    {
        $message = new Body('test');
        $this->assertEquals('test', (string) $message);
    }
}
