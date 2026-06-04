<?php
declare(strict_types=1);

namespace Genkgo\Push;

interface RecipientInterface
{
    public function get(string $key): string;
}
