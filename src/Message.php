<?php
declare(strict_types=1);

namespace Genkgo\Push;

final class Message
{
    private Title $title;

    /** @var array<int|string, string|int|float|bool|array<int|string, string|int|float|bool>> */
    private array $extra;

    public function __construct(private readonly Body $body)
    {
        $this->title = new Title('');
        $this->extra = [];
    }

    public function getBody(): Body
    {
        return $this->body;
    }

    public function getTitle(): Title
    {
        return $this->title;
    }

    /**
     * @return array<int|string, string|int|float|bool|array<int|string, string|int|float|bool>>
     */
    public function getExtra(): array
    {
        return $this->extra;
    }

    /**
     * @param Title $title
     * @return Message
     */
    public function withTitle(Title $title): self
    {
        $clone = clone $this;
        $clone->title = $title;
        return $clone;
    }

    /**
     * @param string|int|float|bool|array<int|string, string|int|float|bool> $value
     */
    public function withExtra(string|int $key, mixed $value): self
    {
        $clone = clone $this;
        $clone->extra[$key] = $value;
        return $clone;
    }
}
