<?php
declare(strict_types=1);

namespace Genkgo\Push\Certificate\Apple;

final class CombinedCertificate
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
    public function getPrivateKey(): PrivateKey
    {
        return $this->privateKey;
    }

    /**
     * @return SignedCertificate
     */
    public function getSignedCertificate(): SignedCertificate
    {
        return $this->signedCertificate;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->signedCertificate . (string)$this->privateKey;
    }

    /**
     * @param string $concatenated
     * @return CombinedCertificate
     */
    public static function fromString(string $concatenated): self
    {
        list($signedCertificate, $privateKey) = \explode('-' . PHP_EOL . '-', $concatenated);

        return new self(
            PrivateKey::fromString('-' . $privateKey),
            new SignedCertificate($signedCertificate . '-' . PHP_EOL)
        );
    }
}
