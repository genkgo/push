<?php
namespace Genkgo\Push\Unit\Certificate;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Certificate\Apple\AppDetails;

class AppDetailsTest extends AbstractTestCase
{
    public function test()
    {
        $app = new AppDetails('app1', 'A1', 'First App');
        $this->assertEquals('app1', $app->getAppId());
        $this->assertEquals('A1', $app->getAppIdId());
        $this->assertEquals('First App', $app->getName());
    }
}
