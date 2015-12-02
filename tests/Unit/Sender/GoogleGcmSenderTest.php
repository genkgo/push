<?php
namespace Genkgo\Push\Unit\Sender;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Body;
use Genkgo\Push\Message;
use Genkgo\Push\Recipient\AndroidDeviceRecipient;
use Genkgo\Push\Recipient\AppleDeviceRecipient;
use Genkgo\Push\Sender\GoogleGcmSender;

class GoogleGcmSenderTest extends AbstractTestCase
{
    public function testSupports()
    {
        $message = new Message(new Body('test'));
        $recipient = new AndroidDeviceRecipient('token');

        $sender = new GoogleGcmSender('key');
        $this->assertTrue($sender->supports($message, $recipient));
    }

    public function testNotSupports()
    {
        $message = new Message(new Body('test'));
        $recipient = new AppleDeviceRecipient('token');

        $sender = new GoogleGcmSender('key');
        $this->assertFalse($sender->supports($message, $recipient));
    }
}
