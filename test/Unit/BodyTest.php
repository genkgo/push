<?php
declare(strict_types=1);

namespace Genkgo\Push\Unit;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Body;

final class BodyTest extends AbstractTestCase
{
    public function testToString(): void
    {
        $message = new Body('test');
        $this->assertEquals('test', (string)$message);
    }
}
