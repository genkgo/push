<?php
declare(strict_types=1);

namespace Genkgo\Push\Integration;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Firebase\AuthorizationHeaderProviderInterface;
use Genkgo\Push\Firebase\CloudMessaging;
use Genkgo\Push\Firebase\Notification;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;

final class CloudMessagingTest extends AbstractTestCase
{
    public function testDataIsString()
    {
        $provider = $this->createMock(AuthorizationHeaderProviderInterface::class);
        $provider
            ->expects($this->at(0))
            ->method('__invoke')
            ->willReturn('Bearer test');

        $client = $this->createMock(ClientInterface::class);
        $client
            ->expects($this->at(0))
            ->method('send')
            ->with(
                $this->callback(
                    function (Request $request) {
                        $body = \json_decode((string)$request->getBody(), true);
                        $this->assertSame("1", $body['message']['data']['true']);
                        $this->assertSame("", $body['message']['data']['false']);
                        $this->assertSame("1815", $body['message']['data']['int']);
                        return true;
                    }
                )
            );

        $notification = new Notification(
            'body',
            'title',
            [
                'true' => true,
                'false' => false,
                'int' => 1815,
            ]
        );

        $cloudMessaging = new CloudMessaging($client, $provider);
        $cloudMessaging->send('project-xyz', 'token', $notification);
    }
}
