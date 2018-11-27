<?php
declare(strict_types=1);

namespace Genkgo\Push;

final class Title
{
    /**
     * @var string
     */
    private $title;

    /**
     * @param string $title
     */
    public function __construct(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->title;
    }
}
