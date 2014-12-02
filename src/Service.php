<?php

namespace DependencyGraph;

class Service implements ServiceInterface
{
    /**
     * @var \ReflectionClass
     */
    private $type;

    /**
     * @var \ReflectionClass
     */
    private $class;

    /**
     * @param \ReflectionClass $type
     */
    public function __construct(\ReflectionClass $type, \ReflectionClass $class)
    {
        if (!$class->isInstantiable()) {
            throw new \InvalidArgumentException("`{$class->getName()}` is not instantiable.");
        }
        if (!($class->getName() === $type->getName() || $class->isSubclassOf($type))) {
            throw new \InvalidArgumentException("`{$class->getName()}` is not sub-class of `{$type->getName()}`");
        }
        $this->type = $type;
        $this->class = $class;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return $this->type->getName();
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return $this->type;
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
        return false;
    }
}
