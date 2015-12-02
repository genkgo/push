<?php
namespace Genkgo\Push\Sender;

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
class AppleApnSender implements SenderInterface {

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct (Connection $connection) {
        $this->connection = $connection;
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
        $appleMessage->setBody($message->getBody());
        $appleMessage->setDeviceToken($recipient->getToken());

        $notification = new Notification($this->connection);
        $notification->send($appleMessage);
    }
}