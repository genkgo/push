<?php
declare(strict_types=1);

namespace Genkgo\Push\Sender;

use Apple\ApnPush\Certificate\Certificate;
use Apple\ApnPush\Notification\Connection;
use Apple\ApnPush\Notification\Message as AppleMessage;
use Apple\ApnPush\Notification\Notification;
use Genkgo\Push\Message;
use Genkgo\Push\Recipient\AppleDeviceRecipient;
use Genkgo\Push\RecipientInterface;
use Genkgo\Push\SenderInterface;

final class AppleApnSender implements SenderInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * AppleApnSender constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
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
        $appleMessage = new AppleMessage();
        $appleMessage->setBody((string)$message->getBody());
        $appleMessage->setDeviceToken($recipient->getToken());
        $appleMessage->setCustomData($message->getExtra());

        $notification = new Notification($this->connection);
        $notification->send($appleMessage);
    }

    /**
     * @param $certificate
     * @param $passphrase
     * @param bool|false $sandboxMode
     * @return AppleApnSender
     */
    public static function fromCertificate($certificate, $passphrase, $sandboxMode = false): self
    {
        return new self(new Connection(new Certificate($certificate, $passphrase), $sandboxMode));
    }
}
