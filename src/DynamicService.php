<?php

namespace DependencyGraph;

class DynamicService implements ServiceInterface
{
    /**
     * @var \ReflectionClass $type
     */
    private $class;

    /**
     * @param \ReflectionClass $type
     */
    public function __construct(\ReflectionClass $class)
    {
        $this->class = $class;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return $this->class->getName();
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return $this->class;
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritDoc}
     */
    public function isDynamic()
    {
        return true;
    }
}
