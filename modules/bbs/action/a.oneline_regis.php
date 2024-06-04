<?php
if(!defined('__KIMS__')) exit;

if (!$my['uid']) echo '[RESULT:정상적인 접근이 아닙니다.:RESULT]'; 
$R = getUidData($table['s_comment'],$parent);
if (!$R['uid']) echo '[RESULT:부모댓글이 지정되지 않았습니다.:RESULT]'; 

$parentmbr	= $R['mbruid'];
$mbruid		= $my['uid'];
$id			= $my['id'];
$name		= $my['uid'] ? $my['name'] : trim($name);
$nic		= $my['uid'] ? $my['nic'] : $name;
$pw			= $pw ? md5($pw) : ''; 
$content	= trim($content);
$html		= $html ? $html : 'TEXT';
$report		= 0;
$point		= $d['comment']['give_opoint'];
$d_regis	= $date['totime'];
$d_modify	= '';
$d_oneline	= '';
$ip			= $_SERVER['REMOTE_ADDR'];
$agent		= $_SERVER['HTTP_USER_AGENT'];
$adddata	= trim($adddata);


if ($d['comment']['badword_action'])
{
	$badwordarr = explode(',' , $d['comment']['badword']);
	$badwordlen = count($badwordarr);
	for($i = 0; $i < $badwordlen; $i++)
	{
		if(!$badwordarr[$i]) continue;

		if(strstr($content,$badwordarr[$i]))
		{
			if ($d['comment']['badword_action'] == 1)
			{
				echo '[RESULT:등록이 제한된 단어를 사용하셨습니다.:RESULT]'; 
			}
			else {
				$badescape = strCopy($badwordarr[$i],$d['comment']['badword_escape']);
				$content = str_replace($badwordarr[$i],$badescape,$content);
			}
		}
	}
}

if ($uid)
{
	$R = getUidData($table['s_oneline'],$uid);
	if (!$R['uid']) echo '[RESULT:존재하지 않는 한줄의견입니다. :RESULT]'; 
	if(!$my['admin'] && $my['uid'] != $R['mbruid']) echo '[RESULT:정상적인 접근이 아닙니다.:RESULT]'; 

	$QVAL = "hidden='$hidden',content='$content',html='$html',d_modify='$d_regis',adddata='$adddata'";
	getDbUpdate($table['s_oneline'],$QVAL,'uid='.$R['uid']);
}
else 
{
	//동기화
	$cyncArr = getArrayString($R['cync']);
	$fdexp = explode(',',$cyncArr['data'][2]);		
	if($fdexp[0]&&$fdexp[2]&&$cyncArr['data'][3])
	{
		$cyncQue = $fdexp[2].'='.$fdexp[2].'+1';
		if($fdexp[3]) $cyncQue .= ','.$fdexp[3]."='".$d_regis."'";
		getDbUpdate($cyncArr['data'][3],$cyncQue,$fdexp[0].'='.$cyncArr['data'][1]);
	}

	$maxuid = getDbCnt($table['s_oneline'],'max(uid)','');
	$uid = $maxuid ? $maxuid+1 : 1;
	
	$QKEY = "uid,site,parent,parentmbr,hidden,name,nic,mbruid,id,content,html,report,point,d_regis,d_modify,ip,agent,adddata";
	$QVAL = "'$uid','$s','$parent','$parentmbr','$hidden','$name','$nic','$mbruid','$id','$content','$html','$report','$point','$d_regis','$d_modify','$ip','$agent','$adddata'";
	getDbInsert($table['s_oneline'],$QKEY,$QVAL);
	getDbUpdate($table[$m.'data'],'oneline=oneline+1','uid='.$post);	 // 추가내용 
	getDbUpdate($table['s_comment'],"oneline=oneline+1,d_oneline='".$d_regis."'",'uid='.$parent);
	getDbUpdate($table['s_numinfo'],'oneline=oneline+1',"date='".$date['today']."' and site=".$s);


	if ($point&&$my['uid'])
	{
		getDbInsert($table['s_point'],'my_mbruid,by_mbruid,price,content,d_regis',"'".$my['uid']."','0','".$point."','한줄의견(".getStrCut(str_replace('&amp;',' ',strip_tags($content)),15,'').")포인트','".$date['totime']."'");
		getDbUpdate($table['s_mbrdata'],'point=point+'.$point,'memberuid='.$my['uid']);
	}
}
echo '[RESULT:ok:RESULT]';
exit;
?>
