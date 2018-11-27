<?php
declare(strict_types=1);

namespace Genkgo\Push\Certificate\Apple;

use InvalidArgumentException;
use ReflectionClass;

final class Type
{
    public const DEVELOPMENT = "BKLRAVXMGM";

    public const PRODUCTION = "UPV3DW712I";

    /**
     * @var string
     */
    private $type;

    /**
     * @param $type
     */
    public function __construct(string $type)
    {
        if (\in_array($type, [self::DEVELOPMENT, self::PRODUCTION]) === false) {
            throw new InvalidArgumentException('Unknown type ' . $type);
        }

        $this->type = $type;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getHumanReadable(): string
    {
        $class = new ReflectionClass(static::class);
        $constants = \array_flip($class->getConstants());

        return \ucfirst(\strtolower($constants[$this->type]));
    }
}
