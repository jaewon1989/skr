<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 
include_once $g['dir_module'].'var/define.path.php'; // class, 모듈, 레이아웃 패스 세팅 
$chatbot = new Chatbot();

$result=array();
$result['error']=false;

if(!$vendor || !$bot){
	$result['error'] = true;
	$result['content'] = 'error : 200';
	echo json_encode($result);
	exit;
}

// 엔터티 저장 
if($act=='save-keyword'){
	$max_q = 'depth='.$depth;
	if($parent) $max_q.=' and parent='.$parent;
	$MAXC = getDbCnt($table[$m.'keyword'],'max(gid)',$max_q);
    $sarr = explode(',' , trim($keywords));
	$slen = count($sarr);
	
	for ($i = 0 ; $i < $slen; $i++)
	{
		if (!$sarr[$i]) continue;
		$gid	= $MAXC+1+$i;
		$xname	= trim($sarr[$i]);
		$is_name = getDbRows($table[$m.'keyword'],'depth='.$depth.' and keyword='.$xname);
		$QKEY = "gid,vendor,bot,is_child,parent,depth,hidden,mobile,printType,keyword,showMenu";
		$QVAL = "'$gid','$vendor','$bot','0','$parent','$depth','$hidden','1','1','$xname',0";
		getDbInsert($table[$m.'keyword'],$QKEY,$QVAL);
	}
	if ($parent)
	{
		getDbUpdate($table[$m.'keyword'],'is_child=1','uid='.$parent);
	}
	db_query("OPTIMIZE TABLE ".$table[$m.'keyword'],$DB_CONNECT); 

	$content = $chatbot->getKeywordList($vendor,$bot,$depth,$parent);

}else if($act=='get-subMenu'){
   $sub_depth = $depth+1;	
   $content = $chatbot->getKeywordList($vendor,$bot,$sub_depth,$parent);
}else if($act=='delete-keyword'){

	include $g['path_core'].'function/menu.func.php';
	$subQue = getMenuCodeToSql($table[$m.'keyword'],$uid,'uid');
	
	if($subQue)
	{
		$DAT = getDbSelect($table[$m.'keyword'],$subQue,'*');
		while($R=db_fetch_array($DAT))
		{
			getDbDelete($table[$m.'keyword'],'uid='.$R['uid']);
			getDbDelete($table[$m.'keywordInfo'],'kwd_uid='.$R['uid']);
		}
		
		if ($parent)
		{
			if (!getDbRows($table[$m.'keyword'],'parent='.$parent))
			{
				getDbUpdate($table[$m.'keyword'],'is_child=0','uid='.$parent);
			}
		}
		db_query("OPTIMIZE TABLE ".$table[$m.'keyword'],$DB_CONNECT); 
	}
	$message = 'success';

}else if($act=='edit-gid'){
	$i=1;
    foreach ($keyword_member as $val){
        getDbUpdate($table[$m.'keyword'],'gid='.($i++),'uid='.$val); 
    }
    $message = '순서가 변경되었습니다.';
}else if($act=='update-keyword'){
	// keyword 테이블 업데이트 
    getDbUpdate($table[$m.'keyword'],"keyword='$keyword',showMenu='$showMenu'",'uid='.$uid);

	// keywordInfo 테이블 업데이트 
	$is_row = getDbRows($table[$m.'keywordInfo'],'kwd_uid='.$uid.' and vendor='.$vendor.' and bot='.$bot);
	if($is_row){
	   $QVAL ="link1='$link1',link2='$link2',price1='$price1',price2='$price2',summary='$summary',content='$content',img_url='$img_url'";
	   getDbUpdate($table[$m.'keywordInfo'],$QVAL,'kwd_uid='.$uid.' and vendor='.$vendor.' and bot='.$bot);	
	}else{
		$QKEY = "kwd_uid,vendor,bot,title,summary,content,price1,price2,img_url,link1,link2";
		$QVAL = "'$uid','$vendor','$bot','$title','$summary','$content','$price1','$price2','$img_url','$link1','$link2'";
		getDbInsert($table[$m.'keywordInfo'],$QKEY,$QVAL);
	}

	$content = $chatbot->getKeywordList($vendor,$bot,$depth,$parent);

}else if($act=='change-showMenu'){
    $new_showMenu = $showMenu?0:1;
    // keyword 테이블 업데이트 
    getDbUpdate($table[$m.'keyword'],"showMenu='$new_showMenu'",'uid='.$uid);

	$content = $chatbot->getKeywordList($vendor,$bot,$depth,$parent); 
	$message = $showMenu?'숨김처리 되었습니다.':'출력처리 도었습니다.'; 
}

$result['content'] = $content;
$result['message'] = $message;

echo json_encode($result);
exit;
?>
