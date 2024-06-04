<?php

require_once 'bot/repository/BotMapper.php';

class BotService
{
    private $botMapper;

    public function __construct()
    {
        global $DB_CONNECT;
        $this->botMapper = new BotMapper($DB_CONNECT);
    }

    public function getBotByUid($uid): Bot
    {
        $botDto = $this->botMapper->getBotByUid($uid);
        $dto = $botDto->fetch_assoc();
        return $this->getBot($dto);
    }

    public function getBotList(BotModel $botModel): array
    {
        global $my;
        /*foreach ($botList as $bot) {
            if ($GLOBALS['_cloud_'] === true && !array_key_exists($bot['id'], $_SESSION['mbr_bot'])) continue;
            if (!in_array($bot['uid'], $my['mybot'])) continue;
        }*/

        $botDtoList = $this->botMapper->getBotList($botModel);
        $botList = array();
        while ($dto = $botDtoList->fetch_assoc()) {
            $bot = $this->getBot($dto);
            if(in_array($bot->uid, $my['mybot'])){
                $botList[] = $bot;
            }
        }
        return $botList;
    }

    /**
     * @param $dto
     * @return Bot
     */
    public function getBot($dto): Bot
    {
        $bot = (new BotDTO)->toEntity($dto);

        $callNo = '';

        if ('call' == $bot->bottype && $bot->callno) {
            $callNoList = explode(',', $bot->callno);

            foreach ($callNoList as $no) {
                $callNo .= getStrToPhoneFormat($no) . ', ';
            }
            $bot->callno = rtrim($callNo, ', ');
        }

        if (empty($bot->avatar) || !file_exists($_SERVER['DOCUMENT_ROOT'] . $bot->avatar)) {
            $_skin = (!isset($bot->chat_skin) || empty($bot->chat_skin)) ? 'skin.default' : $bot->chat_skin;
            $bot->avatar = '/_core/skin/images/sender_ico_' . explode(".", $_skin)[1] . '.png';
        }
        return $bot;
    }

    public function isBotExist($botUid): bool
    {
        return 'true' === $this->botMapper->isBotExist($botUid)->fetch_assoc()['result'];
    }
}