<?php
declare(strict_types=1);

namespace Genkgo\Push\Unit\Sender;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Body;
use Genkgo\Push\Message;
use Genkgo\Push\Recipient\AndroidDeviceRecipient;
use Genkgo\Push\Recipient\AppleDeviceRecipient;
use Genkgo\Push\Sender\GoogleGcmSender;

final class GoogleGcmSenderTest extends AbstractTestCase
{
    public function testSupports()
    {
        $message = new Message(new Body('test'));
        $recipient = new AndroidDeviceRecipient('token');

        $sender = GoogleGcmSender::fromApiKey('key');
        $this->assertTrue($sender->supports($message, $recipient));
    }

    public function testNotSupports()
    {
        $message = new Message(new Body('test'));
        $recipient = new AppleDeviceRecipient('token');

        $sender = GoogleGcmSender::fromApiKey('key');
        $this->assertFalse($sender->supports($message, $recipient));
    }
}
