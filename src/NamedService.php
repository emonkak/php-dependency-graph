<?php

namespace DependencyGraph;

class NamedService implements ServiceInterface
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
     * {@inheritDoc}
     */
    public function __toString()
    {
        $name = $this->param->getName();
        $class = $this->param->getClass();
        return $class ? "{$name}@{$class->getName()}" : "{$name}@var";
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return $this->param->getClass();
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->param->getClass();
    }

    /**
     * {@inheritDoc}
     */
    public function isDynamic()
    {
        return true;
    }
}
