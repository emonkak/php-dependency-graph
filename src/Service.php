<?php

namespace DependencyGraph;

class Service
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
        $this->type = $type;
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->type->getName();
    }

    /**
     * @return \ReflectionClass
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return \ReflectionClass
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return boolean
     */
    public function isDynamic()
    {
        return false;
    }
}
