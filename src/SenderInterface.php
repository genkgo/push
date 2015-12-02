<?php
namespace Genkgo\Push;

/**
 * Interface SenderInterface
 * @package Genkgo\Push
 */
interface SenderInterface
{
    /**
     * @param Message $message
     * @param RecipientInterface $recipient
     * @return bool
     */
    public function supports(Message $message, RecipientInterface $recipient);

    /**
     * @param Message $message
     * @param RecipientInterface $recipient
     * @return void
     */
    public function send(Message $message, RecipientInterface $recipient);
}
