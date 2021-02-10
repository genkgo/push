<?php
declare(strict_types=1);

namespace Genkgo\Push\Sender;

use Apple\ApnPush\Certificate\Certificate;
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

final class AppleApnSender implements SenderInterface
{
    /**
     * @var AppleSender
     */
    private $sender;

    /**
     * @var string
     */
    private $bundleId;

    /**
     * @var bool
     */
    private $sandbox;

    /**
     * @param AppleSender $sender
     * @param string $bundleId
     * @param bool $sandbox
     */
    public function __construct(AppleSender $sender, string $bundleId, bool $sandbox = false)
    {
        $this->sender = $sender;
        $this->sandbox = $sandbox;
        $this->bundleId = $bundleId;
    }

    /**
     * @param Message $message
     * @param RecipientInterface $recipient
     * @return bool
     */
    public function supports(Message $message, RecipientInterface $recipient): bool
    {
        return $recipient instanceof AppleDeviceRecipient;
    }

    /**
     * @param Message $message
     * @param RecipientInterface|AppleDeviceRecipient $recipient
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
        $receiver = new Receiver(new DeviceToken($recipient->getToken()), $this->bundleId);

        try {
            $this->sender->send($receiver, $notification, $this->sandbox);
        } catch (UnregisteredException $e) {
            throw new UnknownRecipientException($e->getMessage());
        } catch (BadDeviceTokenException $e) {
            throw new InvalidRecipientException($e->getMessage());
        } catch (BadExpirationDateException $e) {
            throw new InvalidMessageException($e->getMessage());
        } catch (BadMessageIdException $e) {
            throw new InvalidMessageException($e->getMessage());
        } catch (BadPriorityException $e) {
            throw new InvalidMessageException($e->getMessage());
        } catch (BadTopicException $e) {
            throw new InvalidMessageException($e->getMessage());
        } catch (DeviceTokenNotForTopicException $e) {
            throw new InvalidMessageException($e->getMessage());
        } catch (DuplicateHeadersException $e) {
            throw new InvalidMessageException($e->getMessage());
        } catch (IdleTimeoutException $e) {
            throw new ConnectionException($e->getMessage());
        } catch (MissingDeviceTokenException $e) {
            throw new InvalidMessageException($e->getMessage());
        } catch (MissingTopicException $e) {
            throw new InvalidMessageException($e->getMessage());
        } catch (PayloadEmptyException $e) {
            throw new InvalidMessageException($e->getMessage());
        } catch (TopicDisallowedException $e) {
            throw new InvalidMessageException($e->getMessage());
        } catch (BadCertificateException $e) {
            throw new InvalidMessageException($e->getMessage());
        } catch (BadCertificateEnvironmentException $e) {
            throw new InvalidMessageException($e->getMessage());
        } catch (ExpiredProviderTokenException $e) {
            throw new InvalidMessageException($e->getMessage());
        } catch (ForbiddenException $e) {
            throw new ForbiddenToSendMessageException($e->getMessage());
        } catch (InvalidProviderTokenException $e) {
            throw new InvalidMessageException($e->getMessage());
        } catch (MissingProviderTokenException $e) {
            throw new InvalidMessageException($e->getMessage());
        } catch (BadPathException $e) {
            throw new ConnectionException($e->getMessage());
        } catch (MethodNotAllowedException $e) {
            throw new ConnectionException($e->getMessage());
        } catch (UndefinedErrorException $e) {
            throw new ConnectionException($e->getMessage());
        }
    }

    /**
     * @param string $certificate
     * @param string $passphrase
     * @param bool $sandboxMode
     * @return AppleApnSender
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
     * @param string $token
     * @param string $keyId
     * @param string $teamId
     * @param string $bundleId
     * @param bool $sandboxMode
     * @return AppleApnSender
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
