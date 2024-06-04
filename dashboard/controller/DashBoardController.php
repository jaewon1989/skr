<?php
    // import
    require_once 'common/controller/CommonController.php';

    require_once 'bot/repository/dto/BotBuilder.php';
    require_once 'bot/repository/dto/BotDTO.php';
    require_once 'bot/repository/BotMapper.php';
    require_once 'bot/service/dto/Bot.php';
    require_once 'bot/service/BotService.php';
    require_once 'bot/controller/model/BotModel.php';

    require_once 'statistics/repository/dto/StatisticsBuilder.php';
    require_once 'statistics/repository/dto/StatisticsDTO.php';
    require_once 'statistics/repository/StatisticsMapper.php';
    require_once 'statistics/service/dto/Statistics.php';
    require_once 'statistics/service/dto/StatisticsCount.php';
    require_once 'statistics/service/StatisticsService.php';

    require_once 'dashboard/controller/model/DashBoardResponseModel.php';


class DashBoardController extends CommonController
{
    private $botService;
    private $statisticsService;
    private $response;

    public function __construct() {
        $this->botService = new BotService();
        $this->statisticsService = new StatisticsService();
    }

    public function getDashboard(Bot $bot)
    {
        global $TMPL;
        $viewHtml = 'view/dashboard/dashboard';

        $botInfo = $this->botService->getBotByUid($bot->uid);
        $statistics = DashBoardResponseModel::of($this->statisticsService->getUserStatisticsList($bot), $this->statisticsService->getAnswerStatisticsList($bot));

        $TMPL['uid'] = $bot->uid;
        $TMPL['vendor'] = $bot->vendor;
        $TMPL['name'] = $botInfo->name;
        $TMPL['title'] = ($botInfo->bottype == 'call' ? '콜봇 관리' : '챗봇 관리').' / 대시보드';
        $TMPL['startDate'] = date('Y-m-d', strtotime('first day of this month'));
        $TMPL['endDate'] = date('Y-m-d', strtotime('today'));

        $TMPL['userTotalAccessCount'] = $statistics->userTotalAccessCount;
        $TMPL['userMonthlyAccessCount'] = $statistics->userMonthlyAccessCount;
        $TMPL['userTotalRevisitAccessRate'] = $statistics->userTotalRevisitAccessRate;
        $TMPL['userMonthlyRevisitAccessRate'] = $statistics->userMonthlyRevisitAccessRate;
        $TMPL['userPreviousMonthlyAccessRate'] = $statistics->userPreviousMonthlyAccessRate;
        $TMPL['userPreviousMonthlyAccessRateClass'] = $statistics->userPreviousMonthlyAccessRate == 0 ? '' : ($statistics->userPreviousMonthlyAccessRate > 0 ? 'green' : 'red');
        $TMPL['userPreviousMonthlyRevisitRate'] = $statistics->userPreviousMonthlyRevisitRate;
        $TMPL['userPreviousMonthlyRevisitRateClass'] = $statistics->userPreviousMonthlyRevisitRate == 0 ? '' : ($statistics->userPreviousMonthlyRevisitRate > 0 ? 'green' : 'red');
        $TMPL['totalAnsweredRate'] = $statistics->totalAnsweredRate;
        $TMPL['monthlyAnsweredRate'] = $statistics->monthlyAnsweredRate;
        $TMPL['totalUnAnsweredCount'] = $statistics->totalUnAnsweredCount;
        $TMPL['monthlyUnAnsweredCount'] = $statistics->monthlyUnAnsweredCount;
        $TMPL['previousMonthlyAnsweredRate'] = $statistics->previousMonthlyAnsweredRate;
        $TMPL['previousMonthlyAnsweredRateClass'] = $statistics->previousMonthlyAnsweredRate == 0 ? '' : ($statistics->previousMonthlyAnsweredRate > 0 ? 'green' : 'red');
        $TMPL['previousMonthlyUnAnsweredRate'] = $statistics->previousMonthlyUnAnsweredRate;
        $TMPL['previousMonthlyUnAnsweredRateClass'] = $statistics->previousMonthlyUnAnsweredRate == 0 ? '' : ($statistics->previousMonthlyUnAnsweredRate > 0 ? 'green' : 'red');

        $html = new HtmlParser($viewHtml);
        $this->response['dashboard'] = $html->parse();
        $this->response['commonElement'] = parent::getCommonElement();
        return $this->response;
    }

}