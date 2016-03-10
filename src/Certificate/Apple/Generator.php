<?php
namespace Genkgo\Push\Certificate\Apple;

/**
 * Class Generator
 * @package Genkgo\Push\Certificate\Apple
 */
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
     * @param $appId
     * @return PushCertificate
     */
    public function generate(Type $type, $appId)
    {
        $app = $this->portalConnection->fetchApp($appId);
        $privateKey = new PrivateKey();

        $csrCommonName = $app->getName() . ' ' . $type->getHumanReadable() . ' Push Certificate';
        $signingRequest = new SigningRequest($privateKey, $csrCommonName, $this->portalConnection->getAppleId());

        return new PushCertificate(
            $privateKey,
            $this->portalConnection->signCertificate($signingRequest, $type, $app->getAppIdId())
        );
    }
}
