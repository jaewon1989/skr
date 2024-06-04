<?php

class BlackList
{
    use GetSetGenerator;

    private $uid;
    private $blackList;
    private $bot;
    private $d_regis;


    public function __construct(BlackListDTO $builder)
    {
        $reflector = new ReflectionClass($builder);
        $properties = $reflector->getProperties(ReflectionProperty::IS_PRIVATE);

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $property->setAccessible(true);
            $propertyValue = $property->getValue($builder);
            $this->{$propertyName} = $propertyValue;
        }

    }
}