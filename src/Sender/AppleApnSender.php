<?php
declare(strict_types=1);

namespace Genkgo\Push\Sender;

use Apple\ApnPush\Certificate\Certificate;
use Apple\ApnPush\Exception\CertificateFileNotFoundException;
use Apple\ApnPush\Exception\SendNotification\BadCertificateEnvironmentException;
use Apple\ApnPush\Exception\SendNotification\BadCertificateException;
use Apple\ApnPush\Exception\SendNotification\BadDeviceTokenException;
use Apple\ApnPush\Exception\SendNotification\BadExpirationDateException;
use Apple\ApnPush\Exception\SendNotification\BadMessageIdException;
use Apple\ApnPush\Exception\SendNotification\BadPathException;
use Apple\ApnPush\Exception\SendNotification\BadPriorityException;
use Apple\ApnPush\Exception\SendNotification\BadTopicException;
use Apple\ApnPush\Exception\SendNotification\DeviceTokenNotForTopicException;
use Apple\ApnPush\Exception\SendNotification\DuplicateHeadersException;
use Apple\ApnPush\Exception\SendNotification\ExpiredProviderTokenException;
use Apple\ApnPush\Exception\SendNotification\ForbiddenException;
use Apple\ApnPush\Exception\SendNotification\IdleTimeoutException;
use Apple\ApnPush\Exception\SendNotification\InvalidProviderTokenException;
use Apple\ApnPush\Exception\SendNotification\MethodNotAllowedException;
use Apple\ApnPush\Exception\SendNotification\MissingDeviceTokenException;
use Apple\ApnPush\Exception\SendNotification\MissingProviderTokenException;
use Apple\ApnPush\Exception\SendNotification\MissingTopicException;
use Apple\ApnPush\Exception\SendNotification\PayloadEmptyException;
use Apple\ApnPush\Exception\SendNotification\SendNotificationException;
use Apple\ApnPush\Exception\SendNotification\TopicDisallowedException;
use Apple\ApnPush\Exception\SendNotification\UndefinedErrorException;
use Apple\ApnPush\Exception\SendNotification\UnregisteredException;
use Apple\ApnPush\Model\Alert;
use Apple\ApnPush\Model\Aps;
use Apple\ApnPush\Model\DeviceToken;
use Apple\ApnPush\Model\Notification;
use Apple\ApnPush\Model\Payload;
use Apple\ApnPush\Model\Receiver;
use Apple\ApnPush\Protocol\Http\Authenticator\CertificateAuthenticator;
use Apple\ApnPush\Sender\Builder\Http20Builder;
use Apple\ApnPush\Sender\SenderInterface as AppleSender;
use Genkgo\Push\Apn\JwtAuthenticator;
use Genkgo\Push\Exception\ConnectionException;
use Genkgo\Push\Exception\ForbiddenToSendMessageException;
use Genkgo\Push\Exception\InvalidMessageException;
use Genkgo\Push\Exception\InvalidRecipientException;
use Genkgo\Push\Exception\UnknownRecipientException;
use Genkgo\Push\Message;
use Genkgo\Push\Recipient\AppleDeviceRecipient;
use Genkgo\Push\RecipientInterface;
use Genkgo\Push\SenderInterface;

