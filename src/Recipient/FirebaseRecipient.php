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
     * @param $token
     * @return RecipientInterface
     */
    public static function fromString($token): RecipientInterface
    {
        return new self($token);
    }
}
