<?php
declare(strict_types=1);

namespace Genkgo\Push;

use Genkgo\Push\Exception\UnsupportedMessageRecipient;

final class Gateway
{
    /**
     * @var array|SenderInterface[]
     */
    private $senders = [];

    /**
     * @param array|SenderInterface[] $senders
     */
    public function __construct(array $senders)
    {
        $this->senders = $senders;
    }

    /**
     * @param Message $message
     * @param RecipientInterface $recipient
     * @throws Exception\ForbiddenToSendMessageException
     * @throws Exception\InvalidMessageException
     * @throws Exception\InvalidRecipientException
     * @throws Exception\UnknownRecipientException
     * @throws UnsupportedMessageRecipient
     */
    public function send(Message $message, RecipientInterface $recipient): void
    {
        foreach ($this->senders as $sender) {
            if ($sender->supports($message, $recipient)) {
                $sender->send($message, $recipient);
                return;
            }
        }

        $recipientClass = \get_class($recipient);
        throw new UnsupportedMessageRecipient(
            "Could not find a sender that supports the combination of message and recipient ({$recipientClass})"
        );
    }
}
