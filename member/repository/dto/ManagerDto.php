<?php

class ManagerDto
{
    use GetSetGenerator;
    use RepositoryTrait;

    private $uid;
    private $auth;
    private $mbruid;
    private $vendor;
    private $bot;
    private $parentmbr;
    private $role;
    private $role_intro;
    private $d_regis;
}