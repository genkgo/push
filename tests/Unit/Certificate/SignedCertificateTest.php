<?php
namespace Genkgo\Push\Unit\Certificate;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Certificate\Apple\SignedCertificate;

class SignedCertificateTest extends AbstractTestCase
{
    public function test()
    {
        $signedCertificate = SignedCertificate::fromBinaryEncodedDer(
            file_get_contents(__DIR__. '/../../Stubs/signed.certificate.cer')
        );

        $this->assertTrue(is_resource($signedCertificate->asResource()));
        $this->assertStringStartsWith('-----BEGIN CERTIFICATE-----', (string)$signedCertificate);
    }
}
