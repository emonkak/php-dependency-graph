<?php

namespace PimpleHelper;

/**
 * Represents the strategy of service provider's source generation.
 */
abstract class AbstractServiceProviderDumper
{
    /**
     * Dumps the class definition code which can eval from PHP.
     *
     * @param string $className The service provider's class name.
     * @param array $services Service instances
     * @return string
     */
    public function dumpClass($className, array $services)
    {
        $lastSeparatorPos = strrpos($className, '\\');
        if ($lastSeparatorPos !== false) {
            $namespace = substr($className, 0, $lastSeparatorPos);
            $shortClassName = substr($className, $lastSeparatorPos + 1);
        } else {
            $namespace = '';
            $shortClassName = $className;
        }

        $serviceDefinitions = [];
        foreach ($services as $service) {
            $paramDefinitions = [];
            foreach ($service->getParams() as $param) {
                $paramDefinitions[] = $param->dumpBy($this);
            }
            $serviceDefinitions[] = $this->dumpServiceDefinition($service, $paramDefinitions);
        }

        return $this->dumpClassDefinition($namespace, $shortClassName, $serviceDefinitions);
    }

    /**
     * Dumps the class definition code.
     *
     * @param string $namespace
     * @param string $shortClassName
     * @param array $serviceDefinitions
     * @return string
     */
    abstract public function dumpClassDefinition($namespace, $shortClassName, array $serviceDefinitions);

    /**
     * Dumps the service definition code.
     *
     * @param Service $service
     * @param array $paramDefinitions
     * @return string
     */
    abstract public function dumpServiceDefinition(Service $service, array $paramDefinitions);

    /**
     * Dumps the constructor's typed parameter.
     *
     * @param TypedParam $param
     * @return string
     */
    abstract public function dumpTypedParam(TypedParam $param);

    /**
     * Dumps the constructor's named parameter.
     *
     * @param NamedParam $param
     * @return string
     */
    abstract public function dumpNamedParam(NamedParam $param);

    /**
     * Dumps the constructor's named parameter without type.
     *
     * @param NameOnlyParam $param
     * @return string
     */
    abstract public function dumpNameOnlyParam(NameOnlyParam $param);
}
