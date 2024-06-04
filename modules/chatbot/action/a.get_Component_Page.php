<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값
include_once $g['dir_module'].'var/define.path.php'; // class, 모듈, 레이아웃 패스 세팅
$chatbot = new Chatbot();

$result=array();
$result['error']=false;

$uid = $_POST['uid'];
$register = $_POST['register'];
$target = $_POST['target'];
$markup = $_POST['markup'];
$title = $_POST['title'];
$id = $_POST['id'];
$vendor = $_POST['vendor'];
$bot = $_POST['bot'];

// 공통 TMPL
$TMPL['uid']=$uid;
$TMPL['register']=$register;
$TMPL['g_s'] = $g['s'];
$TMPL['r'] = $r;
$TMPL['m'] = $m;

if($markup=='chatbox'){
   $bot_id = $id;
   $skin = $chatbot->getChatBox($bot_id);

}else if($markup=='userChatBox'){
    $data = array();
    $id_arr = explode('-',$id);
    $data['bot'] = $id_arr[0];
    $data['userUid'] = $id_arr[1];
    $data['roomToken'] = $id_arr[2];

    $skin = $chatbot->getUserChatBox($data);


}else if($markup=='mobile-botView'){

   $skin = $chatbot->getMobileView($uid);

}else if($markup=='mLogin' || $markup=='mJoin' || $markup=='mProfile'){

    if($markup=='mLogin' || $markup=='mJoin'){
        // ***************************************** sns 로그인 버튼 세팅
        $g['mdl_slogin'] = 'slogin';
        include_once($g['path_module'].$g['mdl_slogin'].'/var/var.php');
        include_once($g['path_module'].$g['mdl_slogin'].'/lib/snsfunction.php');

        $slogin['naver']['callapi'] = str_replace("%3Dnaver&", "%3Dnaver%26start%3Dplay&", $slogin['naver']['callapi']);
        $slogin['naver']['callapi'] = str_replace("www.", "", $slogin['naver']['callapi']);

        $TMPL['facebook_callapi'] = $slogin['facebook']['callapi'];
        $TMPL['naver_callapi'] = $slogin['naver']['callapi'];
        $TMPL['kakao_callapi'] = $slogin['kakao']['callapi'];

        $slogin_widget = new skin('user/slogin');
        $TMPL['slogin_widget'] = $slogin_widget->make(); // 최종 버튼 출력
    }

    if($markup=='mProfile' || $markup=='mJoin'){
       // ****************************************** 성별 리스트 세팅
        $gender_list = '
         <li'.($my['sex']==1?' class="cb-selected"':'').' data-sex="1">남</li>
         <li'.($my['sex']==2?' class="cb-selected"':'').' data-sex="2">여</li>';
        $TMPL['gender_list'] = $gender_list;

        // ********************************* 연령 리스트 세팅
        $age_list='';
        $age_arr=array("10","20","30","40","50","60");
        foreach ($age_arr as $age){
            $age_list .='
            <li data-age="'.$age.'" '.($age==$my['age']?'class="cb-selected"':'').'>
                '.$age.'대'.($age=='60'?' 이상':'').'
            </li>';
        }
        $TMPL['age_list'] = $age_list;
        if($markup=='mProfile'){
            if($my['photo']){
               $avatar_src = $g['url_root'].'/_var/avatar/'.$my['photo'];
               $TMPL['avatar_bg'] = 'style="background: url('.$avatar_src.') center center no-repeat;background-size:170px 170px;"';
               $TMPL['cb_icon_user'] = '';
            }
            else $TMPL['cb_icon_user'] = ' cb-icon-user';
            $TMPL['sex'] = $my['sex'];
            $TMPL['age'] = $my['age'];
            $TMPL['name'] = $my['name'];
            $TMPL['email'] = $my['email'];
            $TMPL['zip'] = $my['zip'];
            $TMPL['addr1'] = $my['addr1'];
            $TMPL['addr2'] = $my['addr2'];
            $TMPL['tel2'] = $my['tel2'];
            $TMPL['my_hidden'] ='style="display:none;"';
            $TMPL['my_disabled'] ='disabled';
        }

        $form_inputs = new skin('user/form_inputs');
        // input 리스트 세팅
        $TMPL['form_inputs'] = $form_inputs->make(); // 정보수정 페이지와 함께 사용하기 위해서 input 들을 별도 관리
    }
    if($markup=='mLogin') $markup_file = 'user/login';
    else if($markup=='mJoin') $markup_file = 'user/join';
    else if($markup=='mProfile') $markup_file = 'user/profile';
}else if($markup=='mIdpwsearch'){
    $markup_file = 'user/idpwsearch';
}else if($markup=='mSearch'){
    $markup_file = 'search/main';

}else if($markup=='userChatLog'){
    include_once $g['path_core'] . "function/simple_html_dom.php";

    // 챗로그 상세
    $data = array();
    $id_arr = explode('-',$id);
    $bot = $id_arr[0];
    $userUid = $id_arr[1];
    $roomToken = $id_arr[2];

    $aPrintType = array('T'=>'텍스트', 'B'=>'버튼', 'text'=>'텍스트', 'hMenu'=>'버튼', 'card'=>'카드', 'img'=>'이미지', 'if'=>'조건', 'hform'=>'html폼');

    $aChatLog = array();

    // 의도분류 기준 스코어
    $bs = getDbData($table[$m.'botSettings'], "bot='".$bot."' and name='intentMV'", 'value');
    $intentMV = $bs['value'] ? $bs['value'] : $chatbot->intentMV;

    $_wh = "vendor='".$vendor."' and bot='".$bot."' and roomToken='".$roomToken."'";
    if($userUid) $_wh.=' and userUid='.$userUid;
    $query = "Select * From ".$table[$m.'chatLog']." Where ".$_wh." Order by uid ASC";
    $aUserLog = $chatbot->getAssoc($query);
    foreach($aUserLog as $aLog) {
        $_tempLog = array();
        $_tempLog['uid'] = $aLog['uid'];
        $_tempLog['printType'] = $aLog['printType'] == 'T' || $aLog['printType'] == 'W'? "텍스트" : "버튼";
        $_tempLog['content'] = $aLog['printType'] == "W" ? "Welcome" : $aLog['content'];
        $_tempLog['intent'] = $aLog['score'] && $aLog['score'] >= $intentMV ? $aLog['intent'] : "";
        $_tempLog['score'] = $aLog['score'] && $aLog['score'] >= $intentMV ? number_format(($aLog['score']*100),2)."%" : "";
        $_tempLog['node'] = $aLog['is_unknown'] ? "UNKNOWN" : $aLog['node'];
        if($aLog['entity']) {
            $entity = "";
            $aEntity = explode(",", $aLog['entity']);
            foreach($aEntity as $E) {
                $aE = explode("|", $E);
                if($aE[2] == "S" && preg_match("/".$aE[3]."/", $entity)) continue;
                $entity .=$aE[0].":".$aE[3].", ";
            }
            $entity = rtrim($entity, ", ");
            $_tempLog['entity'] = $entity;
        }

        $_tempLog['responses'] = array();

        // 응답 검색
        $_wh = "vendor='".$vendor."' and bot='".$bot."' and roomToken='".$roomToken."' and chat='".$aLog['uid']."'";
        $query = "Select * From ".$table[$m.'botChatLog']." Where ".$_wh." Order by uid ASC";
        $rows=$chatbot->getAssoc($query);
        foreach($rows as $index=>$row) {
            if($row['printType'] == 'node') continue;

            $_tempRes = array();

            // 응답 html 파싱
            $_tempRes['content'] = "";

            if($row['content'] == strip_tags($row['content'])) {
                $_tempRes['printType'] = $aPrintType['text'];
                $_tempRes['content'] .=$row['content'];
            } else {
                $oHtml = str_get_html($row['content']);

                if($oHtml->find("div.cb-chatting-balloon")) {
                    if($row['findType'] == 'F') {
                        $_tempRes['printType'] = "FAQ";
                        if($row['score']) $_tempLog['score'] = number_format($row['score'],2)."%";
                    } else {
                        $_tempRes['printType'] = $aPrintType['text'];
                    }
                    $_tempRes['content'] .=$oHtml->find("div.cb-chatting-balloon", 0)->plaintext;
                } else if($oHtml->find("figure[data-type=img]")) {
                    $_tempRes['printType'] = $aPrintType['img'];
                    foreach($oHtml->find("figure[data-type=img]") as $oItem) {
                        $_tempRes['content'] .="<span class='test_resimg' style='background-image:url(".$oItem->find("img", 0)->src.");'></span>";
                    }
                } else if($oHtml->find("[data-role=menuType-resItem]")) {
                    $_tempRes['printType'] = $aPrintType['hMenu'];
                    foreach($oHtml->find("[data-role=menuType-resItem]") as $oItem) {
                        $_tempRes['content'] .=$oItem->plaintext.", ";
                    }
                } else if($oHtml->find("[data-role=cardType-resItem]")) {
                    $_tempRes['printType'] = $aPrintType['card'];
                    foreach($oHtml->find("[data-role=cardType-resItem]") as $oItem) {
                        $_tempRes['content'] .=$oItem->find('.card-title', 0)->plaintext.", ";
                    }
                } else if($row['printType'] == 'hform' && $oHtml->find("div.bot_form")) {
                    $_tempRes['printType'] = $aPrintType['hform'];
                    $_tempRes['content'] .='html 양식';
                }
            }

            $_tempRes['content'] = rtrim($_tempRes['content'], ", ");
            $_tempLog['responses'][] = $_tempRes;
        }

        $aChatLog[] = $_tempLog;
    }

    $html = "";
    foreach($aChatLog as $aLog) {
        $rowspan = count($aLog['responses']) > 1 ? "rowspan='".count($aLog['responses'])."'" : "";

        if(count($aLog['responses']) <= 1) {
            $html .="<tr>";
            $html .="   <td>".$aLog['printType']."</td>";
            $html .="   <td class='aleft'>".$aLog['content']."</td>";
            $html .="   <td>".$aLog['intent']."</td>";
            $html .="   <td>".$aLog['score']."</td>";
            $html .="   <td class='aleft'>".$aLog['entity']."</td>";
            $html .="   <td>".$aLog['node']."</td>";
            $html .="   <td>".$aLog['responses'][0]['printType']."</td>";
            $html .="   <td class='aleft'>".$aLog['responses'][0]['content']."</td>";
            $html .="</tr>";
        } else {
            foreach($aLog['responses'] as $index=>$aRes) {
                if($index == 0) {
                    $html .="<tr>";
                    $html .="   <td ".$rowspan.">".$aLog['printType']."</td>";
                    $html .="   <td ".$rowspan." class='aleft'>".$aLog['content']."</td>";
                    $html .="   <td ".$rowspan.">".$aLog['intent']."</td>";
                    $html .="   <td ".$rowspan.">".$aLog['score']."</td>";
                    $html .="   <td ".$rowspan." class='aleft'>".$aLog['entity']."</td>";
                    $html .="   <td ".$rowspan.">".$aLog['node']."</td>";
                    $html .="   <td>".$aRes['printType']."</td>";
                    $html .="   <td class='aleft'>".$aRes['content']."</td>";
                    $html .="</tr>";
                } else {
                    $html .="<tr>";
                        $html .="   <td class='bl'>".$aRes['printType']."</td>";
                        $html .="   <td class='aleft'>".$aRes['content']."</td>";
                    $html .="</tr>";
                }
            }
        }
    }

    $result['content'] = $html;
    echo json_encode($result);
    exit;
}

if($markup=='mobile-botView' || $markup=='chatbox' || $markup == 'userChatBox' ) $result['content'] = $skin->make();
else{
   $skin=new skin($markup_file);
   $result['content']=$skin->make();
}


echo json_encode($result);
exit;
?>
