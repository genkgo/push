<?php
declare(strict_types=1);

namespace Genkgo\Push\Sender;

use Genkgo\Push\Message;
use Genkgo\Push\Recipient\AndroidDeviceRecipient;
use Genkgo\Push\RecipientInterface;
use Genkgo\Push\SenderInterface;
use PHP_GCM\Message as GcmMessage;
use PHP_GCM\Sender;

final class GoogleGcmSender implements SenderInterface
{
    /**
     * @var Sender
     */
    private $sender;

    /**
     * @var int
     */
    private $retries;

    /**
     * @param Sender $sender
     * @param int $retries
     */
    public function __construct(Sender $sender, $retries = 5)
    {
        $this->sender = $sender;
        $this->retries = $retries;
    }

    /**
     * @param Message $message
     * @param RecipientInterface|AndroidDeviceRecipient $recipient
     * @return bool
     */
    public function supports(Message $message, RecipientInterface $recipient): bool
    {
        return $recipient instanceof AndroidDeviceRecipient;
    }

    /**
     * @param Message $message
     * @param RecipientInterface $recipient
     * @codeCoverageIgnore
     */
    public function send(Message $message, RecipientInterface $recipient): void
    {
        $randomCollapse = \rand(11, 100);

        $gcmMessage = new GcmMessage("{$randomCollapse}", [
            'message' => (string)$message->getBody(),
            'title', (string)$message->getTitle()
        ]);

        $extra = $message->getExtra();
        foreach ($extra as $key => $value) {
            $gcmMessage->addData($key, $value);
        }

        $this->sender->sendMulti($gcmMessage, [$recipient->getToken()], $this->retries);
    }

    /**
     * @param string $apiKey
     * @param int $retries
     * @return GoogleGcmSender
     */
    public static function fromApiKey($apiKey, $retries = 5): self
    {
        $sender = new self(new Sender($apiKey));
        $sender->retries = $retries;
        return $sender;
    }
}
