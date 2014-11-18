<?php

namespace DependencyGraph;

use Composer\Autoload\ClassLoader;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * The service provider loader on memory.
 */
class ServiceProviderLoaderOnMemory implements ServiceProviderLoader
{
    /**
     * @var array of the srouce string
     */
    private $sources = [];

    /**
     * {@inheritDoc}
     */
    public function load($className)
    {
        if (!isset($this->sources[$className])) {
            throw new FileNotFoundException(
                "Failed to load `$className` because file does not exist."
            );
        }

        eval($this->sources[$className]);
    }

    /**
     * {@inheritDoc}
     */
    public function canLoad($className)
    {
        return isset($this->sources[$className]);
    }

    /**
     * {@inheritDoc}
     */
    public function write($className, $source)
    {
        $this->sources[$className] = $source;
    }
}
