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
     * @var string
     */
    private $identifier;

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
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
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
     * @param int $identifier
     * @return Message
     */
    public function withIdentifier($identifier)
    {
        if (filter_var($identifier, FILTER_VALIDATE_INT) === false) {
            throw new \InvalidArgumentException('Identifier must be an integer');
        }

        $clone = clone $this;
        $clone->identifier = $identifier;
        return $clone;
    }
}
