<?php

class BotDTO
{
    /*
     * TODO 사실 ORM 에서 자동으로 아래 DTO 의 항목들에 mapping 을 해줘야 의미가 있는 field 들 인데..
     * 의미가 없으므로 주석처리 한다 나중을 위해서 남겨둔다.
     */
    /*private $uid;
    private $active;
    private $dtotype;
    private $role;
    private $is_temp;
    private $gid;
    private $type;
    private $auth;
    private $vendor;
    private $induCat;
    private $hidden;
    private $display;
    private $name;
    private $service;
    private $intro;
    private $website;
    private $dtourl;
    private $mbruid;
    private $id;
    private $callno;
    private $userno;
    private $content;
    private $html;
    private $tag;
    private $lang;
    private $hit;
    private $likes;
    private $report;
    private $point;
    private $d_regis;
    private $d_modify;
    private $avatar;
    private $upload;
    private $monitering_fa;
    private $nrank;
    private $user_uid;
    private $c_uid;
    private $paid;
    private $o_uid;
    private $error_msg;*/

    public static function Builder(): BotBuilder
    {
        return new BotBuilder();
    }

    public function toEntity($dto): Bot
    {
        return self::Builder()
            ->invokeSetter($dto)
            ->build();
    }
}