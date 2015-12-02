<?php
namespace Genkgo\Push\Unit;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Body;
use Genkgo\Push\Exception\UnsupportedMessageRecipient;
use Genkgo\Push\Gateway;
use Genkgo\Push\Message;
use Genkgo\Push\Recipient\AndroidDeviceRecipient;
use Genkgo\Push\SenderInterface;

class GatewayTest extends AbstractTestCase
{
    public function testSend()
    {
        $message = new Message(new Body('test'));
        $recipient = new AndroidDeviceRecipient('token');

        $sender = $this->getMock(SenderInterface::class);
        $sender->expects($this->at(0))->method('supports')->with($message, $recipient)->willReturn(true);
        $sender->expects($this->at(1))->method('send')->with($message, $recipient)->willReturn(true);

        $gateway = new Gateway([$sender]);
        $gateway->send($message, $recipient);
    }

    public function testExceptionUnsupportedSender()
    {
        $this->setExpectedException(UnsupportedMessageRecipient::class);

        $message = new Message(new Body('test'));
        $recipient = new AndroidDeviceRecipient('token');

        $sender = $this->getMock(SenderInterface::class);
        $sender->expects($this->at(0))->method('supports')->with($message, $recipient)->willReturn(false);

        $gateway = new Gateway([$sender]);
        $gateway->send($message, $recipient);
    }
}
