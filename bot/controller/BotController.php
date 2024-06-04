<?php
    // import
    require_once 'common/controller/CommonController.php';

    require_once 'bot/repository/dto/BotBuilder.php';
    require_once 'bot/repository/dto/BotDTO.php';
    require_once 'bot/repository/BotMapper.php';
    require_once 'bot/service/dto/Bot.php';
    require_once 'bot/service/BotService.php';

    require_once 'bot/controller/model/BotModel.php';

class BotController extends CommonController
{
    private $botService;
    public function __construct() {
        $this->botService = new BotService();
    }

    public function getBotByUid($uid): Bot
    {
        return $this->botService->getBotByUid($uid);
    }

    public function getBotList(BotModel $model): array
    {
        return $this->botService->getBotList($model);
    }

}