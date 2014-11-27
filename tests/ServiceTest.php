<?php

namespace DependencyGraph\Tests;

use DependencyGraph\Service;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotInstantiableClassGiven()
    {
        new Service(
            new \ReflectionClass(\Iterator::class),
            new \ReflectionClass(\Iterator::class)
        );
    }

    public function testToString()
    {
        $type = new \ReflectionClass(\Iterator::class);
        $class = new \ReflectionClass(\ArrayIterator::class);
        $service = new Service($type, $class);

        $this->assertSame('Iterator', (string) $service);
    }

    public function testGetType()
    {
        $type = new \ReflectionClass(\Iterator::class);
        $class = new \ReflectionClass(\ArrayIterator::class);
        $service = new Service($type, $class);

        $this->assertSame($type, $service->getType());
    }

    public function testGetClass()
    {
        $type = new \ReflectionClass(\Iterator::class);
        $class = new \ReflectionClass(\ArrayIterator::class);
        $service = new Service($type, $class);

        $this->assertSame($class, $service->getClass());
    }

    public function testIsDynamic()
    {
        $type = new \ReflectionClass(\Iterator::class);
        $class = new \ReflectionClass(\ArrayIterator::class);
        $service = new Service($type, $class);

        $this->assertFalse($service->isDynamic());
    }
}
