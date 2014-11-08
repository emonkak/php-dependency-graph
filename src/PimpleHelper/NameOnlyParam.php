<?php

namespace PimpleHelper;

class NameOnlyParam
{
    /**
     * @var \ReflectionParameter
     */
    private $param;

    /**
     * @param \ReflectionParameter $param
     */
    public function __construct(\ReflectionParameter $param)
    {
        $this->param = $param;
    }

    /**
     * @return \ReflectionParameter
     */
    public function getParam()
    {
        return $this->param;
    }

    /**
     * @param AbstractServiceProviderDumper $dumper
     * @return string
     */
    public function dumpBy(AbstractServiceProviderDumper $dumper)
    {
        return $dumper->dumpNameOnlyParam($this);
    }
}
