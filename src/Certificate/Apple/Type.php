<?php
namespace Genkgo\Push\Certificate\Apple;

use InvalidArgumentException;
use ReflectionClass;

/**
 * Class Type
 * @package Genkgo\Push\Certificate\Apple
 */
final class Type
{
    /**
     *
     */
    const DEVELOPMENT = "BKLRAVXMGM";

    /**
     *
     */
    const PRODUCTION = "UPV3DW712I";

    /**
     * @var string
     */
    private $type;

    /**
     * @param $type
     */
    public function __construct($type)
    {
        if (in_array($type, [self::DEVELOPMENT, self::PRODUCTION]) === false) {
            throw new InvalidArgumentException('Unknown type ' . $type);
        }

        $this->type = $type;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getHumanReadable()
    {
        $class = new ReflectionClass(static::class);
        $constants = array_flip($class->getConstants());

        return ucfirst(strtolower($constants[$this->type]));
    }
}
