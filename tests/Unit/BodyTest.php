<?php
namespace Genkgo\Push\Unit;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Body;
use Genkgo\Push\Exception\UnsupportedMessageRecipient;
use Genkgo\Push\Gateway;
use Genkgo\Push\Message;
use Genkgo\Push\Recipient\AndroidDeviceRecipient;
use Genkgo\Push\SenderInterface;
use Symfony\Component\Yaml\Tests\B;

class BodyTest extends AbstractTestCase {

    public function testToString () {
        $message = new Body('test');
        $this->assertEquals('test', (string) $message);
    }

}