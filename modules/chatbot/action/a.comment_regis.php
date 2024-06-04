<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 
include_once $g['dir_module'].'var/define.pass.php'; // class, 모듈, 레이아웃 패스 세팅 

//post, parent, depth, content 는 a.do_UserAction.php 에서 넘어온다.

$result=array();
$result['error']=false;

if (!$_SESSION['wcode']){
	$result['error']=true;
	$result['message']='[100]정상적인 접근이 아닙니다.';
	echo json_encode($result);
	exit;
}

// depth =1 인 경우 부모글은 s_comment 의 글이다 
if($depth) $R = getUidData($table[$m.'comment'],$parent);
else $R = getUidData($table[$m.'post'],$parent);

if (!$R['uid']){
	$result['error']=true;
    $result['message']='[200]정상적인 접근이 아닙니다.';
	echo json_encode($result);
	exit;	
}  
$parentmbr	= $R['mbruid']; // post mbruid
$mbruid		= $my['uid'];
$id			= $my['id'];
$name		= $my['uid'] ? $my['name'] : trim($name);
$nic		= $my['uid'] ? $my['nic'] : $name;
$pw			= $pw ? md5($pw) : ''; 
$content	= trim($content);
$html		= $html ? $html : 'TEXT';
$likes      = 0;
$report		= 0;
$point		= $d['comment']['give_opoint'];
$d_regis	= $date['totime'];
$d_modify	= '';
$d_comment	= '';
$ip			= $_SERVER['REMOTE_ADDR'];
$agent		= $_SERVER['HTTP_USER_AGENT'];
$adddata	= trim($adddata);

$hashTag = $feed->gethashtags($content); // 해시태그 붙은 단어 추출
$tag = trim($hashTag);

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
				$result['error']=true;
			    $result['message']='제한된 단어를 사용하셨습니다.';
				echo json_encode($result);
				exit;	
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
	$R = getUidData($table[$m.'comment'],$uid);
	if (!$R['uid']){
       	$result['error']=true;
		$result['message']='[300]정상적인 접근이 아닙니다.';
		echo json_encode($result);
		exit;	 
	}  
	if(!$my['admin'] && $my['uid'] != $R['mbruid']){
	    $result['error']=true;
		$result['message']='[400]정상적인 접근이 아닙니다.';
		echo json_encode($result);
		exit;	
	}  
	$QVAL = "hidden='$hidden',content='$content',tag='$tag',html='$html',d_modify='$d_regis',upload='$upload',adddata='$adddata'";
	getDbUpdate($table[$m.'comment'],$QVAL,'uid='.$R['uid']);
}
else 
{
    
	$maxuid = getDbCnt($table[$m.'comment'],'max(uid)','');
	$uid = $maxuid ? $maxuid+1 : 1;
	
	$QKEY = "uid,depth,parent,parentmbr,hidden,name,nic,mbruid,tag,id,content,html,report,point,d_regis,d_modify,upload,ip,agent,adddata";
	$QVAL = "'$uid','$depth','$parent','$parentmbr','$hidden','$name','$nic','$mbruid','$tag','$id','$content','$html','$report','$point','$d_regis','$d_modify','$upload','$ip','$agent','$adddata'";
	
	getDbInsert($table[$m.'comment'],$QKEY,$QVAL);
	$last_uid=getDbCnt($table[$m.'comment'],'max(uid)',''); // 방금 입력된 댓글  
	getDbUpdate($table[$m.'post'],"comment=comment+1,d_comment='".$d_regis."'",'uid='.$parent);

	$LASTUID = getDbCnt($table[$m.'comment'],'max(uid)','');
	
	// 한줄의견일 경우 
	if($depth)  getDbUpdate($table[$m.'comment'],'is_child=1','uid='.$parent);  
	getDbUpdate($table['s_numinfo'],'comment=comment+1',"date='".$date['today']."' and site=".$s);

	if ($point&&$my['uid'])
	{
		getDbInsert($table['s_point'],'my_mbruid,by_mbruid,price,content,d_regis',"'".$my['uid']."','0','".$point."','한줄의견(".getStrCut(str_replace('&amp;',' ',strip_tags($content)),15,'').")포인트','".$date['totime']."'");
		getDbUpdate($table['s_mbrdata'],'point=point+'.$point,'memberuid='.$my['uid']);
	}
}

// 신규등록 
$NOWUID = $LASTUID ? $LASTUID : $R['uid'];

$feed = new feed();

// 태그 등록 함수 실행 
if($tag || $R['tag']) $feed->RegisPostTag($tag,$R,'comment',$moduleid,$reply,$NOWUID);

$write_position=$position?$position:'feedView';
$commentList = $feed->getComment('feed',$post,1,$write_position);   

// json 리턴 
$result['message']='댓글이 등록되었습니다.';
$result['content']=$commentList[0];
$result['query']=$commentList[1];


?>
