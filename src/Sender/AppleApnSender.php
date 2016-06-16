<?php
namespace Genkgo\Push\Sender;

use Apple\ApnPush\Certificate\Certificate;
use Apple\ApnPush\Notification\Connection;
use Apple\ApnPush\Notification\Message as AppleMessage;
use Apple\ApnPush\Notification\Notification;
use Genkgo\Push\Message;
use Genkgo\Push\Recipient\AppleDeviceRecipient;
use Genkgo\Push\RecipientInterface;
use Genkgo\Push\SenderInterface;

/**
 * Class AppleApnSender
 * @package Genkgo\Push\Sender
 */
final class AppleApnSender implements SenderInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param $certificate
     * @param $passphrase
     * @param bool|false $sandboxMode
     */
    public function __construct($certificate, $passphrase, $sandboxMode = false)
    {
        $this->connection = new Connection(new Certificate($certificate, $passphrase), $sandboxMode);
    }

    /**
     * @param Message $message
     * @param RecipientInterface $recipient
     * @return bool
     */
    public function supports(Message $message, RecipientInterface $recipient)
    {
        return $recipient instanceof AppleDeviceRecipient;
    }

    /**
     * @param Message $message
     * @param RecipientInterface|AppleDeviceRecipient $recipient
     * @codeCoverageIgnore
     */
    public function send(Message $message, RecipientInterface $recipient)
    {
        $appleMessage = new AppleMessage();
        $appleMessage->setBody((string) $message->getBody());
        $appleMessage->setDeviceToken($recipient->getToken());
        $appleMessage->setCustomData($message->getExtra());

        $notification = new Notification($this->connection);
        $notification->send($appleMessage);
    }
}
