<?php

namespace DependencyGraph;

class DependencyAnalyzer
{
    /**
     * @var array of Service
     */
    private $bindings = [];

    /**
     * @var array of \ReflectionClass
     */
    private $namedTypes = [];

    /**
     * Registers the type and class to resolve the dependent.
     *
     * @param string $typeName The fully qualified class name.
     * @param string $className The fully qualified class name.
     * @return DependencyAnalyzer
     */
    public function registerType($typeName, $className)
    {
        if (isset($this->bindings[$typeName])) {
            throw new \InvalidArgumentException("`$typeName` is already registerd.");
        }

        $type = new \ReflectionClass($typeName);
        $class = new \ReflectionClass($className);
        $this->bindings[$typeName] = new Service($type, $class);

        return $this;
    }

    /**
     * Registers the type to use dynamic binding.
     *
     * @param string $typeName The fully qualified class name.
     * @return DependencyAnalyzer
     */
    public function registerDynamicType($typeName)
    {
        if (isset($this->bindings[$typeName])) {
            throw new \InvalidArgumentException("`$typeName` is already registerd.");
        }

        $type = new \ReflectionClass($typeName);
        $this->bindings[$typeName] = new DynamicService($type);

        return $this;
    }

    /**
     * Marks to use name-based binding.
     *
     * @param string $typeName The fully qualified class name.
     * @return DependencyAnalyzer
     */
    public function markAsNamedType($typeName)
    {
        if (isset($this->namedTypes[$typeName])) {
            throw new \InvalidArgumentException("`$typeName` is already marked as named type.");
        }

        $type = new \ReflectionClass($typeName);
        $this->namedTypes[$typeName] = $type;

        return $this;
    }

    /**
     * @param array $classNames The class names to resolve the dependent.
     * @return DependencyGraph
     */
    public function execute(array $classNames)
    {
        $dependencyGraph = new DependencyGraph(function($x) { return (string) $x; });
        $services = [];

        foreach ($classNames as $className) {
            $class = new \ReflectionClass($className);
            $services[] = new Service($class, $class);
        }

        do {
            $nextServices = [];

            foreach ($services as $service) {
                $class = $service->getClass();
                $dependencies = [];

                if ($service->isDynamic()) {
                    $params = [];
                } else {
                    $constructor = $class->getConstructor();
                    $params = $constructor ? $constructor->getParameters() : [];
                }

                foreach ($params as $param) {
                    $paramClass = $param->getClass();
                    if (!$paramClass) {
                        throw new \InvalidArgumentException(
                            "The `{$param->getName()}` argument of `{$class->getName()}` is not specified type."
                        );
                    }

                    $paramClassName = $paramClass->getName();
                    if (isset($this->namedTypes[$paramClassName])) {
                        $dependency = new NamedService($param->getName(), $paramClass);
                    } else {
                        $dependency = isset($this->bindings[$paramClassName])
                            ? $this->bindings[$paramClassName]
                            : new Service($paramClass, $paramClass);
                    }

                    $dependencies[] = $nextServices[] = $dependency;
                }

                $dependencyGraph->addObject($service, $dependencies);
            }
        } while (count($services = $nextServices) > 0);

        return $dependencyGraph;
    }
}
