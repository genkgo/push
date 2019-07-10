<?php
declare(strict_types=1);

namespace Genkgo\Push\Firebase;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;

final class OauthBearerTokenProvider implements AuthorizationHeaderProviderInterface
{
    private const AUTH_ENDPOINT = 'https://www.googleapis.com/oauth2/v4/token';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $serviceAccountFile;

    /**
     * @param ClientInterface $client
     * @param string $serviceAccountFile
     */
    public function __construct(ClientInterface $client, string $serviceAccountFile)
    {
        $this->client = $client;
        $this->serviceAccountFile = $serviceAccountFile;
    }

    /**
     * @return string
     */
    public function __invoke(): string
    {
        $serviceAccount = \file_get_contents($this->serviceAccountFile);
        if ($serviceAccount === false) {
            throw new \UnexpectedValueException('Cannot read service account ' . $this->serviceAccountFile);
        }

        $googleJson = \json_decode($serviceAccount, true);

        $now = \time();
        $expiration = $now + (60 * 60);
        $builder = (new Builder())
            ->issuedBy($googleJson['client_email'])
            ->issuedAt($now)
            ->expiresAt($expiration)
            ->withClaim('scope', 'https://www.googleapis.com/auth/cloud-platform')
            ->permittedFor(self::AUTH_ENDPOINT);

        $authResponse = (string)$this->client
            ->send(
                new Request(
                    'POST',
                    self::AUTH_ENDPOINT,
                    [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                    ],
                    \http_build_query([
                        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                        'assertion' => (string)$builder->getToken(new Sha256(), new Key($googleJson['private_key']))
                    ])
                )
            )
            ->getBody();

        $authTokens = \json_decode($authResponse, true);
        return 'Bearer ' . $authTokens['access_token'];
    }
}
