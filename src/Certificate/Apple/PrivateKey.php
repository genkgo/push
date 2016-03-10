<?php
namespace Genkgo\Push\Certificate\Apple;

/**
 * Class PrivateKey
 * @package Genkgo\Push\Certificate\Apple
 */
final class PrivateKey
{
    /**
     * @var resource
     */
    private $key;

    /**
     *
     */
    private function generate()
    {
        if ($this->key === null) {
            $this->key = openssl_pkey_new([
                'digest_alg' => 'sha1',
                'private_key_bits' => 2048,
                'private_key_type' => OPENSSL_KEYTYPE_RSA
            ]);
        }
    }

    /**
     * @return mixed
     */
    public function asResource()
    {
        $this->generate();

        return $this->key;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $this->generate();

        $output = '';
        openssl_pkey_export($this->key, $output);
        return $output;
    }

    /**
     * @param $pem
     * @return PrivateKey
     */
    public static function fromString($pem)
    {
        $privateKey = new self();
        $privateKey->key = openssl_pkey_get_private($pem);
        return $privateKey;
    }
}
