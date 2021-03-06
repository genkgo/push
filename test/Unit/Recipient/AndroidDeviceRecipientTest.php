<?php
declare(strict_types=1);

namespace Genkgo\Push\Unit\Recipient;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Recipient\AndroidDeviceRecipient;

final class AndroidDeviceRecipientTest extends AbstractTestCase
{
    public function testToken(): void
    {
        $recipient = new AndroidDeviceRecipient('test');
        $this->assertEquals('test', $recipient->getToken());
    }

    public function testFromString(): void
    {
        $recipient = AndroidDeviceRecipient::fromString('test');
        $this->assertInstanceOf(AndroidDeviceRecipient::class, $recipient);
    }
}
