<?php
class BotBuilder
{
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

    public function build(): Bot
    {
        return new Bot($this);
    }

    /**
     * @return mixed
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param mixed $uid
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBottype()
    {
        return $this->bottype;
    }

    /**
     * @param mixed $bottype
     */
    public function setBottype($bottype)
    {
        $this->bottype = $bottype;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsTemp()
    {
        return $this->is_temp;
    }

    /**
     * @param mixed $is_temp
     */
    public function setIs_temp($is_temp)
    {
        $this->is_temp = $is_temp;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGid()
    {
        return $this->gid;
    }

    /**
     * @param mixed $gid
     */
    public function setGid($gid)
    {
        $this->gid = $gid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * @param mixed $auth
     */
    public function setAuth($auth)
    {
        $this->auth = $auth;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * @param mixed $vendor
     */
    public function setVendor($vendor)
    {
        $this->vendor = $vendor;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInduCat()
    {
        return $this->induCat;
    }

    /**
     * @param mixed $induCat
     */
    public function setInduCat($induCat)
    {
        $this->induCat = $induCat;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * @param mixed $hidden
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDisplay()
    {
        return $this->display;
    }

    /**
     * @param mixed $display
     */
    public function setDisplay($display)
    {
        $this->display = $display;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param mixed $service
     */
    public function setService($service)
    {
        $this->service = $service;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIntro()
    {
        return $this->intro;
    }

    /**
     * @param mixed $intro
     */
    public function setIntro($intro)
    {
        $this->intro = $intro;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param mixed $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBoturl()
    {
        return $this->boturl;
    }

    /**
     * @param mixed $boturl
     */
    public function setBoturl($boturl)
    {
        $this->boturl = $boturl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMbruid()
    {
        return $this->mbruid;
    }

    /**
     * @param mixed $mbruid
     */
    public function setMbruid($mbruid)
    {
        $this->mbruid = $mbruid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCallno()
    {
        return $this->callno;
    }

    /**
     * @param mixed $callno
     */
    public function setCallno($callno)
    {
        $this->callno = $callno;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserno()
    {
        return $this->userno;
    }

    /**
     * @param mixed $userno
     */
    public function setUserno($userno)
    {
        $this->userno = $userno;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * @param mixed $html
     */
    public function setHtml($html)
    {
        $this->html = $html;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param mixed $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param mixed $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHit()
    {
        return $this->hit;
    }

    /**
     * @param mixed $hit
     */
    public function setHit($hit)
    {
        $this->hit = $hit;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * @param mixed $likes
     */
    public function setLikes($likes)
    {
        $this->likes = $likes;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * @param mixed $report
     */
    public function setReport($report)
    {
        $this->report = $report;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPoint()
    {
        return $this->point;
    }

    /**
     * @param mixed $point
     */
    public function setPoint($point)
    {
        $this->point = $point;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDRegis()
    {
        return $this->d_regis;
    }

    /**
     * @param mixed $d_regis
     */
    public function setD_regis($d_regis)
    {
        $this->d_regis = $d_regis;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDModify()
    {
        return $this->d_modify;
    }

    /**
     * @param mixed $d_modify
     */
    public function setD_modify($d_modify)
    {
        $this->d_modify = $d_modify;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param mixed $avatar
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpload()
    {
        return $this->upload;
    }

    /**
     * @param mixed $upload
     */
    public function setUpload($upload)
    {
        $this->upload = $upload;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMoniteringFa()
    {
        return $this->monitering_fa;
    }

    /**
     * @param mixed $monitering_fa
     */
    public function setMonitering_fa($monitering_fa)
    {
        $this->monitering_fa = $monitering_fa;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNrank()
    {
        return $this->nrank;
    }

    /**
     * @param mixed $nrank
     */
    public function setNrank($nrank)
    {
        $this->nrank = $nrank;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserUid()
    {
        return $this->user_uid;
    }

    /**
     * @param mixed $user_uid
     */
    public function setUser_uid($user_uid)
    {
        $this->user_uid = $user_uid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCUid()
    {
        return $this->c_uid;
    }

    /**
     * @param mixed $c_uid
     */
    public function setC_uid($c_uid)
    {
        $this->c_uid = $c_uid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPaid()
    {
        return $this->paid;
    }

    /**
     * @param mixed $paid
     */
    public function setPaid($paid)
    {
        $this->paid = $paid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOUid()
    {
        return $this->o_uid;
    }

    /**
     * @param mixed $o_uid
     */
    public function setO_uid($o_uid)
    {
        $this->o_uid = $o_uid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getErrorMsg()
    {
        return $this->error_msg;
    }

    /**
     * @param mixed $error_msg
     */
    public function setError_msg($error_msg)
    {
        $this->error_msg = $error_msg;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getChatSkin()
    {
        return $this->chat_skin;
    }

    /**
     * @param mixed $chat_skin
     */
    public function setChat_skin($chat_skin)
    {
        $this->chat_skin = $chat_skin;
        return $this;
    }

    public function invokeSetter(array $dto): self
    {
        foreach ($dto as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if (method_exists($this, $setter)) {
                $this->{$setter}($value);
            }
        }
        return $this;
    }

}