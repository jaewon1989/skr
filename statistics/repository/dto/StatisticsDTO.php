<?php

class StatisticsDTO
{

    public static function Builder(): StatisticsBuilder
    {
        return new StatisticsBuilder();
    }

    public function toEntity($dto): Statistics
    {
        return self::Builder()
            ->invokeSetter($dto)
            ->build();
    }
}