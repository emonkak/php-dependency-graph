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
     * {@inheritDoc}
     */
    public function registerClass($className)
    {
        $class = new \ReflectionClass($className);
        $this->bindings[$class] = $class;
        $this->types[$class] = true;
    }

    /**
     * {@inheritDoc}
     */
    public function registerType($typeName, $className)
    {
        $type = new \ReflectionClass($typeName);
        $class = new \ReflectionClass($className);
        $this->bindings[$type] = $class;
        $this->types[$class] = true;
    }

    /**
     * {@inheritDoc}
     */
    public function markAsDynamic($typeName)
    {
        $this->dynamicTypes[$typeName] = true;
    }

    /**
     * {@inheritDoc}
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
        $definitions = [];

        do {
            $nextQueue = [];

            foreach ($queue as $typeName => $class) {
                $paramDefinitions = [];
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
                                if (!isset($queue[$paramClassName])
                                    && !isset($definitions[$paramClassName])) {
                                    $nextQueue[$paramClassName] = $paramClass;
                                }
                                $paramDefinitions[] = new TypedParam($param, $paramClass);
                            }
                        } else {
                            // Name-based binding
                            $paramDefinitions[] = new NameOnlyParam($param);
                        }
                    }
                }

                $definitions[$typeName] = new Service(
                    new \ReflectionClass($typeName),
                    $class,
                    $paramDefinitions
                );
            }

            $queue = $nextQueue;
        } while (count($queue));

        return $definitions;
    }
}
