<?php

class StatisticsCount
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

    public static function of($userTotalAccessCount, $userMonthlyAccessCount,
                              $userTotalRevisitAccessRate, $userMonthlyRevisitAccessRate,
                              $userPreviousMonthlyAccessRate, $userPreviousMonthlyRevisitRate,
                              $totalAnsweredRate, $monthlyAnsweredRate,
                              $totalUnAnsweredCount, $monthlyUnAnsweredCount,
                              $previousMonthlyAnsweredRate, $previousMonthlyUnAnsweredRate): StatisticsCount
    {
        $instance = new self();
        $instance->userTotalAccessCount = $userTotalAccessCount;
        $instance->userMonthlyAccessCount = $userMonthlyAccessCount;
        $instance->userTotalRevisitAccessRate = $userTotalRevisitAccessRate;
        $instance->userMonthlyRevisitAccessRate = $userMonthlyRevisitAccessRate;
        $instance->userPreviousMonthlyAccessRate = $userPreviousMonthlyAccessRate;
        $instance->userPreviousMonthlyRevisitRate = $userPreviousMonthlyRevisitRate;

        $instance->totalAnsweredRate = $totalAnsweredRate;
        $instance->monthlyAnsweredRate = $monthlyAnsweredRate;
        $instance->totalUnAnsweredCount = $totalUnAnsweredCount;
        $instance->monthlyUnAnsweredCount = $monthlyUnAnsweredCount;
        $instance->previousMonthlyAnsweredRate = $previousMonthlyAnsweredRate;
        $instance->previousMonthlyUnAnsweredRate = $previousMonthlyUnAnsweredRate;
        return $instance;
    }
}