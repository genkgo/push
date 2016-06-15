<?php
namespace Genkgo\Push;

interface RecipientInterface
{
    /**
     * @return string
     */
    public function getToken();

    /**
     * @param $token
     * @return RecipientInterface
     */
    public static function fromString($token);
}
