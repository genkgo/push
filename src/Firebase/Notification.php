<?php
declare(strict_types=1);

namespace Genkgo\Push\Firebase;

final class Notification
{
    /**
     * @var string
     */
    private $body;

    /**
     * @var string
     */
    private $title;

    /**
     * @param string $body
     * @param string $title
     */
    public function __construct(string $body, string $title)
    {
        $this->body = $body;
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }
}
