<?php

class MbridMapper
{
    private $_dbSession;


    public function __construct($sessionFactory)
    {
        $this->_dbSession = $sessionFactory;
    }

    public function chkDupId($id)
    {
        $sql = "
                SELECT 
                    CASE
                        WHEN    EXISTS(
                                    SELECT  1 
                                    FROM    rb_s_mbrid
                                    WHERE   id = '" . $id . "'
                                )
                        THEN    'true'
                        ELSE    'false'
                    END AS result;
        ";

        return mysqli_query($this->_dbSession, $sql);
    }

    public function setMbrid(MbridDto $mbridDto)
    {
        $columnsAndValues = $mbridDto->getColumnsAndValues();
        $sql = "
                INSERT INTO  rb_s_mbrid (" . implode(", ", $columnsAndValues['columns']) . ")
                VALUES ('" . implode("', '", $columnsAndValues['values']) . "')";

        $queryResult = mysqli_query($this->_dbSession, $sql);
        if (false === $queryResult) {
            return 0;
        }

        return mysqli_insert_id($this->_dbSession);
    }

    public function getMemberDataByMbruid($mbruid)
    {
        $sql = "
                SELECT      mbrid.id, mbrid.pw, mbrdata.d_regis, mbrdata.before_pw1
                FROM        rb_s_mbrid AS mbrid
                LEFT JOIN   rb_s_mbrdata AS mbrdata
                        ON  mbrid.uid = mbrdata.memberuid
                WHERE       mbrid.uid = " . $mbruid;

        return mysqli_query($this->_dbSession, $sql);
    }

    public function updateMbrid(MbridDto $mbridDto)
    {
        $sql = "
                UPDATE  rb_s_mbrid
                SET     pw = '" . $mbridDto->pw . "'
                WHERE   uid = " . $mbridDto->uid;

        return mysqli_query($this->_dbSession, $sql);
    }

}