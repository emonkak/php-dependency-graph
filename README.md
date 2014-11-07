# Pimple Service Provider Generator [![Build Status](https://travis-ci.org/emonkak/pimple-service-provider-generator.png)](https://travis-ci.org/emonkak/pimple-service-provider-generator)

## Example

```php
$generator = new ServiceProviderGenerator();
$generator->registerClass(Bootstrapper::class);
$generator->registerType(IFoo::class, Foo::class);
$generator->registerType(IBar::class, Bar::class);
$generator->markAsDynamic(IBaz::class);

// Generate the service provider source
$generator->generate('MyServiceProvider');

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
```

It generates the following code.

```php
class MyServiceProvider implements \Pimple\ServiceProviderInterface
{
    public function register(\Pimple\Container $c)
    {
        $c['Bootstrapper'] = function($c) { return new \Bootstrapper($c['IFoo'], $c['IBar'], $c['baz1@IBaz'], $c['baz2@IBaz'], $c['qux@']); };
        $c['IFoo'] = function($c) { return new \Foo($c['Piyo'], $c['Payo']); };
        $c['IBar'] = function($c) { return new \Bar($c['Poyo']); };
        $c['Piyo'] = function($c) { return new \Piyo(); };
        $c['Payo'] = function($c) { return new \Payo(); };
        $c['Poyo'] = function($c) { return new \Poyo(); };
    }
}
```
