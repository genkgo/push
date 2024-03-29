<?php
declare(strict_types=1);

namespace Genkgo\Push\Firebase;

use Genkgo\Push\Exception\ForbiddenToSendMessageException;
use Genkgo\Push\Exception\UnknownRecipientException;
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
     */
    public function send(string $projectId, string $token, Notification $notification): void
    {
        $authorizationHeader = \call_user_func($this->authorizationHeaderProvider);

        try {
            $json = \json_encode([
                'message' => [
                    'token' => $token,
                    'data' => $this->convertDataToStrings($notification->getData()),
                    'notification' => [
                        'body' => $notification->getBody(),
                        'title' => $notification->getTitle(),
                    ]
                ]
            ]);

            if ($json === false) {
                throw new \UnexpectedValueException('Cannot encode HTTP message');
            }

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
                        $json
                    )
                );
        } catch (ClientException $e) {
            $response = $e->getResponse();
            if ($response !== null && $response->getStatusCode() === 403) {
                throw new ForbiddenToSendMessageException(
                    'Cannot send message due to access restriction:' . $e->getMessage(),
                    $e->getCode(),
                    $e
                );
            }

            if ($response !== null && $response->getStatusCode() === 404) {
                throw new UnknownRecipientException(
                    'Cannot send message, unknown recipient:' . $e->getMessage(),
                    $e->getCode(),
                    $e
                );
            }

            throw $e;
        }
    }

    /**
     * @param array<string|int, mixed> $data
     * @return array<string|int, string>
     */
    private function convertDataToStrings(array $data): array
    {
        $callback = function ($item) {
            return (string)$item;
        };

        $func = function ($item) use (&$func, &$callback) {
            return \is_array($item) ? \array_map($func, $item) : \call_user_func($callback, $item);
        };

        /** @var array<string|int, string> $result */
        $result = \array_map($func, $data);
        return $result;
    }
}
