<?php

class DashBoardResponseModel
{
    use GetSetGenerator;
    private $userTotalAccessCount;
    private $userMonthlyAccessCount;
    private $userTotalRevisitAccessRate;
    private $userMonthlyRevisitAccessRate;
    private $userPreviousMonthlyAccessRate;
    private $userPreviousMonthlyRevisitRate;
    private $totalAnsweredRate;
    private $monthlyAnsweredRate;
    private $totalUnAnsweredCount;
    private $monthlyUnAnsweredCount;
    private $previousMonthlyAnsweredRate;
    private $previousMonthlyUnAnsweredRate;

    public static function of(StatisticsCount $statisticsAccess, StatisticsCount $statisticsAnswer): DashBoardResponseModel
    {
        $instance = new self();
        $instance->userTotalAccessCount = $statisticsAccess->userTotalAccessCount;
        $instance->userMonthlyAccessCount = $statisticsAccess->userMonthlyAccessCount;
        $instance->userTotalRevisitAccessRate = $statisticsAccess->userTotalRevisitAccessRate;
        $instance->userMonthlyRevisitAccessRate = $statisticsAccess->userMonthlyRevisitAccessRate;
        $instance->userPreviousMonthlyAccessRate = $statisticsAccess->userPreviousMonthlyAccessRate;
        $instance->userPreviousMonthlyRevisitRate = $statisticsAccess->userPreviousMonthlyRevisitRate;

        $instance->totalAnsweredRate = $statisticsAnswer->totalAnsweredRate;
        $instance->monthlyAnsweredRate = $statisticsAnswer->monthlyAnsweredRate;
        $instance->totalUnAnsweredCount = $statisticsAnswer->totalUnAnsweredCount;
        $instance->monthlyUnAnsweredCount = $statisticsAnswer->monthlyUnAnsweredCount;
        $instance->previousMonthlyAnsweredRate = $statisticsAnswer->previousMonthlyAnsweredRate;
        $instance->previousMonthlyUnAnsweredRate = $statisticsAnswer->previousMonthlyUnAnsweredRate;
        return $instance;
    }
}