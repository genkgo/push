<?php
declare(strict_types=1);

namespace Genkgo\Push\Apn;

use Apple\ApnPush\Protocol\Http\Authenticator\AuthenticatorInterface;
use Apple\ApnPush\Protocol\Http\Request;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Ecdsa\MultibyteStringConverter;
use Lcobucci\JWT\Signer\Ecdsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token;

final class JwtAuthenticator implements AuthenticatorInterface
{
    /** @var \Iterator<int, Token>  */
    private \Iterator $tokenGenerator;

    public function __construct(
        /** @var non-empty-string */
        private readonly string $token,
        /** @var non-empty-string */
        private readonly string $keyId,
        /** @var non-empty-string */
        private readonly string $teamId,
        /** @var non-empty-string */
        private readonly string $refreshAfter = 'PT30M'
    ) {
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

            // support both lcobucci/jwt 4 and 5
            $configuration = Configuration::forSymmetricSigner(
                new Sha256(new MultibyteStringConverter()),
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
