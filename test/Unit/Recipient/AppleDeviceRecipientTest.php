<?php
declare(strict_types=1);

namespace Genkgo\Push\Unit\Recipient;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Recipient\AppleDeviceRecipient;

final class AppleDeviceRecipientTest extends AbstractTestCase
{
    public function testToken(): void
    {
        $recipient = new AppleDeviceRecipient('test');
        $this->assertEquals('test', $recipient->getToken());
    }

    public function testFromString(): void
    {
        $recipient = AppleDeviceRecipient::fromString('test');
        $this->assertInstanceOf(AppleDeviceRecipient::class, $recipient);
    }
}
