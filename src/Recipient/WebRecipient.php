<?php
declare(strict_types=1);

namespace Genkgo\Push\Recipient;

use Genkgo\Push\RecipientInterface;

final readonly class WebRecipient implements RecipientInterface
{
    public function __construct(
        private string $endpoint,
        private string $auth,
        private string $p256dh
    ) {
    }

    public function get(string $key): string
    {
        return match ($key) {
            'endpoint' => $this->endpoint,
            'keys.auth' => $this->auth,
            'keys.p256dh' => $this->p256dh,
            default => throw new \InvalidArgumentException('Unknown key ' . $key),
        };
    }
}
