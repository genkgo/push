<?php
declare(strict_types=1);

namespace Genkgo\Push\Firebase;

use Genkgo\Push\Exception\ForbiddenToSendMessageException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

final class CloudMessaging
{
    private const FCM_ENDPOINT = 'https://fcm.googleapis.com/v1';

    /**
     * @var AuthorizationHeaderProviderInterface
     */
    private $authorizationHeaderProvider;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @param ClientInterface $client
     * @param AuthorizationHeaderProviderInterface $authorizationHeaderProvider
     */
    public function __construct(
        ClientInterface $client,
        AuthorizationHeaderProviderInterface $authorizationHeaderProvider
    ) {
        $this->authorizationHeaderProvider = $authorizationHeaderProvider;
        $this->client = $client;
    }

    /**
     * @param string $projectId
     * @param string $token
     * @param Notification $notification
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(string $projectId, string $token, Notification $notification): void
    {
        $authorizationHeader = \call_user_func($this->authorizationHeaderProvider);

        try {
            $this->client
                ->send(
                    new Request(
                        'POST',
                        \sprintf(
                            '%s/projects/%s/messages:send',
                            self::FCM_ENDPOINT,
                            $projectId
                        ),
                        [
                            'Content-Type' => 'application/json',
                            'Authorization' => $authorizationHeader,
                        ],
                        \json_encode([
                            'message' => [
                                'token' => $token,
                                'data' => $this->convertDataToStrings($notification->getData()),
                                'notification' => [
                                    'body' => $notification->getBody(),
                                    'title' => $notification->getTitle(),
                                ]
                            ]
                        ])
                    )
                );
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 403) {
                throw new ForbiddenToSendMessageException(
                    'Cannot send message due to access restriction:' . $e->getMessage(),
                    $e->getCode(),
                    $e
                );
            }

            throw $e;
        }
    }

    /**
     * @param array $data
     * @return array
     */
    private function convertDataToStrings(array $data): array
    {
        $callback = function ($item) {
            return (string)$item;
        };

        $func = function ($item) use (&$func, &$callback) {
            return \is_array($item) ? \array_map($func, $item) : \call_user_func($callback, $item);
        };

        return \array_map($func, $data);
    }
}
