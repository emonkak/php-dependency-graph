<?php

namespace DependencyGraph\Tests;

use DependencyGraph\DynamicService;

class DynamicServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testToString()
    {
        $class = new \ReflectionClass(\DateTime::class);
        $dynamicService = new DynamicService($class);

        $this->assertSame('DateTime', (string) $dynamicService);
    }

    public function testGetType()
    {
        $class = new \ReflectionClass(\DateTime::class);
        $dynamicService = new DynamicService($class);

        $this->assertSame($class, $dynamicService->getType());
    }

    public function testGetClass()
    {
        $class = new \ReflectionClass(\DateTime::class);
        $dynamicService = new DynamicService($class);

        $this->assertSame($class, $dynamicService->getClass());
    }

    public function testIsDynamic()
    {
        $class = new \ReflectionClass(\DateTime::class);
        $dynamicService = new DynamicService($class);

        $this->assertTrue($dynamicService->isDynamic());
    }
}
