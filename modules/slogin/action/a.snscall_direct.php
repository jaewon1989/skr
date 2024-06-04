<?php
if(!defined('__KIMS__')) exit;
include $g['dir_module'].'var/var.php';
include $g['path_module'].'member/var/var.join.php';
include $g['dir_module'].'_main.php'; // 함수 파일 추가
include_once $g['path_core'].'function/rss.func.php';
$sns_arr = array('t','f','g','k','n','i');
$sns_cnt = count($sns_arr);

if ($d['social']['use_t'])
{
	if ($type == 't')
	{
		require_once($g['dir_module'].'oauth/twitteroauth/twitteroauth.php');
		$TWITCONN = new TwitterOAuth($d['social']['key_t'],$d['social']['secret_t']);
		$RCVTOKEN = $TWITCONN -> getRequestToken($g['url_root'].'/?r='.$r.'&m='.$m.'&a=snscall_direct&twitter=Y');
		$_SESSION['t_token'] = $RCVTOKEN['oauth_token'];
		$_SESSION['t_sekey'] = $RCVTOKEN['oauth_token_secret'];
		switch ($TWITCONN -> http_code)
		{
			case 200:
			$TWITURL = $TWITCONN -> getAuthorizeURL($RCVTOKEN['oauth_token']);
			break;
			default:
			getLink('','','죄송합니다. 트위터 서버가 응답하지 않습니다.','close');
			break;
		}
		header('Location:'.$TWITURL);
		exit;
	}
	if ($twitter == 'Y')
	{
		if ($denied)
		{
			getLink('','','트위터 연결을 취소하셨습니다.','close');
		}
		require_once($g['path_module'].'social/oauth/twitteroauth/twitteroauth.php');
		$TWITCONN = new TwitterOAuth($d['social']['key_t'], $d['social']['secret_t'], $_SESSION['t_token'], $_SESSION['t_sekey']);
		$ACCESSTWIT = $TWITCONN -> getAccessToken($_REQUEST['oauth_verifier']);
		$_SESSION['t_token'] = $ACCESSTWIT['oauth_token'];
		$_SESSION['t_sekey'] = $ACCESSTWIT['oauth_token_secret'];
		$_SESSION['t_mbrid'] = $ACCESSTWIT['screen_name'];
		if (!$_SESSION['t_mbrid'])
		{
			getLink('','','죄송합니다. 세션에 문제가 있습니다. 다시 시도해 주세요.','close');
		}		
		$link='https://twitter.com/'.$_SESSION['t_mbrid'];
		$_rs2 = 'on,'.$link.','.$_SESSION['t_token'].','.$_SESSION['t_sekey'].','.$_SESSION['t_mbrid'].','.$_REQUEST['oauth_verifier'].',';
		$_rs1 = '';
		for($i = 0; $i < $sns_cnt; $i++)
		{
			$_rs1 .= ($i==0?$_rs2:$g['mysns'][$i]).'|';
		}
		// 로그인 상태인 경우 	
		if ($my['uid'])
		{
			$_ISSNS = getDbData($table['s_mbrsns'],"st='".$_SESSION['t_mbrid']."'",'*');
			if ($_ISSNS['memberuid']&&$_ISSNS['memberuid']!=$my['uid'])
			{
				$_SESSION['t_token'] = '';
				$_SESSION['t_sekey'] = '';
				$_SESSION['t_mbrid'] = '';
				$_SESSION['plussns'] = $_ISSNS['memberuid'];
				getLink('','',"이미 다른계정에 연결되어 있습니다. \\n회원님의 계정이라면 통합해 주세요.",'close');
			}
			getDbUpdate($table['s_mbrdata'],"sns='".$_rs1."'",'memberuid='.$my['uid']);
			if (!$g['mysns'][0])
			{
				if(getDbRows($table['s_mbrsns'],'memberuid='.$my['uid']))
				{
					getDbUpdate($table['s_mbrsns'],"st='".$_SESSION['t_mbrid']."'",'memberuid='.$my['uid']);
				}
				else {
					getDbInsert($table['s_mbrsns'],'memberuid,st',"'".$my['uid']."','".$_SESSION['t_mbrid']."'");
				}
			}
		}
		else {
			// 기존에 sns 로 가입되어 있는지 체크 
			$_ISSNS = getDbData($table['s_mbrsns'],"st='".$_SESSION['t_mbrid']."'",'*');
			
			// 기존에 sns 로 가입되어 있는 경우 로그인 프로세스만 진행  
			if($_ISSNS['memberuid'])
			{
				$M	= getUidData($table['s_mbrid'],$_ISSNS['memberuid']);
				getDbUpdate($table['s_mbrdata'],"num_login=num_login+1,now_log=1,last_log='".$date['totime']."'",'memberuid='.$M['uid']);
				getDbUpdate($table['s_numinfo'],'login=login+1',"date='".$date['today']."' and site=".$s);
				$_SESSION['mbr_uid'] = $M['uid'];
				$_SESSION['mbr_pw']  = $M['pw'];
			
				 // 로그인 유지기능 추가 
			      setAccessToken($M['uid'],''); // sys.function.php 파일 함수  참조
          	     }
			// 기존에 sns 로 가입되어 있지 않은 경우는 회원가입&로그인 프로세스 진행
			else {
				include_once $g['path_core'].'function/rss.func.php';
				$TR = $TWITCONN->get('account/verify_credentials',array('include_email'=>'true'));

			    $id = 'st'.sprintf('%-012s',str_replace('.','',$g['time_start']));
				getDbInsert($table['s_mbrid'],'site,id,pw',"'$s','$id',''");
				$memberuid  = getDbCnt($table['s_mbrid'],'max(uid)','');
				$picdata = getUrlData($TR->profile_image_url,10); // $g['path_core'].'function/rss.func.php' 함수 참조
			   	if ($picdata) $photo=setAvatar($picdata,$id,'small'); // _main.php 함수 참조
				$picdata = getUrlData(str_replace('_normal','_reasonably_small',$TR->profile_image_url),10);
				if ($picdata)	$photo=setAvatar($picdata,$id,'big');
			
			      $_QKEY=getMbrDataKey();// _main.php 함수 참조
				$_QVAL = "'$memberuid','$s','1','".$d['member']['join_group']."','".$d['member']['join_level']."','0','0','',";
				$_QVAL.= "'".$TR->email."','".$TR->name."','".$TR->name."','','$photo','".$link."','0','0','0','0','','','',";
				$_QVAL.= "'','','','','0','0','0','1','0','".$d['member']['join_point']."','0','0','0','1','','','1','".$date['totime']."','".$date['totime']."','0','".$date['totime']."','','$_rs1',''";
		       
			      // 회원가입 프로세스 함수 호출 _main.php 해당 함수 참조
			      setSnsMember($_QKEY,$_QVAL,$memberuid,'st',$_SESSION['t_mbrid']);  		
		
			}
		}
		getLink('reload','opener.','트위터와 연결되었습니다.','close');
	}
}
// facebook
if ($d['social']['use_f'])
{

	if ($type == 'f')
	{ 
        $state= 'fb_'.$d['social']['key_f'].'_state';
        $_SESSION[$state]=$state;
		 $state='fb_'.$d['social']['key_f'].'_state';
	     $redirect_uri = urlencode($g['url_root'].'/?r='.$r.'&m='.$m.'&a=snscall_direct&facebook=Y');
		 $loginUrl = "http://www.facebook.com/dialog/oauth?"
          . "client_id=" . $d['social']['key_f']
          . "&redirect_uri=" . $redirect_uri
          . "&state=" . $state
          . "&response_type=code"
          . "&scope=public_profile,email"; // openid%20profile
         	
		  header('Location:'.$loginUrl);
		exit;
	}

    
	if ($facebook == 'Y')
	{
     	      $f_mbrid = 'fb_'.$d['social']['key_f'].'_user_id';
		$f_token = 'fb_'.$d['social']['key_f'].'_access_token';
	
		$_SESSION[$f_token] = '';
		$_SESSION[$f_mbrid] = '';
	
           require_once $g['path_module'].'social/oauth/facebook/src/facebook.php';
		$FC = new Facebook(array('appId'=>$d['social']['key_f'],'secret'=>$d['social']['secret_f']));      
		$FUID = $FC->getUser();

     	      if ($FUID) {
			try {			
			} catch (FacebookApiException $e) {
				$FUID = null;
				getLink('','','페이스북 연결을 취소하셨습니다.','close');
			}
		}
		
		if ($FUID)
		{
			if (!$_SESSION[$f_mbrid])
			{
				getLink('','','죄송합니다. 세션에 문제가 있습니다. 다시 시도해 주세요.','close');
			}
                 // 회원정보 가져오기 
			$FR=$FC->api('/me?fields=link,id,name,email,gender,picture.type(large)');
			$id =$FR['id'];
			$name =$FR['name'];
			$email =$FR['email'];
			$profile_image_url =filter_var($FR['picture']['data']['url'], FILTER_VALIDATE_URL);
			$gender=$FR['gender'];
			$profile_url=$FR['link'];
	           $_rs1 = '';
			$_rs2 = 'on,'.$profile_url.','.$_SESSION[$f_token].',"",'.$_SESSION[$f_mbrid].',"",';
	
			for($i = 0; $i < $sns_cnt; $i++)
			{
				$_rs1 .= ($i==1?$_rs2:$g['mysns'][$i]).'|';
			}			
			if ($my['uid'])
			{
				$_ISSNS = getDbData($table['s_mbrsns'],"sf='".$_SESSION[$f_mbrid]."'",'*');
				if ($_ISSNS['memberuid']&&$_ISSNS['memberuid']!=$my['uid'])
				{
					$_SESSION[$f_token] = '';
					$_SESSION[$f_mbrid] = '';
					$f_start = '';
					$_SESSION['plussns'] = $_ISSNS['memberuid'];
					getLink('','top.opener.',"이미 다른계정에 연결되어 있습니다. \\n회원님의 계정이라면 통합해 주세요.",'close');
				}
				getDbUpdate($table['s_mbrdata'],"sns='".$_rs1."'",'memberuid='.$my['uid']);
				if (!$g['mysns'][1])
				{
					if(getDbRows($table['s_mbrsns'],'memberuid='.$my['uid']))
					{
						getDbUpdate($table['s_mbrsns'],"sf='".$_SESSION[$f_mbrid]."'",'memberuid='.$my['uid']);
					}
					else {
						getDbInsert($table['s_mbrsns'],'memberuid,sf',"'".$my['uid']."','".$_SESSION[$f_mbrid]."'");
					}
				}
			}
			else {
				$_ISSNS = getDbData($table['s_mbrsns'],"sf='".$_SESSION[$f_mbrid]."'",'*');
				if($_ISSNS['memberuid'])
				{
					$M	= getUidData($table['s_mbrid'],$_ISSNS['memberuid']);
					getDbUpdate($table['s_mbrdata'],"num_login=num_login+1,now_log=1,last_log='".$date['totime']."'",'memberuid='.$M['uid']);
					getDbUpdate($table['s_numinfo'],'login=login+1',"date='".$date['today']."' and site=".$s);
					$_SESSION['mbr_uid'] = $M['uid'];
					$_SESSION['mbr_pw']  = $M['pw'];

					// 로그인 유지기능 추가 
				      setAccessToken($M['uid'],''); // sys.function.php 파일 함수  참조
	                 }
				else {
	                      include_once $g['path_core'].'function/rss.func.php';
					$id = 'sf'.sprintf('%-012s',str_replace('.','',$g['time_start']));
					getDbInsert($table['s_mbrid'],'site,id,pw',"'$s','$id',''");
					$memberuid  = getDbCnt($table['s_mbrid'],'max(uid)','');
				      				
					$picdata = getUrlData($profile_image_url,10);
					if ($picdata) $photo=setAvatar($picdata,$id,'big');

					$_QKEY=getMbrDataKey();// _main.php 함수 참조
					$_QVAL = "'$memberuid','$s','1','1','1','0','0','',";
					$_QVAL.= "'".$email."','".$name."','".$name."','','".$photo."','".$profile_url."','".($gender=='male'?1:2)."','','','0','','','',";
					$_QVAL.= "'','','','','0','0','0','1','0','".$d['member']['join_point']."','0','0','0','1','','','1','".$date['totime']."','".$date['totime']."','0','".$date['totime']."','','$_rs1',''";
				   
				    // 회원가입 프로세스 함수 호출 _main.php 해당 함수 참조
		               setSnsMember($_QKEY,$_QVAL,$memberuid,'sf',$_SESSION[$f_mbrid]); 
				}
			}
			getLink('reload','top.opener.','페이스북과 연결되었습니다.','close');
		}
		else {
			echo '<div style="width:100%;height:100%;text-align:center;padding-top:250px;line-height:250%;"><img src="'.$g['dir_module'].'/asset/img/loader.gif" alt="" /><br /><div style="font-weight:bold;font-size:15px;color:#999999;">페이스북에 연결하고 있습니다. 잠시만 기다려 주세요.</div></div>';
			echo '<iframe src="'.$FC->getLoginUrl().'" width="0" height="0" frameborder="0"></iframe>';
			exit;
		}
	}
}
// google + 
if ($d['social']['use_g'])
{
	if ($type == 'g')
	{
		 $_SESSION['state'] = rand(0,999999999);
            $redirect_uri = urlencode($g['url_root'].'/?r='.$r.'&m='.$m.'&a=snscall_direct&google=Y');
		 $loginUrl = "https://accounts.google.com/o/oauth2/auth?"
          . "client_id=" . $d['social']['key_g']
          . "&redirect_uri=" . $redirect_uri
          . "&state=" . $_SESSION['state']
          . "&response_type=code"
          . "&scope=https://www.googleapis.com/auth/plus.login profile email" // openid%20profile
          . "&include_granted_scopes=true";
        	
		   header('Location:'.$loginUrl);
		exit;
	}
    // redirect 
	if ($google == 'Y')
	{
		//error_reporting(E_ALL);
           $redirect_uri = urldecode($g['url_root'].'/?r='.$r.'&m='.$m.'&a=snscall_direct&google=Y');
		if ($denied)
		{
			getLink('','','구글과 연결을 취소하셨습니다.','close');
		}
      
           //Google API PHP Library includes
		require_once($g['dir_module'].'oauth/google/google-api-php-client-0.6.7/src/Google_Client.php');
		require_once($g['dir_module'].'oauth/google/google-api-php-client-0.6.7/src/contrib/Google_Oauth2Service.php');
       
           $client = new Google_Client();
   
           $client->setApplicationName("KimsQ Google OAuth Login");
		$client->setClientId($d['social']['key_g']);
		$client->setClientSecret($d['social']['secret_g']);
		$client->setRedirectUri($redirect_uri);
           $objOAuthService = new Google_Oauth2Service($client);
	
		//Access Token 세션에 저장
		if (isset($_GET['code'])) {
		   $client->authenticate($_GET['code']);
		   $_SESSION['g_token'] = $client->getAccessToken();
		   header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
		}
		//Access Token 으로 사용자 정보 요청하기
		if (isset($_SESSION['g_token']) && $_SESSION['g_token']) {
		    $client->setAccessToken($_SESSION['g_token']);
		}
		//Get User Data from Google Plus
		//If New, Insert to Database
		if ($client->getAccessToken())
		{
	            //For logged in user, get details from google using access token
		      $user                 = $objOAuthService->userinfo->get();
		      $user_id              = $user['id'];
		      $user_name            = filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
		      $email                = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
		      $profile_url          = filter_var($user['link'], FILTER_VALIDATE_URL);
		      $profile_image_url    = filter_var($user['picture'], FILTER_VALIDATE_URL);
		      $personMarkup         = "<div><img src='$profile_image_url?sz=50'></div>";
		      $_SESSION['g_token']    = $client->getAccessToken();
		      $_SESSION['g_mbrid']    = $user_id;
		      if (!$_SESSION['g_mbrid'])
		      {
			      getLink('','','사용자 정보를 가져오지 못했습니다. 다시 시도해 주세요.','close');
	  	      }	
	  	      $_rs2 = 'on,'.$profile_url.','.$_SESSION['g_token'].',"",'.$_SESSION['g_mbrid'].',"",';
		      $_rs1 = '';
		 	 for($i = 0; $i < $sns_cnt; $i++)
			 {
			  	 $_rs1 .= ($i==2?$_rs2:$g['mysns'][$i]).'|';
			 }   
                  if ($my['uid'])
		      {
				$_ISSNS = getDbData($table['s_mbrsns'],"sg='".$_SESSION['g_mbrid']."'",'*');
				if ($_ISSNS['memberuid']&&$_ISSNS['memberuid']!=$my['uid'])
				{
					$_SESSION['g_token'] = '';
					$_SESSION['g_mbrid'] = '';
				
					$_SESSION['plussns'] = $_ISSNS['memberuid'];
					getLink('','top.opener.',"이미 다른계정에 연결되어 있습니다. \\n회원님의 계정이라면 통합해 주세요.",'close');
				}
				getDbUpdate($table['s_mbrdata'],"sns='".$_rs1."'",'memberuid='.$my['uid']);
				if (!$g['mysns'][1])
				{
					if(getDbRows($table['s_mbrsns'],'memberuid='.$my['uid']))
					{
						getDbUpdate($table['s_mbrsns'],"sg='".$_SESSION['g_mbrid']."'",'memberuid='.$my['uid']);
					}
					else {
						getDbInsert($table['s_mbrsns'],'memberuid,sg',"'".$my['uid']."','".$_SESSION['g_mbrid']."'");
					}
				}
			}
		      else {
			      $_ISSNS = getDbData($table['s_mbrsns'],"sg='".$_SESSION['g_mbrid']."'",'*');
			      if($_ISSNS['memberuid'])
			      {
					$M	= getUidData($table['s_mbrid'],$_ISSNS['memberuid']);
					getDbUpdate($table['s_mbrdata'],"num_login=num_login+1,now_log=1,last_log='".$date['totime']."'",'memberuid='.$M['uid']);
					getDbUpdate($table['s_numinfo'],'login=login+1',"date='".$date['today']."' and site=".$s);
					$_SESSION['mbr_uid'] = $M['uid'];
					$_SESSION['mbr_pw']  = $M['pw'];

					// 로그인 유지기능 추가 
				      setAccessToken($M['uid'],''); // sys.function.php 파일 함수  참조
		           }
			     else {
	                       include_once $g['path_core'].'function/rss.func.php';  
					$id = 'sg'.sprintf('%-012s',str_replace('.','',$g['time_start']));
					getDbInsert($table['s_mbrid'],'site,id,pw',"'$s','$id',''");
					$memberuid  = getDbCnt($table['s_mbrid'],'max(uid)','');
									
					$picdata = getUrlData($profile_image_url,10);
					if ($picdata) $photo=setAvatar($picdata,$id,'big');
					$_QKEY=getMbrDataKey();// _main.php 함수 참조
					$_QVAL = "'$memberuid','$s','1','1','1','0','0','',";
					$_QVAL.= "'".$email."','".$user_name."','".$user_name."','','".$photo."','".$profile_url."','','','','0','','','',";
					$_QVAL.= "'','','','','0','0','0','1','0','".$d['member']['join_point']."','0','0','0','1','','','1','".$date['totime']."','".$date['totime']."','0','".$date['totime']."','','$_rs1',''";
				   
				      // 회원가입 프로세스 함수 호출 _main.php 해당 함수 참조
		                 setSnsMember($_QKEY,$_QVAL,$memberuid,'sg',$_SESSION['g_mbrid']); 
			     } 
       	     }
		}
		else
		{
		    //For Guest user, get google login url
		    $authUrl = $client->createAuthUrl();
		}
   
		getLink('reload','opener.','구글과 연결되었습니다.','close');
	}
}// if use google

