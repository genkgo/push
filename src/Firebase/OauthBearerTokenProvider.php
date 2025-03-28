<?php
declare(strict_types=1);

namespace Genkgo\Push\Firebase;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

final readonly class OauthBearerTokenProvider implements AuthorizationHeaderProviderInterface
{
    private const string AUTH_ENDPOINT = 'https://www.googleapis.com/oauth2/v4/token';

    public function __construct(
        private ClientInterface $client,
        private RequestFactoryInterface&StreamFactoryInterface $requestFactory,
        private string $serviceAccountFile
    ) {
    }

    public function providerHeaderValue(string $scope = 'https://www.googleapis.com/auth/cloud-platform'): string
    {
        $serviceAccount = \file_get_contents($this->serviceAccountFile);
        if ($serviceAccount === false) {
            throw new \UnexpectedValueException('Cannot read service account ' . $this->serviceAccountFile);
        }

        /** @var array{private_key: non-empty-string, client_email: non-empty-string}|false $googleJson */
        $googleJson = \json_decode($serviceAccount, true);
        if (!$googleJson) {
            throw new \UnexpectedValueException('Invalid service account file ' . $this->serviceAccountFile . ' passed, cannot decode json.');
        }

        $configuration = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($googleJson['private_key'])
        );

        $now = new \DateTimeImmutable();
        $expiration = $now->add(new \DateInterval('PT' . (60 * 60) . 'S'));

        $builder = $configuration->builder()
            ->issuedBy($googleJson['client_email'])
            ->issuedAt($now)
            ->expiresAt($expiration)
            ->withClaim('scope', $scope)
            ->permittedFor(self::AUTH_ENDPOINT);

        $authResponse = (string)$this->client
            ->sendRequest(
                $this->requestFactory->createRequest(
                    'POST',
                    self::AUTH_ENDPOINT,
                )
                    ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
                    ->withBody(
                        $this->requestFactory->createStream(
                            \http_build_query([
                                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                                'assertion' => $builder->getToken(
                                    $configuration->signer(),
                                    $configuration->signingKey()
                                )->toString()
                            ])
                        )
                    )
            )
            ->getBody();

        /** @var array{access_token: non-empty-string}|false $authTokens */
        $authTokens = \json_decode($authResponse, true);

        if (!$authTokens) {
            throw new \UnexpectedValueException('Expecting key `access_token` from Oauth request.');
        }

        return 'Bearer ' . $authTokens['access_token'];
    }
}
