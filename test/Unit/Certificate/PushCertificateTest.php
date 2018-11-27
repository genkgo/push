<?php
declare(strict_types=1);

namespace Genkgo\Push\Unit\Certificate;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Certificate\Apple\PrivateKey;
use Genkgo\Push\Certificate\Apple\CombinedCertificate;
use Genkgo\Push\Certificate\Apple\SignedCertificate;

final class PushCertificateTest extends AbstractTestCase
{
    public function test()
    {
        $cert = new CombinedCertificate(
            new PrivateKey(),
            SignedCertificate::fromBinaryEncodedDer(
                \file_get_contents(
                    __DIR__ . '/../../Stubs/signed.certificate.cer'
                )
            )
        );

        $this->assertSame(
            (string)$cert,
            (string)$cert->getSignedCertificate() .
            (string)$cert->getPrivateKey()
        );

        $this->assertSame(
            (string)$cert->getSignedCertificate() .
            (string)$cert->getPrivateKey(),
            (string)CombinedCertificate::fromString(
                (string)$cert->getSignedCertificate() .
                (string)$cert->getPrivateKey()
            )
        );
    }
}
