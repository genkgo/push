<?php
namespace Genkgo\Push\Unit\Certificate;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Certificate\Apple\PrivateKey;
use Genkgo\Push\Certificate\Apple\PushCertificate;
use Genkgo\Push\Certificate\Apple\SignedCertificate;

class PushCertificateTest extends AbstractTestCase
{
    public function test()
    {
        $cert = new PushCertificate(
            new PrivateKey(),
            SignedCertificate::fromBinaryEncodedDer(
                file_get_contents(
                    __DIR__. '/../../Stubs/signed.certificate.cer'
                )
            )
        );

        $this->assertSame(
            (string) $cert,
            (string) $cert->getSignedCertificate() .
            (string) $cert->getPrivateKey()
        );

        $this->assertSame(
            (string) $cert->getSignedCertificate() .
            (string) $cert->getPrivateKey(),
            (string) PushCertificate::fromString(
                (string) $cert->getSignedCertificate() .
                (string) $cert->getPrivateKey()
            )
        );
    }
}
