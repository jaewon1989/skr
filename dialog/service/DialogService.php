<?php

require_once 'dialog/repository/dto/DialogDTO.php';
require_once 'dialog/repository/dto/DialogNodeDTO.php';
require_once 'dialog/repository/dto/DialogResApiParamDTO.php';
require_once 'dialog/repository/dto/DialogResApiOutputDTO.php';
require_once 'dialog/repository/dto/DialogResGroupDTO.php';
require_once 'dialog/repository/dto/DialogResItemDTO.php';
require_once 'dialog/repository/dto/DialogResItemOCDTO.php';

require_once 'dialog/repository/mapper/DialogMapper.php';
require_once 'dialog/repository/mapper/DialogNodeMapper.php';
require_once 'dialog/repository/mapper/DialogResApiParamMapper.php';
require_once 'dialog/repository/mapper/DialogResApiOutputMapper.php';
require_once 'dialog/repository/mapper/DialogResGroupMapper.php';
require_once 'dialog/repository/mapper/DialogResItemMapper.php';
require_once 'dialog/repository/mapper/DialogResItemOCMapper.php';

require_once 'dialog/service/dto/DialogResItemChangeInfo.php';
require_once 'dialog/service/dto/DialogResItemOCChangeInfo.php';

class DialogService
{
    private $_dbErrorVal = 0;
    private $_isError = true;
    private $_dialogMapper;
    private $_dialogNodeMapper;
    private $_dialogResGroupMapper;
    private $_dialogResItemMapper;
    private $_dialogResItemOCMapper;
    private $_dialogResApiParamMapper;
    private $_dialogResApiOutputMapper;
    private $_errorMsgs = [
        'botUid' => 'botUid incorrect',
        'dialogUid' => 'dialogUid incorrect'
    ];
    private $_defaultDialogData = [
        'gid' => 0,
        'active' => 0,
    ];

    private $_defaultDialogNodeData = [
        'Welcome' => [
            'gid' => 1,
            'isson' => 1,
            'parent' => 0,
            'depth' => 0,
            'id' => 1,
            'name' => 'Welcome'
        ],
        '시작' => [
            'gid' => 1,
            'isson' => 0,
            'parent' => 1,
            'depth' => 1,
            'id' => 2,
            'name' => '시작'
        ],
        'Fallback' => [
            'gid' => 100000,
            'isson' => 0,
            'parent' => 1,
            'depth' => 1,
            'id' => 3,
            'name' => 'Fallback',
            'track_flag' => 0,
            'is_unknown' => 1
        ]
    ];


    public function __construct()
    {
        global $DB_CONNECT;
        $this->_dialogMapper = new DialogMapper($DB_CONNECT);
        $this->_dialogNodeMapper = new DialogNodeMapper($DB_CONNECT);
        $this->_dialogResGroupMapper = new DialogResGroupMapper($DB_CONNECT);
        $this->_dialogResItemMapper = new DialogResItemMapper($DB_CONNECT);
        $this->_dialogResItemOCMapper = new DialogResItemOCMapper($DB_CONNECT);
        $this->_dialogResApiOutputMapper = new DialogResApiOutputMapper($DB_CONNECT);
        $this->_dialogResApiParamMapper = new DialogResApiParamMapper($DB_CONNECT);
    }

    public function createDialog(DialogParamsModel $dialogParams): array
    {
        $vendor = $this->_dialogMapper->getDialogVendorByBotUid($dialogParams->botUid)->fetch_assoc()['vendor'];
        if (!empty($vendor)) {
            $insertDialogUid = $this->_setDefaultDialog($dialogParams, $vendor);
            if ($this->_dbErrorVal !== $insertDialogUid) {
                $this->_isError = false;
                $this->_setDefaultDialogNode($dialogParams, $vendor, $insertDialogUid);
            }
        }

        return [
            'error' => $this->_isError,
            'result' => true === $this->_isError ? $this->_errorMsgs['botUid'] : 'success'
        ];
    }

