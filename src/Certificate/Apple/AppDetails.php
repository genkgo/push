<?php
namespace Genkgo\Push\Certificate\Apple;

/**
 * Class AppDetails
 * @package Genkgo\Push\Certificate\Apple
 */
final class AppDetails
{
    /**
     * @var string
     */
    private $appId;
    /**
     * @var string
     */
    private $appIdId;
    /**
     * @var string
     */
    private $name;

    /**
     * @param string $appId
     * @param string $appIdId
     * @param string $name
     */
    public function __construct($appId, $appIdId, $name)
    {
        $this->appId = $appId;
        $this->appIdId = $appIdId;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @return string
     */
    public function getAppIdId()
    {
        return $this->appIdId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
