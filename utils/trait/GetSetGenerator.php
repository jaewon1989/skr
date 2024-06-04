<?php

trait GetSetGenerator
{

    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        return null;
    }

    public function __isset($property)
    {
        if (property_exists($this, $property)) {
            return isset($this->$property);
        }

        return false;
    }

    public function __unset($property)
    {
        if (property_exists($this, $property)) {
            unset($this->$property);
        }
    }

    public function __call($name, $arguments)
    {
        $methodPrefix = substr($name, 0, 3);
        $propertyName = lcfirst(substr($name, 3));

        if ('get' === $methodPrefix) {
            return $this->{$propertyName};
        }

        if ('set' === $methodPrefix) {
            $this->{$propertyName} = $arguments[0];
        }
    }

}