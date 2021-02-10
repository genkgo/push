<?php
declare(strict_types=1);

namespace Genkgo\Push\Unit\Apn;

use Apple\ApnPush\Protocol\Http\Request;
use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Apn\JwtAuthenticator;

final class JwtAuthenticatorTest extends AbstractTestCase
{
    public function testCreateToken(): void
    {
        $request = new Request('https://test', 'test');
        $authenticator = new JwtAuthenticator(__DIR__ . '/../../Stubs/test.p8', 'AB1234', 'Q12345');
        $authRequest = $authenticator->authenticate($request)->getHeaders()['Authorization'];

        $this->assertNotEquals('Bearer ', $authRequest);
    }

    public function testDefaultSameToken(): void
    {
        $request = new Request('https://test', 'test');
        $authenticator = new JwtAuthenticator(__DIR__ . '/../../Stubs/test.p8', 'AB1234', 'Q12345');
        $authRequest1 = $authenticator->authenticate($request)->getHeaders()['Authorization'];
        \sleep(1);
        $authRequest2 = $authenticator->authenticate($request)->getHeaders()['Authorization'];

        $this->assertNotEquals('Bearer ', $authRequest1);
        $this->assertNotEquals('Bearer ', $authRequest2);
        $this->assertEquals($authRequest1, $authRequest2);
    }

    public function testPreventTooManyProviderTokenUpdates(): void
    {
        $request = new Request('https://test', 'test');
        $authenticator = new JwtAuthenticator(__DIR__ . '/../../Stubs/test.p8', 'AB1234', 'Q12345', 'PT1S');
        $authRequest1 = $authenticator->authenticate($request)->getHeaders()['Authorization'];
        \sleep(2);
        $authRequest2 = $authenticator->authenticate($request)->getHeaders()['Authorization'];

        $this->assertNotEquals('Bearer ', $authRequest1);
        $this->assertNotEquals('Bearer ', $authRequest2);
        $this->assertNotEquals($authRequest1, $authRequest2);
    }
}
