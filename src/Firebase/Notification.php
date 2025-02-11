<?php
declare(strict_types=1);

namespace Genkgo\Push\Firebase;

final readonly class Notification
{
    /**
     * @param array<int|string, string|int|float|bool|array<int|string, string|int|float|bool>> $data
     */
    public function __construct(
        private string $body,
        private string $title,
        private array $data = []
    ) {
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return array<int|string, string|int|float|bool|array<int|string, string|int|float|bool>>
     */
    public function getData(): array
    {
        return $this->data;
    }
}
