<?php

class StatisticsResponseModel
{

    use GetSetGenerator;
    private $userTotalAccessCount;
    private $userTotalRevisitAccessRate;
    private $totalUnAnsweredCount;
    private $totalAnsweredRate;

    public static function of(StatisticsCount $statisticsAccess, StatisticsCount $statisticsAnswer): StatisticsResponseModel
    {
        $instance = new self();
        $instance->userTotalAccessCount = $statisticsAccess->userTotalAccessCount;
        $instance->userTotalRevisitAccessRate = $statisticsAccess->userTotalRevisitAccessRate;

        $instance->totalAnsweredRate = $statisticsAnswer->totalAnsweredRate;
        $instance->totalUnAnsweredCount = $statisticsAnswer->totalUnAnsweredCount;
        return $instance;
    }

}