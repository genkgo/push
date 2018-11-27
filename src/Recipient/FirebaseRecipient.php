<?php
declare(strict_types=1);

namespace Genkgo\Push\Recipient;

use Genkgo\Push\RecipientInterface;

final class FirebaseRecipient implements RecipientInterface
{
    /**
     * @var string
     */
    private $token;

    /**
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return RecipientInterface
     */
    public static function fromString(string $token): RecipientInterface
    {
        return new self($token);
    }
}
