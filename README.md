# PHP Dependency Graph

[![Build Status](https://travis-ci.org/emonkak/php-dependency-graph.png)](https://travis-ci.org/emonkak/php-dependency-graph)
[![Coverage Status](https://coveralls.io/repos/emonkak/php-dependency-graph/badge.png)](https://coveralls.io/r/emonkak/php-dependency-graph)

## Generate the dependency graph

```php
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
}

class Piyo {}
class Payo {}
class Poyo {}

$dependencyGraph = (new DependencyResolver())
    ->registerType(IFoo::class, Foo::class)
    ->registerType(IBar::class, Bar::class)
    ->registerDynamicType(IQux::class)
    ->markAsNamedType(IBaz::class)
    ->execute([Bootstrapper::class]);
```

## Generate the Pimple's service provider source

```php
$serviceProviderGenerator = new ServiceProviderGenerator();
$source = $serviceProviderGenerator->generate('MyServiceProvider', $dependencyGraph);

// Defines the `Bootstrapper` class
eval($source);
```

It generates the following source.

```php
class MyServiceProvider implements \Pimple\ServiceProviderInterface
{
    public function register(\Pimple\Container $c)
    {
        $c['Bootstrapper'] = function($c) { return new \Bootstrapper($c['IFoo'], $c['IBar'], $c['baz1@IBaz'], $c['baz2@IBaz'], $c['IQux']); };
        $c['IFoo'] = function($c) { return new \Foo($c['Piyo'], $c['Payo'], $c['Piyo'], $c['Payo']); };
        $c['IBar'] = function($c) { return new \Bar($c['Poyo'], $c['Poyo']); };
        $c['Piyo'] = function($c) { return new \Piyo(); };
        $c['Payo'] = function($c) { return new \Payo(); };
        $c['Poyo'] = function($c) { return new \Poyo(); };
    }
}
```

## Resolve the 'Bootstrapper' of the previous section

```php
$container = new \Pimple\Container();
(new MyServiceProvider())->register($container);

// Sets the dynamic binding
$container[IQux::class] = function() {
  return new Qux();
};

// Sets named types
$container['baz1@' . IBaz::class] = function() {
    return new Baz(1);
};
$container['baz2@' . IBaz::class] = function() {
    return new Baz(2);
};

// Gets `Bootstrapper` instance
$bootstrapper = $container[Bootstrapper::class];
```

## Display the dependency tree

```php
foreach (new \RecursiveTreeIterator($dependencyGraph) as $object) {
    echo $object, PHP_EOL;
}
```

It displays the following text.

```
\-Bootstrapper
  |-IFoo
  | |-Piyo
  | \-Payo
  |-IBar
  | \-Poyo
  |-baz1@IBaz
  |-baz2@IBaz
  \-IQux
```
