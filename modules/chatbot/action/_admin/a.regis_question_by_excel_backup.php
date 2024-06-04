<?php
if(!defined('__KIMS__')) exit;

include_once $g['path_core'].'function/string.func.php';
require_once $g['dir_module'].'/includes/excel_reader.php'; // 엑셀 리더 클래스 인클루드 

if (isset($_SERVER['argv'])) {
    $options = $_SERVER['argv'];
    array_shift($options);
} else {
    $options = array();
}

// 품사기호를 한글로 
function get_PM_to_Han($PM){
   $pm_to_han=array(
      "NNG"=>'일반명사',
      "NNP"=>'고유명사',
      "NNB"=>'의존명사',
      "NNBC"=>'단위를 나타내는 명사',
      "NR"=>'수사',
      "NP"=>'대명사',
      "VV"=>'동사',
      "VA"=>'형용사',
      "VX"=>'보조용언',
      "VCP"=>'긍정지정사',
      "VCN"=>'부정지정사',
      "MM"=>'관형사',
      "MAG"=>'일반 부사',
      "MAJ"=>'접속 부사',
      "IC"=>'감탄사',
      "JKS"=>'주격 조사',
      "JKC"=>'보격 조사',
      "JKG"=>'관형격 조사',
      "JKO"=>'목적격 조사', 
      "JKB"=>'부사격 조사',
      "JKV"=>'호격 조사',
      "JKQ"=>'인용격 조사',
      "JX"=>'보조사',
      "JC"=>'접속 조사',
      "EP"=>'선어말 어미',
      "EF"=>'종결 어미',
      "EC"=>'연결 어미',
      "ETN"=>'명사형 전성 어미',
      "ETM"=>'관형형 전성 어미', 
      "XPN"=>'체언 접두사',
      "XSN"=>'명사 파생 접미사',
      "XSV"=>'동사 파생 접미사',
      "XSA"=>'형용사 파생 접미사',
      "XR"=>'어근',
      "SF"=>'마침표, 물음표, 느낌표',
      "SE"=>'따옴표',
      "SSO"=>'여는 괄호 (, [',
      "SSC"=>'닫는 괄호 ), ]',
      "SC"=>'구분자 , · / :',
      "SL"=>'외국어',
      "SH"=>'한자',
      "SN"=>'숫자',
      "SY"=>'줄임표, 따옴표',
      "UNKNOWN"=>'잘못된 단어',
    ); 
    $PM_arr=explode('+',$PM);
    $pm_name='';
    for($i=0;$i<count($PM_arr);$i++) {
        //$pm_name .= $pm_to_han[$PM_arr[$i]].'+'; 
        $pm_name .=$PN_arr[$i].'+';
    }
          
    return rtrim($pm_name,'+');
}

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


$display =1;
$hidden = 0;
$use_default=1;
$vendor=1;
$language ='KOR';
$r_type ='A';
print_r($data->sheets[0]);
exit;
for($y = 2; $y <= count($data->sheets[0]["cells"]); $y++)
 {

    //sheets[0]["cells"][$y][1] : 첫번째 시트 [셀] [열][행] : Y 축 X 축 
    $quesCat = $data->sheets[0]["cells"][$y][1]; // 서비스
    $r_uid = $data->sheets[0]["cells"][$y][2];// 코드 
    $content = $data->sheets[0]["cells"][$y][3]; // collection 

    $sentence = $content;
    $t = mecab_new($options);

    $mecab = mecab_sparse_tonode($t, $sentence);
    $mop='';
    $pat_data=''; // 패턴 데이타 
    while($m) {
	    //writeln(mecab_node_surface($m)); // 형태소 추출
	    //writeln(mecab_node_length($m)); // 형태소 길이
	    $feature = mecab_node_feature($mecab);
	    $feature_arr = explode(',',$feature);
	    $PS = $feature_arr[0]; // 품사 
	    if($PS!='BOS/EOS'){
	    	$mop_mark = get_PM_to_Han($PS);
	    	$mop_data = mecab_node_surface($mecab);
	        $mop .= $mop_mark.':'.$mop_data; // 형태소 
	        if($mop_mark=='NNG') $pat_data .=$mop_data.',';
	    }
	    $mecab = mecab_node_next($mecab);
	}
	$morpheme = $mop;
	$pattern = rtrim($pat_data,',');
  
    $QKEY = "display,hidden,use_default,vendor,r_uid,r_type,quesCat,pattern,lang,content,morpheme";
    $QVAL = "'$display','$hidden','$use_default','$vendor','$r_uid','$r_type','$quesCat','$pattern','$language','$content','$morpheme'";
    getDbInsert($table[$m.'question'],$QKEY,$QVAL);
 }

getLink('','parent.parent.','','');

?>
