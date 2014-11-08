<?php

namespace PimpleHelper;

class Service
{
    private $type;
    private $class;
    private $params;

    public function __construct(\ReflectionClass $type, \ReflectionClass $class, array $params)
    {
        $this->type = $type;
        $this->class = $class;
        $this->params = $params;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function getParams()
    {
        return $this->params;
    }
}
