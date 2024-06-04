<?php
if(!defined('__KIMS__')) exit;

include_once $g['path_core'].'function/string.func.php';
require_once $g['dir_module'].'/includes/excel_reader.php'; // 엑셀 리더 클래스 인클루드 
require_once $g['dir_module'].'var/var.php';
require_once $g['dir_module'].'var/define.path.php';
$chatbot = new Chatbot();


//선택한 파일 정보 값
$Upfile=$_FILES['xlsfile']; // 선택한 파일
$tmpname= $Upfile['tmp_name']; // 임시파일 
$realname= $Upfile['name']; // 실제 파일
$fileExt	= strtolower(getExt($realname)); // 확장자 얻기


if (is_uploaded_file($tmpname)) // 파일이 업로드되었다 가 참이면.... 
{
	$upfolder = substr($date['today'],0,8); // 년월일을 업로드 폴더 구분기준으로 설정 
	$saveDir	= $g['path_file'].'tmp/xls_uploads/'; // 
	$savePath1	= $saveDir.substr($upfolder,0,4);// 년도 폴더 지정 (없으면 아래 for 문으로  만든다)
	$savePath2	= $savePath1.'/'.substr($upfolder,4,2); // 월 폴더 지정 (없으면  아래 for 문으로 만든다)  
	$savePath3	= $savePath2.'/'.substr($upfolder,6,2); // 일 폴더 지정(없으면 아래 for 문으로 만든다)
	//getLink('','',$saveDir,'');
	// 위 폴더가 없으면 새로 만들기  
	for ($i = 1; $i < 4; $i++)
	{
		if (!is_dir(${'savePath'.$i}))
		{
			mkdir(${'savePath'.$i},0707);//
			@chmod(${'savePath'.$i},0707);
		}
	}
	
	$newname =date("Ymd").'_'.$realname; // 년월일_파일명.확장자 
	$saveFile =$savePath3.'/'.$newname; // 파일이 저장되는 최종 폴더 
	if ($Overwrite == 'true' || !is_file($saveFile))
	{
		move_uploaded_file($tmpname,$saveFile); // 업로드된 임시파일명(tmpname)을 DB 에 저장할 파일(saveFile) 로 복사한다.
		@chmod($saveFile,0707); // 새로 들어왔으니 권한 신규 부여
	}	
	
} // 파일업로드 체크 

$register = $my['uid']?$my['uid']:1;
$vendor = $vendor?$vendor:1;
$language = $language?$language:'KOR';

$lines = file($_FILES['xlsfile']['tmp_name']);
// print_r($lines);
// exit;
$i=1;
foreach($lines as $line)
{
    $row = explode(',',$line);
   
    // 카테고리별로 업로드할 경우 
    if($category){
      $question = trim($row[0]);// 질문  
      $reply = trim($row[1]); // 답변  
    }else{
      $quesCat = trim($row[0]); // 카테고리 
      $question = trim($row[1]);// 질문  
      $reply = trim($row[2]); // 답변  
    }  
  
    $msg_array = $chatbot->getMopAndPattern($question);
    $pattern = $msg_array['pat'];
   
    $QKEY="register,vendor,quesCat,question,pattern,reply,lang";
    $QVAL="'$register','$vendor','$quesCat','$question','$pattern','$reply','$language'";
    getDbInsert($table[$m.'ruleC'],$QKEY,$QVAL);
    $i++;

}

getLink('reload','parent.','','');

?>
