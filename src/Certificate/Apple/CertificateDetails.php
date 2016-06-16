<?php
namespace Genkgo\Push\Certificate\Apple;

/**
 * Class CertificateDetails
 * @package Genkgo\Push\Certificate\Apple
 */
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
    public function __construct($certificateId, $name)
    {
        $this->certificateId = $certificateId;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getCertificateId()
    {
        return $this->certificateId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
