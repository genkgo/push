<?php
declare(strict_types=1);

namespace Genkgo\Push;

final class Body
{
    /**
     * @var string
     */
    private $body;

    /**
     * @param string $body
     */
    public function __construct(string $body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->body;
    }
}
