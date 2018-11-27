<?php
declare(strict_types=1);

namespace Genkgo\Push\Certificate\Apple;

final class PrivateKey
{
    /**
     * @var resource
     */
    private $key;

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
    public function __toString(): string
    {
        $this->generate();

        $output = '';
        \openssl_pkey_export($this->key, $output);
        return $output;
    }

    /**
     * @param $pem
     * @return PrivateKey
     */
    public static function fromString($pem): self
    {
        $privateKey = new self();
        $privateKey->key = \openssl_pkey_get_private($pem);
        return $privateKey;
    }
}
