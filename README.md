# PHP Dependency Graph

[![Build Status](https://travis-ci.org/emonkak/php-dependency-graph.png)](https://travis-ci.org/emonkak/php-dependency-graph)
[![Coverage Status](https://coveralls.io/repos/emonkak/php-dependency-graph/badge.png)](https://coveralls.io/r/emonkak/php-dependency-graph)

## Generate the dependency graph

```php
$dependencyGraph = (new DependencyAnalyzer())
    ->registerClass(new \ReflectionClass(Bootstrapper::class))
    ->registerType(new \ReflectionClass(IFoo::class), new \ReflectionClass(Foo::class))
    ->registerType(new \ReflectionClass(IBar::class), new \ReflectionClass(Bar::class))
    ->markAsDynamic(new \ReflectionClass(IBaz::class))
    ->execute();

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
```

## Generate the service provider source

```php
(new ServiceProviderGenerator())->generate('MyServiceProvider', $dependencyGraph);
```

It generates the following code.

```php
class MyServiceProvider implements \Pimple\ServiceProviderInterface
{
    public function register(\Pimple\Container $c)
    {
        $c['Bootstrapper'] = function($c) { return new \Bootstrapper($c['IFoo'], $c['IBar'], $c['baz1@IBaz'], $c['baz2@IBaz']; };
        $c['IFoo'] = function($c) { return new \Foo($c['Piyo'], $c['Payo']); };
        $c['IBar'] = function($c) { return new \Bar($c['Poyo']); };
        $c['Piyo'] = function($c) { return new \Piyo(); };
        $c['Payo'] = function($c) { return new \Payo(); };
        $c['Poyo'] = function($c) { return new \Poyo(); };
    }
}
```

## Display the dependency tree

```php
foreach (new \RecursiveTreeIterator($dependencyGraph) as $object) {
    echo $object, PHP_EOL;
}
```

It echo the following text.

```
\-Bootstrapper
  |-IFoo
  | |-Piyo
  | \-Payo
  |-IBar
  | \-Poyo
  |-baz1@IBaz
  \-baz2@IBaz
```
