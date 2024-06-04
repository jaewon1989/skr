<?php
if(!defined('__KIMS__')) exit;

$snsSendResult = '';
if ($sns_t||$sns_f||$sns_m||$sns_y)
{
	include_once $g['path_module'].'social/var/var.php';
	include_once $g['path_core'].'function/rss.func.php';

	if($d['social']['use_b'])
	{
		$ourldata = getUrlData("http://api.bit.ly/v3/shorten?login=".$d['social']['key_b']."&apikey=".$d['social']['secret_b']."&longUrl=".urlencode($orignUrl),10);
		$orignUrl = getJSONData(stripslashes($ourldata),'url');
	}

	$mingid = getDbCnt($table['socialdata'],'min(gid)','');
	$snsgid = $mingid ? $mingid-1 : 1000000000;
	$QKEY = 'gid,provider,snsid,subject,name,nic,mbruid,id,targeturl,cync,d_regis';
}
if ($sns_t)
{
	$_mysnsdat=explode(',',$g['mysns'][0]);
	require_once($g['path_module'].'social/oauth/twitteroauth/twitteroauth.php');
	$TWITCONN = new TwitterOAuth($d['social']['key_t'], $d['social']['secret_t'],$_mysnsdat[2],$_mysnsdat[3]);
	$TWITRESULT = $TWITCONN -> post('statuses/update', array('status' => $orignContent.'   '.$orignUrl));
	if(is_object($TWITRESULT))
	{
		$QVAL = "'$snsgid','t','".($TWITRESULT->user->screen_name)."','$subject','$name','$nic','$my[uid]','$my[id]','http://twitter.com/".($TWITRESULT->user->screen_name)."','$xcync','$date[totime]'";
		getDbInsert($table['socialdata'],$QKEY,$QVAL);
		$snsSendResult .= getDbCnt($table['socialdata'],'max(uid)','').',';
		$snsgid--;
	}
}

if ($sns_f)
{
	$_mysnsdat=explode(',',$g['mysns'][1]);
	require_once($g['path_module'].'social/oauth/facebook/src/facebook.php');
	$FBCONN = new Facebook(array('appId'=>$d['social']['key_f'],'secret'=>$d['social']['secret_f'],'cookie'=>true));
	$FBRESULT = $FBCONN->api('/'. $_mysnsdat[4].'/feed?access_token='.$_mysnsdat[2],'POST',array('access_toten'=>$_mysnsdat[2],'message' => $orignSubject.'   '.$orignUrl));
	if($FBRESULT['id'])
	{
		$FBPARAM = explode('_',$FBRESULT['id']);
		$FBPAURL = 'http://facebook.com/permalink.php?story_fbid='.$FBPARAM[1].'&id='.$_mysnsdat[4];
		$QVAL = "'$snsgid','f','".$_mysnsdat[4]."','$subject','$name','$nic','$my[uid]','$my[id]','$FBPAURL','$xcync','$date[totime]'";
		getDbInsert($table['socialdata'],$QKEY,$QVAL);
		$snsSendResult .= getDbCnt($table['socialdata'],'max(uid)','').',';
		$snsgid--;
	}
}

if ($sns_m)
{
	$_mysnsdat=explode(',',$g['mysns'][2]);
	$sendContent = urlencode($orignContent).'+++["'.urlencode($_HS['title']).'":'.urlencode($orignUrl).'+]';
	$ME2RESULT = getUrlData("http://me2day.net/api/create_post/".$_mysnsdat[4].".json?uid=".$_mysnsdat[4]."&ukey=12345678".md5('12345678'.$_mysnsdat[3])."&akey=".$d['social']['key_m']."&post[body]=".$sendContent,10);
	if($ME2RESULT)
	{
		$QVAL = "'$snsgid','m','".getJSONData($ME2RESULT,'id')."','$subject','$name','$nic','$my[uid]','$my[id]','".getJSONData($ME2RESULT,'permalink')."','$xcync','$date[totime]'";
		getDbInsert($table['socialdata'],$QKEY,$QVAL);
		$snsSendResult .= getDbCnt($table['socialdata'],'max(uid)','').',';
		$snsgid--;
	}
}

if ($sns_y)
{
	$_mysnsdat=explode(',',$g['mysns'][3]);
	require_once($g['path_module'].'social/oauth/twitteroauth/yozm.php');
	$YOZMCONN = new YozmOAuth($d['social']['key_y'], $d['social']['secret_y'],$_mysnsdat[2],$_mysnsdat[3]);
	$YOZMRESULT = $YOZMCONN -> post('message/add', array('message' => $orignContent.'   '.$orignUrl));
	if(is_object($YOZMRESULT))
	{
		$QKEY = 'gid,provider,snsid,subject,name,nic,mbruid,id,targeturl,cync,d_regis';
		$QVAL = "'$snsgid','y','".($YOZMRESULT->message->user->url_name)."','$subject','$name','$nic','$my[uid]','$my[id]','".($YOZMRESULT->message->permanent_url)."','$xcync','$date[totime]'";
		getDbInsert($table['socialdata'],$QKEY,$QVAL);
		$snsSendResult .= getDbCnt($table['socialdata'],'max(uid)','').',';
		$snsgid--;
	}
}
?>