<?php
declare(strict_types=1);

namespace Genkgo\Push;

interface SenderInterface
{
    /**
     * @param Message $message
     * @param RecipientInterface $recipient
     * @return bool
     */
    public function supports(Message $message, RecipientInterface $recipient): bool;

    /**
     * @param Message $message
     * @param RecipientInterface $recipient
     * @return void
     */
    public function send(Message $message, RecipientInterface $recipient): void;
}
