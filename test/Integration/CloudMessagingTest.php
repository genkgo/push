<?php
declare(strict_types=1);

namespace Genkgo\Push\Integration;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Exception\ForbiddenToSendMessageException;
use Genkgo\Push\Firebase\AuthorizationHeaderProviderInterface;
use Genkgo\Push\Firebase\CloudMessaging;
use Genkgo\Push\Firebase\Notification;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

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
            ->method('sendRequest')
            ->with(
                $this->callback(
                    function (RequestInterface $request) {
                        $body = \json_decode((string)$request->getBody(), true);
                        $this->assertSame("1", $body['message']['data']['true']); // @phpstan-ignore-line
                        $this->assertSame("", $body['message']['data']['false']); // @phpstan-ignore-line
                        $this->assertSame("1815", $body['message']['data']['int']); // @phpstan-ignore-line
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

        $cloudMessaging = new CloudMessaging($client, new HttpFactory(), $provider);
        $cloudMessaging->send('project-xyz', 'token', $notification);
    }

    public function testForbidden(): void
    {
        $this->expectException(ForbiddenToSendMessageException::class);

        $provider = $this->createMock(AuthorizationHeaderProviderInterface::class);
        $provider
            ->method('__invoke')
            ->willReturn('Bearer test');

        $httpFactory = new HttpFactory();
        $client = $this->createMock(ClientInterface::class);
        $client
            ->method('sendRequest')
            ->willReturnCallback(
                fn () => new Response(
                    403,
                    ['Content-Type' => 'application/json'],
                    $httpFactory->createStream((string)\json_encode(['error' => ['message' => 'Forbidden']]))
                )
            );

        $notification = new Notification('body', 'title');
        $cloudMessaging = new CloudMessaging($client, $httpFactory, $provider);

        $cloudMessaging->send('project-xyz', 'token', $notification);
    }
}
