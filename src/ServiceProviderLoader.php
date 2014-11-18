<?php

namespace DependencyGraph;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Provides a loading of the service provider that is automatically generated.
 */
interface ServiceProviderLoader
{
    /**
     * Loads the class of the service provider.
     *
     * @param string $className The class name of the service provider.
     * @throws FileNotFoundException
     */
    public function load($className);

    /**
     * Gets a value that indicates whether the specified URI can be loaded.
     *
     * @param string $className The class name of the service provider.
     * @return bool
     */
    public function canLoad($className);

    /**
     * Writes the source of the service provider.
     *
     * @param string $className The class name of the service provider.
     * @param string $source The source of the service provider.
     * @throws IOException If the file cannot be written to.
     */
    public function write($className, $source);

    /**
     * Clears the all sources of the service provider.
     *
     * @throws IOException When removal fails
     */
    public function clear();
}
