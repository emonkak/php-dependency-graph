<?php

namespace DependencyGraph;

class DependencyObject
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @var array of DependencyObject
     */
    private $dependencies = [];

    /**
     * @var array of DependencyObject
     */
    private $precedencies = [];

    /**
     * @param string $key
     * @param string $value
     */
    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function __toString()
    {
        return $this->key;
    }

    /**
     * @param DependencyObject $object
     */
    public function addDependency(DependencyObject $object)
    {
        $this->dependencies[$object->getKey()] = $object;
    }

    /**
     * @param DependencyObject $object
     */
    public function addPrecedency(DependencyObject $object)
    {
        $this->precedencies[$object->getKey()] = $object;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
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
     * @param DependencyObject $other
     * @return boolean
     */
    public function dependsTo(DependencyObject $other)
    {
        foreach ($this->dependencies as $object) {
            if ($object->dependsToImpl($other)) {
                return true;
            }
        }

        return false;
    }

    private function dependsToImpl(DependencyObject $other)
    {
        foreach ($this->dependencies as $object) {
            if ($object->dependsToImpl($other)) {
                return true;
            }
        }

        return $this->equalsTo($other);
    }

    /**
     * @return boolean
     */
    public function equalsTo(DependencyObject $other)
    {
        return $this === $other || $this->key === $other->key;
    }

    /**
     * @param DependencyObject $other
     * @return boolean
     */
    public function isDependedBy(DependencyObject $other)
    {
        foreach ($this->precedencies as $object) {
            if ($object->isDependedByImpl($other)) {
                return true;
            }
        }

        return false;
    }

    private function isDependedByImpl(DependencyObject $other)
    {
        foreach ($this->precedencies as $object) {
            if ($object->isDependedByImpl($other)) {
                return true;
            }
        }

        return $this->equalsTo($other);
    }
}
