<?php
declare(strict_types=1);

namespace Genkgo\Push\Apn;

use Apple\ApnPush\Protocol\Http\Authenticator\AuthenticatorInterface;
use Apple\ApnPush\Protocol\Http\Request;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Ecdsa\Sha256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token;

final class JwtAuthenticator implements AuthenticatorInterface
{
    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $keyId;

    /**
     * @var string
     */
    private $teamId;

    /**
     * @var string
     */
    private $refreshAfter;

    /**
     * @var \Iterator<int, Token>
     */
    private $tokenGenerator;

    /**
     * @param string $token
     * @param string $keyId
     * @param string $teamId
     * @param string $refreshAfter
     */
    public function __construct(string $token, string $keyId, string $teamId, string $refreshAfter = 'PT30M')
    {
        $this->token = $token;
        $this->keyId = $keyId;
        $this->teamId = $teamId;
        $this->refreshAfter = $refreshAfter;
        $this->tokenGenerator = $this->newGenerator();
    }

    /**
     * @return \Iterator<int, Token>
     */
    private function newGenerator(): \Iterator
    {
        $now = new \DateTimeImmutable();

        $newToken = function () use (&$now) {
            $keyContent = \file_get_contents($this->token);
            if ($keyContent === false) {
                throw new \UnexpectedValueException('Cannot fetch token content from ' . $this->token . ', file not readable?');
            }

            if ($keyContent === '') {
                throw new \UnexpectedValueException('Cannot fetch token content from ' . $this->token . ', empty file');
            }

            $configuration = Configuration::forSymmetricSigner(
                Sha256::create(),
                InMemory::plainText($keyContent)
            );

            $expiration = $now->add(new \DateInterval('PT1H'));

            $builder = $configuration->builder()
                ->issuedBy($this->teamId)
                ->issuedAt($now)
                ->expiresAt($expiration)
                ->withHeader('kid', $this->keyId);

            if (!\file_exists($this->token)) {
                throw new \UnexpectedValueException('Cannot find token ' . $this->token . ', invalid path');
            }

            return $builder->getToken($configuration->signer(), $configuration->signingKey());
        };

        $lastToken = $newToken();
        while (true) { // @phpstan-ignore-line
            $newNow = new \DateTimeImmutable();

            if ($newNow > $now->add(new \DateInterval($this->refreshAfter))) {
                $now = $newNow;
                $lastToken = $newToken();
            }

            yield $lastToken;
        }
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
        $this->tokenGenerator->next();
        return $request->withHeader('Authorization', \sprintf('Bearer %s', $this->tokenGenerator->current()->toString()));
    }
}
