<?php
declare(strict_types=1);

namespace Genkgo\Push;

interface RecipientInterface
{
    public function getToken(): string;

    public static function fromString(string $token): self;
}
