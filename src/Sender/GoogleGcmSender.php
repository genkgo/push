<?php
namespace Genkgo\Push\Sender;

use Genkgo\Push\Message;
use Genkgo\Push\Recipient\AndroidDeviceRecipient;
use Genkgo\Push\RecipientInterface;
use Genkgo\Push\SenderInterface;
use PHP_GCM\Message as GcmMessage;
use PHP_GCM\Sender;

/**
 * Class GoogleGcmSender
 * @package Genkgo\Push\Sender
 */
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
     * @param string $apiKey
     * @param int $retries
     */
    public function __construct($apiKey, $retries = 5)
    {
        $this->sender = new Sender($apiKey);
        $this->retries = $retries;
    }


    /**
     * @param Message $message
     * @param RecipientInterface|AndroidDeviceRecipient $recipient
     * @return bool
     */
    public function supports(Message $message, RecipientInterface $recipient)
    {
        return $recipient instanceof AndroidDeviceRecipient;
    }

    /**
     * @param Message $message
     * @param RecipientInterface $recipient
     * @codeCoverageIgnore
     */
    public function send(Message $message, RecipientInterface $recipient)
    {
        $randomCollapse = rand(11, 100);

        $gcmMessage = new GcmMessage("{$randomCollapse}", [
            'message' => (string) $message->getBody(),
            'title', (string) $message->getTitle()
        ]);

        $extra = $message->getExtra();
        foreach ($extra as $key => $value) {
            $gcmMessage->addData($key, $value);
        }

        $this->sender->sendMulti($gcmMessage, [$recipient->getToken()], $this->retries);
    }
}
