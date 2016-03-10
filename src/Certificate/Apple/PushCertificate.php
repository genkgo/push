<?php
namespace Genkgo\Push\Certificate\Apple;

/**
 * Class PushCertificate
 * @package Genkgo\Push\Certificate\Apple
 */
final class PushCertificate
{
    /**
     * @var PrivateKey
     */
    private $privateKey;
    /**
     * @var SignedCertificate
     */
    private $signedCertificate;

    /**
     * @param PrivateKey $privateKey
     * @param SignedCertificate $signedCertificate
     */
    public function __construct(PrivateKey $privateKey, SignedCertificate $signedCertificate)
    {
        $this->privateKey = $privateKey;
        $this->signedCertificate = $signedCertificate;
    }

    /**
     * @return PrivateKey
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * @return SignedCertificate
     */
    public function getSignedCertificate()
    {
        return $this->signedCertificate;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->signedCertificate . (string) $this->privateKey;
    }

    /**
     * @param $concatenated
     * @return PushCertificate
     */
    public static function fromString($concatenated)
    {
        list($signedCertificate, $privateKey) = explode('-' . PHP_EOL . '-', $concatenated);

        return new self(
            PrivateKey::fromString('-' . $privateKey),
            new SignedCertificate($signedCertificate . '-'  . PHP_EOL)
        );
    }
}
