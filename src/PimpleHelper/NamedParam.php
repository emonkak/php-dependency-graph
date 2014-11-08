<?php

namespace PimpleHelper;

class NamedParam
{
    /**
     * @var \ReflectionParameter
     */
    private $param;

    /**
     * @var \ReflectionClass
     */
    private $paramClass;

    /**
     * @param \ReflectionParameter $param
     * @param \ReflectionClass $paramClass
     */
    public function __construct(\ReflectionParameter $param, \ReflectionClass $paramClass)
    {
        $this->param = $param;
        $this->paramClass = $paramClass;
    }

    /**
     * @return \ReflectionParameter
     */
    public function getParam()
    {
        return $this->param;
    }

    /**
     * @return \ReflectionClass
     */
    public function getParamClass()
    {
        return $this->paramClass;
    }

    /**
     * @param AbstractServiceProviderDumper $dumper
     * @return string
     */
    public function dumpBy(AbstractServiceProviderDumper $dumper)
    {
        return $dumper->dumpNamedParam($this);
    }
}
