<?php

namespace DependencyGraph;

class NamedService
{
    /**
     * @var \ReflectionParameter
     */
    private $param;

    /**
     * @param \ReflectionParameter $type
     */
    public function __construct(\ReflectionParameter $param)
    {
        $this->param = $param;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $name = $this->param->getName();
        $class = $this->param->getClass();
        return $class ? "{$name}@{$class->getName()}" : "{$name}@var";
    }

    /**
     * @return \ReflectionClass
     */
    public function getType()
    {
        return $this->param->getClass();
    }

    /**
     * @return \ReflectionClass
     */
    public function getClass()
    {
        return $this->param->getClass();
    }

    /**
     * @return boolean
     */
    public function isDynamic()
    {
        return true;
    }
}
