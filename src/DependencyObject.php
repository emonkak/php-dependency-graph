<?php

namespace DependencyGraph;

class DependencyObject
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @var callable
     */
    private $keySelector;

    /**
     * @var callable
     */
    private $equalityComparer;

    /**
     * @var array of DependencyObject
     */
    private $dependencies;

    /**
     * @var array of DependencyObject
     */
    private $precedencies;

    /**
     * @param \ReflectionClass $class
     * @param callable $keySelector
     * @param callable $equalityComparer
     * @param array $dependencies
     * @param array $precedencies
     */
    public function __construct(
        $value,
        callable $keySelector,
        callable $equalityComparer,
        array $dependencies = [],
        array $precedencies = []
    ) {
        $this->value = $value;
        $this->keySelector = $keySelector;
        $this->equalityComparer = $equalityComparer;
        $this->dependencies = $dependencies;
        $this->precedencies = $precedencies;
    }

    public function __toString()
    {
        return call_user_func($this->keySelector, $this->value);
    }

    /**
     * @param DependencyObject $object
     */
    public function addDependency(DependencyObject $object)
    {
        $this->dependencies[] = $object;
    }

    /**
     * @param DependencyObject $object
     */
    public function addPrecedency(DependencyObject $object)
    {
        $this->precedencies[] = $object;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return array of DependencyObject
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * @return array of DependencyObject
     */
    public function getPrecedencies()
    {
        return $this->precedencies;
    }

    /**
     * @param mixed $other
     * @return boolean
     */
    public function dependsTo($other)
    {
        foreach ($this->dependencies as $object) {
            if ($object->dependsToImpl($other)) {
                return true;
            }
        }

        return false;
    }

    private function dependsToImpl($other)
    {
        foreach ($this->dependencies as $object) {
            if ($object->dependsToImpl($other)) {
                return true;
            }
        }

        return call_user_func($this->equalityComparer, $this->value, $other);
    }

    /**
     * @param string $identifier
     * @return boolean
     */
    public function isDependedBy($other)
    {
        foreach ($this->precedencies as $object) {
            if ($object->isDependedByImpl($other)) {
                return true;
            }
        }

        return false;
    }

    private function isDependedByImpl($other)
    {
        foreach ($this->precedencies as $object) {
            if ($object->isDependedByImpl($other)) {
                return true;
            }
        }

        return call_user_func($this->equalityComparer, $this->value, $other);
    }
}
