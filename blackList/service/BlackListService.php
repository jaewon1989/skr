<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/blackList/repository/dto/BlackListDTO.php';

require_once $_SERVER['DOCUMENT_ROOT'].'/blackList/repository/mapper/BlackListMapper.php';

require_once $_SERVER['DOCUMENT_ROOT'].'/blackList/service/dto/BlackList.php';

class BlackListService
{
    private $_dbErrorVal = 0;
    private $_isError = true;
    private $_blackListMapper;


    public function __construct()
    {
        global $DB_CONNECT;
        $this->_blackListMapper = new BlackListMapper($DB_CONNECT);
    }

    public function getBlackList(BlackListParamsModel $blackListParams): BlackList
    {
        $blackListResult = $this->_blackListMapper->getBlackListByBlackListBotUid($blackListParams->botUid)->fetch_assoc();

        return new BlackList((new BlackListDTO())->toEntity($blackListResult));
    }

    public function insertBlackList(BlackListParamsModel $blackListParams): bool
    {
        $blackListInsertResult = $this->_isError;
        $blackListParams->blackList = $this->blackListRemoveWhitespace($blackListParams->blackList);
        $isBlackList = $this->_blackListMapper->getBlackListByBlackListBotUid($blackListParams->botUid)->fetch_assoc();

        if(empty($isBlackList)){
            $blackListInsertResult = $this->_blackListMapper->insertBlackList($blackListParams);
        }

        return $blackListInsertResult;
    }

    public function updateBlackList(BlackListParamsModel $blackListParams): bool
    {
        $blackListParams->blackList = $this->blackListRemoveWhitespace($blackListParams->blackList);

        return $this->_blackListMapper->updateBlackList($blackListParams);
    }

    public function getCleanMessageForBlacklist(BlackListParamsModel $blackListParams) :string
    {
        $blackListQueryResult = $this->_blackListMapper->getBlackListByBlackListBotUid($blackListParams->botUid)->fetch_assoc()['blackList'];
        $filteredMessage = $blackListParams->cleanMessage;

        if (!empty($blackListQueryResult)) {
            $blackListWordArray = explode(',', $blackListQueryResult);

            foreach ($blackListWordArray as $blackListWord) {
                $filteredMessage = str_replace($blackListWord, '', $filteredMessage);
            }
        }

        return $filteredMessage;
    }

    public function blackListRemoveWhitespace($blackList): string
    {
        $blackListItems = explode(',', $blackList);
        $trimmedBlackListItems = array_map(function($item) {
            return str_replace(' ', '', trim($item));
        }, $blackListItems);
        $cleanedBlackList = implode(',', $trimmedBlackListItems);
        return preg_replace('/,+/', ',', $cleanedBlackList);
    }

}