<?php
if(!defined('__KIMS__')) exit;

require_once $g['path_module'].'chatbot/includes/base.class.php';
require_once $g['path_module'].'chatbot/includes/module.class.php';
$chatbot = new Chatbot();

$start_reply = $chatbot->get_time();
$result=array();
$result['error']=false;

$message=$_POST['message']; 
$msg_array = $chatbot->getMopAndPattern($message);
$msg_pat = $msg_array['pat'];
$_reply='';
$RCD = getDbSelect($table['chatbotrule'],'uid>0','pattern,reply');
$NUM = getDbRows($table['chatbotrule'],'uid>0');
while ($R=db_fetch_array($RCD)){
	$pattern = $R['pattern'];
    similar_text($pattern,$msg_pat,$percent);  
    //$percent.'-['.$R['reply'].']';
    if($percent > 90){
       $_SESSION['reply'] = $percent.'-'.$pattern.'-'.$msg_pat.'-'.$R['reply'];
       break;
    }else{
       $_SESSION['reply']=$percent.'-'.$pattern.'-'.$msg_pat.'-'.$R['reply']; 
    }
}
$end_reply = $chatbot->get_time();
$cha_time = $end_reply-$start_reply;
$reply_arr = explode('-',$_SESSION['reply']);
if($reply_arr[0]>90) $Todo = '답변 출력 ';
else $Todo ='다른 답변 찾거나 다른 질문 유도';
$result_html = '
<style>#result-tbl th {width:20%}</style>
<table class="table" id="result-tbl" style="margin-top:30px;">
   <tr>
      <th>항목</th><td colspan="2">내용</td>
   </tr>
   <tr>
     <th>입력 내용 & 패턴 </th><td>'.$message.'</td><td style="width:30%"><span style="color:blue;">'.$reply_arr[2].'</span></td>
   </tr>
   <tr>
     <th>답변 내용 & 답변룰</th><td>'.$reply_arr[3].'</td><td><span style="color:red;">'.$reply_arr[1].'</span></td>
   </tr>
   <tr>
     <th>패턴 유사율(%)</th><td colspan="2">'.$reply_arr[0].'</td>
   </tr>
   <tr>
     <th>측정시간(초)</th><td colspan="2">'.$cha_time.'</td>
   </tr>
   <tr>
     <th>데이타 row </th><td colspan="2">'.number_format($NUM).'</td>
   </tr>
   <tr>
     <th>결과 처리 </th><td colspan="2">'.$Todo.'</td>
   </tr>

</table>   
';


$result['content'] = $result_html;

echo json_encode($result);
exit;
?>
