<?php
declare(strict_types=1);

namespace Genkgo\Push\Certificate\Apple;

final class SignedCertificate
{
    /**
     * @var string
     */
    private $certificate;

    /**
     * @param string $certificate
     */
    public function __construct(string $certificate)
    {
        $this->certificate = $certificate;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->certificate;
    }

    /**
     * @return resource
     */
    public function asResource()
    {
        return \openssl_x509_read($this->certificate);
    }

    /**
     * @param string $der
     * @return SignedCertificate
     */
    public static function fromBinaryEncodedDer(string $der): self
    {
        $pem = '-----BEGIN CERTIFICATE-----' . PHP_EOL
            . \chunk_split(\base64_encode((string)$der), 64, PHP_EOL)
            . '-----END CERTIFICATE-----' . PHP_EOL;

        return new self($pem);
    }
}