// 카카오톡  
if ($d['social']['use_k'])
{
	if ($type == 'k')
	{
		 $_SESSION['state'] = rand(0,999999999);
            $redirect_uri = urlencode($g['url_root'].'/?r='.$r.'&m='.$m.'&a=snscall_direct&kakao=Y');
		 $loginUrl = "https://kauth.kakao.com/oauth/authorize?"
          . "client_id=" . $d['social']['key_k']
          . "&redirect_uri=" . $redirect_uri
          . "&state=" . $_SESSION['state']
          . "&response_type=code"
          . "&scope=";
        	
		   header('Location:'.$loginUrl);
		exit;
	}
    // redirect 
	if ($kakao == 'Y')
	{
           $redirect_uri = urlencode($g['url_root'].'/?r='.$r.'&m='.$m.'&a=snscall_direct&kakao=Y');
		if ($denied)
		{
			getLink('','','카카오톡 연결을 취소하셨습니다.','close');
		}
      
           //Access Token 받기 : getCURLData==> _main.php 함수 참조 
		if (isset($_GET['code'])) {
		      $getTokenUrl='https://kauth.kakao.com/oauth/token?client_id='.$d['social']['key_k'].'&grant_type=authorization_code&code='.$_GET['code'].'&redirect_uri='.$redirect_uri;
                 $dat1 = json_decode(getCURLData($getTokenUrl,''), true);
      	}
	
		if (isset($dat1['access_token']))
		{
			$dat2 = json_decode(getCURLData('https://kapi.kakao.com/v1/user/me',array("Authorization: Bearer ".$dat1['access_token'])), true);
			$isksuser = json_decode(getCURLData('https://kapi.kakao.com/v1/api/story/isstoryuser',array("Authorization: Bearer ".$dat1['access_token'])), true);
			if ($isksuser['isStoryUser']) $dat3 = json_decode(getCURLData('https://kapi.kakao.com/v1/api/story/profile',array("Authorization: Bearer ".$dat1['access_token'])), true);
       		$user_id              = $dat2['id'];
			$user_nic            = filter_var($dat2['properties']['nickname'], FILTER_SANITIZE_SPECIAL_CHARS);
			$user_name            = filter_var($dat3['nickName'], FILTER_SANITIZE_SPECIAL_CHARS);			
			$profile_url          = filter_var($dat3['permalink'], FILTER_VALIDATE_URL);
			$profile_image_url    = filter_var($dat3['profileImageURL'], FILTER_VALIDATE_URL);
			$_SESSION['k_token']    = $dat1['access_token'];
			$_SESSION['k_mbrid']    = $user_id;
			if (!$_SESSION['k_mbrid'])
			{
			     getLink('','','사용자 정보를 가져오지 못했습니다. 다시 시도해 주세요.','close');
		  	}	
		  	$_rs2 = 'on,'.$profile_url.','.$_SESSION['k_token'].',"",'.$_SESSION['k_mbrid'].',"",';
			$_rs1 = '';
			for($i = 0; $i < $sns_cnt; $i++)
			{
				$_rs1 .= ($i==3?$_rs2:$g['mysns'][$i]).'|';
			}   
                 if ($my['uid'])
			{
				$_ISSNS = getDbData($table['s_mbrsns'],"sk='".$_SESSION['k_mbrid']."'",'*');
				if ($_ISSNS['memberuid']&&$_ISSNS['memberuid']!=$my['uid'])
				{
					$_SESSION['k_token'] = '';
					$_SESSION['k_mbrid'] = '';
				
					$_SESSION['plussns'] = $_ISSNS['memberuid'];
					getLink('','top.opener.',"이미 다른계정에 연결되어 있습니다. \\n회원님의 계정이라면 통합해 주세요.",'close');
				}
				getDbUpdate($table['s_mbrdata'],"sns='".$_rs1."'",'memberuid='.$my['uid']);
				if (!$g['mysns'][1])
				{
					if(getDbRows($table['s_mbrsns'],'memberuid='.$my['uid']))
					{
						getDbUpdate($table['s_mbrsns'],"sk='".$_SESSION['k_mbrid']."'",'memberuid='.$my['uid']);
					}
					else {
						getDbInsert($table['s_mbrsns'],'memberuid,sk',"'".$my['uid']."','".$_SESSION['k_mbrid']."'");
					}
				}
			}
			else {
				$_ISSNS = getDbData($table['s_mbrsns'],"sk='".$_SESSION['k_mbrid']."'",'*');
				if($_ISSNS['memberuid'])
				{
					$M	= getUidData($table['s_mbrid'],$_ISSNS['memberuid']);
					getDbUpdate($table['s_mbrdata'],"num_login=num_login+1,now_log=1,last_log='".$date['totime']."'",'memberuid='.$M['uid']);
					getDbUpdate($table['s_numinfo'],'login=login+1',"date='".$date['today']."' and site=".$s);
					$_SESSION['mbr_uid'] = $M['uid'];
					$_SESSION['mbr_pw']  = $M['pw'];

					// 로그인 유지기능 추가 
				      setAccessToken($M['uid'],''); // sys.function.php 파일 함수  참조
	                 }
				else {
	                      include_once $g['path_core'].'function/rss.func.php';  
					$id = 'sk'.sprintf('%-012s',str_replace('.','',$g['time_start']));
					getDbInsert($table['s_mbrid'],'site,id,pw',"'$s','$id',''");
					$memberuid  = getDbCnt($table['s_mbrid'],'max(uid)','');
									
					$picdata = getUrlData($profile_image_url,10);
					if ($picdata) $photo=setAvatar($picdata,$id,'big');
					$_QKEY=getMbrDataKey();// _main.php 함수 참조
					$_QVAL = "'$memberuid','$s','1','1','1','0','0','',";
					$_QVAL.= "'".$email."','".$user_name."','".$user_nic."','','".$photo."','".$profile_url."','','','','0','','','',";
					$_QVAL.= "'','','','','0','0','0','1','0','".$d['member']['join_point']."','0','0','0','1','','','1','".$date['totime']."','".$date['totime']."','0','".$date['totime']."','','$_rs1',''";
				   
				    // 회원가입 프로세스 함수 호출 _main.php 해당 함수 참조
		               setSnsMember($_QKEY,$_QVAL,$memberuid,'sk',$_SESSION['k_mbrid']); 
				} 
	    	     }
	    	     getLink('reload','opener.','카카오톡과 연결되었습니다.','close');     
		}else{
                echo '카카오톡과 연결되지 않았습니다. 관리자에게 문의해주세요.<br />';
                var_dump($dat1);
		}  
	
	}
}// if use kakao

