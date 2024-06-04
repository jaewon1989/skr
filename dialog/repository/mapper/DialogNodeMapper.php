<?php

class DialogNodeMapper
{
    private $_dbSession;


    public function __construct($sessionFactory)
    {
        $this->_dbSession = $sessionFactory;
    }

    public function setDialogNode(DialogNodeDTO $dialogNodeDTO)
    {

        $columnsAndValues = $dialogNodeDTO->getColumnsAndValues();

        $sql = "
                INSERT INTO  rb_chatbot_dialogNode (" . implode(", ", $columnsAndValues['columns']) . ")
                VALUES ('" . implode("', '", $columnsAndValues['values']) . "')";

        $queryResult = mysqli_query($this->_dbSession, $sql);
        if (false === $queryResult) {
            return 0;
        }

        return mysqli_insert_id($this->_dbSession);
    }

    public function copyDialogNode($botUid, $beforeDialogUid, $afterDialogUid)
    {
        $insertColumns = (new DialogNodeDTO())->getColumnsWithoutUid();
        $selectColumns = (new DialogNodeDTO())->changeSelectColumnVal([
            'dialog' => $afterDialogUid,
            'd_regis' => date('YmdHis')
        ]);

        $sql = "
                INSERT INTO  rb_chatbot_dialogNode (" . implode(", ", $insertColumns) . ")
                SELECT      " . implode(", ", $selectColumns) . "
                FROM        rb_chatbot_dialogNode
                WHERE       bot = " . $botUid . "
                AND         dialog = " . $beforeDialogUid . "
                ORDER BY    uid
                ";

        return mysqli_query($this->_dbSession, $sql);
    }

    public function deleteDialogNode($botUid, $dialogUid)
    {
        $sql = "
                DELETE
                FROM    rb_chatbot_dialogNode
                WHERE   bot = " . $botUid . "
                AND     dialog = " . $dialogUid;

        return mysqli_query($this->_dbSession, $sql);
    }

}