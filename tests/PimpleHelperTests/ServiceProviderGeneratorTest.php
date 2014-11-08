<?php

namespace PimpleHelperTests;

use PimpleHelper\ServiceProviderGenerator;
use PimpleHelper\ServiceProviderDumper;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProviderGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerate()
    {
        $generator = new ServiceProviderGenerator(new ServiceProviderDumper());
        $generator->registerClass(Bootstrapper::class);
        $generator->registerType(IFoo::class, Foo::class);
        $generator->registerType(IBar::class, Bar::class);
        $generator->markAsDynamic(IBaz::class);

        $serviceProviderClass = 'MyServiceProvider';
        $source = $generator->generate($serviceProviderClass);
        $this->assertInternalType('string', $source);

        eval($source);
        $this->assertTrue(class_exists($serviceProviderClass, false));

        $serviceProviderInstance = new $serviceProviderClass();
        $this->assertInstanceOf(ServiceProviderInterface::class, $serviceProviderInstance);

        $container = new Container();
        $container['baz1@' . IBaz::class] = function() {
            return new Baz(1);
        };
        $container['baz2@' . IBaz::class] = function() {
            return new Baz(2);
        };
        $container['qux@'] = function() {
            return 'qux';
        };
        $serviceProviderInstance->register($container);

        $bootstrapper = $container[Bootstrapper::class];
        $this->assertInstanceOf(Bootstrapper::class, $bootstrapper);
        $this->assertInstanceOf(IFoo::class, $bootstrapper->foo);
        $this->assertInstanceOf(Piyo::class, $bootstrapper->foo->piyo);
        $this->assertInstanceOf(Payo::class, $bootstrapper->foo->payo);
        $this->assertInstanceOf(IBar::class, $bootstrapper->bar);
        $this->assertInstanceOf(Poyo::class, $bootstrapper->bar->poyo);
        $this->assertInstanceOf(IBaz::class, $bootstrapper->baz1);
        $this->assertInstanceOf(IBaz::class, $bootstrapper->baz2);
        $this->assertSame(1, $bootstrapper->baz1->id);
        $this->assertSame(2, $bootstrapper->baz2->id);
        $this->assertSame('qux', $bootstrapper->qux);
    }
}

class Bootstrapper
{
    public function __construct(IFoo $foo, IBar $bar, IBaz $baz1, IBaz $baz2, $qux)
    {
        $this->foo = $foo;
        $this->bar = $bar;
        $this->baz1 = $baz1;
        $this->baz2 = $baz2;
        $this->qux = $qux;
    }
}

interface IFoo {}
interface IBar {}
interface IBaz {}

class Foo implements IFoo
{
    public function __construct(Piyo $piyo, Payo $payo)
    {
        $this->piyo = $piyo;
        $this->payo = $payo;
    }
}

class Bar implements IBar
{
    public function __construct(Poyo $poyo)
    {
        $this->poyo = $poyo;
    }
}

class Baz implements IBaz
{
    public function __construct($id)
    {
        $this->id = $id;
    }
}

class Piyo {}
class Payo {}
class Poyo {}
