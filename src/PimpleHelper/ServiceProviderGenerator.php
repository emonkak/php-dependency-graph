<?php

namespace PimpleHelper;

/**
 * Service provider's source generator implementation.
 */
class ServiceProviderGenerator implements ServiceProviderGeneratorInterface
{
    /**
     * @var array of \ReflectionClass
     */
    private $bindings = [];

    /**
     * @var array of boolean
     */
    private $dynamicTypes = [];

    /**
     * {@inheritDoc}
     */
    public function registerClass($className)
    {
        $this->bindings[$className] = new \ReflectionClass($className);
    }

    /**
     * {@inheritDoc}
     */
    public function registerType($typeName, $className)
    {
        $this->bindings[$typeName] = new \ReflectionClass($className);
    }

    /**
     * {@inheritDoc}
     */
    public function markAsDynamic($typeName)
    {
        $this->dynamicTypes[$typeName] = true;
    }

    /**
     * {@inheritDoc}
     */
    public function generate($className)
    {
        $definitions = $this->dumpDefnitions();
        $joinedDefinitions = implode("\n", $definitions);

        $lastSeparatorPos = strrpos($className, '\\');
        if ($lastSeparatorPos !== false) {
            $namespace = substr($className, 0, $lastSeparatorPos);
            $shortClassName = substr($className, $lastSeparatorPos + 1);
            $namespaceSource = "namespace $namespace;\n\n";
        } else {
            $shortClassName = $className;
            $namespaceSource = '';
        }

        $classSource = <<<EOL
class $shortClassName implements \Pimple\ServiceProviderInterface
{
    public function register(\Pimple\Container \$c)
    {
$joinedDefinitions
    }
}
EOL;

        return $namespaceSource . $classSource;
    }

    /**
     * @return array of string
     */
    private function dumpDefnitions()
    {
        $queue = $this->bindings;
        $definitions = [];

        do {
            $nextQueue = [];

            foreach ($queue as $typeName => $class) {
                $paramDefinitions = [];
                $constructor = $class->getConstructor();

                if ($constructor) {
                    $params = $constructor->getParameters();

                    foreach ($params as $param) {
                        $paramClass = $param->getClass();

                        if ($paramClass) {
                            $paramClassName = $paramClass->getName();

                            if (isset($this->dynamicTypes[$paramClassName])) {
                                // Type and name-based binding
                                $paramName = $param->getName();
                                $paramDefinitions[] = "\$c['$paramName@$paramClassName']";
                            } else {
                                // Binding from a type
                                if (!isset($queue[$paramClassName])
                                    && !isset($definitions[$paramClassName])) {
                                    $nextQueue[$paramClassName] = $paramClass;
                                }
                                $paramDefinitions[] = "\$c['$paramClassName']";
                            }
                        } else {
                            // Name-based binding
                            $paramName = $param->getName();
                            $paramDefinitions[] = "\$c['$paramName@']";
                        }
                    }
                }

                $className = $class->getName();
                $joinedParamDefinitions = implode(', ', $paramDefinitions);

                $definitions[$typeName] = <<<EOL
        \$c['$typeName'] = function(\$c) { return new \\$className($joinedParamDefinitions); };
EOL;
            }

            $queue = $nextQueue;
        } while (count($queue = $nextQueue));

        return $definitions;
    }
}
