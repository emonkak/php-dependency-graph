<?php

namespace PimpleHelper;

class ServiceProviderDumper extends AbstractServiceProviderDumper
{
    /**
     * {@inheritDoc}
     */
    public function dumpClassDefinition($namespace, $shortClassName, array $serviceDefinitions)
    {
        $joinedServiceDefinitions = implode("\n", $serviceDefinitions);

        $namespaceSource = $namespace !== '' ? "namespace $namespace;\n\n" : '';
        $classSource = <<<EOL
class $shortClassName implements \Pimple\ServiceProviderInterface
{
    public function register(\Pimple\Container \$c)
    {
$joinedServiceDefinitions
    }
}
EOL;

        return $namespaceSource . $classSource;
    }

    /**
     * {@inheritDoc}
     */
    public function dumpServiceDefinition(Service $service, array $paramDefinitions)
    {
        $typeName = $service->getType()->getName();
        $className = $service->getClass()->getName();
        $joinedParamDefinitions = implode(', ', $paramDefinitions);

        return <<<EOL
        \$c['$typeName'] = function(\$c) { return new \\$className($joinedParamDefinitions); };
EOL;
    }

    /**
     * {@inheritDoc}
     */
    public function dumpTypedParam(TypedParam $param)
    {
        $paramClassName = $param->getParamClass()->getName();
        return "\$c['$paramClassName']";
    }

    /**
     * {@inheritDoc}
     */
    public function dumpNamedParam(NamedParam $param)
    {
        $paramName = $param->getParam()->getName();
        $paramClassName = $param->getParamClass()->getName();
        return "\$c['$paramName@$paramClassName']";
    }

    /**
     * {@inheritDoc}
     */
    public function dumpNameOnlyParam(NameOnlyParam $param)
    {
        $paramName = $param->getParam()->getName();
        return "\$c['$paramName@']";
    }
}
