<?php

class VendorMapper
{
    private $_dbSession;


    public function __construct($sessionFactory)
    {
        $this->_dbSession = $sessionFactory;
    }

    public function getVendor()
    {
        $sql = "
                SELECT  uid, mbruid
                FROM    rb_chatbot_vendor
                WHERE   auth = 1
                AND     display = 1
                LIMIT   1
               ";

        return mysqli_query($this->_dbSession, $sql);
    }

}