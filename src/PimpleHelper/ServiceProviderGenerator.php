<?php

namespace PimpleHelper;

/**
 * Resolves dependencies to generate the service provider's source.
 */
class ServiceProviderGenerator
{
    /**
     * @var AbstractServiceProviderDumper $serviceProviderDumper
     */
    private $serviceProviderDumper;

    /**
     * @var \SplObjectStorage of \ReflectionClass
     */
    private $bindings;

    /**
     * @var array of boolean
     */
    private $types = [];

    /**
     * @var array of boolean
     */
    private $dynamicTypes = [];

    /**
     * @param AbstractServiceProviderDumper $serviceProviderDumper
     */
    public function __construct(AbstractServiceProviderDumper $serviceProviderDumper)
    {
        $this->serviceProviderDumper = $serviceProviderDumper;
        $this->bindings = new \SplObjectStorage();
    }

    /**
     * Registers the class to resolve the dependent.
     *
     * @param string The fully qualified class name.
     */
    public function registerClass($className)
    {
        if (isset($this->types[$className])) {
            throw new \InvalidArgumentException("`$typeName` is already registerd.");
        }

        $class = new \ReflectionClass($className);
        if (!$class->isInstantiable()) {
            throw new \InvalidArgumentException("`$className` is not instantiable.");
        }

        $this->bindings[$class] = $class;
        $this->types[$className] = true;
    }

    /**
     * Registers the type and class to resolve the dependent.
     *
     * @param string $typeName The fully qualified class name.
     * @param string $className The fully qualified class name.
     */
    public function registerType($typeName, $className)
    {
        if (isset($this->types[$typeName])) {
            throw new \InvalidArgumentException("`$typeName` is already registerd.");
        }

        $type = new \ReflectionClass($typeName);
        $class = new \ReflectionClass($className);
        if (!$class->isInstantiable()) {
            throw new \InvalidArgumentException("`$className` is not instantiable.");
        }

        $this->bindings[$type] = $class;
        $this->types[$typeName] = true;
    }

    /**
     * Marks to use name-based binding.
     *
     * @param string $typeName The fully qualified class name.
     */
    public function markAsDynamic($typeName)
    {
        $this->dynamicTypes[$typeName] = true;
    }

    /**
     * Generates the ServiceProvider's source.
     *
     * @param string $className The fully qualified class name.
     * @return string source of the class definition.
     */
    public function generate($className)
    {
        $services = $this->resolveServices();
        return $this->serviceProviderDumper->dumpClass($className, $services);
    }

    /**
     * @return array of Service
     */
    private function resolveServices()
    {
        $queue = $this->bindings;
        $services = [];

        do {
            $nextQueue = new \SplObjectStorage();

            foreach ($queue as $type) {
                $paramDefinitions = [];
                $class = $queue[$type];
                $constructor = $class->getConstructor();

                if ($constructor) {
                    foreach ($constructor->getParameters() as $param) {
                        $paramClass = $param->getClass();

                        if ($paramClass) {
                            $paramClassName = $paramClass->getName();

                            if (isset($this->dynamicTypes[$paramClassName])) {
                                $paramDefinitions[] = new NamedParam($param, $paramClass);
                            } else {
                                // Binding from a type
                                if (!isset($this->types[$paramClassName])
                                    && !isset($definitions[$paramClassName])) {
                                    $nextQueue[$paramClass] = $paramClass;
                                }
                                $paramDefinitions[] = new TypedParam($param, $paramClass);
                            }
                        } else {
                            // Name-based binding
                            $paramDefinitions[] = new NameOnlyParam($param);
                        }
                    }
                }

                $typeName = $type->getName();
                $services[$typeName] = new Service($type, $class, $paramDefinitions);
            }

            $queue = $nextQueue;
        } while (count($queue));

        return $services;
    }
}
