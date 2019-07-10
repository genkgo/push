<?php
declare(strict_types=1);

namespace Genkgo\Push\Apn;

use Apple\ApnPush\Protocol\Http\Authenticator\AuthenticatorInterface;
use Apple\ApnPush\Protocol\Http\Request;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Ecdsa\Sha256;
use Lcobucci\JWT\Signer\Key;

final class JwtAuthenticator implements AuthenticatorInterface
{
    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $teamId;

    /**
     * @var string
     */
    private $keyId;

    /**
     * @param string $token
     * @param string $keyId
     * @param string $teamId
     */
    public function __construct(string $token, string $keyId, string $teamId)
    {
        $this->token = $token;
        $this->teamId = $teamId;
        $this->keyId = $keyId;
    }

    /**
     * Authenticate request
     *
     * @param Request $request
     *
     * @return Request
     */
    public function authenticate(Request $request): Request
    {
        $now = \time();
        $expiration = $now + (60 * 60);
        $builder = (new Builder())
            ->issuedBy($this->teamId)
            ->issuedAt($now)
            ->expiresAt($expiration)
            ->withClaim('kid', $this->keyId);

        if (!\file_exists($this->token)) {
            throw new \UnexpectedValueException('Cannot find token ' . $this->token . ', invalid path');
        }

        $keyContent = \file_get_contents($this->token);
        if ($keyContent === false) {
            throw new \UnexpectedValueException('Cannot fetch token content from ' . $this->token . ', file not readable?');
        }

        $token = $builder->getToken(new Sha256(), new Key($keyContent));

        return $request->withHeader('authorization', \sprintf('bearer %s', (string)$token));
    }
}
