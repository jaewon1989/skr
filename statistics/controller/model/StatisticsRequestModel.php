<?php

class StatisticsRequestModel
{

    use GetSetGenerator;
    private $vendor;
    private $uid;
    private $startDate;
    private $endDate;

    public static function of(String $vendor, String $uid, String $startDate, String $endDate): StatisticsRequestModel
    {
        $instance = new self();
        $instance->vendor = $vendor;
        $instance->uid = $uid;
        $instance->startDate = $startDate;
        $instance->endDate = $endDate;
        return $instance;
    }

}