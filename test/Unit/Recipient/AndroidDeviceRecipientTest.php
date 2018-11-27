<?php
declare(strict_types=1);

namespace Genkgo\Push\Unit\Recipient;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Recipient\AndroidDeviceRecipient;

final class AndroidDeviceRecipientTest extends AbstractTestCase
{
    public function testToken()
    {
        $recipient = new AndroidDeviceRecipient('test');
        $this->assertEquals('test', $recipient->getToken());
    }
}
