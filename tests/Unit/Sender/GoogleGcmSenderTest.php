<?php
namespace Genkgo\Push\Unit\Sender;
use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Body;
use Genkgo\Push\Message;
use Genkgo\Push\Recipient\AndroidDeviceRecipient;
use Genkgo\Push\Recipient\AppleDeviceRecipient;
use Genkgo\Push\Sender\AppleApnSender;
use Genkgo\Push\Sender\GoogleGcmSender;
use PHP_GCM\Sender;

/**
 * Class AppleApnSender
 * @package Genkgo\Push\Sender
 */
class GoogleGcmSenderTest extends AbstractTestCase {

    public function testSupports () {
        $connection = $this->getMock(Sender::class, [], [], '', false);
        $message = new Message(new Body('test'));
        $recipient = new AndroidDeviceRecipient('token');

        $sender = new GoogleGcmSender($connection);
        $this->assertTrue($sender->supports($message, $recipient));
    }

    public function testNotSupports () {
        $connection = $this->getMock(Sender::class, [], [], '', false);
        $message = new Message(new Body('test'));
        $recipient = new AppleDeviceRecipient('token');

        $sender = new GoogleGcmSender($connection);
        $this->assertFalse($sender->supports($message, $recipient));
    }

}