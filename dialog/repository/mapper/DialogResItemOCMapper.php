<?php

class DialogResItemOCMapper
{
    private $_dbSession;


    public function __construct($sessionFactory)
    {
        $this->_dbSession = $sessionFactory;
    }

    public function getDialogResItemOCList($botUid, $dialogResItemUid)
    {
        $columns = (new DialogResItemOCDTO())->getColumns();
        $sql = "
                SELECT  " . implode(", ", $columns) . "
                FROM    rb_chatbot_dialogResItemOC
                WHERE   bot = " . $botUid . "
                AND     item = " . $dialogResItemUid;

        return mysqli_query($this->_dbSession, $sql);
    }

    public function getDialogResItemOCByResType(DialogParamsModel $dialogParams, $itemUid)
    {
        $sql = "
                SELECT  uid
                FROM    rb_chatbot_dialogResItemOC
                WHERE   bot = " . $dialogParams->botUid . "
                AND     item = " . $itemUid . "
                AND     resType = 'api';
        ";

        return mysqli_query($this->_dbSession, $sql);
    }

    public function copyDialogResItemOC(DialogParamsModel $dialogParams, DialogResItemChangeInfo $info)
    {
        $insertColumns = (new DialogResItemOCDTO())->getColumnsWithoutUid();
        $selectColumns = (new DialogResItemOCDTO())->changeSelectColumnVal([
            'item' => $info->afterDialogResItemUid
        ]);

        $sql = "
                INSERT INTO  rb_chatbot_dialogResItemOC (" . implode(", ", $insertColumns) . ")
                SELECT      " . implode(", ", $selectColumns) . "
                FROM        rb_chatbot_dialogResItemOC
                WHERE       bot = " . $dialogParams->botUid . "
                AND         item = " . $info->beforeDialogResItemUid . "
                ORDER BY    uid
                ";

        return mysqli_query($this->_dbSession, $sql);
    }

    public function deleteDialogResItemOC($botUid, $dialogResItemUid)
    {
        $sql = "
                DELETE
                FROM    rb_chatbot_dialogResItemOC
                WHERE   bot = " . $botUid . "
                AND     item = " . $dialogResItemUid;

        return mysqli_query($this->_dbSession, $sql);
    }

}