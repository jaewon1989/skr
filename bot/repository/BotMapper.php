<?php

class BotMapper
{
    private $dbSession;

    public function __construct($sessionFactory)
    {
        $this->dbSession = $sessionFactory;
    }

    public function getBotByUid($uid)
    {
        $sql = "SELECT a.*";
        $sql .= "     , GROUP_CONCAT(CASE WHEN b.name = 'chatSkin' THEN b.value ELSE NULL END) AS chat_skin";
        $sql .= " FROM rb_chatbot_bot a";
        $sql .= " LEFT JOIN rb_chatbot_botSettings b on a.uid = b.bot and a.vendor = b.vendor";
        $sql .= " WHERE a.uid = " . $uid;

        return mysqli_query($this->dbSession, $sql);
    }

    public function getBotList(BotModel $botModel)
    {
        $sql = "SELECT a.*";
        $sql .= "     , GROUP_CONCAT(CASE WHEN b.name = 'chatSkin' THEN b.value ELSE NULL END) AS chat_skin";
        $sql .= " FROM rb_chatbot_bot a";
        $sql .= " LEFT JOIN rb_chatbot_botSettings b on a.uid = b.bot and a.vendor = b.vendor";
        $sql .= " WHERE a.vendor = " . $botModel->vendor;
        $sql .= "  AND a.bottype='" . $botModel->botType . "'";

        if (!empty($botModel->searchField)) {
            if ($botModel->searchField === 'name') {
                $sql .= " AND a.name LIKE '%" . $botModel->searchKeyword . "%'";
            } else if ($botModel->searchField === 'callno') {
                $sql .= " AND a.callno LIKE '%" . $botModel->searchKeyword . "%'";
            }
        }

        $sql .= " GROUP BY a.uid";
        $sql .= " ORDER BY a.hidden DESC, a.nrank ASC";

        if (!empty($botModel->sortField)) {
            $sql .= ", {$botModel->sortField} DESC";
        } else {
            $sql .= ", uid DESC";
        }

        return mysqli_query($this->dbSession, $sql);
    }

    public function isBotExist($botUid)
    {
        $sql = "
                SELECT
                    CASE
                        WHEN    EXISTS (
                                    SELECT  1 
                                    FROM    rb_chatbot_bot 
                                    WHERE   uid = " . $botUid . "
                                )
                        THEN    'true'
                        ELSE    'false'
                    END AS result;
        ";

        return mysqli_query($this->dbSession, $sql);
    }
}