<?php
if(!defined('__KIMS__')) exit;

include_once $g['path_module'].$m.'/var/var.php';

$g['dir_include']=$g['dir_module'].'includes/';

// class 인클루드
require_once $g['dir_include'].'base.class.php';
//require_once $g['dir_module'].'languages/'.(!empty($_GET['lang']) ? $_GET['lang']:'korean').'.php';
require_once $g['dir_include'].'module.class.php';
$chatbot = new Chatbot();

$g['chatbot_start']	= getLinkFilter($g['s'].'/?'.($_HS['usescode']?'r='.$r.'&amp;':'').($c?'c='.$c:'m='.$m),array('skin'));
$g['chatbot_reset']	= getLinkFilter($g['chatbot_start'],array('cat','iframe'));
$g['chatbot_list']	= $g['chatbot_reset'].getLinkFilter('',array('p','sort','orderby','recnum','type','where','keyword','ptype'));
$g['chatbot_page']	= $g['chatbot_reset'].'&amp;page='.$page;
$g['chatbot_view']	= $g['chatbot_list'].'&amp;page=view&amp;uid=';
$g['chatbot_action']= $g['chatbot_list'].'&amp;a=';
$g['browtitle']	 = '봇톡스';
$g['meta_sbj']	 = '챗봇을 고용하세요. - 봇톡스';

// 업체정보 세팅 (메니져와 최초 가입회원 구분)
if($my['manager']){
    $MG = getDbData($table[$m.'manager'],'mbruid='.$my['uid'],'vendor');
    $V = getDbData($table[$m.'vendor'],'uid='.$MG['vendor'],'*');
} else {
    if($my['uid']) $V = getDbData($table[$m.'vendor'],'mbruid='.$my['uid'],'*');
}

// 벤더별 봇 갯수
if($V['uid']) $BNUM = getDbRows($table[$m.'bot'],'vendor='.$V['uid']);

// 업체 페이지
if (substr($page,0,3) == 'adm')
{
	//if (!$_SESSION['mbr_uid']) getLink($g['s'].'/?r='.$r.'&mod=login','','','');
	// 내부 로그인과 sso 로그인 체크
	if(isset($_GET['peso'])) {
	    // 내부 슈퍼 로그인
	    if (!$_SESSION['mbr_uid']) getLink($g['s'].'/?r='.$r.'&mod=login','','','');
	} else {
	    // 일반 로그인 체크(sso)
	    if (!$_SESSION['mbr_uid']) {
	        header("Location: ".$g['sid_sso_login']."/router/?continue=".$g['sid_send_url']); exit;
	    }
	}

	if (!$V['uid']) {
	    if($GLOBALS['_cloud_'] !== true) {
	        unset($_SESSION);
	        getLink($g['s'].'/?r='.$r.'&mod=login','','등록된 벤더 계정이 존재하지 않습니다.','');
	    } else {
	        getLink($g['s'],'','등록된 벤더 계정이 존재하지 않습니다.','');
	    }
	}

	$_page = str_replace('adm/','', $page);

    if (in_array('main', $my['mymenu'])) {
        $my['mymenu'][] = 'list';
    }

	if(!in_array($_page, $my['mymenu'])) {
	    echo "<script>alert('접속 권한이 없습니다.'); history.back();</script>"; exit;
	}

	// nexus sso sid 전송
	//getSsoSIDSend();

	if($V['uid']){
		$sort   = $sort ? $sort : 'gid';
		$orderby= $orderby ? $orderby : 'asc';
		$recnum = $recnum && $recnum < 200 ? $recnum : 10;
        $vendor = $V['uid'];

        if(isset($_GET['bottype'])) {
            $_SESSION['bottype'] = isset($_GET['bottype']) ? $_GET['bottype'] : ($_SESSION['bottype'] ? $_SESSION['bottype'] : 'chat');
        } else {
            if(!isset($_SESSION['bottype'])) {
                $_chatCnt = getDbRows($table[$m.'bot'], "bottype='chat' and vendor='".$V['uid']."' and hidden=0 and display=1");
                $_SESSION['bottype'] = $_chatCnt == 0 ? 'call' : 'chat';
            }
        }

        if(isset($_GET['bot'])){
        	$_SESSION['vendor_bot']='';
        	$_SESSION['vendor_bot'] = $V['uid'].'-'.$_GET['bot'];
        }

        // 디폴트로 bot 할당
        $_SESSION['roleType'] = $_GET['roleType'] ? $_GET['roleType'] : 'bot';

    	if($page != 'adm/main' && $page != 'adm/list' && strpos($page, 'adm/member') === false){
        	if(!isset($_SESSION['vendor_bot'])){
	           getLink($g['s'].'/adm/main','','','');
	        }else{
	            if($_SESSION['vendor_bot']) {
    	            $VB = explode('-',$_SESSION['vendor_bot']);
    	            $bot = $VB[1];
    	            $B = getUidData($table[$m.'bot'],$bot);

    	            if($B['vendor']!=$V['uid']){
    	                getLink($g['s'].'/adm/main','','','');
    	            }
    	            $_SESSION['bottype'] = $B['bottype'];
    	        }
	        }
	    }

        // 등록된 첫번째 dialog 데이타
        if($bot) {
            $data= array();
            $data['vendor'] = $V['uid'];
            $data['bot'] = $bot;
            $RFD = $chatbot->getVendorAdmDialog($data);

            // dialog 세팅
            $dialog = $_GET['dialog']?$_GET['dialog']:$RFD;

            if($dialog) {
                $D = getUidData($table[$m.'dialog'],$dialog);
                $dialog_gid = $D['gid'];
            }
        }

	}
}

