<?php
namespace Genkgo\Push\Unit\Sender;
use Apple\ApnPush\Certificate\CertificateInterface;
use Apple\ApnPush\Notification\Connection;
use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Body;
use Genkgo\Push\Message;
use Genkgo\Push\Recipient\AndroidDeviceRecipient;
use Genkgo\Push\Recipient\AppleDeviceRecipient;
use Genkgo\Push\Sender\AppleApnSender;

/**
 * Class AppleApnSender
 * @package Genkgo\Push\Sender
 */
class AppleApnSenderTest extends AbstractTestCase {

    public function testSupports () {
        $connection = new Connection($this->getMock(CertificateInterface::class));
        $message = new Message(new Body('test'));
        $recipient = new AppleDeviceRecipient('token');

        $sender = new AppleApnSender($connection);
        $this->assertTrue($sender->supports($message, $recipient));
    }

    public function testNotSupports () {
        $connection = new Connection($this->getMock(CertificateInterface::class));
        $message = new Message(new Body('test'));
        $recipient = new AndroidDeviceRecipient('token');

        $sender = new AppleApnSender($connection);
        $this->assertFalse($sender->supports($message, $recipient));
    }

}