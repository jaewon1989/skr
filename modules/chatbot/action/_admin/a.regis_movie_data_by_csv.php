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
$tbl_full = 'hcn_mv_full';
$tbl_search = 'hcn_mv_search';
$tbl_related = 'hcn_mv_related';
$tbl_keyword = 'hcn_mv_keyword';

$tbl_array = array("full","search","related","keyword");

for($i=0;$i<4;$i++){
	getDbDelete('hcn_mv_'.$tbl_array[$i],"aid<>''");
	//db_query('ALTER hcn_mv_'.$tbl_array[0].' AUTO_INCREMENT=1',$DB_CONNECT);
}

$lines = file($_FILES['xlsfile']['tmp_name']);

$i=0;
foreach($lines as $line)
{
    if($i>0){
    	$row = explode(',',$line);   
	    $aid = trim($row[0]);
	    $cat1 = trim($row[1]);
	    $cat2 = trim($row[2]);
	    $cat3 = trim($row[3]);
	    $s_name = trim($row[4]); // 검색 제목
	    $f_name = trim($row[5]); // 프론트 제목
	    $director = trim($row[6]); // 감독 
	    $actors = trim($row[7]); // 배우 
	    $rated = trim($row[8]); // 등급분류 
	    $genre = trim($row[9]); // 장르 
	    $max_run = trim($row[10]); // 최대 상영시간 
	    $r_time = trim($row[11]); // 런닝 타임 
	    $p_year = trim($row[12]); // 제작년도 
	    $grade = trim($row[13]); // 평점
	    $country = trim($row[14]); // 국가
	    $kwd_1 = trim($row[15]); //
	    $kwd_2 = trim($row[16]); //
	    $kwd_3 = trim($row[17]); //
	    $kwd_4 = trim($row[18]); //
	    $kwd_5 = trim($row[19]); //
	    $kwd_6 = trim($row[20]); //
	    $kwd_7 = trim($row[21]); // 
	    $kwd_8 = trim($row[22]); //
	    $kwd_9 = trim($row[23]); //
	    $kwd_10 = trim($row[24]); //
	    $rmv_1 = str_replace('(^^)',',',trim($row[25])); //
	    $rmv_2 = str_replace('(^^)',',',trim($row[26])); //
	    $rmv_3 = str_replace('(^^)',',',trim($row[27])); //
	    $rmv_4 = str_replace('(^^)',',',trim($row[28])); //
	    $rmv_5 = str_replace('(^^)',',',trim($row[29])); //
	    $rmv_6 = str_replace('(^^)',',',trim($row[30])); //
	    $rmv_7 = str_replace('(^^)',',',trim($row[31])); // 
	    $rmv_8 = str_replace('(^^)',',',trim($row[32])); //
	    $rmv_9 = str_replace('(^^)',',',trim($row[33])); //
	    $rmv_10 = str_replace('(^^)',',',trim($row[34])); //
	    $d_regis = $date['totime'];
		    
        // full 테이블에 저장 
        $QKEY ="aid,cat1,cat2,cat3,s_name,f_name,director,actors,rated,genre,max_run,r_time,p_year,grade,country,";
        $QKEY.="kwd_1,kwd_2,kwd_3,kwd_4,kwd_5,kwd_6,kwd_7,kwd_8,kwd_9,kwd_10,rmv_1,rmv_2,rmv_3,rmv_4,rmv_5,rmv_6,rmv_7,rmv_8,rmv_9,rmv_10,d_regis";
        $QVAL ="'$aid','$cat1','$cat2','$cat3','$s_name','$f_name','$director','$actors','$rated','$genre','$max_run','$r_time','$p_year','$grade','$country',";
        $QVAL.="'$kwd_1','$kwd_2','$kwd_3','$kwd_4','$kwd_5','$kwd_6','$kwd_7','$kwd_8','$kwd_9','$kwd_10','$rmv_1','$rmv_2','$rmv_3','$rmv_4','$rmv_5','$rmv_6','$rmv_7','$rmv_8','$rmv_9','$rmv_10','$d_regis'";
        getDbInsert($tbl_full,$QKEY,$QVAL);
        
        // search 테이블에 저장 
       
        $QKEY ="aid,s_name,f_name,director,actors,rated,genre,p_year,grade,country";
        $QVAL ="'$aid','$s_name','$f_name','$director','$actors','$rated','$genre','$p_year','$grade','$country'";
        getDbInsert($tbl_search,$QKEY,$QVAL);

        // related 테이블에 저장 
        $QKEY ="aid,rmv_1,rmv_2,rmv_3,rmv_4,rmv_5,rmv_6,rmv_7,rmv_8,rmv_9,rmv_10";
        $QVAL ="'$aid','$rmv_1','$rmv_2','$rmv_3','$rmv_4','$rmv_5','$rmv_6','$rmv_7','$rmv_8','$rmv_9','$rmv_10'";
        getDbInsert($tbl_related,$QKEY,$QVAL);

        // keyword 테이블에 저장 
        $kwd_1_arr = explode('/',$kwd_1);
        $kwd_2_arr = explode('/',$kwd_2);
        $kwd_3_arr = explode('/',$kwd_3);
        $kwd_4_arr = explode('/',$kwd_4);
        $kwd_5_arr = explode('/',$kwd_5);
        $kwd_6_arr = explode('/',$kwd_6);
        $kwd_7_arr = explode('/',$kwd_7);
        $kwd_8_arr = explode('/',$kwd_8);
        $kwd_9_arr = explode('/',$kwd_9);
        $kwd_10_arr = explode('/',$kwd_10);


        $QKEY ="aid,kwd_1,kwd_2,kwd_3,kwd_4,kwd_5,kwd_6,kwd_7,kwd_8,kwd_9,kwd_10";
        $QVAL ="'$aid','$kwd_1_arr[0]','$kwd_2_arr[0]','$kwd_3_arr[0]','$kwd_4_arr[0]','$kwd_5_arr[0]','$kwd_6_arr[0]',";
        $QVAL .="'$kwd_7_arr[0]','$kwd_8_arr[0]','$kwd_9_arr[0]','$kwd_10_arr[0]'";
        getDbInsert($tbl_keyword,$QKEY,$QVAL);

    }
    
    $i++;

}

getLink('reload','parent.','','');

?>
