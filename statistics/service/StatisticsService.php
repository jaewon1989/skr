<?php

class StatisticsService
{
    private $mapper;
    public function __construct(){
        global $DB_CONNECT;
        $this->mapper = new StatisticsMapper($DB_CONNECT);
    }

    public function getUserStatisticsList(Bot $bot): StatisticsCount
    {
        $statisticsDtoList = $this->mapper->getUserStatisticsList($bot);
        $statisticsList = array();

        while ($dto = $statisticsDtoList->fetch_assoc()) {
            $statisticsList[] = (new StatisticsDTO())->toEntity($dto);
        }

        $userTotalAccessCount = 0;
        $userTotalRevisitCount = 0;
        $userMonthlyAccessCount = 0;
        $userMonthlyRevisitCount = 0;
        $userPreviousMonthlyAccessCount = 0;
        $userPreviousMonthlyRevisitCount = 0;

        // 총 누적 접속 수
        array_map(function($statistics) use (&$userTotalAccessCount) {
            $userTotalAccessCount += $statistics->page;
        }, $statisticsList);

        // 총 누적 재방문 접속 수
        array_map(function($statistics) use (&$userTotalRevisitCount) {
            if($statistics->type == 2) {
                $userTotalRevisitCount += $statistics->page;
            }
        }, $statisticsList);

        //총 월간 접속 수
        array_map(function($statistics) use (&$userMonthlyAccessCount) {
            if($statistics->d_regis >= date('Ymd', strtotime('first day of this month'))) {
                $userMonthlyAccessCount += $statistics->page;
            }

        }, $statisticsList);

        //총 월간 재방문 접속 수
        array_map(function($statistics) use (&$userMonthlyRevisitCount) {
            if($statistics->type == 2 && $statistics->d_regis >= date('Ymd', strtotime('first day of this month'))) {
                $userMonthlyRevisitCount += $statistics->page;
            }

        }, $statisticsList);

        //총 저번 달 접속 수
        array_map(function($statistics) use (&$userPreviousMonthlyAccessCount) {
            if($statistics->d_regis >= date('Ym01', strtotime('-1 month')) && $statistics->d_regis <= date('Ymt', strtotime('-1 month'))) {
                $userPreviousMonthlyAccessCount += $statistics->page;
            }

        }, $statisticsList);

        //총 저번 달 재방문 접속 수
        array_map(function($statistics) use (&$userPreviousMonthlyRevisitCount) {
            if($statistics->type == 2 && $statistics->d_regis >= date('Ym01', strtotime('-1 month')) && $statistics->d_regis <= date('Ymt', strtotime('-1 month'))) {
                $userPreviousMonthlyRevisitCount += $statistics->page;
            }

        }, $statisticsList);

        $userTotalRevisitAccessRate = $userTotalRevisitCount == 0 ? 0 : round($userTotalRevisitCount / $userTotalAccessCount * 100, 2);
        $userMonthlyRevisitAccessRate = $userMonthlyRevisitCount == 0 ? 0 : round($userMonthlyRevisitCount / $userMonthlyAccessCount * 100, 2);;

        $userPreviousMonthlyAccessRate = $userPreviousMonthlyAccessCount == 0 ? 0 : round(($userMonthlyAccessCount - $userPreviousMonthlyAccessCount) / $userPreviousMonthlyAccessCount * 100, 2);
        $userPreviousMonthlyRevisitRate = $userPreviousMonthlyRevisitCount == 0 ? 0 : round(($userMonthlyRevisitCount - $userPreviousMonthlyRevisitCount) / $userPreviousMonthlyRevisitCount * 100, 2);

        return StatisticsCount::of(
            $userTotalAccessCount, $userMonthlyAccessCount,
            $userTotalRevisitAccessRate, $userMonthlyRevisitAccessRate,
            $userPreviousMonthlyAccessRate, $userPreviousMonthlyRevisitRate,
            0,0, 0, 0, 0, 0);
    }

