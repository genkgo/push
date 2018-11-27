<?php
declare(strict_types=1);

namespace Genkgo\Push\Certificate\Apple;

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
    public function __construct(string $appId, string $appIdId, string $name)
    {
        $this->appId = $appId;
        $this->appIdId = $appIdId;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getAppId(): string
    {
        return $this->appId;
    }

    /**
     * @return string
     */
    public function getAppIdId(): string
    {
        return $this->appIdId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
