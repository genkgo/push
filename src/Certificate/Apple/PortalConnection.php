<?php
namespace Genkgo\Push\Certificate\Apple;

use Genkgo\Push\Exception\ApplePortalException;
use Genkgo\Push\Exception\ApplicationAlreadyHasPushCertificateException;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

final class PortalConnection
{
    /**
     * @var Client
     */
    private $client;
    /**
     * @var string
     */
    private $appleId;
    /**
     * @var string
     */
    private $password;
    /**
     * @var string
     */
    private $teamId;
    /**
     * @var string
     */
    private $appIdKey;
    /**
     * @var CookieJar
     */
    private $cookieJar;
    /**
     * @var array
     */
    private $csrfTokens = [];
    /**
     * @var array
     */
    private $apps;
    /**
     * @var array
     */
    private $certificates;

    /**
     * @param Client $client
     * @param $appleId
     * @param $password
     * @param $teamId
     */
    public function __construct(Client $client, $appleId, $password, $teamId)
    {
        $this->client = $client;
        $this->teamId = $teamId;
        $this->password = $password;
        $this->appleId = $appleId;
    }

    /**
     * @return string
     */
    public function getAppleId()
    {
        return $this->appleId;
    }

    /**
     * @throws ApplePortalException
     */
    private function initialize()
    {
        if ($this->appIdKey === null) {
            $this->cookieJar = new CookieJar();

            $appIdKeyResponse = $this->client->get(
                'https://developer.apple.com/account/',
                [
                    'allow_redirects' => false,
                    'cookies' => $this->cookieJar
                ]
            );

            $appIdKeyHeader = $appIdKeyResponse->getHeader('Location')[0];
            $url = parse_url($appIdKeyHeader);
            parse_str($url['query'], $query);
            $this->appIdKey = $query['appIdKey'];

            $loginResponse = $this->client->post(
                'https://idmsa.apple.com/IDMSWebAuth/authenticate',
                [
                    'cookies' => $this->cookieJar,
                    'form_params' => [
                        'appleId' => $this->appleId,
                        'accountPassword' => $this->password,
                        'appIdKey' => $this->appIdKey
                    ]
                ]
            );

            if (strpos((string) $loginResponse->getBody(), 'entered incorrectly') !== false) {
                throw new ApplePortalException('Incorrect login details');
            }

            $csrfResponse = $this->client->post(
                'https://developer.apple.com/services-account/QH65B2/account/ios/identifiers/listAppIds.action',
                [
                    'cookies' => $this->cookieJar,
                    'form_params' => [
                        'teamId' => $this->teamId,
                        'pageNumber' => 1,
                        'pageSize' => 30,
                        'sort' => 'name=asc'
                    ]
                ]
            );

            $this->csrfTokens['csrf'] = $csrfResponse->getHeader('csrf')[0];
            $this->csrfTokens['csrf_ts'] = $csrfResponse->getHeader('csrf_ts')[0];
        }
    }

    /**
     * @param $appId
     * @return AppDetails
     * @throws ApplePortalException
     */
    public function fetchApp($appId)
    {
        $apps = $this->fetchApps();
        if (isset($apps[$appId])) {
            return $apps[$appId];
        }

        throw new ApplePortalException('Cannot find app with appId' . $appId);
    }

    /**
     * @return array|AppDetails[]
     * @throws ApplePortalException
     */
    public function fetchApps()
    {
        $this->initialize();

        if ($this->apps === null) {
            $this->apps = [];
            $pageSize = 500;
            $pageNumber = 1;
            do {
                $appsResponse = json_decode((string) $this->client->post(
                    'https://developer.apple.com/services-account/QH65B2/account/ios/identifiers/listAppIds.action',
                    [
                        'cookies' => $this->cookieJar,
                        'form_params' => [
                            'teamId' => $this->teamId,
                            'pageNumber' => $pageNumber,
                            'pageSize' => $pageSize,
                            'sort' => 'name=asc'
                        ]
                    ]
                )->getBody(), true);

                $pageNumber++;

                foreach ($appsResponse['appIds'] as $appIdData) {
                    $this->apps[$appIdData['identifier']] = new AppDetails(
                        $appIdData['identifier'],
                        $appIdData['appIdId'],
                        $appIdData['name']
                    );
                }
            } while (count($appsResponse['appIds']) === $pageSize);
        }

        return $this->apps;
    }

    /**
     * @param Type $type
     * @return array|CertificateDetails[]
     * @throws ApplePortalException
     */
    public function fetchCertificates(Type $type)
    {
        $this->initialize();

        if ($this->certificates === null) {
            $this->certificates = [];
            $pageSize = 500;
            $pageNumber = 1;
            do {
                $certificatesResponse = json_decode((string) $this->client->post(
                    'https://developer.apple.com/services-account/QH65B2/account/ios/certificate/listCertRequests.action',
                    [
                        'cookies' => $this->cookieJar,
                        'form_params' => [
                            'teamId' => $this->teamId,
                            'pageNumber' => $pageNumber,
                            'pageSize' => $pageSize,
                            'types' => (string) $type,
                            'sort' => 'name=asc'
                        ]
                    ]
                )->getBody(), true);

                $pageNumber++;

                foreach ($certificatesResponse['certRequests'] as $certData) {
                    $this->certificates[$certData['certificateId']] = new CertificateDetails(
                        $certData['certificateId'],
                        $certData['name']
                    );
                }
            } while (count($certificatesResponse['certRequests']) === $pageSize);
        }

        return $this->certificates;
    }

    /**
     * @param SigningRequest $request
     * @param Type $type
     * @param $appIdId
     * @return SignedCertificate
     * @throws ApplePortalException
     */
    public function signCertificate(SigningRequest $request, Type $type, $appIdId)
    {
        $this->initialize();

        $createCertificateResponse = $this->client->post(
            'https://developer.apple.com/services-account/QH65B2/account/ios/certificate/submitCertificateRequest.action',
            [
                'cookies' => $this->cookieJar,
                'headers' => $this->csrfTokens,
                'form_params' => [
                    'teamId' => $this->teamId,
                    'type' => (string) $type,
                    'csrContent' => (string) $request,
                    'appIdId' => $appIdId,
                ]
            ]
        );

        $certificatePayload = (string) $createCertificateResponse->getBody();
        if (strpos($certificatePayload, 'already')) {
            throw new ApplicationAlreadyHasPushCertificateException(
                'There are too many push certificates for this app'
            );
        }

        $certificateJson = json_decode($certificatePayload, true);
        $certificateId = $certificateJson['certRequest']['certificate']['certificateId'];

        $certificateDownloadResponse = $this->client->get(
            'https://developer.apple.com/services-account/QH65B2/account/ios/certificate/downloadCertificateContent.action',
            [
                'cookies' => $this->cookieJar,
                'headers' => $this->csrfTokens,
                'query' => [
                    'teamId' => $this->teamId,
                    'type' => (string) $type,
                    'certificateId' => $certificateId,
                ]
            ]
        );

        return SignedCertificate::fromBinaryEncodedDer((string) $certificateDownloadResponse->getBody());
    }

    public function revokeCertificate($type, $certificateId)
    {
        $certificateRevokeResponse = $this->client->post(
            'https://developer.apple.com/services-account/QH65B2/account/ios/certificate/revokeCertificate.action',
            [
                'cookies' => $this->cookieJar,
                'headers' => $this->csrfTokens,
                'form_params' => [
                    'teamId' => $this->teamId,
                    'type' => (string) $type,
                    'certificateId' => $certificateId,
                ]
            ]
        );

        return $certificateRevokeResponse->getStatusCode() === 200;
    }
}
