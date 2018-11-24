<?php
declare(strict_types=1);

namespace Genkgo\Push\Unit\Recipient;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Recipient\FirebaseRecipient;

final class FirebaseRecipientTest extends AbstractTestCase
{
    public function testToken()
    {
        $recipient = new FirebaseRecipient('test');
        $this->assertEquals('test', $recipient->getToken());
    }
}
