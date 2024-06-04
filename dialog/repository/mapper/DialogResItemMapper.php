<?php

class DialogResItemMapper
{
    private $_dbSession;


    public function __construct($sessionFactory)
    {
        $this->_dbSession = $sessionFactory;
    }

    public function getDialogResItemList($botUid, $dialogUid)
    {
        $columns = (new DialogResItemDTO())->getColumns();
        $sql = "
                SELECT      " . implode(", ", $columns) . "
                FROM        rb_chatbot_dialogResItem
                WHERE       bot = " . $botUid . "
                AND         dialog = " . $dialogUid . "
                ORDER BY    uid
                ";

        return mysqli_query($this->_dbSession, $sql);
    }

    public function copyDialogResItem($botUid, $beforeDialogUid, $afterDialogUid)
    {
        $insertColumns = (new DialogResItemDTO())->getColumnsWithoutUid();
        $selectColumns = (new DialogResItemDTO())->changeSelectColumnVal([
            'dialog' => $afterDialogUid
        ]);

        $sql = "
                INSERT INTO  rb_chatbot_dialogResItem (" . implode(", ", $insertColumns) . ")
                SELECT      " . implode(", ", $selectColumns) . "
                FROM        rb_chatbot_dialogResItem
                WHERE       bot = " . $botUid . "
                AND         dialog = " . $beforeDialogUid . "
                ORDER BY    uid
                ";

        return mysqli_query($this->_dbSession, $sql);
    }

    public function deleteDialogResItem($botUid, $dialogUid)
    {
        $sql = "
                DELETE
                FROM    rb_chatbot_dialogResItem
                WHERE   bot = " . $botUid . "
                AND     dialog = " . $dialogUid;

        return mysqli_query($this->_dbSession, $sql);
    }

}