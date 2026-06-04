<?php
declare(strict_types=1);

namespace Genkgo\Push\Recipient;

use Genkgo\Push\RecipientInterface;

final readonly class AndroidDeviceRecipient implements RecipientInterface
{
    public function __construct(private string $token)
    {
    }

    public function get(string $key): string
    {
        if ($key !== 'token') {
            throw new \InvalidArgumentException('key should be "token"');
        }

        return $this->token;
    }

    public static function fromString(string $token): self
    {
        return new self($token);
    }
}
