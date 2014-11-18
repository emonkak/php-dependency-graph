<?php

namespace DependencyGraphTests;

use DependencyGraph\DependencyAnalyzer;
use DependencyGraph\ServiceProviderGenerator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProviderGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerate()
    {
        $serviceProviderGenerator = new ServiceProviderGenerator();
        $serviceProviderClass = 'MyServiceProvider';
        $dependencyGraph = (new DependencyAnalyzer())
            ->registerClass(Bootstrapper::class)
            ->registerType(IFoo::class, Foo::class)
            ->registerType(IBar::class, Bar::class)
            ->markAsNamedType(IBaz::class)
            ->markAsDynamic(IQux::class)
            ->execute();

        $source = $serviceProviderGenerator->generate($serviceProviderClass, $dependencyGraph);
        $this->assertInternalType('string', $source);

        eval($source);
        $this->assertTrue(class_exists($serviceProviderClass, false));

        $serviceProviderInstance = new $serviceProviderClass();
        $this->assertInstanceOf(ServiceProviderInterface::class, $serviceProviderInstance);

        $container = new Container();
        $serviceProviderInstance->register($container);

        $this->assertTrue(isset($container[Bootstrapper::class]));
        $this->assertTrue(isset($container[IFoo::class]));
        $this->assertTrue(isset($container[IBar::class]));
        $this->assertFalse(isset($container[IBaz::class]));
        $this->assertFalse(isset($container[IQux::class]));
        $this->assertFalse(isset($container[Foo::class]));
        $this->assertFalse(isset($container[Bar::class]));
        $this->assertFalse(isset($container[Baz::class]));
        $this->assertFalse(isset($container[Qux::class]));
        $this->assertTrue(isset($container[Piyo::class]));
        $this->assertTrue(isset($container[Payo::class]));
        $this->assertTrue(isset($container[Poyo::class]));

        $container[IQux::class] = function() {
            return new Qux();
        };
        $container['baz1@' . IBaz::class] = function() {
            return new Baz(1);
        };
        $container['baz2@' . IBaz::class] = function() {
            return new Baz(2);
        };

        $bootstrapper = $container[Bootstrapper::class];
        $this->assertInstanceOf(Bootstrapper::class, $bootstrapper);
        $this->assertInstanceOf(IFoo::class, $bootstrapper->foo);
        $this->assertInstanceOf(IBar::class, $bootstrapper->bar);
        $this->assertInstanceOf(IBaz::class, $bootstrapper->baz1);
        $this->assertInstanceOf(IBaz::class, $bootstrapper->baz2);
        $this->assertInstanceOf(IQux::class, $bootstrapper->qux);
        $this->assertInstanceOf(Piyo::class, $bootstrapper->foo->piyo);
        $this->assertInstanceOf(Payo::class, $bootstrapper->foo->payo);
        $this->assertInstanceOf(Poyo::class, $bootstrapper->bar->poyo);
        $this->assertSame(1, $bootstrapper->baz1->id);
        $this->assertSame(2, $bootstrapper->baz2->id);
    }
}

class Bootstrapper
{
    public function __construct(IFoo $foo, IBar $bar, IBaz $baz1, IBaz $baz2, IQux $qux)
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
interface IQux {}

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

class Qux implements IQux
{
    public function __construct()
    {
    }
}

class Piyo {}
class Payo {}
class Poyo {}
