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
     * @var array<mixed, mixed>
     */
    private $data;

    /**
     * @param string $body
     * @param string $title
     * @param array<mixed, mixed> $data
     */
    public function __construct(string $body, string $title, array $data = [])
    {
        $this->body = $body;
        $this->title = $title;
        $this->data = $data;
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

    /**
     * @return array<mixed, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }
}
