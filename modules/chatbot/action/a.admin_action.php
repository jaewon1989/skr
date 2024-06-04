<?php
if(!defined('__KIMS__')) exit;
include $g['path_module'].$m.'/includes/base.class.php';
include $g['path_module'].$m.'/includes/module.class.php';
$chatbot = new Chatbot();

        

// 승인병경 
if($act=='auth'){
    foreach ($list_members as $uid) {
        getDbUpdate($table[$m.$mod],"auth='".$auth."'",'uid='.$uid);       
    }
}else if($act=='display'){
    foreach ($list_members as $uid) {
        getDbUpdate($table[$m.$mod],"display='".$display."'",'uid='.$uid);       
    }
}else if($act=='type'){
    foreach ($list_members as $uid) {
        getDbUpdate($table[$m.$mod],"type='".$type."'",'uid='.$uid);       
    }
}else if($act=='delete'){

    if($front=='questionC'){
        foreach ($list_members as $uid) {
            getDbDelete($table[$m.'ruleC'],'uid='.$uid);
        }    
    }else{
        foreach ($list_members as $uid) {
            getDbDelete($table[$m.$mod],'uid='.$uid);
        } 
    }
    
}else if($act=='updateRule'){
    $suffix = ($_lang=='ko'||!$_lang)?'':'_'.$_lang;
    $QCD = getDbSelect($table[$m.'question'].$suffix,'hidden=0 and uid=155','*');
    while($Q=db_fetch_array($QCD)){
        $R = getDbData($table[$m.'reply'].$suffix,'uid='.$Q['r_uid'],'uid,type,content');
        // 답변관련 세팅 
        $r_uid = $R['uid'];
        $r_type = $R['type'];
        $reply = addslashes($R['content']);
        $vendor = 1;

        // 질문관련 세팅 
        $q_uid = $Q['uid'];
        $language = $Q['lang'];
        $quesCat = $Q['quesCat'];
        $bot = $Q['bot'];
        $mop_array = $chatbot->getMopAndPattern($Q['content']);
       
        $pattern = $mop_array['pat']; 
        $morpheme = $mop_array['mop'];

        $chatbot->getUpdateRule($vendor,$bot,$r_uid,$q_uid,$pattern,$morpheme,$r_type,$reply);

    }   
}else if($act=='dumpData'){
    $label = array("ko"=>"한국어","en"=>"영어","ja"=>"일본어","zh"=>"중국어");
    header( "Content-type: application/vnd.ms-excel;" ); 
    header( "Content-Disposition: attachment; filename=".$label[$_lang]."_질문_답변_".$date['today'].".xls" ); 
    header( "Content-Description: PHP4 Generated Data" );

    echo '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
    echo '<table border="1">';
    echo '<tr>';
    echo '<td>질문</td>';
    echo '<td>답변</td>';
    echo '<td>형태소분석결과</td>';
    echo '</tr>';

    $suffix = ($_lang=='ko'||!$_lang)?'':'_'.$_lang;
    $QCD = getDbSelect($table[$m.'question'].$suffix,'hidden=0 and vendor=1','*'); 
    while($Q=db_fetch_array($QCD)){
       $R = getDbData($table[$m.'reply'].$suffix,'uid='.$Q['r_uid'],'uid,type,content');
       echo '<tr>';
       echo '<td>'.$Q['content'].'</td>';
       echo '<td>'.$R['content'].'</td>';
       echo '<td>'.$Q['morpheme'].'</td>';
       echo '</tr>';
    }
    echo '</table>'; 
    exit;
}else if($act=='download-replyXlsForm'){
    header( "Content-type: application/vnd.ms-excel;" ); 
    header( "Content-Disposition: attachment; filename=답변xls 등록양식.xls"); 
    header( "Content-Description: PHP4 Generated Data" );

    echo '
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <table border="1">
        <tr>
            <td>질문 카테고리 코드(예:/1/5/10) </td>
            <td width="500">답변 내용</td>
        </tr>';
    for($i=0;$i<20;$i++){
        echo'
         <tr>
             <td></td>
             <td></td>
         </tr>'; 
    }    
    echo '
    </table>';
   exit;
}

if($_lang){
    getLink($g['s'].'/?r='.$r.'&m=admin&module='.$m.'&front='.$front.'&_lang='.$_lang,'parent.','','');
}
else getLink($g['s'].'/?r='.$r.'&m=admin&module='.$m.'&front='.$front,'parent.','','');

?>