final readonly class AppleApnSender implements SenderInterface
{
    public function __construct(
        private AppleSender $sender,
        private string $bundleId,
        private bool $sandbox = false
    ) {
    }

    public function supports(Message $message, RecipientInterface $recipient): bool
    {
        return $recipient instanceof AppleDeviceRecipient;
    }

    /**
     * @param RecipientInterface&AppleDeviceRecipient $recipient
     * @throws ConnectionException
     * @throws ForbiddenToSendMessageException
     * @throws InvalidMessageException
     * @throws InvalidRecipientException
     * @throws UnknownRecipientException
     * @throws SendNotificationException
     * @codeCoverageIgnore
     */
    public function send(Message $message, RecipientInterface $recipient): void
    {
        $alert = (new Alert())
            ->withBody((string)$message->getBody())
            ->withTitle((string)$message->getTitle());

        $payload = new Payload(new Aps($alert));
        foreach ($message->getExtra() as $key => $value) {
            $payload = $payload->withCustomData((string)$key, $value);
        }

        $notification = new Notification($payload);

        try {
            $receiver = new Receiver(new DeviceToken($recipient->getToken()), $this->bundleId);
        } catch (\InvalidArgumentException $e) {
            throw new InvalidRecipientException($e->getMessage(), $e->getCode(), $e);
        }

        try {
            $this->sender->send($receiver, $notification, $this->sandbox);
        } catch (UnregisteredException $e) {
            throw new UnknownRecipientException($e->getMessage(), $e->getCode(), $e);
        } catch (BadDeviceTokenException $e) {
            throw new InvalidRecipientException($e->getMessage(), $e->getCode(), $e);
        } catch (BadExpirationDateException $e) {
            throw new InvalidMessageException($e->getMessage(), $e->getCode(), $e);
        } catch (BadMessageIdException $e) {
            throw new InvalidMessageException($e->getMessage(), $e->getCode(), $e);
        } catch (BadPriorityException $e) {
            throw new InvalidMessageException($e->getMessage(), $e->getCode(), $e);
        } catch (BadTopicException $e) {
            throw new InvalidMessageException($e->getMessage(), $e->getCode(), $e);
        } catch (DeviceTokenNotForTopicException $e) {
            throw new InvalidMessageException($e->getMessage(), $e->getCode(), $e);
        } catch (DuplicateHeadersException $e) {
            throw new InvalidMessageException($e->getMessage(), $e->getCode(), $e);
        } catch (IdleTimeoutException $e) {
            throw new ConnectionException($e->getMessage(), $e->getCode(), $e);
        } catch (MissingDeviceTokenException $e) {
            throw new InvalidMessageException($e->getMessage(), $e->getCode(), $e);
        } catch (MissingTopicException $e) {
            throw new InvalidMessageException($e->getMessage(), $e->getCode(), $e);
        } catch (PayloadEmptyException $e) {
            throw new InvalidMessageException($e->getMessage(), $e->getCode(), $e);
        } catch (TopicDisallowedException $e) {
            throw new InvalidMessageException($e->getMessage(), $e->getCode(), $e);
        } catch (BadCertificateException $e) {
            throw new InvalidMessageException($e->getMessage(), $e->getCode(), $e);
        } catch (BadCertificateEnvironmentException $e) {
            throw new InvalidMessageException($e->getMessage(), $e->getCode(), $e);
        } catch (ExpiredProviderTokenException $e) {
            throw new InvalidMessageException($e->getMessage(), $e->getCode(), $e);
        } catch (ForbiddenException $e) {
            throw new ForbiddenToSendMessageException($e->getMessage(), $e->getCode(), $e);
        } catch (InvalidProviderTokenException $e) {
            throw new InvalidMessageException($e->getMessage(), $e->getCode(), $e);
        } catch (MissingProviderTokenException $e) {
            throw new InvalidMessageException($e->getMessage(), $e->getCode(), $e);
        } catch (BadPathException $e) {
            throw new ConnectionException($e->getMessage(), $e->getCode(), $e);
        } catch (MethodNotAllowedException $e) {
            throw new ConnectionException($e->getMessage(), $e->getCode(), $e);
        } catch (UndefinedErrorException $e) {
            throw new ConnectionException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws CertificateFileNotFoundException
     */
    public static function fromCertificate(string $certificate, string $passphrase, bool $sandboxMode = false): self
    {
        // Create certificate and authenticator
        $certificate = new Certificate($certificate, $passphrase);
        $authenticator = new CertificateAuthenticator($certificate);

        $builder = new Http20Builder($authenticator);
        $sender = $builder->build();
        return new self($sender, '', $sandboxMode);
    }

    /**
     * @param non-empty-string $token
     * @param non-empty-string $keyId
     * @param non-empty-string $teamId
     * @param non-empty-string $bundleId
     */
    public static function fromToken(
        string $token,
        string $keyId,
        string $teamId,
        string $bundleId,
        bool $sandboxMode = false
    ): self {
        $builder = new Http20Builder(new JwtAuthenticator($token, $keyId, $teamId));
        $sender = $builder->build();
        return new self($sender, $bundleId, $sandboxMode);
    }
}
