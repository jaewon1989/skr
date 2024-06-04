<?php

    require_once 'common/controller/CommonController.php';

    require_once 'statistics/repository/dto/StatisticsBuilder.php';
    require_once 'statistics/repository/dto/StatisticsDTO.php';
    require_once 'statistics/repository/StatisticsMapper.php';
    require_once 'statistics/service/dto/Statistics.php';
    require_once 'statistics/service/dto/StatisticsCount.php';
    require_once 'statistics/service/StatisticsService.php';

    require_once 'bot/repository/dto/BotBuilder.php';
    require_once 'bot/repository/dto/BotDTO.php';
    require_once 'bot/repository/BotMapper.php';
    require_once 'bot/service/dto/Bot.php';
    require_once 'bot/service/BotService.php';

    require_once 'bot/controller/model/BotModel.php';

    require_once 'main/service/MainService.php';
    require_once 'main/controller/model/MainResponseModel.php';
class MainController extends CommonController
{
    private $mainService;
    private $response;

    public function __construct() {
        $this->mainService = new MainService();
    }

    public function getBotList(BotModel $model): string
    {
        return $this->mainService->getBotList($model);
    }

    public function getHeader(BotModel $model): string
    {
        return $this->mainService->getHeader($model);
    }

    public function getModal(BotModel $model): string
    {
        return $this->mainService->getModal($model);
    }

    public function getMainList(BotModel $model): array
    {
        $this->response['list'] = $this->getBotList($model);
        $this->response['option'] = $this->getHeader($model);
        $this->response['modal'] = $this->getModal($model);
       // $this->response['commonElement'] = $this->commonElement;
        $this->response['commonElement'] = parent::getCommonElement();
        return $this->response;
    }
}