<?php
declare(strict_types=1);

namespace Genkgo\Push;

use Genkgo\Push\Exception\ForbiddenToSendMessageException;
use Genkgo\Push\Exception\InvalidMessageException;
use Genkgo\Push\Exception\InvalidRecipientException;
use Genkgo\Push\Exception\UnknownRecipientException;

interface SenderInterface
{
    public function supports(Message $message, RecipientInterface $recipient): bool;

    /**
     * @throws ForbiddenToSendMessageException
     * @throws InvalidMessageException
     * @throws InvalidRecipientException
     * @throws UnknownRecipientException
     */
    public function send(Message $message, RecipientInterface $recipient): void;
}
