<?php
namespace Genkgo\Push\Unit\Certificate;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Certificate\Apple\PrivateKey;
use Genkgo\Push\Certificate\Apple\SigningRequest;

class SigningRequestTest extends AbstractTestCase
{
    public function test()
    {
        $request = new SigningRequest(
            new PrivateKey(),
            'name',
            'email@address.com'
        );

        $this->assertStringStartsWith('-----BEGIN CERTIFICATE REQUEST-----', (string) $request);
    }

    public function testIllegalCharacter()
    {
        $request = new SigningRequest(
            new PrivateKey(),
            'ëllegal',
            'email@address.com'
        );

        $this->assertStringStartsWith('-----BEGIN CERTIFICATE REQUEST-----', (string) $request);
    }
}
