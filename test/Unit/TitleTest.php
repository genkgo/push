<?php
declare(strict_types=1);

namespace Genkgo\Push\Unit;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Title;

final class TitleTest extends AbstractTestCase
{
    public function testToString(): void
    {
        $message = new Title('test');
        $this->assertEquals('test', (string)$message);
    }
}
