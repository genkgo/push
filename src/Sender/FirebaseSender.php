<?php
declare(strict_types=1);

namespace Genkgo\Push\Sender;

use Genkgo\Push\Firebase\CloudMessaging;
use Genkgo\Push\Firebase\Notification;
use Genkgo\Push\Message;
use Genkgo\Push\Recipient\FirebaseRecipient;
use Genkgo\Push\RecipientInterface;
use Genkgo\Push\SenderInterface;

final class FirebaseSender implements SenderInterface
{
    /**
     * @var CloudMessaging
     */
    private $cloudMessaging;

    /**
     * @var string
     */
    private $projectId;

    /**
     * @param CloudMessaging $cloudMessaging
     * @param string $projectId
     */
    public function __construct(CloudMessaging $cloudMessaging, string $projectId)
    {
        $this->cloudMessaging = $cloudMessaging;
        $this->projectId = $projectId;
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
     * @param Message $message
     * @param RecipientInterface $recipient
     * @return void
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
