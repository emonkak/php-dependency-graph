<?php

namespace PimpleHelper;

/**
 * Service provider's source generator.
 */
interface ServiceProviderGeneratorInterface
{
    /**
     * Registers the class to resolve the dependent.
     *
     * @param string The fully qualified class name.
     */
    public function registerClass($className);

    /**
     * Registers the type and class to resolve the dependent.
     *
     * @param string $typeName The fully qualified class name.
     * @param string $className The fully qualified class name.
     */
    public function registerType($typeName, $className);

    /**
     * Marks to use name-based binding.
     *
     * @param string $typeName The fully qualified class name.
     */
    public function markAsDynamic($typeName);

    /**
     * Generates the ServiceProvider's source.
     *
     * @param string $className The fully qualified class name.
     * @return string source of the class definition.
     */
    public function generate($className);
}
