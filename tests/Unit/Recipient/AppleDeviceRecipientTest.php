<?php
namespace Genkgo\Push\Unit\Recipient;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Recipient\AppleDeviceRecipient;

class AppleDeviceRecipientTest extends AbstractTestCase
{
    public function testToken()
    {
        $recipient = new AppleDeviceRecipient('test');
        $this->assertEquals('test', $recipient->getToken());
    }
}
