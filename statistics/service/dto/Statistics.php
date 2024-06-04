<?php

class Statistics {
    use GetSetGenerator;
    private $botuid;
    private $page;
    private $is_unknown;
    private $type;
    private $d_regis;

    public function __construct(StatisticsBuilder $builder) {
        $reflector = new ReflectionClass($builder);
        $properties = $reflector->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE);

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $property->setAccessible(true);
            $propertyValue = $property->getValue($builder);
            $this->{$propertyName} = $propertyValue;
        }
    }
}