    public function deleteDialog(DialogParamsModel $dialogParams): array
    {
        if ($this->_isDialogExist($dialogParams)) {
            $this->_isError = false;
            $dialogQuery = $this->_dialogMapper->getDialogByDialogUid($dialogParams);
            $dialog = $this->_makeToEntity($dialogQuery->fetch_assoc(), 'DialogDTO');
            if ('Y' === $dialog->is_temp_del) {
                $this->_deleteDialogProcess($dialogParams);
            }
            $this->_changeDialogIsTempDelFlag($dialogParams);
        }

        return [
            'error' => $this->_isError,
            'result' => true === $this->_isError ? $this->_errorMsgs['dialogUid'] : 'success'
        ];
    }

    public function getDialogList(DialogParamsModel $dialogParams): array
    {
        $dialogDTOList = [];
        if ($this->_isBotExist($dialogParams)) {
            $this->_isError = false;
            $dialogListQuery = $this->_dialogMapper->getDialogListByBotUid($dialogParams->botUid);
            while ($dialogDTO = $dialogListQuery->fetch_assoc()) {
                $dialogDTOList[] = $this->_makeToEntity($dialogDTO, 'DialogDTO');
            }
        }

        $dialogList = [];
        foreach ($dialogDTOList as $dialogDTO) {
            $dialogList[] = DialogListResponseModel::of($dialogDTO)->toArray();
        }

        return [
            'error' => $this->_isError,
            'result' => true === $this->_isError ? $this->_errorMsgs['botUid'] : $dialogList
        ];
    }

    public function copyDialog(DialogParamsModel $dialogParams): array
    {
        if ($this->_isDialogExist($dialogParams)) {
            $insertDialogUid = $this->_dialogMapper->copyDialog($dialogParams);
            if ($this->_dbErrorVal !== $insertDialogUid) {
                $this->_isError = false;
                $this->_dialogNodeMapper->copyDialogNode($dialogParams->botUid, $dialogParams->dialogUid, $insertDialogUid);
                $this->_dialogResGroupMapper->copyDialogResGroup($dialogParams->botUid, $dialogParams->dialogUid, $insertDialogUid);
                $this->_dialogResItemMapper->copyDialogResItem($dialogParams->botUid, $dialogParams->dialogUid, $insertDialogUid);
                $this->_copyDialogResItemOCRelated($dialogParams, $insertDialogUid);
            }
        }

        return [
            'error' => $this->_isError,
            'result' => true === $this->_isError ? $this->_errorMsgs['dialogUid'] : 'success'
        ];
    }

    public function updateActiveDialog(DialogParamsModel $dialogParams): array
    {
        if ($this->_isDialogExist($dialogParams)) {
            $this->_isError = false;
            $this->_dialogMapper->updateActiveDialog($dialogParams->botUid, $dialogParams->dialogUid);
        }

        return [
            'error' => $this->_isError,
            'result' => true === $this->_isError ? $this->_errorMsgs['dialogUid'] : 'success'
        ];
    }

    public function chkDupDialogName(DialogParamsModel $dialogParams): array
    {
        $isDuplicate = 'false';
        if ($this->_isBotExist($dialogParams)) {
            $this->_isError = false;
            $isDuplicate = $this->_dialogMapper->chkDupDialogName($dialogParams->botUid, $dialogParams->dialogName)
                ->fetch_assoc()['result'];
        }

        return [
            'error' => $this->_isError,
            'result' => true === $this->_isError ? $this->_errorMsgs['dialogUid'] : $isDuplicate
        ];
    }

    private function _setDefaultDialog(DialogParamsModel $dialogParams, $vendor)
    {
        $this->_defaultDialogData['name'] = $dialogParams->dialogName;
        $this->_defaultDialogData['vendor'] = $vendor;
        $this->_defaultDialogData['bot'] = $dialogParams->botUid;
        $this->_defaultDialogData['d_regis'] = date('YmdHis');

        return $this->_dialogMapper->setDialog($this->_makeDTO($this->_defaultDialogData, 'DialogDTO'));
    }

