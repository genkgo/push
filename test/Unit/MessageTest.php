<?php
declare(strict_types=1);

namespace Genkgo\Push\Unit;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Body;
use Genkgo\Push\Message;
use Genkgo\Push\Title;

final class MessageTest extends AbstractTestCase
{
    public function testImmutability(): void
    {
        $message = new Message(new Body('test'));
        $this->assertNotSame($message, $message->withTitle(new Title('test')));
    }

    public function testBody(): void
    {
        $body = new Body('test');
        $message = new Message($body);
        $this->assertSame('test', (string)$message->getBody());
    }

    public function testTitle(): void
    {
        $title1 = new Title('test');
        $title2 = new Title('new test');

        $message = new Message(new Body('test'));
        $message1 = $message->withTitle($title1);

        $this->assertSame($title1, $message1->getTitle());

        $message2 = $message->withTitle($title2);
        $this->assertSame($title2, $message2->getTitle());
    }

    public function testExtra(): void
    {
        $message = new Message(new Body('test'));
        $message1 = $message->withExtra('localId', 1);
        $message2 = $message->withExtra('localId', 2);
        $message3 = $message1->withExtra('more', 'data');

        $this->assertCount(0, $message->getExtra());
        $this->assertCount(1, $message1->getExtra());
        $this->assertCount(1, $message2->getExtra());
        $this->assertCount(2, $message3->getExtra());
    }

    public function testNoTitle(): void
    {
        $message = new Message(new Body('test'));
        $this->assertEquals('', (string)$message->getTitle());
    }
}
