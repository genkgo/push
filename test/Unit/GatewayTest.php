<?php
declare(strict_types=1);

namespace Genkgo\Push\Unit;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Body;
use Genkgo\Push\Exception\UnsupportedMessageRecipient;
use Genkgo\Push\Gateway;
use Genkgo\Push\Message;
use Genkgo\Push\Recipient\AndroidDeviceRecipient;
use Genkgo\Push\SenderInterface;

final class GatewayTest extends AbstractTestCase
{
    public function testSend(): void
    {
        $message = new Message(new Body('test'));
        $recipient = new AndroidDeviceRecipient('token');

        $sender = $this->createMock(SenderInterface::class);
        $sender->method('supports')->with($message, $recipient)->willReturn(true);
        $sender->method('send')->with($message, $recipient);

        $gateway = new Gateway([$sender]);
        $gateway->send($message, $recipient);

        $this->addToAssertionCount(1);
    }

    public function testExceptionUnsupportedSender(): void
    {
        $this->expectException(UnsupportedMessageRecipient::class);

        $message = new Message(new Body('test'));
        $recipient = new AndroidDeviceRecipient('token');

        $sender = $this->createMock(SenderInterface::class);
        $sender->method('supports')->with($message, $recipient)->willReturn(false);

        $gateway = new Gateway([$sender]);
        $gateway->send($message, $recipient);
    }
}