    private function _setDefaultDialogNode(DialogParamsModel $dialogParams, $vendor, $insertDialogUid): void
    {
        foreach ($this->_defaultDialogNodeData as $dialogNodeData) {
            $dialogNodeData['vendor'] = $vendor;
            $dialogNodeData['bot'] = $dialogParams->botUid;
            $dialogNodeData['dialog'] = $insertDialogUid;
            $dialogNodeData['d_regis'] = date('YmdHis');

            $this->_dialogNodeMapper->setDialogNode($this->_makeDTO($dialogNodeData, 'DialogNodeDTO'));
        }
    }

    private function _getDialogResItemList($botUid, $dialogUid): array
    {
        $dialogResItemDTOList = [];
        $dialogResItemListQuery = $this->_dialogResItemMapper->getDialogResItemList(
            $botUid,
            $dialogUid
        );

        while ($dialogResItemDTO = $dialogResItemListQuery->fetch_assoc()) {
            $dialogResItemDTOList[] = $this->_makeToEntity($dialogResItemDTO, 'DialogResItemDTO');
        }

        return $dialogResItemDTOList;
    }

    private function _changeDialogIsTempDelFlag(DialogParamsModel $dialogParams): void
    {
        $this->_dialogMapper->changeDialogIsTempDelFlag($dialogParams);
    }

    private function _deleteDialogProcess(DialogParamsModel $dialogParams): void
    {
        $this->_dialogMapper->deleteDialog($dialogParams->botUid, $dialogParams->dialogUid);
        $this->_dialogNodeMapper->deleteDialogNode($dialogParams->botUid, $dialogParams->dialogUid);
        $this->_dialogResGroupMapper->deleteDialogResGroup($dialogParams->botUid, $dialogParams->dialogUid);

        $dialogResItemDTOList = $this->_getDialogResItemList($dialogParams->botUid, $dialogParams->dialogUid);
        foreach ($dialogResItemDTOList as $dialogResItemDTO) {
            $this->_deleteDialogResItemOCRelated($dialogParams, $dialogResItemDTO->uid);
        }

        $this->_dialogResItemMapper->deleteDialogResItem($dialogParams->botUid, $dialogParams->dialogUid);
    }

    private function _deleteDialogResItemOCRelated(DialogParamsModel $dialogParams, $dialogResItemUid): void
    {
        $dialogResItemOCDTOList = $this->_getDialogResItemOCDTOList($dialogParams, $dialogResItemUid);
        foreach ($dialogResItemOCDTOList as $dialogResItemOCDTO) {
            if ('api' === $dialogResItemOCDTO->resType) {
                $dialogResItemOCUid = $dialogResItemOCDTO->uid;
                $this->_dialogResApiParamMapper->deleteDialogResApiParam($dialogResItemOCUid);
                $this->_dialogResApiOutputMapper->deleteDialogResApiOutput($dialogParams->botUid, $dialogResItemOCUid);
            }
        }

        $this->_dialogResItemOCMapper->deleteDialogResItemOC($dialogParams->botUid, $dialogResItemUid);
    }

    private function _copyDialogResItemOCRelated(DialogParamsModel $dialogParams, $insertDialogUid): void
    {
        $dialogResItemUidChangeInfo = $this->_getDialogResItemUidChangeInfo($dialogParams, $insertDialogUid);
        foreach ($dialogResItemUidChangeInfo as $info) {
            $this->_dialogResItemOCMapper->copyDialogResItemOC($dialogParams, $info);
            $this->_copyDialogResApiRelated($dialogParams, $info);
        }
    }

