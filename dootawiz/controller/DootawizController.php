<?php

require_once 'interface/util/InterfaceUtil.php';
require_once 'common/controller/CommonController.php';
require_once 'dootawiz/controller/model/DootawizParamsModel.php';
require_once 'dootawiz/service/DootawizService.php';

class DootawizController extends CommonController
{
    private $_params;
    private $_dootawizService;
    private $_interfaceModeRequiredParams = [
        'getTtsPreListeningUrl' => [
            'botUid',
            'ttsSpeed',
            'ttsMsg'
        ]
    ];
    private $_errorMsgs = [
        'mode' => '잘못된 접근입니다.',
        'botUid' => '잘못된 접근입니다.',
        'ttsSpeed' => 'TTS 속도를 설정해 주세요.',
        'ttsMsg' => 'TTS 메시지를 입력해 주세요.'
    ];

    public function __construct()
    {
        $this->_dootawizService = new DootawizService();
    }

    /**
     * @throws JsonException
     */
    public function interfaceProcess($params): void
    {
        $this->_params = DootawizParamsModel::of($params);
        InterfaceUtil::chkInterfaceParams($this->_params, $this->_interfaceModeRequiredParams, $this->_errorMsgs);
        InterfaceUtil::returnJsonEncodeBuilder($this->{$this->_params->mode}());
    }

    public function getTtsPreListeningUrl()
    {
        return $this->_dootawizService->getTtsPreListeningUrl($this->_params);
    }
}