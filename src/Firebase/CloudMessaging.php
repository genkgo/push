<?php
declare(strict_types=1);

namespace Genkgo\Push\Firebase;

use Genkgo\Push\Exception\ForbiddenToSendMessageException;
use Genkgo\Push\Exception\InvalidMessageException;
use Genkgo\Push\Exception\InvalidRecipientException;
use Genkgo\Push\Exception\UnknownErrorException;
use Genkgo\Push\Exception\UnknownRecipientException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

final readonly class CloudMessaging
{
    private const string FCM_ENDPOINT = 'https://fcm.googleapis.com/v1';

    public function __construct(
        private ClientInterface $client,
        private RequestFactoryInterface&StreamFactoryInterface $requestFactory,
        private AuthorizationHeaderProviderInterface $authorizationHeaderProvider
    ) {
    }

    /**
     * @throws UnknownRecipientException
     * @throws ForbiddenToSendMessageException
     * @throws ClientExceptionInterface
     * @throws UnknownErrorException
     * @throws InvalidRecipientException
     * @throws InvalidMessageException
     */
    public function send(string $projectId, string $token, Notification $notification): void
    {
        $authorizationHeader = $this->authorizationHeaderProvider->providerHeaderValue();

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

        $response = $this->client
            ->sendRequest(
                $this->requestFactory->createRequest(
                    'POST',
                    \sprintf(
                        '%s/projects/%s/messages:send',
                        self::FCM_ENDPOINT,
                        $projectId
                    ),
                )
                    ->withHeader('Content-Type', 'application/json')
                    ->withHeader('Authorization', $authorizationHeader)
                    ->withBody($this->requestFactory->createStream($json))
            );

        if ($response->getStatusCode() < 300) {
            return;
        }

        $contentTypeHeader = $response->getHeaderLine('content-type');
        if (!\str_contains($contentTypeHeader, 'application/json')) {
            throw new UnknownErrorException('Cannot send message, unknown error. Got status code: ' . $response->getStatusCode());
        } else {
            $responseText = (string)$response->getBody();
            try {
                /** @var array{error: array{message: string}}|null $responseJson */
                $responseJson = \json_decode($responseText, true, 512, \JSON_THROW_ON_ERROR);
                $error = $responseJson['error']['message'] ?? '';
            } catch (\JsonException) {
                $error = '';
            }
        }

        if ($response->getStatusCode() === 400) {
            throw new InvalidRecipientException($error);
        }

        if ($response->getStatusCode() === 404) {
            throw new UnknownRecipientException($error);
        }

        if ($response->getStatusCode() === 403) {
            throw new ForbiddenToSendMessageException($error);
        }

        if ($response->getStatusCode() === 429) {
            throw new ForbiddenToSendMessageException($error);
        }

        if ($response->getStatusCode() === 401) {
            throw new InvalidMessageException($error);
        }

        throw new UnknownErrorException('Cannot send message, unknown error. Got status code: ' . $response->getStatusCode());
    }

    /**
     * @param array<int|string, string|int|float|bool|array<int|string, string|int|float|bool>> $data
     * @return array<string|int, string>
     */
    private function convertDataToStrings(array $data): array
    {
        $callback = fn (string|int|float|bool|null $item): string => (string)$item;

        $func = function (array|string|int|float|bool|null $item) use (&$func, &$callback) {
            if (\is_array($item)) {
                /** @var callable(mixed): mixed $func */
                return \array_map($func, $item);
            }

            return $callback($item);
        };

        /** @var array<string|int, string> $result */
        $result = \array_map($func, $data);
        return $result;
    }
}
