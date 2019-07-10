<?php
declare(strict_types=1);

namespace Genkgo\Push\Sender;

use Apple\ApnPush\Certificate\Certificate;
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

        $this->sender->send($receiver, $notification, $this->sandbox);
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
