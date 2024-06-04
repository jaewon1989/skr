<?php

class MainResponseModel
{
    use GetSetGenerator;
    private $uid;
    private $botType;
    private $vendor;
    private $botName;
    private $service;
    private $mbrUid;
    private $id;
    private $callNo;
    private $dRegis;
    private $avatar;
    private $userTotalAccessCount;
    private $userMonthlyAccessCount;

    public static function of(Bot $bot, StatisticsCount $statisticsCount): MainResponseModel
    {
        $instance = new self();
        $instance->uid = $bot->uid;
        $instance->botType = $bot->bottype;
        $instance->vendor = $bot->vendor;
        $instance->botName = $bot->name;
        $instance->service = $bot->service;
        $instance->mbrUid = $bot->mbrUid;
        $instance->id = $bot->id;
        $instance->callNo = $bot->callno;
        $instance->dRegis = DateTime::createFromFormat('YmdHis', $bot->d_regis)->format('Y-m-d H:i:s');
        $instance->avatar = $bot->avatar;
        $instance->userTotalAccessCount = $statisticsCount->userTotalAccessCount;
        $instance->userMonthlyAccessCount = $statisticsCount->userMonthlyAccessCount;
        return $instance;
    }
}