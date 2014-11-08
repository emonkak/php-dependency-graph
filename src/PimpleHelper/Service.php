<?php

namespace PimpleHelper;

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
     * @var array
     */
    private $params;

    /**
     * @param \ReflectionClass $type
     * @param \ReflectionClass $class
     * @param array $params The constructor parameters
     */
    public function __construct(\ReflectionClass $type, \ReflectionClass $class, array $params)
    {
        $this->type = $type;
        $this->class = $class;
        $this->params = $params;
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
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}
