<?php
declare(strict_types=1);

namespace Genkgo\Push\Unit\Sender;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Body;
use Genkgo\Push\Firebase\AuthorizationHeaderProviderInterface;
use Genkgo\Push\Firebase\CloudMessaging;
use Genkgo\Push\Message;
use Genkgo\Push\Recipient\AndroidDeviceRecipient;
use Genkgo\Push\Recipient\FirebaseRecipient;
use Genkgo\Push\Sender\FirebaseSender;
use GuzzleHttp\ClientInterface;

final class FirebaseSenderTest extends AbstractTestCase
{
    public function testSupports()
    {
        $message = new Message(new Body('test'));
        $recipient = new FirebaseRecipient('token');

        $client = $this->createMock(ClientInterface::class);
        $authorization = $this->createMock(AuthorizationHeaderProviderInterface::class);

        $sender = new FirebaseSender(new CloudMessaging($client, $authorization), '1234');
        $this->assertTrue($sender->supports($message, $recipient));
    }

    public function testNotSupports()
    {
        $message = new Message(new Body('test'));
        $recipient = new AndroidDeviceRecipient('token');

        $client = $this->createMock(ClientInterface::class);
        $authorization = $this->createMock(AuthorizationHeaderProviderInterface::class);

        $sender = new FirebaseSender(new CloudMessaging($client, $authorization), '1234');
        $this->assertFalse($sender->supports($message, $recipient));
    }
}