// 총관리자 페이지
if(substr($page,0,5) =='suAdm'||substr($page,0,2) =='LC'){
	if (!$my['uid']) getLink($g['s'].'/?r='.$r.'&mod=login','','','');
	if ($my['id']!='superadmin') getLink($g['s'],'','접근 권한이 없습니다.','');
}

// 챗봇 만들기
if($page == 'build/step1')
{
	if (!$my['uid']) getLink($g['s'].'/?r='.$r.'&mod=login&referer='.urlencode($g['chatbot_start'].'&amp;page=build/step1'),'','','');
	else{
        // 봇 정보 세팅
        $bot_q = 'vendor='.$V['uid'];
        $BCD = getDbSelect($table[$m.'bot'],$bot_q,'*');
	    if ($BNUM && $V['type']==1 && !$uid) getLink($g['s'].'/?r='.$r.'&c=mybot','','이미 등록되어 있습니다. 추가 등록은 프리미엄 업체만 가능합니다.','');
	}

}


if($page =='view' && $uid)
{
	$R=getUidData($table[$m.'bot'],$uid);
	if (!$my['admin'] && !$R['auth']) getLink($g['chatbot_reset'],'','존재하지 않는 자료입니다.','');
	$QMK=getUidData($table[$m.'vendor'],$R['vendor']);
	$g['browtitle']	 = '봇톡스- '.$R['name'];
	$g['meta_sbj']	 = '봇톡스- '.$R['name'];

	if (!strstr($_SESSION['module_'.$m.'_view'],'['.$R['uid'].']'))
	{
		getDbUpdate($table[$m.'goods'],'hit=hit+1','uid='.$R['uid']);
		$_SESSION['module_'.$m.'_view'] .= '['.$R['uid'].']';
	}

	if ($R['upload'])
	{
		$d['upload'] = array();
		$d['upload']['tmp'] = $R['upload'];
		$d['_pload'] = getArrayString($R['upload']);
		foreach($d['_pload']['data'] as $_val)
		{
			$U = getUidData($table['s_upload'],$_val);
			if (!$U['uid'])
			{
				$R['upload'] = str_replace('['.$_val.']','',$R['upload']);
				$d['_pload']['count']--;
			}
			else {
				$d['upload']['data'][] = $U;
				if (!$U['cync'])
				{
					$_CYNC = "cync='[".$m."][".$R['uid']."][uid,down][".$table[$m.'goods']."][".$R['mbruid']."][m:".$m.",page:view,uid:".$R['uid']."]'";
					getDbUpdate($table['s_upload'],$_CYNC,'uid='.$U['uid']);
				}
			}
		}
		if ($R['upload'] != $d['upload']['tmp'])
		{
			getDbUpdate($table[$m.'goods'],"upload='".$R['upload']."'",'uid='.$R['uid']);
		}
		$d['upload']['count'] = $d['_pload']['count'];
	}

}

