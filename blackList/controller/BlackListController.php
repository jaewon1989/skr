<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/utils/trait/GetSetGenerator.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/utils/trait/RepositoryTrait.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/blackList/controller/model/BlackListParamsModel.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/blackList/controller/model/BlackListResponseModel.php';

require_once $_SERVER['DOCUMENT_ROOT'].'/blackList/service/BlackListService.php';

require_once $_SERVER['DOCUMENT_ROOT'].'/interface/util/InterfaceUtil.php';

class BlackListController
{
    private $_params;
    private $_blackListService;
    private $_interfaceRequiredParams = [
        'getBlackList' => [
            'botUid'
        ],
        'insertBlackList' => [
            'botUid',
            'blackListUid',
            'blackList'
        ],
        'updateBlackList' => [
            'botUid',
            'blackListUid'
        ]
    ];
    private $_errorMsgs = [
        'mode' => 'mode error',
        'botUid' => 'botUid error',
        'blackList' => 'blackList value error'
    ];


    public function __construct()
    {
        $this->_blackListService = new BlackListService();
    }

    /**
     * @throws JsonException
     */
    public function interfaceProcess($params): void
    {
        $this->_params = BlackListParamsModel::of($params);
        InterfaceUtil::chkInterfaceParams($this->_params, $this->_interfaceRequiredParams, $this->_errorMsgs);
        InterfaceUtil::returnJsonEncodeBuilder($this->{$this->_params->mode}());
    }

    public function getBlackList(): array
    {
        return [
            'error' => '',
            'result' => BlackListResponseModel::of($this->_blackListService->getBlackList($this->_params))->toArray()
        ];
    }

    public function insertBlackList(): array
    {
        return [
            'error' => '',
            'result' => $this->_blackListService->insertBlackList($this->_params)
        ];

    }

    public function updateBlackList(): array
    {
        return [
            'error' => '',
            'result' => $this->_blackListService->updateBlackList($this->_params)
        ];

    }

    public function getCleanMessageForBlacklist($params): string
    {
        return $this->_blackListService->getCleanMessageForBlacklist(BlackListParamsModel::of($params));
    }
}