<?php
declare(strict_types=1);

namespace Genkgo\Push\Sender;

use Genkgo\Push\Exception\ForbiddenToSendMessageException;
use Genkgo\Push\Exception\InvalidMessageException;
use Genkgo\Push\Exception\InvalidRecipientException;
use Genkgo\Push\Exception\UnknownRecipientException;
use Genkgo\Push\Firebase\CloudMessaging;
use Genkgo\Push\Firebase\Notification;
use Genkgo\Push\Message;
use Genkgo\Push\Recipient\FirebaseRecipient;
use Genkgo\Push\RecipientInterface;
use Genkgo\Push\SenderInterface;
use GuzzleHttp\Exception\RequestException;

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
        try {
            $this->cloudMessaging->send(
                $this->projectId,
                $recipient->getToken(),
                new Notification(
                    (string)$message->getBody(),
                    (string)$message->getTitle(),
                    $message->getExtra()
                )
            );
        } catch (RequestException $e) {
            $response = $e->getResponse();
            if (!$response) {
                throw $e;
            }

            $contentTypeHeader = $response->getHeaderLine('content-type');
            if (\strpos($contentTypeHeader, 'application/json') === false) {
                $error = $e->getMessage();
            } else {
                $responseText = (string)$response->getBody();
                $responseJson = \json_decode($responseText, true);

                if ($responseJson && isset($responseJson['error']['message'])) {
                    $error = $responseJson['error']['message'];
                } else {
                    $error = $e->getMessage();
                }
            }

            if ($response->getStatusCode() === 400) {
                throw new InvalidRecipientException($error);
            }

            if ($response->getStatusCode() === 404) {
                throw new UnknownRecipientException($error);
            }

            if ($response->getStatusCode() === 403) {
                throw new ForbiddenToSendMessageException($error);
            }

            if ($response->getStatusCode() === 429) {
                throw new ForbiddenToSendMessageException($error);
            }

            if ($response->getStatusCode() === 401) {
                throw new InvalidMessageException($error);
            }

            throw $e;
        }
    }
}
