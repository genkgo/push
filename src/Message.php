<?php
namespace Genkgo\Push;

/**
 * Class Message
 * @package Genkgo\Push
 */
final class Message {

    /**
     * @var Body
     */
    private $body;
    /**
     * @var Title
     */
    private $title;

    /**
     * @param Body $body
     */
    public function __construct (Body $body) {
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
     * @param Body $body
     * @return Message
     */
    public function setBody(Body $body)
    {
        $clone = clone $this;
        $clone->body = $body;
        return $clone;
    }

    /**
     * @return Title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param Title $title
     * @return Message
     */
    public function setTitle(Title $title)
    {
        $clone = clone $this;
        $clone->title = $title;
        return $clone;
    }

}