<?php
    // import
    require_once 'common/controller/CommonController.php';

    require_once 'statistics/repository/dto/StatisticsBuilder.php';
    require_once 'statistics/repository/dto/StatisticsDTO.php';
    require_once 'statistics/repository/StatisticsMapper.php';
    require_once 'statistics/service/dto/Statistics.php';
    require_once 'statistics/service/dto/StatisticsCount.php';
    require_once 'statistics/service/StatisticsService.php';

    require_once 'statistics/controller/model/StatisticsRequestModel.php';
    require_once 'statistics/controller/model/StatisticsResponseModel.php';

class StatisticsController extends CommonController
{
    private $statisticsService;
    public function __construct() {
        $this->statisticsService = new StatisticsService();
    }

    public function getUserStatisticsListByDuration(StatisticsRequestModel $statisticsRequestModel): StatisticsResponseModel
    {
        /*return StatisticsResponseModel::of(StatisticsCount::of(
            111, 0,
            20, 0,
            0, 0,
            0,0, 0, 0, 0, 0),
            StatisticsCount::of(0, 0, 0, 0, 0, 0,
                90,0,
                10, 0,
                0, 0)
        );*/
        return StatisticsResponseModel::of($this->statisticsService->getUserStatisticsListByDuration($statisticsRequestModel), $this->statisticsService->getAnswerStatisticsListByDuration($statisticsRequestModel));
    }

}