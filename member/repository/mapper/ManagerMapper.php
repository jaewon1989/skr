<?php

class ManagerMapper
{
    private $_dbSession;


    public function __construct($sessionFactory)
    {
        $this->_dbSession = $sessionFactory;
    }

    public function setManager(ManagerDto $managerDto)
    {
        $columnsAndValues = $managerDto->getColumnsAndValues();
        $sql = "
                INSERT INTO  rb_chatbot_manager (" . implode(", ", $columnsAndValues['columns']) . ")
                VALUES ('" . implode("', '", $columnsAndValues['values']) . "')";

        $queryResult = mysqli_query($this->_dbSession, $sql);
        if (false === $queryResult) {
            return 0;
        }

        return mysqli_insert_id($this->_dbSession);
    }

}