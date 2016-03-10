<?php
namespace Genkgo\Push\Certificate\Apple;

/**
 * Class SigningRequest
 * @package Genkgo\Push\Certificate\Apple
 */
final class SigningRequest
{
    /**
     * @var string
     */
    private $commonName;

    /**
     * @var string
     */
    private $emailAddress;
    /**
     * @var resource
     */
    private $request;
    /**
     * @var PrivateKey
     */
    private $privateKey;
    /**
     * @param PrivateKey $privateKey
     * @param $commonName
     * @param $emailAddress
     */
    public function __construct(PrivateKey $privateKey, $commonName, $emailAddress)
    {
        $this->commonName = $commonName;
        $this->emailAddress = $emailAddress;
        $this->privateKey = $privateKey;
    }

    /**
     *
     */
    private function generate()
    {
        if ($this->request === null) {
            $csr = openssl_csr_new([
                'CN' => $this->commonName,
                'emailAddress' => $this->emailAddress
            ], $this->privateKey->asResource());

            $this->request = $csr;
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $this->generate();

        $output = '';
        openssl_csr_export($this->request, $output);

        return $output;
    }
}
