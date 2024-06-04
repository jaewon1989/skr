<?php

class Bot {
    use GetSetGenerator;
    private $uid;
    private $active;
    private $bottype;
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
    private $boturl;
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
    private $error_msg;
    private $chat_skin;

    public function __construct(BotBuilder $builder) {
        $reflector = new ReflectionClass($builder);
        $properties = $reflector->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE);

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $property->setAccessible(true);
            $propertyValue = $property->getValue($builder);
            $this->{$propertyName} = $propertyValue;
        }
    }
}