$page	= $page ? $page : 'chat';

if ($g['mobile'] && $_SESSION['pcmode'] != 'Y')
{
    if($page=='chat'||$page=='view') $_HM['layout'] = $d['chatbot']['layout_mobile_default'];
    else if(substr($page,0,3) == 'adm') $_HM['layout'] = $d['chatbot']['layout_mobile_admin'];
    else if(substr($page,0,5) == 'suAdm') $_HM['layout'] = $d['chatbot']['layout_mobile_suAdmin'];
    else if(substr($page,0,2) == 'LC') $_HM['layout'] = $d['chatbot']['layout_desktop_LC'];
    else $_HM['layout'] = $d['chatbot']['layout_mobile'];

	$skin = $d['chatbot']['skin_mobile'];
}
else {
	if($page=='chat') $_HM['layout'] = $d['chatbot']['layout_desktop_chat'];
	else if(substr($page,0,3) == 'adm') $_HM['layout'] = $d['chatbot']['layout_desktop_admin'];
    else if(substr($page,0,5) == 'suAdm') $_HM['layout'] = $d['chatbot']['layout_desktop_suAdmin'];
    else if(substr($page,0,2) == 'LC') $_HM['layout'] = $d['chatbot']['layout_desktop_LC'];
	else $_HM['layout'] = $d['chatbot']['layout_desktop']?$d['chatbot']['layout_desktop']:$_HS['layout'];

	$skin = $d['chatbot']['skin_desktop'];
}


if ($iframe == 'Y' && !$my['uid'])
{
	if (strpos('_front,brandlist',$page))
	{
		getLink($g['s'].'/?r='.$r.'&mod=qlogin&iframe='.$iframe.'&referer='.urlencode($g['chatbot_page']),'','','');
	}
}

$CONF=array();
$CONF['theme_path']=$g['dir_module'].'theme';
$CONF['theme_name']=$skin;



$g['dir_module_skin'] = $g['dir_module'].'theme/'.$skin.'/';
$g['url_module_skin'] = $g['url_module'].'/theme/'.$skin;
$g['img_module_skin'] = $g['url_module_skin'].'/images';

$g['dir_module_mode'] = $g['dir_module_skin'].$page;
$g['url_module_mode'] = $g['url_module_skin'].'/'.$page;

if($d['chatbot']['sosokmenu'])
{
	$_CA = explode('/',$d['chatbot']['sosokmenu']);
	$g['location'] = '<a href="'.RW(0).'">HOME</a>';
	$_tmp['count'] = count($_CA);
	$_tmp['split_id'] = '';
	for ($_i = 0; $_i < $_tmp['count']; $_i++)
	{
		$_tmp['location'] = getDbData($table['s_menu'],"id='".$_CA[$_i]."'",'*');
		$_tmp['split_id'].= ($_i?'/':'').$_tmp['location']['id'];
		$g['location']   .= ' &gt; <a href="'.RW('c='.$_tmp['split_id']).'">'.$_tmp['location']['name'].'</a>';
		$_HM['uid'] = $_tmp['location']['uid'];
	}
}

if(!$g['add_footer_inc'] && $_SESSION['marketurl'] && $iframe=='Y') $g['add_footer_inc'] = $g['dir_module'].'var/iframeReset.php';
$g['main'] = $g['dir_module_mode'].'.php';
?>