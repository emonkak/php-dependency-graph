<?php

namespace DependencyGraphTests;

use DependencyGraph\NamedService;

class NamedServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerToString
     */
    public function testToString($expectedString, $class, $name)
    {
        $param = $this->getReflectionParameterMock();
        $param->method('getClass')->willReturn($class);
        $param->method('getName')->willReturn($name);

        $namedService = new NamedService($param);

        $this->assertSame($expectedString, (string) $namedService);
    }

    public function providerToString()
    {
        return [
            ['dateTime@DateTime', new \ReflectionClass(\DateTime::class), 'dateTime'],
            ['namedService@DependencyGraph\NamedService', new \ReflectionClass(NamedService::class), 'namedService'],
            ['foo@var', null, 'foo']
        ];
    }

    public function testGetType()
    {
        $class = new \ReflectionClass(\DateTime::class);
        $param = $this->getReflectionParameterMock();
        $param->method('getClass')->willReturn($class);

        $namedService = new NamedService($param);

        $this->assertSame($class, $namedService->getType());
    }

    public function testGetClass()
    {
        $class = new \ReflectionClass(\DateTime::class);
        $param = $this->getReflectionParameterMock();
        $param->method('getClass')->willReturn($class);

        $namedService = new NamedService($param);

        $this->assertSame($class, $namedService->getClass());
    }

    public function testIsDynamic()
    {
        $param = $this->getReflectionParameterMock();
        $namedService = new NamedService($param);

        $this->assertTrue($namedService->isDynamic());
    }

    private function getReflectionParameterMock()
    {
        return $this->getMockBuilder(\ReflectionParameter::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
