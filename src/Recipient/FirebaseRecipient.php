<?php
declare(strict_types=1);

namespace Genkgo\Push\Recipient;

use Genkgo\Push\RecipientInterface;

final readonly class FirebaseRecipient implements RecipientInterface
{
    public function __construct(private string $token)
    {
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public static function fromString(string $token): self
    {
        return new self($token);
    }
}
