<?php

class DialogResApiOutputMapper
{
    private $_dbSession;


    public function __construct($sessionFactory)
    {
        $this->_dbSession = $sessionFactory;
    }

    public function copyDialogResApiOutput(DialogResItemOCChangeInfo $info)
    {
        $insertColumns = (new DialogResApiOutputDTO())->getColumnsWithoutUid();
        $selectColumns = (new DialogResApiOutputDTO())->changeSelectColumnVal([
            'itemOC' => $info->afterDialogResItemOCUid
        ]);

        $sql = "
                INSERT INTO  rb_chatbot_dialogResApiOutput (" . implode(", ", $insertColumns) . ")
                SELECT      " . implode(", ", $selectColumns) . "
                FROM        rb_chatbot_dialogResApiOutput
                WHERE       itemOC = " . $info->beforeDialogResItemOCUid . "
                ORDER BY    uid
                ";

        return mysqli_query($this->_dbSession, $sql);
    }

    public function deleteDialogResApiOutput($botUid, $dialogResItemOCUid)
    {
        $sql = "
                DELETE
                FROM    rb_chatbot_dialogResApiOutput
                WHERE   bot = " . $botUid . "
                AND     itemOC = " . $dialogResItemOCUid;

        return mysqli_query($this->_dbSession, $sql);
    }

}