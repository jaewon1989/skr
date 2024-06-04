<?php

class DialogResGroupMapper
{
    private $_dbSession;


    public function __construct($sessionFactory)
    {
        $this->_dbSession = $sessionFactory;
    }

    public function copyDialogResGroup($botUid, $beforeDialogUid, $afterDialogUid)
    {
        $insertColumns = (new DialogResGroupDTO())->getColumnsWithoutUid();
        $selectColumns = (new DialogResGroupDTO())->changeSelectColumnVal([
            'dialog' => $afterDialogUid
        ]);

        $sql = "
                INSERT INTO  rb_chatbot_dialogResGroup (" . implode(", ", $insertColumns) . ")
                SELECT      " . implode(", ", $selectColumns) . "
                FROM        rb_chatbot_dialogResGroup
                WHERE       bot = " . $botUid . "
                AND         dialog = " . $beforeDialogUid . "
                ORDER BY    uid
                ";

        return mysqli_query($this->_dbSession, $sql);
    }

    public function deleteDialogResGroup($botUid, $dialogUid)
    {
        $sql = "
                DELETE
                FROM    rb_chatbot_dialogResGroup
                WHERE   bot = " . $botUid . "
                AND     dialog = " . $dialogUid;

        return mysqli_query($this->_dbSession, $sql);
    }

}