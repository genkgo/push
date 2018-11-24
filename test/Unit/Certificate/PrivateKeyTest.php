<?php
declare(strict_types=1);

namespace Genkgo\Push\Unit\Certificate;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Certificate\Apple\PrivateKey;

final class PrivateKeyTest extends AbstractTestCase
{
    public function test()
    {
        $key = new PrivateKey();
        $this->assertTrue(\is_resource($key->asResource()));
        $this->assertStringStartsWith('-----BEGIN PRIVATE KEY-----', (string)$key);
        $this->assertSame((string)$key, (string)PrivateKey::fromString((string)$key));
    }
}
