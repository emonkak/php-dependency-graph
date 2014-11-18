<?php

namespace DependencyGraph;

class DynamicService
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
     * @return string
     */
    public function __toString()
    {
        return $this->class->getName();
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
