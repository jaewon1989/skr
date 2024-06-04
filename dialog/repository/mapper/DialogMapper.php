<?php

class DialogMapper
{
    private $_dbSession;


    public function __construct($sessionFactory)
    {
        $this->_dbSession = $sessionFactory;
    }

    public function getDialogVendorByBotUid($botUid)
    {
        $sql = "
                SELECT  vendor
                FROM    rb_chatbot_dialog
                WHERE   bot = " . $botUid . "
                LIMIT   1
                ";

        return mysqli_query($this->_dbSession, $sql);
    }

    public function setDialog(DialogDTO $dialogDTO)
    {
        $columnsAndValues = $dialogDTO->getColumnsAndValues();
        $sql = "
                INSERT INTO  rb_chatbot_dialog (" . implode(", ", $columnsAndValues['columns']) . ")
                VALUES ('" . implode("', '", $columnsAndValues['values']) . "')";

        $queryResult = mysqli_query($this->_dbSession, $sql);
        if (false === $queryResult) {
            return 0;
        }

        return mysqli_insert_id($this->_dbSession);
    }

    public function copyDialog(DialogParamsModel $dialogParams)
    {

        $insertColumns = (new DialogDTO())->getColumnsWithoutUid();
        $selectColumns = (new DialogDTO())->changeSelectColumnVal([
            'name' => "'" . $dialogParams->dialogName . "'",
            'active' => 0,
            'd_regis' => date('YmdHis')
        ]);

        $sql = "
                INSERT INTO  rb_chatbot_dialog (" . implode(", ", $insertColumns) . ")
                SELECT      " . implode(", ", $selectColumns) . "
                FROM        rb_chatbot_dialog
                WHERE       bot = " . $dialogParams->botUid . "
                AND         uid = " . $dialogParams->dialogUid;

        $queryResult = mysqli_query($this->_dbSession, $sql);
        if (false === $queryResult) {
            return 0;
        }

        return mysqli_insert_id($this->_dbSession);
    }

    public function getDialogByDialogUid(DialogParamsModel $dialogParams)
    {
        $columns = (new DialogDTO())->getColumns();
        $sql = "
                SELECT      " . implode(", ", $columns) . "
                FROM        rb_chatbot_dialog
                WHERE       bot = " . $dialogParams->botUid . "
                AND         uid = " . $dialogParams->dialogUid;

        return mysqli_query($this->_dbSession, $sql);
    }

    public function getDialogListByBotUid($botUid)
    {
        $columns = (new DialogDTO())->getColumns();
        $sql = "
                SELECT      " . implode(", ", $columns) . "
                FROM        rb_chatbot_dialog
                WHERE       bot = " . $botUid . "
                AND         type = 'D'
                ORDER BY    uid
        ";

        return mysqli_query($this->_dbSession, $sql);
    }

    public function changeDialogIsTempDelFlag(DialogParamsModel $dialogParams)
    {
        $sql = "
                UPDATE  rb_chatbot_dialog
                SET     is_temp_del = 'Y'
                WHERE   bot = " . $dialogParams->botUid . "
                AND     uid = " . $dialogParams->dialogUid;

        return mysqli_query($this->_dbSession, $sql);
    }

    public function updateActiveDialog($botUid, $dialogUid)
    {
        $sql = "
                UPDATE  rb_chatbot_dialog
                SET     active = 
                        CASE
                            WHEN uid = " . $dialogUid . " THEN 1
                            ELSE 0
                        END
                WHERE   bot = " . $botUid;

        return mysqli_query($this->_dbSession, $sql);
    }

    public function chkDupDialogName($botUid, $dialogName)
    {
        $sql = "
                SELECT 
                    CASE
                        WHEN    EXISTS(
                                    SELECT  1 
                                    FROM    rb_chatbot_dialog
                                    WHERE   bot = " . $botUid . "
                                    AND     name = '" . $dialogName . "'
                                )
                        THEN    'true'
                        ELSE    'false'
                    END AS result;
        ";

        return mysqli_query($this->_dbSession, $sql);
    }

    public function deleteDialog($botUid, $dialogUid)
    {
        $sql = "
                DELETE
                FROM    rb_chatbot_dialog
                WHERE   bot = " . $botUid . "
                AND     uid = " . $dialogUid;

        return mysqli_query($this->_dbSession, $sql);
    }

    public function isBotExist($botUid)
    {
        $sql = "
                SELECT
                    CASE
                        WHEN    EXISTS (
                                    SELECT  1
                                    FROM    rb_chatbot_dialog
                                    WHERE   bot = " . $botUid . "
                                )
                        THEN    'true'
                        ELSE    'false'
                    END AS result;
        ";

        return mysqli_query($this->_dbSession, $sql);
    }

    public function isDialogExist($botUid, $dialogUid)
    {
        $sql = "
                SELECT
                    CASE
                        WHEN    EXISTS (
                                    SELECT  1
                                    FROM    rb_chatbot_dialog
                                    WHERE   bot = " . $botUid . "
                                    AND     uid = " . $dialogUid . "
                                )
                        THEN    'true'
                        ELSE    'false'
                    END AS result;
        ";

        return mysqli_query($this->_dbSession, $sql);
    }

}