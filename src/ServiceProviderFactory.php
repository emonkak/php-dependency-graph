<?php

namespace DependencyGraph;

/**
 * Provides the instantiation of the service provider.
 */
class ServiceProviderFactory
{
    /**
     * @var DependencyAnalyzer
     */
    private $dependencyAnalyzer;

    /**
     * @var ServiceProviderGenerator
     */
    private $serviceProviderGenerator;

    /**
     * @var ServiceProviderLoader
     */
    private $serviceProviderLoader;

    /**
     * @param DependencyAnalyzer $dependencyAnalyzer
     * @param ServiceProviderGenerator $serviceProviderGenerator
     * @param ServiceProviderLoader $serviceProviderLoader
     */
    public function __construct(
        DependencyAnalyzer $dependencyAnalyzer,
        ServiceProviderGenerator $serviceProviderGenerator,
        ServiceProviderLoader $serviceProviderLoader)
    {
        $this->dependencyAnalyzer = $dependencyAnalyzer;
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
                $dependencyGraph = $this->dependencyAnalyzer->execute($serviceClasses);
                $source = $this->serviceProviderGenerator->generate($serviceProviderClass, $dependencyGraph);
                $this->serviceProviderLoader->write($serviceProviderClass, $source);
            }

            $this->serviceProviderLoader->load($serviceProviderClass);
        }

        return new $serviceProviderClass();
    }
}
