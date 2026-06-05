<?php
declare(strict_types=1);

namespace Genkgo\Push\Sender;

use Genkgo\Push\Exception\ForbiddenToSendMessageException;
use Genkgo\Push\Exception\InvalidMessageException;
use Genkgo\Push\Exception\InvalidRecipientException;
use Genkgo\Push\Exception\UnknownErrorException;
use Genkgo\Push\Exception\UnknownRecipientException;
use Genkgo\Push\Message;
use Genkgo\Push\Recipient\FirebaseRecipient;
use Genkgo\Push\Recipient\WebRecipient;
use Genkgo\Push\RecipientInterface;
use Genkgo\Push\SenderInterface;
use Genkgo\Push\WebPush\Keys;
use Genkgo\Push\WebPush\PayloadEncryption;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Ecdsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

final readonly class WebPushSender implements SenderInterface
{
    /**
     * @param non-empty-string $privateKeyFile
     * @param non-empty-string $publicKeyFile
     * @param non-empty-string $sub
     */
    public function __construct(
        private ClientInterface $client,
        private RequestFactoryInterface&StreamFactoryInterface $requestFactory,
        private PayloadEncryption $payloadEncryption,
        private string $privateKeyFile,
        private string $publicKeyFile,
        private string $sub,
    ) {
    }

    /**
     * @param Message $message
     * @param RecipientInterface $recipient
     * @return bool
     */
    public function supports(Message $message, RecipientInterface $recipient): bool
    {
        return $recipient instanceof FirebaseRecipient;
    }

    /**
     * @param RecipientInterface&WebRecipient $recipient
     * @throws ClientExceptionInterface
     * @throws ForbiddenToSendMessageException
     * @throws InvalidMessageException
     * @throws InvalidRecipientException
     * @throws UnknownErrorException
     * @throws UnknownRecipientException
     * @throws \JsonException
     */
    public function send(Message $message, RecipientInterface $recipient): void
    {
        $privateKey = InMemory::file($this->privateKeyFile);
        $publicKey = InMemory::file($this->publicKeyFile);
        $config = Configuration::forAsymmetricSigner(new Sha256(), $privateKey, $publicKey);

        $moment = new \DateTimeImmutable();
        $moment = $moment->setTime((int)$moment->format('H'), (int)$moment->format('i'));

        $endpoint = \parse_url($recipient->get('endpoint'));
        if (!isset($endpoint['scheme'], $endpoint['host'])) {
            throw new InvalidRecipientException('Failed to parse endpoint');
        }

        $origin = $endpoint['scheme'] . '://' . $endpoint['host'];

        $token = $config->builder()
            ->issuedAt($moment)
            ->expiresAt($moment->modify('+20 minutes'))
            ->permittedFor($origin)
            ->relatedTo($this->sub)
            ->getToken(
                $config->signer(),
                $config->signingKey()
            );

        $response = $this->client->sendRequest(
            $this->requestFactory
                ->createRequest('POST', $recipient->get('endpoint'))
                ->withHeader('Content-Type', 'application/octet-stream')
                ->withHeader('Content-Encoding', 'aes128gcm')
                ->withHeader('TTL', '2419200')
                ->withHeader(
                    'Authorization',
                    \sprintf(
                        'vapid t=%s, k=%s',
                        $token->toString(),
                        Keys::convertPublicKeyToApplicationServerKey($publicKey->contents()),
                    )
                )
                ->withBody(
                    $this->requestFactory->createStream(
                        $this->payloadEncryption->encrypt(
                            [
                                'title' => (string)$message->getTitle(),
                                'body'  => (string)$message->getBody(),
                                'data' => $message->getExtra(),
                            ],
                            $recipient
                        )
                    )
                )
        );

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() <= 299) {
            return;
        }

        if ($response->getStatusCode() <= 399) {
            throw new UnknownErrorException('Did expect a 300-300 response from a WebPush endpoint, got ' . $response->getStatusCode());
        }

        if ($response->getHeaderLine('Content-Type') === 'application/json') {
            /** @var array{error: ?string, message: ?string} $errorDetails */
            $errorDetails = \json_decode($response->getBody()->getContents(), true, 512, \JSON_THROW_ON_ERROR);

            $exceptionMessage = ($errorDetails['error'] ?? 'Error') . ': ' . ($errorDetails['message'] ?? 'Unknown error');
            match ($response->getStatusCode()) {
                400 => throw new InvalidMessageException($exceptionMessage),
                401 => throw new InvalidRecipientException($exceptionMessage),
                403 => throw new ForbiddenToSendMessageException($exceptionMessage),
                404 => throw new UnknownRecipientException($exceptionMessage),
                default => throw new UnknownErrorException($exceptionMessage),
            };
        }

        throw new UnknownErrorException(
            'Unknown error response ' . $response->getStatusCode() . ' ' . $response->getReasonPhrase() . ', ' . $response->getBody()->getContents()
        );
    }
}
