<?php

declare(strict_types=1);

namespace Genkgo\Push\Unit\WebPush;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\WebPush\Keys;

final class KeysTest extends AbstractTestCase
{
    public function testNewKeys(): void
    {
        $keys = Keys::newKeys();
        $this->assertStringStartsWith('-----BEGIN PRIVATE KEY-----', $keys['privateKey']);
        $this->assertStringStartsWith('-----BEGIN PUBLIC KEY-----', $keys['publicKey']);
    }

    public function testP256dhToPublicKey(): void
    {
        $keys = Keys::newKeys();

        $this->assertEquals(
            $keys['publicKey'],
            Keys::p256dhToPublicKey(
                Keys::convertPublicKeyToApplicationServerKey($keys['publicKey'])
            )
        );
    }
}
