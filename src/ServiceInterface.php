<?php

namespace DependencyGraph;

interface ServiceInterface
{
    /**
     * Returns the identifier of this service.
     *
     * @return string
     */
    public function __toString();

    /**
     * Gets the type of this service.
     *
     * @return \ReflectionClass
     */
    public function getType();

    /**
     * Gets the class of this service.
     *
     * @return \ReflectionClass
     */
    public function getClass();

    /**
     * Gets a value that indicates whether resolving of the type is dynamic.
     *
     * @return boolean
     */
    public function isDynamic();
}
