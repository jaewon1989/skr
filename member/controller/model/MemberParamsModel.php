<?php

class MemberParamsModel
{
    use GetSetGenerator;
    use RepositoryTrait;

    private $mode;
    private $group;
    private $mbruid;
    private $level;
    private $name;
    private $id;
    private $pw1;
    private $pw2;
    private $pw_change;
    private $prev_pw;
    private $email;
    private $is_lock;
}