<?php
declare(strict_types=1);

namespace Genkgo\Push\Sender;

use Genkgo\Push\Message;
use Genkgo\Push\Recipient\WindowsDeviceRecipient;
use Genkgo\Push\RecipientInterface;
use Genkgo\Push\SenderInterface;
use JildertMiedema\WindowsPhone\WindowsPhonePushNotification;

final class WindowsSender implements SenderInterface
{
    /**
     * @var WindowsPhonePushNotification
     */
    private $connection;

    /**
     * @param WindowsPhonePushNotification $connection
     */
    public function __construct(WindowsPhonePushNotification $connection)
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
        return $recipient instanceof WindowsDeviceRecipient;
    }

    /**
     * @param Message $message
     * @param RecipientInterface|WindowsDeviceRecipient $recipient
     * @codeCoverageIgnore
     */
    public function send(Message $message, RecipientInterface $recipient): void
    {
        $this->connection->pushToast(
            $recipient->getToken(),
            (string)$message->getTitle(),
            (string)$message->getBody()
        );
    }

    /**
     * @return WindowsSender
     */
    public static function fromDefault(): self
    {
        return new self(new WindowsPhonePushNotification());
    }
}
