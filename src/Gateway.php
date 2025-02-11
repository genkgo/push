<?php
declare(strict_types=1);

namespace Genkgo\Push;

use Genkgo\Push\Exception\UnsupportedMessageRecipient;

final readonly class Gateway
{
    /**
     * @param array<int|string, SenderInterface> $senders
     */
    public function __construct(private array $senders)
    {
    }

    /**
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

        $recipientClass = $recipient::class;
        throw new UnsupportedMessageRecipient(
            "Could not find a sender that supports the combination of message and recipient ({$recipientClass})"
        );
    }
}
