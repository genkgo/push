<?php
declare(strict_types=1);

namespace Genkgo\Push;

final class Message
{
    private Title $title;

    private ?string $navigate;

    /** @var array<int|string, string|int|float|bool|array<int|string, string|int|float|bool>> */
    private array $extra;

    public function __construct(private readonly Body $body)
    {
        $this->title = new Title('');
        $this->navigate = null;
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

    public function getNavigate(): ?string
    {
        return $this->navigate;
    }

    /**
     * @return array<int|string, string|int|float|bool|array<int|string, string|int|float|bool>>
     */
    public function getExtra(): array
    {
        return $this->extra;
    }

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

    public function withNavigate(string $url): self
    {
        $clone = clone $this;
        $clone->navigate = $url;
        return $clone;
    }
}
