<?php
namespace Genkgo\Push\Unit\Sender;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Body;
use Genkgo\Push\Message;
use Genkgo\Push\Recipient\AppleDeviceRecipient;
use Genkgo\Push\Recipient\WindowsDeviceRecipient;
use Genkgo\Push\Sender\WindowsSender;

/**
 * Class AppleApnSender
 * @package Genkgo\Push\Sender
 */
class WindowsSenderTest extends AbstractTestCase
{
    public function testSupports()
    {
        $message = new Message(new Body('test'));
        $recipient = new WindowsDeviceRecipient('token');

        $sender = WindowsSender::fromDefault();
        $this->assertTrue($sender->supports($message, $recipient));
    }

    public function testNotSupports()
    {
        $message = new Message(new Body('test'));
        $recipient = new AppleDeviceRecipient('token');

        $sender = WindowsSender::fromDefault();
        $this->assertFalse($sender->supports($message, $recipient));
    }
}
