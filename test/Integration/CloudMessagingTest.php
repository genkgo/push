<?php
declare(strict_types=1);

namespace Genkgo\Push\Integration;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Exception\ForbiddenToSendMessageException;
use Genkgo\Push\Firebase\AuthorizationHeaderProviderInterface;
use Genkgo\Push\Firebase\CloudMessaging;
use Genkgo\Push\Firebase\Notification;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

final class CloudMessagingTest extends AbstractTestCase
{
    public function testDataIsString(): void
    {
        $provider = $this->createMock(AuthorizationHeaderProviderInterface::class);
        $provider
            ->method('__invoke')
            ->willReturn('Bearer test');

        $client = $this->createMock(ClientInterface::class);
        $client
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

    public function testForbidden(): void
    {
        $this->expectException(ForbiddenToSendMessageException::class);

        $provider = $this->createMock(AuthorizationHeaderProviderInterface::class);
        $provider
            ->method('__invoke')
            ->willReturn('Bearer test');

        $client = $this->createMock(ClientInterface::class);
        $client
            ->method('send')
            ->willReturnCallback(
                function (Request $request) {
                    throw new ClientException(
                        'Forbidden',
                        $request,
                        new Response(403)
                    );
                }
            );

        $notification = new Notification('body', 'title');
        $cloudMessaging = new CloudMessaging($client, $provider);

        $cloudMessaging->send('project-xyz', 'token', $notification);
    }
}
