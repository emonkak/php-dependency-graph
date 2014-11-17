<?php

namespace DependencyGraph;

class DependencyAnalyzer
{
    /**
     * @var array of Service
     */
    private $services = [];

    /**
     * @var array of \ReflectionClass
     */
    private $bindings = [];

    /**
     * @var array of boolean
     */
    private $dynamicTypes = [];

    /**
     * Registers the class to resolve the dependent.
     *
     * @param \ReflectionClass $class
     * @return DependencyAnalyzer
     */
    public function registerClass(\ReflectionClass $class)
    {
        $className = $class->getName();
        if (isset($this->services[$className])) {
            throw new \InvalidArgumentException("`$className` is already registerd.");
        }

        $this->services[$className] = new Service($class, $class);

        return $this;
    }

    /**
     * Registers the type and class to resolve the dependent.
     *
     * @param \ReflectionClass $type
     * @param \ReflectionClass $class
     * @return DependencyAnalyzer
     */
    public function registerType(\ReflectionClass $type, \ReflectionClass $class)
    {
        $typeName = $type->getName();
        if (isset($this->bindings[$typeName])) {
            throw new \InvalidArgumentException("`$typeName` is already registerd.");
        }

        $this->bindings[$typeName] = $class;

        return $this;
    }

    /**
     * Marks to use name-based binding.
     *
     * @param \ReflectionClass $type
     * @return DependencyAnalyzer
     */
    public function markAsDynamic(\ReflectionClass $type)
    {
        $typeName = $type->getName();
        if (isset($this->dynamicTypes[$typeName])) {
            throw new \InvalidArgumentException("`$typeName` is already marked as dynamic.");
        }

        $this->dynamicTypes[$typeName] = true;

        return $this;
    }

    /**
     * @return DependencyGraph
     */
    public function execute()
    {
        $services = $this->services;
        $dependencyGraph = new DependencyGraph(
            function($x) { return (string) $x; },
            function($x, $y) { return (string) $x === (string) $y; }
        );

        do {
            $nextServices = [];

            foreach ($services as $service) {
                $class = $service->getClass();
                $constructor = $class->getConstructor();
                if (!$constructor) {
                    continue;
                }

                $params = $service->isDynamic() ? [] : $constructor->getParameters();
                $dependencies = [];

                foreach ($params as $param) {
                    $paramClass = $param->getClass();
                    if (!$paramClass) {
                        continue;
                    }

                    $paramClassName = $paramClass->getName();
                    if (isset($this->dynamicTypes[$paramClassName])) {
                        $dependency = new NamedService($param->getName(), $paramClass);
                    } elseif (isset($this->bindings[$paramClassName])) {
                        $dependency = new Service($paramClass, $this->bindings[$paramClassName]);
                    } else {
                        $dependency = new Service($paramClass, $paramClass);
                    }

                    $dependencies[] = $nextServices[] = $dependency;
                }

                $dependencyGraph->addObject($service, $dependencies);
            }
        } while (count($services = $nextServices) > 0);

        return $dependencyGraph;
    }
}
