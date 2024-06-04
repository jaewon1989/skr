<?php
if(!defined('__KIMS__')) exit;
$m ='chatbot';
require_once $g['path_module'].$m.'/includes/base.class.php';
require_once $g['path_module'].$m.'/includes/module.class.php';
require_once $g['path_module'].$m.'/includes/multipattern.class.php';
$chatbot = new Chatbot();

require_once $g['path_module'].$m.'/lib/nlp/autoloader.php';

use \NlpTools\Tokenizers\WhitespaceTokenizer;
use \NlpTools\Similarity\JaccardIndex;
use \NlpTools\Similarity\CosineSimilarity;
use \NlpTools\Similarity\Simhash;

$result=array();
$result['error']=false;

$message=$_POST['message']; 
$msg_array = $chatbot->getMopAndPattern($message);
$msg_pat = $msg_array['pat'];
$rsp = '<h3>입력내용 분석패선 : '.$msg_pat.'</h3>';

$tok = new WhitespaceTokenizer();
$J = new JaccardIndex();
$cos = new CosineSimilarity();
$simhash = new Simhash(16); // 16 bits hash

$RCD = getDbSelect($table[$m.'rule'],'uid>0','pattern,reply,q_uid');
$rsp .='<table class="table" id="result-tbl" style="margin-top:30px;">';
$rsp .='<tr><td style="width:25%;">질문데이타</td><td>질문패턴</td><td>simialr_text</td><td>similarity</td><td style="width:15%">패턴 매칭갯수</td><td style="width:25%">답변</td></tr>';
while ($R=db_fetch_array($RCD)){
 	$pattern = $R['pattern'];
    $Q = getDbData($table[$m.'question'],'uid='.$R['q_uid'],'content');
    $question = $Q['content'];
    similar_text($question,$message,$percent);  
       $search_array = array();
    $pattern_array = explode(',',$pattern);
    foreach ($pattern_array as $keyword) {
         array_push($search_array,$keyword); 
    } 
    $mp = new MultiPattern($search_array);
    $reg_result = $mp->matchAll($message, $match);

 
    $setA = $tok->tokenize($question);
    $setB = $tok->tokenize($message);
    
    $s_val = $simhash->similarity($setA,$setB);

        $rsp .='<tr>';
        $rsp .='<td>'.$question.'</td><td>'.$pattern.'</td><td>'.$percent.'</td><td>'.$s_val.'</td><td>'.$reg_result.'</td><td>'.$R['reply'].'</td>';
        $rsp .='</tr>';   
        //break;
    
  
}
$rsp.='</table>';

$result['content'] = $rsp;

echo json_encode($result);
exit;
?>
