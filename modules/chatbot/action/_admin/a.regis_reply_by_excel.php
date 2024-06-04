<?php
if(!defined('__KIMS__')) exit;

include_once $g['path_core'].'function/string.func.php';
require_once $g['dir_module'].'/includes/excel_reader2.php'; // 엑셀 리더 클래스 인클루드 
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

$data =new Spreadsheet_Excel_Reader($saveFile,true,"UTF-8");
// print_r($data->sheets[0]);
// exit;
$display =1;
$hidden = 0;
$vendor =1;
$bot =1;

for($y = 2; $y <= count($data->sheets[0]["cells"]); $y++)
 {

    //sheets[0]["cells"][$y][1] : 첫번째 시트 [셀] [열][행] : Y 축 X 축 
    $quesCat = $data->sheets[0]["cells"][$y][1]; // 답변 코드
    $content = $data->sheets[0]["cells"][$y][2];// 답변 

    $is_reply = getDbData($table[$m.'reply'],"quesCat='".$quesCat."'",'uid');

    if($is_reply['uid']){
    	getDbUpdate($table[$m.'reply'],"content='".$content."'",'uid='.$uid);
    }else{
        $QKEY = "display,hidden,quesCat,vendor,bot,content";
        $QVAL = "'$display','$hidden','$quesCat','$vendor','$bot','$content'";
        getDbInsert($table[$m.'reply'],$QKEY,$QVAL);	
    }  
    
 }

getLink('reload','parent.','','');

?>
