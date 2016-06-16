<?php
namespace Genkgo\Push\Unit;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Body;
use Genkgo\Push\Message;
use Genkgo\Push\Title;

class MessageTest extends AbstractTestCase
{
    public function testImmutability()
    {
        $message = new Message(new Body('test'));
        $this->assertNotSame($message, $message->withTitle(new Title('test')));
    }

    public function testGetterBody()
    {
        $body = new Body('test');
        $message = new Message($body);
        $this->assertSame('test', (string) $message->getBody());
    }

    public function testGetterSetterTitle()
    {
        $title1 = new Title('test');
        $title2 = new Title('new test');

        $message = new Message(new Body('test'));
        $message1 = $message->withTitle($title1);

        $this->assertSame($title1, $message1->getTitle());

        $message2 = $message->withTitle($title2);
        $this->assertSame($title2, $message2->getTitle());
    }

    public function testGetterSetterIdentifier()
    {
        $identifier1 = 1;
        $identifier2 = 2;

        $message = new Message(new Body('test'));
        $message1 = $message->withIdentifier($identifier1);

        $this->assertSame($identifier1, $message1->getIdentifier());

        $message2 = $message->withIdentifier($identifier2);
        $this->assertSame($identifier1, $message1->getIdentifier());
        $this->assertSame($identifier2, $message2->getIdentifier());
    }
}
