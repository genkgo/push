<?php
declare(strict_types=1);

namespace Genkgo\Push\Certificate\Apple;

final class Generator
{
    /**
     * @var PortalConnection
     */
    private $portalConnection;

    /**
     * @param PortalConnection $portalConnection
     */
    public function __construct(PortalConnection $portalConnection)
    {
        $this->portalConnection = $portalConnection;
    }

    /**
     * @param Type $type
     * @param string $appId
     * @return CombinedCertificate
     */
    public function generate(Type $type, string $appId): CombinedCertificate
    {
        $app = $this->portalConnection->fetchApp($appId);
        $privateKey = new PrivateKey();

        $csrCommonName = $app->getName() . ' ' . $type->getHumanReadable() . ' Push Certificate';
        $signingRequest = new SigningRequest($privateKey, $csrCommonName, $this->portalConnection->getAppleId());

        return new CombinedCertificate(
            $privateKey,
            $this->portalConnection->signCertificate($signingRequest, $type, $app->getAppIdId())
        );
    }

    /**
     * @param Type $type
     * @param string $appId
     */
    public function revoke(Type $type, string $appId): void
    {
        $app = $this->portalConnection->fetchApp($appId);

        /** @var CertificateDetails[] $certificates */
        $certificates = \array_filter(
            $this->portalConnection->fetchCertificates($type),
            function (CertificateDetails $certificateDetails) use ($app) {
                return $certificateDetails->getName() === $app->getAppId();
            }
        );

        foreach ($certificates as $certificate) {
            $this->portalConnection->revokeCertificate($type, $certificate->getCertificateId());
        }
    }
}
