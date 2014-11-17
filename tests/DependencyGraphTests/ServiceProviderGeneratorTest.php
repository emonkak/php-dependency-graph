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
        $dependencyAnalyzer = (new DependencyAnalyzer())
            ->registerClass(new \ReflectionClass(Bootstrapper::class))
            ->registerType(new \ReflectionClass(IFoo::class), new \ReflectionClass(Foo::class))
            ->registerType(new \ReflectionClass(IBar::class), new \ReflectionClass(Bar::class))
            ->markAsDynamic(new \ReflectionClass(IBaz::class));

        $serviceProviderGenerator = new ServiceProviderGenerator();
        $serviceProviderClass = 'MyServiceProvider';
        $dependencyGraph = $dependencyAnalyzer->execute();

        $source = $serviceProviderGenerator->generate($serviceProviderClass, $dependencyGraph);
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
    }
}

class Bootstrapper
{
    public function __construct(IFoo $foo, IBar $bar, IBaz $baz1, IBaz $baz2)
    {
        $this->foo = $foo;
        $this->bar = $bar;
        $this->baz1 = $baz1;
        $this->baz2 = $baz2;
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