// 네이버  
if ($d['social']['use_n'])
{
	if ($type == 'n')
	{
		 $_SESSION['state'] = rand(0,999999999);
            $redirect_uri = urlencode($g['url_root'].'/?r='.$r.'&m='.$m.'&a=snscall_direct&naver=Y');
		 $loginUrl = "https://nid.naver.com/oauth2.0/authorize?"
          . "client_id=" . $d['social']['key_n']
          . "&redirect_uri=" . $redirect_uri
          . "&state=" . $_SESSION['state']
          . "&response_type=code"
          . "&scope=";
        	
		header('Location:'.$loginUrl);
		exit;
	}
    // redirect 
	if ($naver == 'Y')
	{
           $redirect_uri = urlencode($g['url_root'].'/?r='.$r.'&m='.$m.'&a=snscall_direct&naver=Y');
		if ($denied)
		{
			getLink('','','네이버 연결을 취소하셨습니다.','close');
		}
      
           //Access Token 받기 : getCURLData==> _main.php 함수 참조 
		if (isset($_GET['code'])) {
		      $getTokenUrl='https://nid.naver.com/oauth2.0/token?client_id='.$d['social']['key_n'].'&client_secret='.$d['social']['secret_n'];
		      $getTokenUrl.='&grant_type=authorization_code&state='.$_SESSION['state'].'&code='.$_GET['code'].'&redirect_uri='.$redirect_uri;
                 $dat = json_decode(getCURLData($getTokenUrl,''), true);
      	}
	
		if (isset($dat['access_token']))
		{
			$xml = simplexml_load_string(getCURLData('https://apis.naver.com/nidlogin/nid/getUserProfile.xml',array("Authorization: Bearer ".$dat['access_token'])));
			
       		$user_id              = (string)$xml -> response -> id;
			$user_nic            = (string)$xml -> response -> nickname;
			$user_name        = (string)$xml -> response -> name;
			$email= (string)$xml -> response -> email;			
			$profile_image_url    =(string)$xml -> response -> profile_image;
			$age = (string)$xml -> response -> age;
			$gender = (string)$xml -> response -> gender;
			$sex = $gender== 'M' ? 1 : 2;
			$birthday = str_replace('-','',(string)$xml -> response -> birthday);
	
			$_SESSION['n_token']    = $dat['access_token'];
			$_SESSION['n_mbrid']    = $user_id;
			if (!$_SESSION['n_mbrid'])
			{
			     getLink('','','사용자 정보를 가져오지 못했습니다. 다시 시도해 주세요.','close');
		  	}	
		  	$_rs2 = 'on,'.$profile_url.','.$_SESSION['n_token'].',"",'.$_SESSION['n_mbrid'].',"",';
			$_rs1 = '';
			for($i = 0; $i < $sns_cnt; $i++)
			{
				$_rs1 .= ($i==4?$_rs2:$g['mysns'][$i]).'|';
			}   
                 if ($my['uid'])
			{
				$_ISSNS = getDbData($table['s_mbrsns'],"sn='".$_SESSION['n_mbrid']."'",'*');
				if ($_ISSNS['memberuid']&&$_ISSNS['memberuid']!=$my['uid'])
				{
					$_SESSION['n_token'] = '';
					$_SESSION['n_mbrid'] = '';
				
					$_SESSION['plussns'] = $_ISSNS['memberuid'];
					getLink('','top.opener.',"이미 다른계정에 연결되어 있습니다. \\n회원님의 계정이라면 통합해 주세요.",'close');
				}
				getDbUpdate($table['s_mbrdata'],"sns='".$_rs1."'",'memberuid='.$my['uid']);
				if (!$g['mysns'][1])
				{
					if(getDbRows($table['s_mbrsns'],'memberuid='.$my['uid']))
					{
						getDbUpdate($table['s_mbrsns'],"sn='".$_SESSION['n_mbrid']."'",'memberuid='.$my['uid']);
					}
					else {
						getDbInsert($table['s_mbrsns'],'memberuid,sn',"'".$my['uid']."','".$_SESSION['n_mbrid']."'");
					}
				}
			}
			else {
				$_ISSNS = getDbData($table['s_mbrsns'],"sn='".$_SESSION['n_mbrid']."'",'*');
				if($_ISSNS['memberuid'])
				{
					$M	= getUidData($table['s_mbrid'],$_ISSNS['memberuid']);
					getDbUpdate($table['s_mbrdata'],"num_login=num_login+1,now_log=1,last_log='".$date['totime']."'",'memberuid='.$M['uid']);
					getDbUpdate($table['s_numinfo'],'login=login+1',"date='".$date['today']."' and site=".$s);
					$_SESSION['mbr_uid'] = $M['uid'];
					$_SESSION['mbr_pw']  = $M['pw'];

					// 로그인 유지기능 추가 
				      setAccessToken($M['uid'],''); // sys.function.php 파일 함수  참조
	                 }
				else {
	                      include_once $g['path_core'].'function/rss.func.php';  
					$id = 'sn'.sprintf('%-012s',str_replace('.','',$g['time_start']));
					getDbInsert($table['s_mbrid'],'site,id,pw',"'$s','$id',''");
					$memberuid  = getDbCnt($table['s_mbrid'],'max(uid)','');
									
					$picdata = file_get_contents($profile_image_url);//getUrlData($profile_image_url,10);
					if ($picdata) $photo=setAvatar($picdata,$id,'big');
					$_QKEY=getMbrDataKey();// _main.php 함수 참조
					$_QVAL = "'$memberuid','$s','1','1','1','0','0','',";
					$_QVAL.= "'".$email."','".$user_name."','".$user_nic."','','".$photo."','".$profile_url."','','','','0','','','',";
					$_QVAL.= "'','','','','0','0','0','1','0','".$d['member']['join_point']."','0','0','0','1','','','1','".$date['totime']."','".$date['totime']."','0','".$date['totime']."','','$_rs1',''";
				   
				    // 회원가입 프로세스 함수 호출 _main.php 해당 함수 참조
		               setSnsMember($_QKEY,$_QVAL,$memberuid,'sn',$_SESSION['n_mbrid']); 
				} 
	    	     }
	    	     getLink('reload','opener.','네이버와 연결되었습니다.','close');     
		}else{
                echo '네이버와 연결되지 않았습니다. 관리자에게 문의해주세요.<br />';
                var_dump($dat);
		}  
	
	}
}// if use kakao
exit;
?>
