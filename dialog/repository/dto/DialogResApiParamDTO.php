<?php

class DialogResApiParamDTO
{
    use GetSetGenerator;
    use RepositoryTrait;

    private $uid;
    private $itemOC;
    private $api;
    private $req;
    private $name;
    private $description;
    private $required;
    private $position;
    private $val_type;
    private $param_type;
    private $length;
    private $text_val;
    private $varchar_val;
}