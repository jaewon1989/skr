<?php

class DialogDTO
{
    use GetSetGenerator;
    use RepositoryTrait;

    private $uid;
    private $type;
    private $is_temp;
    private $gid;
    private $name;
    private $intro;
    private $active;
    private $vendor;
    private $bot;
    private $graph;
    private $d_regis;
    private $d_update;
    private $o_uid;
    private $o_botuid;
    private $is_temp_del;
}