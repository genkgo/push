<?php
declare(strict_types=1);

namespace Genkgo\Push\Unit\Recipient;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Recipient\WindowsDeviceRecipient;

final class WindowsDeviceRecipientTest extends AbstractTestCase
{
    public function testToken()
    {
        $recipient = new WindowsDeviceRecipient('test');
        $this->assertEquals('test', $recipient->getToken());
    }
}
