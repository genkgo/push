<?php
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
    public function __construct($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }
}
