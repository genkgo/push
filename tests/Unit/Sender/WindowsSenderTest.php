<?php
namespace Genkgo\Push\Unit\Sender;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Body;
use Genkgo\Push\Message;
use Genkgo\Push\Recipient\AppleDeviceRecipient;
use Genkgo\Push\Recipient\WindowsDeviceRecipient;
use Genkgo\Push\Sender\WindowsSender;
use JildertMiedema\WindowsPhone\WindowsPhonePushNotification;

/**
 * Class AppleApnSender
 * @package Genkgo\Push\Sender
 */
class WindowsSenderTest extends AbstractTestCase {

    public function testSupports () {
        $connection = new WindowsPhonePushNotification();
        $message = new Message(new Body('test'));
        $recipient = new WindowsDeviceRecipient('token');

        $sender = new WindowsSender($connection);
        $this->assertTrue($sender->supports($message, $recipient));
    }

    public function testNotSupports () {
        $connection = new WindowsPhonePushNotification();
        $message = new Message(new Body('test'));
        $recipient = new AppleDeviceRecipient('token');

        $sender = new WindowsSender($connection);
        $this->assertFalse($sender->supports($message, $recipient));
    }

}