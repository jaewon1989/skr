<?php

class DialogNodeDTO
{
    use GetSetGenerator;
    use RepositoryTrait;

    private $uid;
    private $gid;
    private $isson;
    private $parent;
    private $depth;
    private $hidden;
    private $id;
    private $name;
    private $vendor;
    private $bot;
    private $dialog;
    private $recCondition;
    private $context;
    private $recQry;
    private $track_flag;
    private $node_action;
    private $jumpTo_node;
    private $is_unknown;
    private $use_topic;
    private $d_regis;
    private $timeout;
    private $timeout_msg;
    private $unrecognized_count;
    private $unrecognized_msg;
    private $exceeded_msg;
    private $fail_msg;
}