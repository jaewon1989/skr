<?php

class BlackListParamsModel
{
    use GetSetGenerator;

    private $mode;
    private $botUid;
    private $blackListUid;
    private $blackList;
    private $cleanMessage;


    public static function of($params = []): BlackListParamsModel
    {
        $instance = new self();

        foreach ($params as $key => $value){
            if(property_exists($instance, $key)){
                $instance->{$key} = $value;
            }
        }

        return $instance;
    }

}