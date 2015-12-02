<?php
namespace Genkgo\Push;

/**
 * Class Body
 * @package Genkgo\Push
 */
final class Body
{
    /**
     * @var string
     */
    private $body;

    /**
     * @param string $body
     */
    public function __construct($body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->body;
    }
}
