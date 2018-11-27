<?php
declare(strict_types=1);

namespace Genkgo\Push;

interface RecipientInterface
{
    /**
     * @return string
     */
    public function getToken(): string;

    /**
     * @param string $token
     * @return RecipientInterface
     */
    public static function fromString(string $token): RecipientInterface;
}
