<?php
namespace Genkgo\Push\Unit;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Body;
use Genkgo\Push\Exception\UnsupportedMessageRecipient;
use Genkgo\Push\Gateway;
use Genkgo\Push\Message;
use Genkgo\Push\Recipient\AndroidDeviceRecipient;
use Genkgo\Push\SenderInterface;
use Genkgo\Push\Title;
use Symfony\Component\Yaml\Tests\B;

class MessageTest extends AbstractTestCase {

    public function testImmutability () {
        $message = new Message(new Body('test'));
        $this->assertNotSame($message, $message->setBody(new Body('test')));
        $this->assertNotSame($message, $message->setTitle(new Title('test')));
    }

    public function testGetterSetterBody () {
        $oldBody = new Body('test');
        $newBody = new Body('new test');

        $message = new Message($oldBody);
        $message = $message->setBody($newBody);

        $this->assertSame($newBody, $message->getBody());
    }

    public function testGetterSetterTitle () {
        $title1 = new Title('test');
        $title2 = new Title('new test');

        $message = new Message(new Body('test'));
        $message = $message->setTitle($title1);

        $this->assertSame($title1, $message->getTitle());

        $message = $message->setTitle($title2);
        $this->assertSame($title2, $message->getTitle());
    }

}