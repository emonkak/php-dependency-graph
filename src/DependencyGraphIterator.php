<?php

namespace DependencyGraph;

class DependencyGraphIterator extends \ArrayIterator implements \RecursiveIterator
{
    public function getChildren()
    {
        return new static($this->current()->getDependencies());
    }

    public function hasChildren()
    {
        return count($this->current()->getDependencies()) > 0;
    }
}
