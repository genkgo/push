<?php
declare(strict_types=1);

namespace Genkgo\Push\Certificate\Apple;

final class PrivateKey
{
    /**
     * @var resource
     */
    private $key;

    /**
     * @var string
     */
    private $passphrase;

    /**
     * @param string $passphrase
     */
    public function __construct(string $passphrase = '')
    {
        $this->passphrase = $passphrase;
    }

    private function generate(): void
    {
        if ($this->key === null) {
            $this->key = \openssl_pkey_new([
                'digest_alg' => 'sha1',
                'private_key_bits' => 2048,
                'private_key_type' => OPENSSL_KEYTYPE_RSA
            ]);
        }
    }

    /**
     * @return resource
     */
    public function asResource()
    {
        $this->generate();

        return $this->key;
    }

    /**
     * @return string
     */
    public function getPassphrase(): string
    {
        return $this->passphrase;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $this->generate();

        $output = '';

        if ($this->passphrase !== '') {
            \openssl_pkey_export($this->key, $output, $this->passphrase);
        } else {
            \openssl_pkey_export($this->key, $output);
        }

        return $output;
    }

    /**
     * @param string $pem
     * @param string $passphrase
     * @return PrivateKey
     */
    public static function fromString(string $pem, string $passphrase = ''): self
    {
        $key = \openssl_pkey_get_private($pem, $passphrase);
        if ($key === false) {
            throw new \InvalidArgumentException('Cannot create private key from string');
        }

        $privateKey = new self($passphrase);
        $privateKey->key = $key;
        return $privateKey;
    }
}
