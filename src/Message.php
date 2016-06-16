<?php
namespace Genkgo\Push;

/**
 * Class Message
 * @package Genkgo\Push
 */
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
    }

    /**
     * @return Body
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return Title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return array
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param Title $title
     * @return Message
     */
    public function withTitle(Title $title)
    {
        $clone = clone $this;
        $clone->title = $title;
        return $clone;
    }

    /**
     * @param $key
     * @param $value
     * @return Message
     */
    public function withExtra($key, $value)
    {
        $clone = clone $this;
        $clone->extra[$key] = $value;
        return $clone;
    }
}
