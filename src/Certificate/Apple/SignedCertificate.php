<?php
namespace Genkgo\Push\Certificate\Apple;

/**
 * Class SignedCertificate
 * @package Genkgo\Push\Certificate\Apple
 */
final class SignedCertificate
{
    /**
     * @var string
     */
    private $certificate;

    /**
     * @param string $certificate
     */
    public function __construct($certificate)
    {
        $this->certificate = $certificate;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->certificate;
    }

    /**
     * @return resource
     */
    public function asResource()
    {
        return openssl_x509_read($this->certificate);
    }

    /**
     * @param $der
     * @return SignedCertificate
     */
    public static function fromBinaryEncodedDer($der)
    {
        $pem = '-----BEGIN CERTIFICATE-----' . PHP_EOL
            . chunk_split(base64_encode((string) $der), 64, PHP_EOL)
            . '-----END CERTIFICATE-----' . PHP_EOL
        ;

        return new self($pem);
    }
}
