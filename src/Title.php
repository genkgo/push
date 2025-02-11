<?php
declare(strict_types=1);

namespace Genkgo\Push;

final readonly class Title
{
    public function __construct(private string $title)
    {
    }

    public function __toString(): string
    {
        return $this->title;
    }
}
