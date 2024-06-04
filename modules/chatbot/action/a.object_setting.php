<?php
if(!defined('__KIMS__')) exit;

checkAdmin(0);

$id			= trim($id);
$name		= trim($name);
$d_regis	= $date['totime'];

if (!$name) getLink('','','오브젝트 이름을 입력해 주세요.','');
if (!$c_table) getLink('','','댓글 저장 테이블을 선택해주세요.','');
if($use_oneline)
{
   if(!$o_table) getLink('','','한줄의견 저장 테이블을 입력해주세요. ','');
}

if ($uid)
{
 	$R = getUidData($table[$m.'object'],$uid);

	$QVAL = "name='$name',p_theme='$p_theme',m_theme='$m_theme',use_oneline='$use_oneline',p_table='$p_table',c_perm_write='$c_perm_write',c_table='$c_table',";
	$QVAL .="c_snsconnect='$c_snsconnect',c_recnum='$c_recnum',c_sort='$c_sort',c_orderby='$c_orderby',c_point='$c_point',c_onelinedel='$c_onelinedel',";
	$QVAL .="o_table='$o_table',o_orderby='$o_orderby',o_point='$o_point',badword='$badword',badword_action='$badword_action',badword_escape='$badword_escape'";

	getDbUpdate($table[$m.'object'],$QVAL,'uid='.$R['uid']);
   
    $msg='수정되었습니다.';
	$_link=$g['s'].'/?r='.$r.'&m=admin&module='.$m.'&front=settings&uid='.$uid;
}
else {

   if (!$id) getLink('','','오브젝트 아이디를 입력해 주세요.','');
	if(getDbRows($table[$m.'object'],"id='".$id."'")) getLink('','','이미 같은 아이디의 오브젝트가 존재합니다.','');

	$QKEY = "id,name,p_theme,m_theme,use_oneline,p_table,c_perm_write,c_table,c_snsconnect,c_recnum,c_sort,c_orderby,c_point,c_onelinedel,o_table,";
	$QKEY .= "o_orderby,o_point,badword,badword_action,badword_escape";
	$QVAL  = "'$id','$name','$p_theme','$m_theme','$use_oneline','$p_table','$c_perm_write','$c_table','$c_snsconnect','$c_recnum','$c_sort','$c_orderby',";
	$QVAL .= "'$c_point','$c_onelinedel','$o_table','$o_orderby','$o_point','$badword','$badword_action','$badword_escape'";

	getDbInsert($table[$m.'object'],$QKEY,$QVAL);

	$LASTUID = getDbCnt($table[$m.'object'],'max(uid)','');
	$msg='신규 오브젝트가 생성되었습니다.';
	$_link=$g['s'].'/?r='.$r.'&m=admin&module='.$m.'&front=main';
}

getLink($_link,'parent.',$msg,'');
?>
