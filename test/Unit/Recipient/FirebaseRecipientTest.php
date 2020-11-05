<?php
declare(strict_types=1);

namespace Genkgo\Push\Unit\Recipient;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Recipient\FirebaseRecipient;

final class FirebaseRecipientTest extends AbstractTestCase
{
    public function testToken(): void
    {
        $recipient = new FirebaseRecipient('test');
        $this->assertEquals('test', $recipient->getToken());
    }

    public function testFromString(): void
    {
        $recipient = FirebaseRecipient::fromString('test');
        $this->assertInstanceOf(FirebaseRecipient::class, $recipient);
    }
}
