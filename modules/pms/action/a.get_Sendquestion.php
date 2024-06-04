<?php
if(!defined('__KIMS__')) exit;
include_once $g['path_module'].$m.'/_main.php';
include_once $g['path_module'].$m.'/var/var.php';
include_once $g['path_core'].'function/email.func.php'; 

// 레이아웃 추출 
if ($g['mobile']&&$_SESSION['pcmode']!='Y') $layout=$d['shop']['layout_m'];
else $layout=$d['shop']['layout'];
$layout_arr=explode('/',$layout);
$layout_folder=$layout_arr[0];
include_once $g['path_layout'].$layout_folder.'/_var/_var.home.php'; // 레이아웃 설정값 

$R=getUidData($table[$m.'product'],$uid);
$email_list=$d['layout']['helpdesk_qna-email'];
$email_array=explode(',',$email_list);
$to_name='관리자';
$title=$R['name'].' 에 대해 문의드립니다.';
foreach ($email_array as $to_email) {
      getSendMail($to_email.'|'.$to_name, $from_email.'|'.$from_name,$title, $from_content, 'HTML');
}

$result=array();
$result['error']=false;
$result['email_list']=$email_list;
if($email_list) $result['msg']='문의가 접수되었습니다';
else $result['msg']='관리자에게 문의해주시기 바랍니다.';

echo json_encode($result,true);
exit;
?>