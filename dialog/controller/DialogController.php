<?php

require_once 'interface/util/InterfaceUtil.php';
require_once 'common/controller/CommonController.php';
require_once 'dialog/controller/model/DialogParamsModel.php';
require_once 'dialog/controller/model/DialogListResponseModel.php';
require_once 'dialog/service/DialogService.php';

class DialogController extends CommonController
{
    private $_params;
    private $_dialogService;
    private $_interfaceModeRequiredParams = [
        'createDialog' => [
            'botUid',
            'dialogName'
        ],
        'deleteDialog' => [
            'botUid',
            'dialogUid'
        ],
        'getDialogList' => [
            'botUid',
        ],
        'copyDialog' => [
            'botUid',
            'dialogUid',
            'dialogName'
        ],
        'updateActiveDialog' => [
            'botUid',
            'dialogUid'
        ],
        'chkDupDialogName' => [
            'botUid',
            'dialogName'
        ]
    ];
    private $_errorMsgs = [
        'mode' => 'mode error',
        'botUid' => 'botUid error',
        'dialogName' => 'dialog name error',
        'dialogUid' => 'dialogUid error'
    ];


    public function __construct()
    {
        $this->_dialogService = new DialogService();
    }

    /**
     * @throws JsonException
     */
    public function interfaceProcess($params): void
    {
        $this->_params = DialogParamsModel::of($params);
        InterfaceUtil::chkInterfaceParams($this->_params, $this->_interfaceModeRequiredParams, $this->_errorMsgs);
        InterfaceUtil::returnJsonEncodeBuilder($this->{$this->_params->mode}());
    }

    public function createDialog(): array
    {
        return $this->_dialogService->createDialog($this->_params);
    }

    public function deleteDialog(): array
    {
        return $this->_dialogService->deleteDialog($this->_params);
    }

    public function getDialogList(): array
    {
        return $this->_dialogService->getDialogList($this->_params);
    }

    public function copyDialog()
    {
        return $this->_dialogService->copyDialog($this->_params);
    }

    public function updateActiveDialog(): array
    {
        return $this->_dialogService->updateActiveDialog($this->_params);
    }

    public function chkDupDialogName(): array
    {
        return $this->_dialogService->chkDupDialogName($this->_params);
    }

}