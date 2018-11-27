<?php
declare(strict_types=1);

namespace Genkgo\Push\Certificate\Apple;

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
     * @param string $commonName
     * @param string $emailAddress
     */
    public function __construct(PrivateKey $privateKey, string $commonName, string $emailAddress)
    {
        $this->commonName = $commonName;
        $this->emailAddress = $emailAddress;
        $this->privateKey = $privateKey;
    }

    private function generate()
    {
        if ($this->request === null) {
            $commonName = \iconv("UTF-8", "ASCII//TRANSLIT", $this->commonName);
            $privateKeyResource = $this->privateKey->asResource();

            $csr = \openssl_csr_new([
                'CN' => \substr($commonName, 0, 64),
                'emailAddress' => $this->emailAddress
            ], $privateKeyResource);

            $this->request = $csr;
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $this->generate();

        $output = '';
        \openssl_csr_export($this->request, $output);

        return $output;
    }
}