    public function getAnswerStatisticsList(Bot $bot): StatisticsCount
    {
        $statisticsDtoList = $this->mapper->getAnswerStatisticsList($bot);
        $statisticsList = array();

        while ($dto = $statisticsDtoList->fetch_assoc()) {
            $statisticsList[] = (new StatisticsDTO())->toEntity($dto);
        }

        $totalAnswerCount = $statisticsDtoList->num_rows;
        $totalAnsweredCount = 0;
        $totalUnAnsweredCount = 0;

        $monthlyAnswerCount = 0;
        $monthlyAnsweredCount = 0;
        $monthlyUnAnsweredCount = 0;

        $previousMonthlyAnsweredCount = 0;
        $previousMonthlyUnAnsweredCount = 0;

        // 누적 응답수
        array_map(function($statistics) use (&$totalAnsweredCount) {
            if($statistics->is_unknown == 0) {
                $totalAnsweredCount++;
            }

        }, $statisticsList);

        // 누적 미응답수
        array_map(function($statistics) use (&$totalUnAnsweredCount) {
            if($statistics->is_unknown == 1) {
                $totalUnAnsweredCount++;
            }

        }, $statisticsList);

        array_map(function($statistics) use (&$monthlyAnswerCount) {
            if($statistics->d_regis >= date('Ymd000000', strtotime('first day of this month'))) {
                $monthlyAnswerCount++;
            }

        }, $statisticsList);

        // 월간 응답수
        array_map(function($statistics) use (&$monthlyAnsweredCount) {
            if($statistics->is_unknown == 0 && $statistics->d_regis >= date('Ymd000000', strtotime('first day of this month'))) {
                $monthlyAnsweredCount++;
            }

        }, $statisticsList);

        // 저번 달 응답수
        array_map(function($statistics) use (&$previousMonthlyAnsweredCount) {
            if($statistics->is_unknown == 0 && $statistics->d_regis >= date('Ym01000000', strtotime('-1 month')) && $statistics->d_regis <= date('Ymt235959', strtotime('-1 month'))) {
                $previousMonthlyAnsweredCount++;
            }

        }, $statisticsList);

        // 월간 미응답수
        array_map(function($statistics) use (&$monthlyUnAnsweredCount) {
            if($statistics->is_unknown == 1 && $statistics->d_regis >= date('Ymd000000', strtotime('first day of this month'))) {
                $monthlyUnAnsweredCount++;
            }

        }, $statisticsList);

        // 저번 달 미응답수
        array_map(function($statistics) use (&$previousMonthlyUnAnsweredCount) {
            if($statistics->is_unknown == 1 && $statistics->d_regis >= date('Ym01000000', strtotime('-1 month')) && $statistics->d_regis <= date('Ymt235959', strtotime('-1 month'))) {
                $previousMonthlyUnAnsweredCount++;
            }

        }, $statisticsList);

        $totalAnsweredRate = $totalAnsweredCount == 0 ? 0 : round($totalAnsweredCount / $totalAnswerCount * 100, 2);
        $monthlyAnsweredRate = $monthlyAnsweredCount == 0 ? 0 : round($monthlyAnsweredCount / $monthlyAnswerCount * 100, 2);

        $previousMonthlyAnsweredRate = $previousMonthlyAnsweredCount == 0 ? 0 : round(($monthlyAnsweredCount - $previousMonthlyAnsweredCount) / $previousMonthlyAnsweredCount * 100, 2);
        $previousMonthlyUnAnsweredRate = $previousMonthlyUnAnsweredCount == 0 ? 0 : round(($monthlyUnAnsweredCount - $previousMonthlyUnAnsweredCount) / $previousMonthlyUnAnsweredCount * 100, 2);

        return StatisticsCount::of(0, 0, 0, 0, 0, 0,
            $totalAnsweredRate,$monthlyAnsweredRate,
            $totalUnAnsweredCount, $monthlyUnAnsweredCount,
            $previousMonthlyAnsweredRate, $previousMonthlyUnAnsweredRate);
    }

    public function getUserStatisticsListByDuration(StatisticsRequestModel $statisticsRequestModel): StatisticsCount
    {
        $statisticsDtoList = $this->mapper->getUserStatisticsListByDuration($statisticsRequestModel);
        $statisticsList = array();

        while ($dto = $statisticsDtoList->fetch_assoc()) {
            $statisticsList[] = (new StatisticsDTO())->toEntity($dto);
        }

        $userTotalAccessCount = 0;
        $userTotalRevisitCount = 0;

        // 총 누적 접속 수
        array_map(function($statistics) use (&$userTotalAccessCount) {
            $userTotalAccessCount += $statistics->page;
        }, $statisticsList);

        // 총 누적 재방문 접속 수
        array_map(function($statistics) use (&$userTotalRevisitCount) {
            if($statistics->type == 2) {
                $userTotalRevisitCount += $statistics->page;
            }
        }, $statisticsList);


        $userTotalRevisitAccessRate = $userTotalRevisitCount == 0 ? 0 : round($userTotalRevisitCount / $userTotalAccessCount * 100, 2);


        return StatisticsCount::of(
            $userTotalAccessCount, 0,
            $userTotalRevisitAccessRate, 0,
            0, 0,
            0,0, 0, 0, 0, 0);
    }

    public function getAnswerStatisticsListByDuration(StatisticsRequestModel $statisticsRequestModel): StatisticsCount
    {
        $statisticsDtoList = $this->mapper->getAnswerStatisticsListByDuration($statisticsRequestModel);
        $statisticsList = array();

        while ($dto = $statisticsDtoList->fetch_assoc()) {
            $statisticsList[] = (new StatisticsDTO())->toEntity($dto);
        }

        $totalAnswerCount = $statisticsDtoList->num_rows;

        $totalAnsweredCount = 0;
        $totalUnAnsweredCount = 0;

        // 누적 응답수
        array_map(function($statistics) use (&$totalAnsweredCount) {
            if($statistics->is_unknown == 0) {
                $totalAnsweredCount++;
            }

        }, $statisticsList);

        // 누적 미응답수
        array_map(function($statistics) use (&$totalUnAnsweredCount) {
            if($statistics->is_unknown == 1) {
                $totalUnAnsweredCount++;
            }

        }, $statisticsList);

        $totalAnsweredRate = $totalAnsweredCount == 0 ? 0 : round($totalAnsweredCount / $totalAnswerCount * 100, 2);


        return StatisticsCount::of(0, 0, 0, 0, 0, 0,
            $totalAnsweredRate,0,
            $totalUnAnsweredCount, 0,
            0, 0);
    }

}