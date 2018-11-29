<?php
declare(strict_types=1);

namespace Genkgo\Push;

final class Message
{
    /**
     * @var Body
     */
    private $body;

    /**
     * @var Title
     */
    private $title;

    /**
     * @var array
     */
    private $extra = [];

    /**
     * @param Body $body
     */
    public function __construct(Body $body)
    {
        $this->body = $body;
        $this->title = new Title('');
    }

    /**
     * @return Body
     */
    public function getBody(): Body
    {
        return $this->body;
    }

    /**
     * @return Title
     */
    public function getTitle(): Title
    {
        return $this->title;
    }

    /**
     * @return array
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
     * @param mixed $key
     * @param mixed $value
     * @return Message
     */
    public function withExtra($key, $value): self
    {
        $clone = clone $this;
        $clone->extra[$key] = $value;
        return $clone;
    }
}
