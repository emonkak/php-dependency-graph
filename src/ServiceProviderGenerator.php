<?php

namespace DependencyGraph;

/**
 * Represents the strategy of service provider's source generation.
 */
class ServiceProviderGenerator
{
    /**
     * Dumps the class definition code which can eval from PHP.
     *
     * @param string $className The service provider's class name.
     * @param DependencyGraph $dependencyGraph
     * @return string
     */
    public function generate($className, DependencyGraph $dependencyGraph)
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
        foreach ($dependencyGraph->getAllObjects() as $object) {
            if (!$object->getValue()->isDynamic()) {
                $paramExprs = [];
                foreach ($object->getDependencies() as $dependency)  {
                    $paramExprs[] = $this->dumpParamExpr($dependency);
                }

                $serviceDefinitions[] = $this->dumpServiceDefinition($object, $paramExprs);
            }
        }

        return $this->dumpClass($namespace, $shortClassName, $serviceDefinitions);
    }

    /**
     * Dumps the class definition code.
     *
     * @param string $namespace
     * @param string $shortClassName
     * @param array $serviceDefinitions
     * @return string
     */
    protected function dumpClass($namespace, $shortClassName, array $serviceDefinitions)
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
     * Dumps the service definition code.
     *
     * @param Service $service
     * @param array $paramExprs
     * @return string
     */
    protected function dumpServiceDefinition(DependencyObject $object, array $paramExprs)
    {
        $service = $object->getValue();
        $typeName = $service->getType()->getName();
        $className = $service->getClass()->getName();

        $joinedParamExprs = implode(', ', $paramExprs);
        return <<<EOL
        \$c['$typeName'] = function(\$c) { return new \\$className($joinedParamExprs); };
EOL;
    }

    /**
     * Dumps the service parameter.
     *
     * @param DependencyObject $object
     * @return string
     */
    protected function dumpParamExpr(DependencyObject $object)
    {
        return "\$c['{$object->getValue()}']";
    }
}
