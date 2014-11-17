<?php

namespace DependencyGraph;

/**
 * Represents dependency graph by any object.
 */
class DependencyGraph implements \IteratorAggregate
{
    /**
     * @var array of DependencyObject
     */
    private $objects = [];

    /**
     * @var callable
     */
    private $keySelector;

    /**
     * @var callable
     */
    private $equalityComparer;

    /**
     * @param callable $keySelector
     * @param callable $equalityComparer
     */
    public function __construct(callable $keySelector, callable $equalityComparer)
    {
        $this->keySelector = $keySelector;
        $this->equalityComparer = $equalityComparer;
    }

    /**
     * @param mixed $object
     * @param array $dependencies
     * @retuen DependencyObject
     */
    public function addObject($object, array $dependencies)
    {
        $key = call_user_func($this->keySelector, $object);
        if (isset($this->objects[$key])) {
            $object = $this->objects[$key];
        } else {
            $object = new DependencyObject(
                $object,
                $this->keySelector,
                $this->equalityComparer
            );
            $this->objects[$key] = $object;
        }

        foreach ($dependencies as $dependency) {
            $depKey = call_user_func($this->keySelector, $dependency);
            if (isset($this->objects[$depKey])) {
                $depObject = $this->objects[$depKey];
            } else {
                $depObject = $this->objects[$depKey] = new DependencyObject(
                    $dependency,
                    $this->keySelector,
                    $this->equalityComparer
                );
            }

            if ($object->isDependedBy($dependency)) {
                throw new \InvalidArgumentException(sprintf(
                    'Circular dependency detected between `%s` and `%s`',
                    call_user_func($this->keySelector, $depObject),
                    call_user_func($this->keySelector, $object)
                ));
            }

            $object->addDependency($depObject);
            $depObject->addPrecedency($object);
        }

        return $object;
    }

    /**
     * @return array of DependencyObject
     */
    public function getAllObjects()
    {
        return $this->objects;
    }

    /**
     * @return array of DependencyObject
     */
    public function getRootObjects()
    {
        $rootObjects = [];
        foreach ($this->objects as $object) {
            if (count($object->getPrecedencies()) === 0) {
                $rootObjects[] = $object;
            }
        }
        return $rootObjects;
    }

    /**
     * @see \IteratorAggregate
     * @return \Iterator
     */
    public function getIterator()
    {
        return new DependencyGraphIterator($this->getRootObjects());
    }
}