    private function _getDialogResItemUidChangeInfo(DialogParamsModel $dialogParams, $insertDialogUid): array
    {
        $mappingUid = [];
        $beforeDialogResItemList = $this->_getDialogResItemList($dialogParams->botUid, $dialogParams->dialogUid);
        $afterDialogResItemList = $this->_getDialogResItemList($dialogParams->botUid, $insertDialogUid);

        foreach ($beforeDialogResItemList as $idx => $beforeDialogResItem) {
            if ($beforeDialogResItem->id === $afterDialogResItemList[$idx]->id) {
                $mappingUid[] = $this->_makeToEntity(
                    [
                        'beforeDialogResItemUid' => $beforeDialogResItem->uid,
                        'afterDialogResItemUid' => $afterDialogResItemList[$idx]->uid
                    ],
                    'DialogResItemChangeInfo'
                );
            }
        }

        return $mappingUid;
    }

    private function _copyDialogResApiRelated(DialogParamsModel $dialogParams, DialogResItemChangeInfo $dialogResItemChangeInfo)
    {
        $dialogResItemOCChangeInfo = $this->_getDialogResItemOCChangeInfo($dialogParams, $dialogResItemChangeInfo);
        if (!empty($dialogResItemOCChangeInfo->beforeDialogResItemOCUid)
            && !empty($dialogResItemOCChangeInfo->afterDialogResItemOCUid)) {
            $this->_dialogResApiParamMapper->copyDialogResApiParam($dialogResItemOCChangeInfo);
            $this->_dialogResApiOutputMapper->copyDialogResApiOutput($dialogResItemOCChangeInfo);
        }
    }

    private function _getDialogResItemOCChangeInfo(DialogParamsModel $dialogParams, DialogResItemChangeInfo $dialogResItemChangeInfo): DialogResItemOCChangeInfo
    {
        $beforeDialogResItemOCUid = $this->_dialogResItemOCMapper->getDialogResItemOCByResType(
            $dialogParams, $dialogResItemChangeInfo->beforeDialogResItemUid)->fetch_assoc()['uid'];
        $afterDialogResItemOCUid = $this->_dialogResItemOCMapper->getDialogResItemOCByResType(
            $dialogParams, $dialogResItemChangeInfo->afterDialogResItemUid)->fetch_assoc()['uid'];

        return $this->_makeToEntity(
            [
                'beforeDialogResItemOCUid' => $beforeDialogResItemOCUid,
                'afterDialogResItemOCUid' => $afterDialogResItemOCUid
            ],
            'DialogResItemOCChangeInfo'
        );
    }

    private function _getDialogResItemOCDTOList(DialogParamsModel $dialogParams, $dialogResItemUid): array
    {
        $dialogResItemOCDTOList = [];
        $dialogResItemOCListQuery = $this->_dialogResItemOCMapper->getDialogResItemOCList(
            $dialogParams->botUid,
            $dialogResItemUid
        );

        while ($dialogResItemOCDTO = $dialogResItemOCListQuery->fetch_assoc()) {
            $dialogResItemOCDTOList[] = $this->_makeToEntity($dialogResItemOCDTO, 'DialogResItemOCDTO');
        }

        return $dialogResItemOCDTOList;
    }

    private function _makeToEntity($data, $dtoType)
    {
        return (new $dtoType())->toEntity($data);
    }

    private function _makeDTO($data, $dtoType)
    {
        $dto = new $dtoType();

        foreach ($data as $key => $val) {
            $dto->{$key} = $val;
        }

        return $dto;
    }

    private function _isBotExist(DialogParamsModel $dialogParams): bool
    {
        return 'true' === $this->_dialogMapper->isBotExist($dialogParams->botUid)->fetch_assoc()['result'];
    }

    private function _isDialogExist(DialogParamsModel $dialogParams): bool
    {
        return 'true' === $this->_dialogMapper->isDialogExist($dialogParams->botUid, $dialogParams->dialogUid)->fetch_assoc()['result'];
    }

}