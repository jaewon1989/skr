<?php

class BlackListResponseModel
{
    use GetSetGenerator;

    private $uid;
    private $blackList;
    private $bot;
    private $d_regis;


    public static function of(BlackList $blackList): BlackListResponseModel
    {
        $instance = new self();

        $instance->setUid($blackList->uid);
        $instance->setBlackList($blackList->blackList);
        $instance->setBot($blackList->bot);

        return $instance;
    }

    public function toArray(): array
    {
        return [
            'uid' => $this->getUid(),
            'blackList' => $this->getBlackList(),
            'bot' => $this->getBot()
        ];
    }
}