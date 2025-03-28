<?php
declare(strict_types=1);

namespace Genkgo\Push\Firebase;

interface AuthorizationHeaderProviderInterface
{
    public function providerHeaderValue(string $scope = 'https://www.googleapis.com/auth/cloud-platform'): string;
}
