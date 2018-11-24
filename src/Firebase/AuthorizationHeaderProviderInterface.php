<?php
declare(strict_types=1);

namespace Genkgo\Push\Firebase;

interface AuthorizationHeaderProviderInterface
{
    public function __invoke(): string;
}
