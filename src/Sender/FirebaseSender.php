<?php
declare(strict_types=1);

namespace Genkgo\Push\Sender;

use Genkgo\Push\Exception\ForbiddenToSendMessageException;
use Genkgo\Push\Exception\InvalidMessageException;
use Genkgo\Push\Exception\InvalidRecipientException;
use Genkgo\Push\Exception\UnknownErrorException;
use Genkgo\Push\Exception\UnknownRecipientException;
use Genkgo\Push\Firebase\CloudMessaging;
use Genkgo\Push\Firebase\Notification;
use Genkgo\Push\Message;
use Genkgo\Push\Recipient\FirebaseRecipient;
use Genkgo\Push\RecipientInterface;
use Genkgo\Push\SenderInterface;
use Psr\Http\Client\ClientExceptionInterface;

final readonly class FirebaseSender implements SenderInterface
{
    public function __construct(
        private CloudMessaging $cloudMessaging,
        private string $projectId
    ) {
    }

    /**
     * @param Message $message
     * @param RecipientInterface $recipient
     * @return bool
     */
    public function supports(Message $message, RecipientInterface $recipient): bool
    {
        return $recipient instanceof FirebaseRecipient;
    }

    /**
     * @throws ForbiddenToSendMessageException
     * @throws InvalidMessageException
     * @throws InvalidRecipientException
     * @throws UnknownErrorException
     * @throws UnknownRecipientException
     * @throws ClientExceptionInterface
     * @codeCoverageIgnore
     */
    public function send(Message $message, RecipientInterface $recipient): void
    {
        $this->cloudMessaging->send(
            $this->projectId,
            $recipient->getToken(),
            new Notification(
                (string)$message->getBody(),
                (string)$message->getTitle(),
                $message->getExtra()
            )
        );
    }
}
