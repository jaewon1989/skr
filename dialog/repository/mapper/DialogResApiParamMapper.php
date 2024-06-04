<?php

class DialogResApiParamMapper
{
    private $_dbSession;


    public function __construct($sessionFactory)
    {
        $this->_dbSession = $sessionFactory;
    }

    public function copyDialogResApiParam(DialogResItemOCChangeInfo $info)
    {
        $insertColumns = (new DialogResApiParamDTO())->getColumnsWithoutUid();
        $selectColumns = (new DialogResApiParamDTO())->changeSelectColumnVal([
            'itemOC' => $info->afterDialogResItemOCUid
        ]);

        $sql = "
                INSERT INTO  rb_chatbot_dialogResApiParam (" . implode(", ", $insertColumns) . ")
                SELECT      " . implode(", ", $selectColumns) . "
                FROM        rb_chatbot_dialogResApiParam
                WHERE       itemOC = " . $info->beforeDialogResItemOCUid . "
                ORDER BY    uid
                ";

        return mysqli_query($this->_dbSession, $sql);
    }

    public function deleteDialogResApiParam($dialogResItemOCUid)
    {
        $sql = "
                DELETE
                FROM    rb_chatbot_dialogResApiParam
                WHERE   itemOC = " . $dialogResItemOCUid;

        return mysqli_query($this->_dbSession, $sql);
    }

}