<?php
if(!defined('__KIMS__')) exit;
include_once $g['path_module'].'member/var/var.join.php';

$data = $_POST;

// javascript 막기
$data = getStripScript($data);

if($data['linkType'] == "group_add" || $data['linkType'] == "group_modify") {
    if($data['linkType'] == "group_modify") {
        if(!(int)$data['group_uid']) {
            $result = array('result'=>false, 'msg'=>'중요변수가 존재하지 않습니다.');
            echo json_encode($result); exit;
        }
    }

    if(!trim($data['name'])) {
        $result = array('result'=>false, 'msg'=>'그룹명을 입력해 주세요.');
        echo json_encode($result); exit;
    }
    if(isset($data['group_botUid'])) {
        $botUid = "";
        foreach($data['group_botUid'] as $_bot) {
            if(!(int)$_bot) continue;
            $botUid .=$_bot.",";
        }
        $botUid = rtrim($botUid, ",");
    }

    if($data['linkType'] == "group_add") {
        $max_gid = getDbCnt("rb_s_mbrgroup", "max(gid)", "");
        $_QKEY = "name, gid, bot";
        $_QVAL = "'".$data['name']."', '".($max_gid+1)."', '".$botUid."'";
        getDbInsert('rb_s_mbrgroup',$_QKEY,$_QVAL);
    } else {
        getDbUpdate("rb_s_mbrgroup","name='".$data['name']."', bot='".$botUid."'", "uid=".$data['group_uid']);
    }

    $result = array('result'=>true, 'msg'=>'');
    echo json_encode($result); exit;

} else if($data['linkType'] == "group_delete") {
    if(!(int)($data['group_uid'])) {
        $result = array('result'=>false, 'msg'=>'중요변수가 존재하지 않습니다.');
        echo json_encode($result); exit;
    }

    getDbDelete("rb_s_mbrgroup", "uid=".$data['group_uid']);
    $result = array('result'=>true, 'msg'=>'');
    echo json_encode($result); exit;

} else if($data['linkType'] == "member_add") {
    if(!$data['group']) {
        $result = array('result'=>false, 'msg'=>'소속 그룹을 선택해 주세요.');
        echo json_encode($result); exit;
    }

    if(!$data['name']) {
        $result = array('result'=>false, 'msg'=>'사용자명을 입력해 주세요.');
        echo json_encode($result); exit;
    }
    if(!$data['id']) {
        $result = array('result'=>false, 'msg'=>'아이디를 입력해 주세요.');
        echo json_encode($result); exit;
    }
    $idValidate = getValidID($data['id'], 4, 20);
    if ($idValidate !== true) {
        $result = array('result'=>false, 'msg'=>$idValidate);
        echo json_encode($result); exit;
    }
    if(getDbRows($table['s_mbrid'],"id='".$id."'")) {
    	$result = array('result'=>false, 'msg'=>'사용할 수 없는 아이디입니다.');
        echo json_encode($result); exit;
    }

    if(!$data['pw1']) {
        $result = array('result'=>false, 'msg'=>'비밀번호를 입력해 주세요.');
        echo json_encode($result); exit;
    }
    if(!$data['pw2']) {
        $result = array('result'=>false, 'msg'=>'비밀번호를 한번 더 입력해 주세요.');
        echo json_encode($result); exit;
    }
    if($data['pw1'] != $data['pw2']) {
        $result = array('result'=>false, 'msg'=>'비밀번호가 일치하지 않습니다.');
        echo json_encode($result); exit;
    }
    $pwValidate = getValidPassword($data['pw1'], 8, 20);
    if($pwValidate !== true) {
        $result = array('result'=>false, 'msg'=>$pwValidate);
        echo json_encode($result); exit;
    }
    if(!$data['email']) {
        $result = array('result'=>false, 'msg'=>'이메일을 입력해 주세요.');
        echo json_encode($result); exit;
    }
    if(!preg_match("/[\da-z\-_\.]+@([a-z\d]([a-z\d\-]*)([a-z\d]*)\.)+[a-z]{2,6}/i", $data['email'])) {
        $result = array('result'=>false, 'msg'=>'정확한 이메일을 입력해 주세요.');
        echo json_encode($result); exit;
    }
    if(getDbRows($table['s_mbrdata'],"email='".$data['email']."'")) {
    	$result = array('result'=>false, 'msg'=>'이미 등록된 이메일입니다.');
        echo json_encode($result); exit;
    }

    $level = $data['level'];
    $mygroup = $data['group'];
    $id = $data['id'];
    $pw = getCrypt($data['pw1'], $date['totime']);
    $name = $data['name'];
    $email = $data['email'];
    $site = $auth = $manager = 1;
    $comp = $admin = 0;
    $nic = $name;
    $photo = $home = '';
    $d_regis	= $date['totime'];

    getDbInsert($table['s_mbrid'],'site,id,pw',"'".$s."', '".$id."', '".$pw."'");

    $memberuid  = getDbCnt($table['s_mbrid'],'max(uid)','');

    $_QKEY = "memberuid, site, auth, mygroup, level, comp, admin, email, name, nic, d_regis, manager";
    $_QVAL = "'$memberuid','$s','$auth','$mygroup','$level','$comp','$admin','$email','$name','$nic','$d_regis','$manager'";
    getDbInsert($table['s_mbrdata'],$_QKEY,$_QVAL);

    // manager 테이블 저장
    $query = "Select A.uid, A.mbruid From rb_chatbot_vendor A ";
    $query .="Where A.auth=1 and A.display=1 Limit 1";
    $vendor = db_fetch_assoc(db_query($query,$DB_CONNECT));

    $auth =1;
    $QKEY = "auth,mbruid,vendor,bot,parentmbr,role,role_intro,d_regis";
    $QVAL = "'$auth','$memberuid','".$vendor['uid']."','0','".$vendor['mbruid']."','$role','$role_intro','$d_regis'";
    getDbInsert('rb_chatbot_manager',$QKEY,$QVAL);

    $result = array('result'=>true, 'msg'=>'');
    echo json_encode($result); exit;

} else if($data['linkType'] == "member_modify") {
    if(!$data['mbruid']) {
        $result = array('result'=>false, 'msg'=>'정상적인 접근이 아닙니다.');
        echo json_encode($result); exit;
    }

    $query = "Select A.uid as mbruid, A.id, A.pw, B.* From rb_s_mbrid A ";
    $query .="left join rb_s_mbrdata B on A.uid = B.memberuid ";
    $query .="Where A.uid = '".$data['mbruid']."'";
    $RCD = db_query($query,$DB_CONNECT);
    $manager = db_fetch_assoc($RCD);

    if($manager['id'] != $data['id']) {
        $result = array('result'=>false, 'msg'=>'정상적인 접근이 아닙니다.');
        echo json_encode($result); exit;
    }

    if(!$data['group']) {
        $result = array('result'=>false, 'msg'=>'소속 그룹을 선택해 주세요.');
        echo json_encode($result); exit;
    }
    if(!$data['name']) {
        $result = array('result'=>false, 'msg'=>'사용자명을 입력해 주세요.');
        echo json_encode($result); exit;
    }

    if(isset($data['pw_change']) && $data['pw_change'] == 'true') {
        if(!$data['prev_pw']) {
            $result = array('result'=>false, 'msg'=>'기존 비밀번호를 입력해 주세요.');
            echo json_encode($result); exit;
        }

        $prev_pw = getCrypt($data['prev_pw'], $manager['d_regis']);
        if($prev_pw != $manager['pw']) {
            $result = array('result'=>false, 'msg'=>'기존 비밀번호가 맞지 않습니다.');
            echo json_encode($result); exit;
        }

        if(!$data['pw1']) {
            $result = array('result'=>false, 'msg'=>'비밀번호를 입력해 주세요.');
            echo json_encode($result); exit;
        }
        if(!$data['pw2']) {
            $result = array('result'=>false, 'msg'=>'비밀번호를 한번 더 입력해 주세요.');
            echo json_encode($result); exit;
        }
        if($data['pw1'] != $data['pw2']) {
            $result = array('result'=>false, 'msg'=>'비밀번호가 일치하지 않습니다.');
            echo json_encode($result); exit;
        }
        $pwValidate = getValidPassword($data['pw1'], 8, 20);
        if($pwValidate !== true) {
            $result = array('result'=>false, 'msg'=>$pwValidate);
            echo json_encode($result); exit;
        }

        $pw = getCrypt($data['pw1'], $manager['d_regis']);
    }

    if(!$data['email']) {
        $result = array('result'=>false, 'msg'=>'이메일을 입력해 주세요.');
        echo json_encode($result); exit;
    }
    if(!preg_match("/[\da-z\-_\.]+@([a-z\d]([a-z\d\-]*)([a-z\d]*)\.)+[a-z]{2,6}/i", $data['email'])) {
        $result = array('result'=>false, 'msg'=>'정확한 이메일을 입력해 주세요.');
        echo json_encode($result); exit;
    }
    if(getDbRows($table['s_mbrdata'],"memberuid<>'".$mbruid."' and email='".$data['email']."'")) {
    	$result = array('result'=>false, 'msg'=>'이미 등록된 이메일입니다.');
        echo json_encode($result); exit;
    }

    if(isset($data['pw_change']) && $data['pw_change']=='true' && $pw) {
        getDbUpdate($table['s_mbrid'],"pw='".$pw."'","uid=".$data['mbruid']);
    }
    getDbUpdate($table['s_mbrdata'],"mygroup='".$data['group']."', name='".$data['name']."', email='".$data['email']."',last_pw='".$date['today']."',tmpcode=''","memberuid=".$data['mbruid']);

    $result = array('result'=>true, 'msg'=>'');
    echo json_encode($result); exit;

} else if($data['linkType'] == "member_delete") {
    if(!$data['mbruid']) {
        $result = array('result'=>false, 'msg'=>'정상적인 접근이 아닙니다.');
        echo json_encode($result); exit;
    }
    if(!getDbRows($table['s_mbrdata'],"memberuid='".$data['mbruid']."'")) {
        $result = array('result'=>false, 'msg'=>'정상적인 접근이 아닙니다.');
        echo json_encode($result); exit;
    }

    getDbDelete($table['s_mbrdata'], "memberuid=".$data['mbruid']);
    getDbDelete($table['s_mbrid'], "uid=".$data['mbruid']);
    getDbDelete('rb_chatbot_manager', "mbruid=".$data['mbruid']);

    $result = array('result'=>true, 'msg'=>'');
    echo json_encode($result); exit;

} else if($data['linkType'] == "get_member_perm") {
    $result = array('result'=>false);
    if($data['group_uid']) {
        $query = "Select A.* From rb_s_mbrgroup A Where A.uid = '".$data['group_uid']."'";
        $group = db_fetch_assoc(db_query($query,$DB_CONNECT));
        $result['result'] = true;
        $groupMenu = ('chat' === $_SESSION['bottype']) ? $group['menu'] : $group['call_menu'];
        $result['data'] = explode(",", $groupMenu);
    }
    echo json_encode($result); exit;

} else if($data['linkType'] == "member_perm") {
    if(!$data['group_uid']) {
        $result = array('result'=>false, 'msg'=>'그룹을 선택해주세요.');
        echo json_encode($result); exit;
    }
    if(!getDbRows("rb_s_mbrgroup","uid='".$data['group_uid']."'")) {
        $result = array('result'=>false, 'msg'=>'정상적인 접근이 아닙니다.');
        echo json_encode($result); exit;
    }

    if ('chat' === $_SESSION['bottype']){
        getDbUpdate("rb_s_mbrgroup", "menu='".$data['menus']."'", "uid=".$data['group_uid']);
    } else {
        getDbUpdate("rb_s_mbrgroup", "call_menu='".$data['menus']."'", "uid=".$data['group_uid']);
    }

    $result = array('result'=>true, 'msg'=>'');
    echo json_encode($result); exit;
}
?>
