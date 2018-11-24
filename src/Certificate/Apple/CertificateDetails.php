<?php
declare(strict_types=1);

namespace Genkgo\Push\Certificate\Apple;

final class CertificateDetails
{
    /**
     * @var string
     */
    private $certificateId;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $certificateId
     * @param string $name
     */
    public function __construct(string $certificateId, string $name)
    {
        $this->certificateId = $certificateId;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getCertificateId(): string
    {
        return $this->certificateId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
