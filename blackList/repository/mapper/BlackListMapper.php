<?php

class BlackListMapper
{
    private $_dbSession;


    public function __construct($sessionFactory)
    {
        $this->_dbSession = $sessionFactory;
    }

    public function getBlackListByBlackListBotUid($botUid)
    {
        $columns = (new BlackListDTO())->getColumns();
        $sql = "
                SELECT      " . implode(", ", $columns) . "
                FROM        rb_chatbot_blackList
                WHERE       bot = " . $botUid . "
        ";

        return mysqli_query($this->_dbSession, $sql);
    }

    public function insertBlackList($params): bool
    {
        $sql = "
                INSERT INTO rb_chatbot_blackList
                    (blackList, bot, d_regis)
                VALUES
                    ('$params->blackList', $params->botUid, '" . date('YmdHis') . "')
               ";

        $queryResult = mysqli_query($this->_dbSession, $sql);
        return $queryResult === false;
    }

    public function updateBlackList($params): bool
    {
        $sql = "
                UPDATE rb_chatbot_blackList
                SET blackList = '" . $params->blackList . "'
                WHERE uid = " . $params->blackListUid . "
               ";

        $queryResult = mysqli_query($this->_dbSession, $sql);
        return $queryResult === false;
    }

}