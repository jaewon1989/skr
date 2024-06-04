<?php

class StatisticsMapper {
    private $dbSession;

    public function __construct($sessionFactory){
        $this->dbSession = $sessionFactory;
    }

    public function getUserStatisticsList(Bot $bot)
    {
        $sql = "SELECT botuid, page, d_regis, type";
        $sql .= " FROM rb_chatbot_counter";
        $sql .= " WHERE vendor = ".$bot->vendor;
        $sql .= " AND botuid = ".$bot->uid;
        //$sql .= " AND type = 2";

        return mysqli_query($this->dbSession, $sql);
    }

    public function getUserStatisticsListByDuration(StatisticsRequestModel $model)
    {
        $sql = "SELECT botuid, page, d_regis, type";
        $sql .= " FROM rb_chatbot_counter";
        $sql .= " WHERE vendor = ".$model->vendor;
        $sql .= " AND botuid = ".$model->uid;

        if($model->startDate !== '' && $model->endDate !== '' ){
            $sql .= " AND d_regis between '".$model->startDate."' and '".$model->endDate."'";
        }
        //$sql .= " AND type = 2";

        return mysqli_query($this->dbSession, $sql);
    }

    public function getAnswerStatisticsList(Bot $bot){

        $sql = "SELECT is_unknown, d_regis";
        $sql .= " FROM rb_chatbot_chatLog";
        $sql .= " WHERE vendor = ".$bot->vendor;
        $sql .= " AND bot = ".$bot->uid;
        $sql .= " AND roomToken <> ''";

        return mysqli_query($this->dbSession, $sql);
    }

    public function getAnswerStatisticsListByDuration(StatisticsRequestModel $model){

        $sql = "SELECT is_unknown, d_regis";
        $sql .= " FROM rb_chatbot_chatLog";
        $sql .= " WHERE vendor = ".$model->vendor;
        $sql .= " AND bot = ".$model->uid;

        if($model->startDate && $model->endDate){
            $sql .= " AND  d_regis between '".$model->startDate."000000' and '".$model->endDate."235959'";
        }

        $sql .= " AND roomToken <> ''";

        return mysqli_query($this->dbSession, $sql);
    }


}