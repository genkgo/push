<?php
declare(strict_types=1);

namespace Genkgo\Push\Unit\Sender;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Body;
use Genkgo\Push\Message;
use Genkgo\Push\Recipient\AndroidDeviceRecipient;
use Genkgo\Push\Recipient\AppleDeviceRecipient;
use Genkgo\Push\Sender\AppleApnSender;

final class AppleApnSenderTest extends AbstractTestCase
{
    public function testSupports(): void
    {
        $message = new Message(new Body('test'));
        $recipient = new AppleDeviceRecipient('token');

        $sender = AppleApnSender::fromCertificate(__DIR__ . '/../../Stubs/cert.pem', 'password');
        $this->assertTrue($sender->supports($message, $recipient));
    }

    public function testToken(): void
    {
        $message = new Message(new Body('test'));
        $recipient = new AppleDeviceRecipient('token');

        $sender = AppleApnSender::fromToken(__DIR__ . '/../../Stubs/cert.pem', 'AB1234', 'Q12345', 'app.bundle.id');
        $this->assertTrue($sender->supports($message, $recipient));
    }

    public function testNotSupports(): void
    {
        $message = new Message(new Body('test'));
        $recipient = new AndroidDeviceRecipient('token');

        $sender = AppleApnSender::fromCertificate(\dirname(\dirname(__DIR__)) . '/Stubs/cert.pem', 'password');
        $this->assertFalse($sender->supports($message, $recipient));
    }
}
