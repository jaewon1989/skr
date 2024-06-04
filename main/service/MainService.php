<?php

class MainService
{
    private $botService;
    private $statisticsService;
    public function __construct() {
        $this->botService = new BotService();
        $this->statisticsService = new StatisticsService();
    }

    public function getBotList(BotModel $model): string
    {
        global $TMPL;
        $viewHtml = 'view/main/'.$model->viewMode;
        $rows = '';

        $botList = $this->botService->getBotList($model);
        $bots = array();

        foreach ($botList as $bot) {

            $statisticsCount = $this->statisticsService->getUserStatisticsList($bot);
            $bots[] = MainResponseModel::of($bot, $statisticsCount);
        }

        foreach ($bots as $bot) {
            $TMPL['uid'] = $bot->uid;
            $TMPL['vendor'] = $bot->vendor;
            $TMPL['avatar'] = $bot->avatar;
            $TMPL['name'] = $bot->botName;
            $TMPL['callNo'] = $bot->callNo;
            $TMPL['service'] = $bot->service;
            $TMPL['userTotalAccessCount'] = $bot->userTotalAccessCount;
            $TMPL['userMonthlyAccessCount'] = $bot->userMonthlyAccessCount;
            $TMPL['dRegis'] = $bot->dRegis;
            $TMPL['link'] = $g['s'].'/adm/dashboard?bot='.$bot->uid;

            $html = new HtmlParser($viewHtml);
            $rows .= $html->parse();
        }

        $TMPL['bot_rows'] = $rows;
        $skin = new skin('vendor/botList');
        return $skin->make();
    }

    public function getHeader(BotModel $botModel): string
    {
        global $TMPL, $my, $g;

        $TMPL['botType'] = $botModel->botType;
        $TMPL['botTypeName'] = $botModel->botType == 'call' ? '콜봇' : '챗봇';
        $TMPL['listType'] = $botModel->viewMode == 'card' ? 'list': 'large';

        $viewHtml = 'view/main/layout/header';
        $headerHtml = new HtmlParser($viewHtml);
        return $headerHtml->parse();
    }

    public function getModal(BotModel $botModel): string
    {
        global $TMPL;

        $TMPL['botType'] = $botModel->botType;
        $TMPL['botTypeName'] = $botModel->botType == 'call' ? '콜봇' : '챗봇';
        $TMPL['role'] = $_GET['role']?$_GET['role']:'bot';

        $viewHtml = 'view/main/layout/botModal';
        $headerHtml = new HtmlParser($viewHtml);
        return $headerHtml->parse();
    }

}