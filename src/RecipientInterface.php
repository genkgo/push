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
     * @param $token
     * @return RecipientInterface
     */
    public static function fromString($token): RecipientInterface;
}
