<?php

namespace DependencyGraph;

class NamedService
{
    /**
     * @var string
     */
    private $alias;

    /**
     * @var \ReflectionClass $type
     */
    private $class;

    /**
     * @param string $alias
     * @param \ReflectionClass $type
     */
    public function __construct($alias, \ReflectionClass $class)
    {
        $this->alias = $alias;
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "{$this->alias}@{$this->class->getName()}";
    }

    /**
     * @return \ReflectionClass
     */
    public function getType()
    {
        return $this->class;
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
        return true;
    }
}
