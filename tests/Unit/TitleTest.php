<?php
namespace Genkgo\Push\Unit;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Title;

class TitleTest extends AbstractTestCase
{
    public function testToString()
    {
        $message = new Title('test');
        $this->assertEquals('test', (string) $message);
    }
}
