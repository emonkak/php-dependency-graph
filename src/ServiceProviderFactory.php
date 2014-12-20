<?php

namespace DependencyGraph;

/**
 * Provides the instantiation of the service provider.
 */
class ServiceProviderFactory
{
    /**
     * @var DependencyResolver
     */
    private $dependencyResolver;

    /**
     * @var ServiceProviderGenerator
     */
    private $serviceProviderGenerator;

    /**
     * @var ServiceProviderLoader
     */
    private $serviceProviderLoader;

    /**
     * @param DependencyResolver $dependencyResolver
     * @param ServiceProviderGenerator $serviceProviderGenerator
     * @param ServiceProviderLoader $serviceProviderLoader
     */
    public function __construct(
        DependencyResolver $dependencyResolver,
        ServiceProviderGenerator $serviceProviderGenerator,
        ServiceProviderLoader $serviceProviderLoader)
    {
        $this->dependencyResolver = $dependencyResolver;
        $this->serviceProviderGenerator = $serviceProviderGenerator;
        $this->serviceProviderLoader = $serviceProviderLoader;
    }

    /**
     * Creates the instance of the service provider.
     *
     * @param string $serviceProviderClass
     * @param array $serviceClasses
     * @return mixed
     */
    public function createInstance($serviceProviderClass, array $serviceClasses)
    {
        if (!class_exists($serviceProviderClass, false)) {
            if (!$this->serviceProviderLoader->canLoad($serviceProviderClass)) {
                $dependencyGraph = $this->dependencyResolver->resolve($serviceClasses);
                $source = $this->serviceProviderGenerator->generate($serviceProviderClass, $dependencyGraph);
                $this->serviceProviderLoader->write($serviceProviderClass, $source);
            }

            $this->serviceProviderLoader->load($serviceProviderClass);
        }

        return new $serviceProviderClass();
    }
}
