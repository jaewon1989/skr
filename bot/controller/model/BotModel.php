<?php

class BotModel
{
    use GetSetGenerator;
    private $vendor;
    private $mod;
    private $botType;
    private $searchField;
    private $searchKeyword;
    private $sortField;
    private $viewMode;

    public static function of($mod, $botType, $searchField, $searchKeyword, $sortField, $viewMode): BotModel
    {
        global $V;
        $instance = new self();

        $instance->vendor = $V['uid'];
        $instance->mod = isset($mod) && '' == $mod ? 'adm' : $mod;
        $instance->botType = $botType;
        $instance->searchField = $searchField;
        $instance->searchKeyword = $searchKeyword;
        $instance->sortField = $sortField;
        $instance->viewMode = isset($viewMode) && '' == $viewMode ? 'card' : $viewMode;;
        return $instance;
    }
}