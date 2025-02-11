<?php
declare(strict_types=1);

namespace Genkgo\Push;

final readonly class Body
{
    public function __construct(private string $body)
    {
    }

    public function __toString(): string
    {
        return $this->body;
    }
}
