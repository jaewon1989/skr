<?php
function getCURLData($url,$header)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	if(is_array($header)) curl_setopt($ch, CURLOPT_HTTPHEADER,$header); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt($ch, CURLOPT_COOKIE, '' );
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
	$curl_exec = curl_exec($ch);
	curl_close($ch);
	return $curl_exec;
}
function socialLogin($s,$id,$secret,$callBack,$type)
{
	if(!$_SESSION['SL']['state'.$s]) $_SESSION['SL']['state'.$s] = md5(microtime().mt_rand());
	$g['slogin']['client_id'] = $id;
	$g['slogin']['client_secret'] = $secret;
	$g['slogin']['redirect_uri'] = urlencode($callBack);
	$g['slogin']['state'] = $_SESSION['SL']['state'.$s];
	$g['slogin']['code'] = $_REQUEST['code'];
	// 네이버 ******************************************************************************************************************************************/
	if ($s == 'naver')
	{
		$g['slogin']['callapi'] = 'https://nid.naver.com/oauth2.0/authorize?client_id='.$g['slogin']['client_id'].'&response_type=code&redirect_uri='.$g['slogin']['redirect_uri'].'&state='.$g['slogin']['state'];
		$g['slogin']['callurl'] = 'https://nid.naver.com/oauth2.0/token?client_id='.$g['slogin']['client_id'].'&client_secret='.$g['slogin']['client_secret'];
		$g['slogin']['callurl'].= '&grant_type=authorization_code&state='.$g['slogin']['state'].'&code='.$g['slogin']['code'];
		if($type == 'token') 
		{
			if ($g['slogin']['state'] != $_REQUEST['state']) getLink('','','인증에 실패했습니다. 다시 시도해 주세요.','close');
			
			if ($_SESSION['SL'][$s]['userinfo']['token']) $dat['access_token'] = $_SESSION['SL'][$s]['userinfo']['token'];
			else $dat = json_decode(getCURLData($g['slogin']['callurl'],''), true);
			$xml = simplexml_load_string(getCURLData('https://apis.naver.com/nidlogin/nid/getUserProfile.xml',array("Authorization: Bearer ".$dat['access_token'])));
			$result = array();
			$result['token'] = $dat['access_token'];
			$result['name'] = (string)$xml -> response -> nickname;
			$result['email'] = (string)$xml -> response -> email;
			$result['photo'] = (string)$xml -> response -> profile_image;
			$result['age'] = (string)$xml -> response -> age;
			$result['sex'] = (string)$xml -> response -> gender;
			$result['sex'] = $result['sex'] == 'M' ? 1 : 2;
			$result['birthday'] = str_replace('-','',(string)$xml -> response -> birthday);
			$_SESSION['SL'][$s]['userinfo'] = $result;
		}
	}
	// @네이버 ******************************************************************************************************************************************/
	// 카카오 ******************************************************************************************************************************************/
	if ($s == 'kakao')
	{
		$g['slogin']['callapi'] = 'https://kauth.kakao.com/oauth/authorize?client_id='.$g['slogin']['client_id'].'&redirect_uri='.$g['slogin']['redirect_uri'].'&response_type=code&scope=';
		$g['slogin']['callurl'] = 'https://kauth.kakao.com/oauth/token?client_id='.$g['slogin']['client_id'].'&grant_type=authorization_code&code='.$g['slogin']['code'].'&redirect_uri='.$g['slogin']['redirect_uri'];
	
		if ($type == 'token')
		{
			if($_GET['error'] == 'access_denied') getLink('','','인증에 실패했습니다. 다시 시도해 주세요.','close');
			if ($_SESSION['SL'][$s]['userinfo']['token']) $dat1['access_token'] = $_SESSION['SL'][$s]['userinfo']['token'];
			else $dat1 = json_decode(getCURLData($g['slogin']['callurl'],''), true);
			$dat2 = json_decode(getCURLData('https://kapi.kakao.com/v1/user/me',array("Authorization: Bearer ".$dat1['access_token'])), true);
			$isksuser = json_decode(getCURLData('https://kapi.kakao.com/v1/api/story/isstoryuser',array("Authorization: Bearer ".$dat1['access_token'])), true);		
			if ($isksuser['isStoryUser'])
			{
				$dat3 = json_decode(getCURLData('https://kapi.kakao.com/v1/api/story/profile',array("Authorization: Bearer ".$dat1['access_token'])), true);
			}	
			$result = array();
			$result['token'] = $dat1['access_token'];
			$result['uid'] = $dat2['id'];
			$result['name'] = $dat2['properties']['nickname'];
			$result['photo'] = $dat2['properties']['profile_image'];
			$result['photo_thumb'] = $dat2['properties']['thumbnail_image'];
			$result['link'] = $dat3['permalink'];
			$result['birthday'] = $dat3['birthday'];
			$result['birthday_type'] = $dat3['birthdayType'] == 'SOLAR' ? 0 : 1;
			$result['ks_img_profile'] = $dat3['profileImageURL'];
			$result['ks_img_profile_thumb'] = $dat3['thumbnailURL'];
			$result['ks_img_bg'] = $dat3['bgImageURL'];
			$_SESSION['SL'][$s]['userinfo'] = $result;
		}
	}
	// @카카오 ******************************************************************************************************************************************/
	// 페이스북 ******************************************************************************************************************************************/
	if ($s == 'facebook')
	{
		$g['slogin']['callapi'] = 'https://graph.facebook.com/oauth/authorize?client_id='.$g['slogin']['client_id'].'&redirect_uri='.$g['slogin']['redirect_uri'].'&scope=email%20user_birthday';
		$g['slogin']['callurl'] = 'https://graph.facebook.com/oauth/access_token?client_id='.$g['slogin']['client_id'].'&client_secret='.$g['slogin']['client_secret'].'&code='.$g['slogin']['code'].'&redirect_uri='.$g['slogin']['redirect_uri'];
		if ($type == 'token')
		{
			if($_GET['error'] == 'access_denied') getLink('','','인증에 실패했습니다. 다시 시도해 주세요.','close');
			if ($_SESSION['SL'][$s]['userinfo']['token']) $access_token = $_SESSION['SL'][$s]['userinfo']['token'];
			else
			{
				$access_token = explode('=',getCURLData($g['slogin']['callurl'],''));
				$access_token = str_replace('&expires','',$access_token[1]);
			}
			$dat = json_decode(getCURLData('https://graph.facebook.com/me?fields=id,email,first_name,gender,last_name,link,name,birthday&access_token='.$access_token,''), true);
			$result = array();
			$result['token'] = $access_token;
			$result['uid'] = $dat['id'];
			$result['name'] = $dat['last_name'].' '.$dat['first_name']; // $dat['name']
			$result['email'] = $dat['email'];
			$result['photo'] = 'https://graph.facebook.com/'.$dat['id'].'/picture?type=large';
			$result['photo_thumb'] = 'https://graph.facebook.com/'.$dat['id'].'/picture';
			$result['link'] = $dat['link']; 
			$result['sex'] = $dat['gender'] == 'male' ? 1 : 2;
			$_birthday = explode('/',$dat['birthday']);
			$result['birthday'] = $_birthday[2].$_birthday[1].$_birthday[0];
			$_SESSION['SL'][$s]['userinfo'] = $result;
            $index="http://www.heat0.com/index.php";
		}
	}
	// @페이스북 ******************************************************************************************************************************************/
	// 구글 && 유투브 ******************************************************************************************************************************************/
	if ($s == 'google')
	{
		$g['slogin']['callapi'] = 'https://accounts.google.com/o/oauth2/auth?client_id='.$g['slogin']['client_id'].'&redirect_uri='.$g['slogin']['redirect_uri'].'&response_type=code&scope=email%20profile&state=%2Fprofile&approval_prompt=auto';
		$g['slogin']['callapi_youtube'] = 'https://accounts.google.com/o/oauth2/auth?client_id='.$g['slogin']['client_id'].'&redirect_uri='.$g['slogin']['redirect_uri'];
		$g['slogin']['callapi_youtube'].= '&response_type=code&scope=https://www.googleapis.com/auth/youtube%20email%20profile&access_type=offline';
		if ($type == 'token')
		{
			if($_GET['error'] == 'access_denied') getLink('','','인증에 실패했습니다. 다시 시도해 주세요.','close');
			
			$_nowToken = $_REQUEST['state']=='/profile' ? 'token' : 'token_youtube';
			if ($_SESSION['SL'][$s]['userinfo'][$_nowToken])
			{
				$dat1['access_token'] = $_SESSION['SL'][$s]['userinfo'][$_nowToken];
			}
			else 
			{
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "https://accounts.google.com/o/oauth2/token");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, array(
					'code' => $g['slogin']['code'],
					'client_id' => $g['slogin']['client_id'],
					'client_secret' => $g['slogin']['client_secret'],
					'redirect_uri' => urldecode($g['slogin']['redirect_uri']),
					'grant_type' => 'authorization_code'
				));
				$dat1 = (array)json_decode(curl_exec($ch));
				curl_close($ch);
			}
			$dat2 = json_decode(getCURLData('https://www.googleapis.com/oauth2/v2/userinfo',array("Authorization: Bearer ".$dat1['access_token'])), true);
			$result = array();
			if($_nowToken == 'token')
			{
				$result['token'] = $dat1['access_token'];
				$result['token_youtube'] = $_SESSION['SL'][$s]['userinfo']['token_youtube'];
			}
			else {
				$result['token'] = $_SESSION['SL'][$s]['userinfo']['token'];
				$result['token_youtube'] = $dat1['access_token'];
			}
			$result['uid'] = $dat2['id'];
			$result['email'] = $dat2['email'];
			$result['name'] = $dat2['name'];
			$result['photo'] = $dat2['picture'];
			$result['link'] = $dat2['link'];
			$result['sex'] = $dat2['gender'] == 'male' ? 1 : 2;
			$_SESSION['SL'][$s]['userinfo'] = $result;
		}
	}
	// @구글 && 유투브 ******************************************************************************************************************************************/
	// 인스타그램 ******************************************************************************************************************************************/
	if ($s == 'instagram')
	{
		$g['slogin']['callapi'] = 'https://api.instagram.com/oauth/authorize/?client_id='.$g['slogin']['client_id'].'&redirect_uri='.$g['slogin']['redirect_uri'].'&response_type=code';
		if ($type == 'token')
		{
			if($_GET['error'] == 'access_denied') getLink('','','인증에 실패했습니다. 다시 시도해 주세요.','close');
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://api.instagram.com/oauth/access_token");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, array(
				'code' => $g['slogin']['code'],
				'client_id' => $g['slogin']['client_id'],
				'client_secret' => $g['slogin']['client_secret'],
				'redirect_uri' => urldecode($g['slogin']['redirect_uri']),
				'grant_type' => 'authorization_code'
			));
			$dat1 = (array)json_decode(curl_exec($ch));
			curl_close($ch);
			$access_token = $dat1['access_token'];
			$dat2 = (array)$dat1['user'];
			$result = array();
			$result['token'] = $access_token;
			$result['uid'] = $dat2['id'];
			$result['name'] = $dat2['full_name'];
			$result['photo'] = $dat2['profile_picture'];
			$result['photo_thumb'] = $dat2['profile_picture'];
			$result['link'] = $dat2['website']; 
			$_SESSION['SL'][$s]['userinfo'] = $result;
		}
	}
	// @인스타그램 ******************************************************************************************************************************************/
	// 트위터 ******************************************************************************************************************************************/
	if ($s == 'twitter')
	{
		$g['slogin']['callapi'] = $callBack;
		if ($type == 'token')
		{
			if ($GLOBALS['twitter'] == 'Y')
			{
				if($GLOBALS['denied'])
				{
					//getLink($g['s'],'','','');
					header('Location :/?mod=rounge');
					exit;
				}
				require_once($GLOBALS['g']['dir_module'].'twitteroauth/twitteroauth.php');
				$TWITCONN = new TwitterOAuth($g['slogin']['client_id'],$g['slogin']['client_secret'],$_SESSION['t_token'],$_SESSION['t_sekey']);
				$ACCESSTWIT = $TWITCONN -> getAccessToken($_REQUEST['oauth_verifier']);
				$TR = $TWITCONN->get('account/verify_credentials');
				$result = array();
				$result['token'] = $ACCESSTWIT['oauth_token'];
				$result['email'] = '';
				$result['uid'] = $TR->id;
				$result['name'] = $TR->name;
				$result['photo'] = $TR->profile_image_url;
				$result['photo_thumb'] = $TR->profile_image_url;
				$result['link'] = ''; 
				$_SESSION['SL'][$s]['userinfo'] = $result;
				//getLink($g['s'],'','','');
				header('Location :/?mod=rounge');
				exit;
			}
			else {
				require_once($GLOBALS['g']['dir_module'].'twitteroauth/twitteroauth.php');
				$TWITCONN = new TwitterOAuth($g['slogin']['client_id'],$g['slogin']['client_secret']);
				$RCVTOKEN = $TWITCONN -> getRequestToken($g['slogin']['callapi'].'&twitter=Y');
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
		}
	}
	// @트위터 ******************************************************************************************************************************************/
	return $g['slogin'];
}
?>