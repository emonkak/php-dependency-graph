<?php

namespace DependencyGraph;

class DependencyResolver
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
     * @var array
     */
    private $namedValues = [];

    /**
     * Registers the type and class to resolve the dependent.
     *
     * @param string $typeName The fully qualified class name.
     * @param string $className The fully qualified class name.
     * @return DependencyResolver
     */
    public function registerType($typeName, $className)
    {
        if (isset($this->bindings[$typeName])) {
            throw new \InvalidArgumentException("`$typeName` is already registerd.");
        }

        $type = new \ReflectionClass($typeName);
        $class = new \ReflectionClass($className);
        if (!$class->isSubclassOf($type)) {
            throw new \InvalidArgumentException("`$className` is not sub-class of `$typeName`");
        }

        $this->bindings[$typeName] = new Service($type, $class);

        return $this;
    }

    /**
     * Registers the type to use dynamic binding.
     *
     * @param string $typeName The fully qualified class name.
     * @return DependencyResolver
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
     * Registers the type to use name-based binding.
     *
     * @param string $typeName The fully qualified class name.
     * @return DependencyResolver
     */
    public function registerNamedType($typeName)
    {
        if (isset($this->namedTypes[$typeName])) {
            throw new \InvalidArgumentException("`$typeName` is already registerd.");
        }

        $type = new \ReflectionClass($typeName);
        $this->namedTypes[$typeName] = $type;

        return $this;
    }

    /**
     * Registers the value name to use name-based binding.
     *
     * @param string $valueName The name of constructor's parameter
     * @return DependencyResolver
     */
    public function registerNamedValue($valueName)
    {
        if (isset($this->namedValues[$valueName])) {
            throw new \InvalidArgumentException("`$valueName` is already registerd.");
        }

        $this->namedValues[$valueName] = true;

        return $this;
    }

    /**
     * @param array $classNames The class names to resolve the dependent.
     * @return DependencyGraph
     */
    public function resolve(array $classNames)
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
                $dependencies = [];

                if ($service->isDynamic()) {
                    $params = [];
                } else {
                    $class = $service->getClass();
                    $constructor = $class->getConstructor();
                    $params = $constructor ? $constructor->getParameters() : [];
                }

                foreach ($params as $param) {
                    $paramClass = $param->getClass();

                    if ($paramClass) {
                        $paramClassName = $paramClass->getName();

                        if (isset($this->namedTypes[$paramClassName])) {
                            $dependency = new NamedService($param);
                        } else {
                            $dependency = isset($this->bindings[$paramClassName])
                                ? $this->bindings[$paramClassName]
                                : new Service($paramClass, $paramClass);
                        }
                    } else {
                        $paramName = $param->getName();

                        if (isset($this->namedValues[$paramName])) {
                            $dependency = new NamedService($param);
                        } else {
                            if ($param->isOptional()) {
                                continue;
                            }

                            throw new \UnexpectedValueException(
                                "The `{$param->getName()}` dependent of `{$class->getName()}` can not be resolved."
                            );
                        }
                    }

                    $dependencies[] = $nextServices[] = $dependency;
                }

                $dependencyGraph->addObject($service, $dependencies);
            }
        } while (count($services = $nextServices) > 0);

        return $dependencyGraph;
    }
}
