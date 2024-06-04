<?php

class MbrdataMapper
{
    private $_dbSession;


    public function __construct($sessionFactory)
    {
        $this->_dbSession = $sessionFactory;
    }

    public function chkDupEmail($email)
    {
        $sql = "
                SELECT 
                    CASE
                        WHEN    EXISTS(
                                    SELECT  1
                                    FROM    rb_s_mbrdata
                                    WHERE   email = '" . $email . "'
                                )
                        THEN    'true'
                        ELSE    'false'
                    END AS result;
        ";

        return mysqli_query($this->_dbSession, $sql);
    }

    public function chkDupEmailOfMbruid($email, $mbruid)
    {
        $sql = "
                SELECT
                    CASE
                        WHEN    EXISTS(
                                    SELECT  1
                                    FROM    rb_s_mbrdata
                                    WHERE   email = '" . $email . "'
                                    AND     memberuid != " . $mbruid . "
                                )
                        THEN    'true'
                        ELSE    'false'
                    END AS result;
        ";

        return mysqli_query($this->_dbSession, $sql);
    }

    public function setMbrdata(MbrdataDto $mbrdataDto)
    {
        $columnsAndValues = $mbrdataDto->getColumnsAndValues();
        $sql = "
                INSERT INTO  rb_s_mbrdata (" . implode(", ", $columnsAndValues['columns']) . ")
                VALUES ('" . implode("', '", $columnsAndValues['values']) . "')";

        $queryResult = mysqli_query($this->_dbSession, $sql);
        if (false === $queryResult) {
            return 0;
        }

        return mysqli_insert_id($this->_dbSession);
    }

    public function updateMbrdata(MbrdataDto $mbrdataDto)
    {
        $sql = "
                UPDATE  rb_s_mbrdata
                SET     " . implode(', ', $mbrdataDto->getConditionForSet(['memberuid'])) . "
                WHERE   memberuid = " . $mbrdataDto->memberuid;

        return mysqli_query($this->_dbSession, $sql);
    }

    public function chkBeforePw($cryptPw)
    {
        $sql = "
               SELECT 
                   CASE
                        WHEN    EXISTS(
                                    SELECT  1
                                    FROM    rb_s_mbrdata
                                    WHERE   before_pw1 = '" . $cryptPw . "' 
                                    OR      before_pw2 = '" . $cryptPw . "'
                                ) 
                        THEN    'true'
                        ELSE    'false'
                    END AS result
        ";

        return mysqli_query($this->_dbSession, $sql);
    }

}