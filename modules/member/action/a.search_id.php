<?php
if(!defined('__KIMS__')) exit;

$_id = $_POST['id'];
$id = mysqli_real_escape_string(trim($DB_CONNECT, $_id));

$result=array();

$R = getDbData($table['s_mbrid'],"id='".$id."'",'*');
if (!$R['uid']){
   $result['message'] ='200';
   $result['content']= '<span class="input-val">'.$id.'</span><br/>은(는) 가입되지 않은 이메일 계정입니다.';
   if ($g['mobile'] && $_SESSION['pcmode'] != 'Y'){
   	  $result['content'] .='<a class="result-link" href="#" data-toggle="modal" data-target="#modal-join" data-role="getComponent" data-markup="mJoin" data-url="/?mod=join" id="join-link">회원가입하기</a>';
   } 
   else $result['content'] .='<a class="result-link" href="'.RW('mod=join').'" id="join-link">회원가입하기</a>'; 
}else{
   $result['message'] ='100';
   $result['content'] = '<span class="input-val">'.$id.'</span><br/>은(는) 가입된 이메일 계정입니다.';
   if ($g['mobile'] && $_SESSION['pcmode'] != 'Y'){
      $result['content'] .='<a class="result-link" data-role="getLoginModal" data-url="/?mod=login" id="login-link">로그인하기</a>'; 
   }
   else $result['content'] .='<a class="result-link" href="'.RW('mod=login').'" id="login-link">로그인하기</a>'; 
} 
echo json_encode($result,true);
exit;

?>