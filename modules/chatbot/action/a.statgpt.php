<?
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값
include_once $g['dir_module'].'var/define.path.php'; // class, 모듈, 레이아웃 패스 세팅
$chatbot = new Chatbot();

$result=array();
$result['error']=false;

$linkType = trim($_POST['linkType']);
$vendor = trim($_POST['vendor']);
$botuid = trim($_POST['botuid']);
$mod = trim($_POST['mod']); // disable예정
$page = trim($_POST['page']); // disable예정
$d_start = trim($_POST['d_start']);
$d_end = trim($_POST['d_end']);
$prompt = trim($_POST['prompt']);
$extType = trim($_POST['exttype']); // disable예정
$csrfToken = $_SERVER['HTTP_X_CSRFTOKEN']; // 클라이언트에서 전달된 CSRF 토큰 값
$storedToken = $_SESSION['csrf_token']; // csrf

function set_del_file_unlink($file_path){
    if ($file_path != '' && file_exists($file_path)) {
        unlink($file_path); // 파일 삭제
        return true;
    } else {
        return false;
    }
}

function get_download_cnt($tbl, $mbruid, $botuid){
    global $DB_CONNECT;

    $query = "
        SELECT COUNT(*) as cnt
        FROM `".$tbl."`
        WHERE `caption` = 'statgpt'
            AND STR_TO_DATE(`d_regis`, '%Y%m%d%H%i%s') 
                BETWEEN STR_TO_DATE(CONCAT(DATE_FORMAT(NOW(), '%Y%m%d'), '000000'), '%Y%m%d%H%i%s') 
                AND STR_TO_DATE(CONCAT(DATE_FORMAT(NOW(), '%Y%m%d'), '235959'), '%Y%m%d%H%i%s')
            AND `mbruid` = '".$mbruid."'
            AND `bot` = '".$botuid."'
    ";
    $result = 0;
    //$result["query"] = $query;
    $RCD = db_query($query, $DB_CONNECT);

    while($R = db_fetch_assoc($RCD)){
        $result = $R["cnt"];
    }
    
    return $result;
}

function get_del_file_list($tbl){
    // query : 7일 이상 지났을 경우 삭제
    $where = " STR_TO_DATE(`d_regis`, '%Y%m%d%H%i%s') <= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 7 DAY), '%Y-%m-%d 23:59:59') AND `caption` = 'statgpt' limit 100 ";
    $sqldata = " CONCAT('".$_SERVER['DOCUMENT_ROOT']."', url, folder, '/', tmpname) AS url ";
    $RCD = getDbSelect($tbl,$where,$sqldata);
    $result = array();
    while($R = db_fetch_assoc($RCD)){
        $R['unlink'] = set_del_file_unlink($R['url']);
        $result[] = $R;
    }

    return $result;
}

// CSRF 토큰 값 비교
if ($csrfToken !== $storedToken) {
    // CSRF 토큰이 일치하지 않는 경우 처리
    // 예: 오류 응답 반환 또는 요청 거부
    http_response_code(403); // 403 Forbidden 오류 반환
    exit;
}

if($linkType == "conversation"){
    $_WHERE = "A.vendor=".$vendor." and A.roomToken <> '' ";

    if($d_start) $_WHERE .= ' and A.d_regis > '.str_replace('-','',$d_start).($last_s_hm?$last_s_hm.'00':'000000');
    if($d_end) $_WHERE .= ' and A.d_regis < '.str_replace('-','',$d_end).($last_e_hm?$last_e_hm.'59':'235959');
    if($botuid) $_WHERE .=' and A.bot='.$botuid;

    $query = "Select A.uid, A.userName, A.userUid, left(A.d_regis, 8) as d_regis, A.roomToken, count(*) as nCntChat From ".$table[$m.'chatLog']." A ";
    $query .= "Where ".$_WHERE." ";
    $query .= "group by A.roomToken, left(A.d_regis, 8)";
    $query .= "Order by A.uid DESC ";
    $query .= "Limit 0, 100";
    $result["query"] = $query;
    $RCD = db_query($query, $DB_CONNECT);

    while($R = db_fetch_assoc($RCD)){
        $data = $R;
        $data["userPic"] = $chatbot->getUserAvatar($R['userUid'],'src');
        $data["userNameInChat"] = $chatbot->getUserName($R['userUid']);
        $data["d_regis"] = getDateFormat($R['d_regis'],'Y-m-d');

        $_where = "vendor=".$vendor." and bot=".$bot." and roomToken='".$R['roomToken']."' and left(d_regis, 8) = '".$R['d_regis']."' and is_unknown=1";
        $data["nCntUnknown"] = getDbRows($table[$m.'chatLog'], $_where);

        $result["conversation"][] = $data;
    }

}

if($linkType == "learning"){
    // 답변못한 질문 리스트 
    $data = array();
    $data['vendor'] = $vendor;
    $data['bot'] = $botuid?$botuid:'';
    $data['recnum'] = 100;
    $getUnKnownData = $chatbot->getUnKnownData($data);
    $result["learning"]["unKnownList"] = $getUnKnownData[1];
    $result["learning"]["unKnownPageBtn"] = $getUnKnownData[2];

    // 많이한 질문 리스트 
    $data['mod'] = 'question';
    $result["learning"]["questionData"] = $chatbot->getFavorateQuestionData($data);

    // 많이한 단어 리스트 
    $data['mod'] = 'word';
    $result["learning"]["wordData"] = $chatbot->getFavorateQuestionData($data);
}

if($linkType == "prompt_morph"){
    $cnt = get_download_cnt($table[$m.'upload'], $my["uid"], $botuid);
    if($cnt >= 10){
        $result['error']=true;
        $result['message']= "일일 10회 다운로드 제한 횟수를 초과하였습니다. "; // $cnt
    }else{
        $result['file_cnt'] = $cnt;

        $stat_keyword_arr = array();
        $stat_keyword_arr[] = array( "name" => "사용자현황", "value" => "user" );
        $stat_keyword_arr[] = array( "name" => "통계관리", "value" => "statis" );
        $stat_keyword_arr[] = array( "name" => "대화현황", "value" => "convstat" );
        $stat_keyword_arr[] = array( "name" => "대화로그", "value" => "conversation" );
        $stat_keyword_arr[] = array( "name" => "대화분석", "value" => "convanalysis" );
        $stat_keyword_arr[] = array( "name" => "대화흐름분석", "value" => "convflow" );
        $stat_keyword_arr[] = array( "name" => "군집분석", "value" => "gathering" );
        $stat_keyword_arr[] = array( "name" => "학습", "value" => "learning" );

        $document_keyword_arr = array();
        $document_keyword_arr[] = array( "name" => "pdf", "value" => "pdf" );
        $document_keyword_arr[] = array( "name" => "피디에프", "value" => "pdf" );
        $document_keyword_arr[] = array( "name" => "ppt", "value" => "ppt" );
        $document_keyword_arr[] = array( "name" => "피피티", "value" => "ppt" );
        $document_keyword_arr[] = array( "name" => "파워포인트", "value" => "ppt" );
        $document_keyword_arr[] = array( "name" => "doc", "value" => "doc" );
        $document_keyword_arr[] = array( "name" => "워드", "value" => "doc" );
        $document_keyword_arr[] = array( "name" => "word", "value" => "doc" );
        $document_keyword_arr[] = array( "name" => "문서", "value" => "doc" );

        $date_keyword_arr = array();
        $date_keyword_arr[] = array( "name" => "어제", "value" => "1" );
        $date_keyword_arr[] = array( "name" => "오늘", "value" => "2" );
        $date_keyword_arr[] = array( "name" => "일주", "value" => "3" );
        $date_keyword_arr[] = array( "name" => "한달", "value" => "4" );
        $date_keyword_arr[] = array( "name" => "당월", "value" => "5" );
        $date_keyword_arr[] = array( "name" => "전체", "value" => "6" );

        $tail_keyword_arr = array('뽑아', '만들어', '생성', '레포트', '출력', '해줘', '보여줘');

        // 날짜와 9장 추출 정규식 패턴
        //$pattern = "/(\d{2,4})[년\s]*([01]?\d)[월\s]*([0123]?\d)[일\s]*부터\s*([01]?\d)[월\s]*([0123]?\d)[일\s]*까지\s/";
        //$pattern = "/(\d{2,4}(-|년)\s?\d{1,2}(-|월)\s?\d{1,2}(일)?)(부터|~|-)\s?(\d{2,4}(-|년)\s?\d{1,2}(-|월)\s?\d{1,2}(일)?)/u";
        $pattern = "/(\d{2,4}(-|년)\s?\d{1,2}(-|월)\s?\d{1,2}(일)?)(부터|~|-)\s?(\d{2,4}(-|년)\s?\d{1,2}(-|월)\s?\d{1,2}(일)?)/u";
        // date
        $processed_prompt = str_replace(' ', '', $prompt);
        $result["prompt"]["date_match"] = [];
        if (preg_match_all($pattern, $processed_prompt, $matches, PREG_SET_ORDER)) {
            $result["prompt"]["date_match"] = $matches;
            foreach ($matches as $match) {
                $start_date = preg_replace('/[^0-9년월일]/u', '', $match[1]);
                $end_date = preg_replace('/[^0-9년월일]/u', '', $match[6]);
                //$start_date = date('Y-m-d', strtotime($start_date));
                //$end_date = date('Y-m-d', strtotime($end_date));
                //echo "$start_date 부터 $end_date" . PHP_EOL;
                $result["prompt"]["start_date"] = date('Y-m-d', strtotime($start_date));
                $result["prompt"]["end_date"] = date('Y-m-d', strtotime($end_date));
                $process_flag++;
            }
        }

        // date
        $result["prompt"]["date_match_cnt"] = count($result["prompt"]["date_match"]);
        if(count($result["prompt"]["date_match"]) == 0){
            $pattern = '/(\d{2})년?\s?(\d{1,2})월부터?\s?(\d{1,2})월까지/';
            //$question = "Ppt로 9장으로 만들어주고 23년 3월부터 4월까지로 뽑아줘";

            if (preg_match($pattern, $processed_prompt, $matches)) {
                $result["prompt"]["date_match"] = $matches;
                $year = $matches[1];
                $start_month = $matches[2];
                $end_month = $matches[3];

                // 현재 연도 가져오기
                $current_year = date('Y');

                // 연도 2자리를 4자리로 변환
                if ($year < 100) {
                    $year = $current_year - $current_year % 100 + $year;
                }

                $start_date = $year . '-' . sprintf("%02d", $start_month) . '-01';
                $end_date = $year . '-' . sprintf("%02d", $end_month + 1) . '-01';
                $end_date = date('Y-m-d', strtotime($end_date . '-1 day'));

                $result["prompt"]["start_date"] = $start_date;
                $result["prompt"]["end_date"] = $end_date;
                $process_flag++;
            }
        }

        // date
        $pattern = '/(\d{1,2})월(\d{1,2})일부터(\d{1,2})월(\d{1,2})일까지/';
        
        $result["prompt"]["date_match_cnt"] = count($result["prompt"]["date_match"]);
        if(count($result["prompt"]["date_match"]) == 0){
            if (preg_match($pattern, $processed_prompt, $matches)) {
                $result["prompt"]["date_match"] = $matches;
                $startMonth = $matches[1];
                $startDay = $matches[2];
                $endMonth = $matches[3];
                $endDay = $matches[4];

                $start_date = date("Y-m-d", strtotime(Date('Y')."-$startMonth-$startDay"));
                $end_date = date("Y-m-d", strtotime(Date('Y')."-$endMonth-$endDay"));

                $result["prompt"]["start_date"] = $start_date;
                $result["prompt"]["end_date"] = $end_date;
                $process_flag++;
            }
        }

        // date
        /*
        preg_match($pattern, $prompt, $matches);
        $result["prompt"]["date_match"] = $matches;
        $process_flag = 0;

        if(count($matches) > 0){
            $start_year = $matches[1] < 100 ? "20".$matches[1] : $matches[1];
            $start_month = str_pad($matches[2], 2, "0", STR_PAD_LEFT);
            $start_day = str_pad($matches[3], 2, "0", STR_PAD_LEFT);
            $result["prompt"]["start_date"] = "$start_year-$start_month-$start_day";

            $end_year = $matches[1] < 100 ? "20".$matches[1] : $matches[1];
            $end_month = str_pad($matches[4], 2, "0", STR_PAD_LEFT);
            $end_day = str_pad($matches[5], 2, "0", STR_PAD_LEFT);
            $result["prompt"]["end_date"] = "$end_year-$end_month-$end_day";
            $process_flag++;
        }
        */

        // date2
        $date_value = '';
        foreach($date_keyword_arr AS $key => $value){
            if(preg_match("/".$value["name"]."/", $processed_prompt)){
                $date_value = $value["value"];
                break;
            }
        }
        if($date_value != ''){
            $result["prompt"]["datetype"] = $date_value;
            $process_flag++;
        }

        // type
        $stat_value = [];
        foreach($stat_keyword_arr AS $key => $value){
            if(preg_match("/".$value["name"]."/", $processed_prompt)){
                $stat_value[] = $value["value"];
            }
        }
        if(count($stat_value) > 0){
            $result["prompt"]["type"] = $stat_value;
            $process_flag++;
        }

        // page
        preg_match("/(\d+장)/u", $processed_prompt, $matches_page);
        if(count($matches_page) > 0){
            $result["prompt"]["page"] = preg_replace("/[^0-9]*/s", "", $matches_page[1]);
            $process_flag++;
        }

        // document
        $document_value = '';
        foreach($document_keyword_arr AS $key => $value){
            if(preg_match("/".$value["name"]."/s", $processed_prompt)){
                $document_value = $value["value"];
                break;
            }
        }
        if($document_value != ''){
            $result["prompt"]["docu"] = $document_value;
            $process_flag++;
        }

        // filename
        preg_match_all('/(.+?)[은|는](.+?)[으로|로]/u', $processed_prompt, $matches_filename, PREG_SET_ORDER);

        foreach ($matches_filename as $match) {
            $subject = $match[1];
            $target = $match[2];
            //echo "문장: " . $match[0] . "<br>";
            //echo "명사: " . $subject . ", " . $target . "<br>";
            if($subject == "파일명" || $subject = "파일제목" || $subject == "파일이름" || $subject == "문서명" || $subject == "문서제목" || $subject == "문서이름"){
                $result["prompt"]["filename"] = $target;
            }
        }

        // content only
        $regex = '/^(학습|통계관리)(?:에서|의)\s*(.*?)\s*만/';
        if (preg_match($regex, $processed_prompt, $matches)) {
            $excluded = $matches[1] . '|' . $matches[2];
            $result["prompt"]["only_content"][] = $excluded;
        }

        // content delete
        //$regex = '/(.+?)에서 (.+?)(은|는) 빼(줘|고)/';
        //$regex = '/(.+?)(에|에서)\s*([^은는]+?)(?:은|는)\s*빼(?:줘|고)/u';
        //$regex = '/(.+?)(?:에|에서|에서는)\s*([^은는]+?)(?:은|는)\s*빼(?:줘|고)/u';
        $regex = '/(학습|통계관리)에서(?:는)?(.*?)(?:은|는)\s*빼(?:줘|고)/u';
        if (preg_match($regex, $prompt, $matches)) {
            $excluded = $matches[1] . "|" . $matches[2];
            $result["prompt"]["del_content"][] = $excluded;
        }

        // tail
        $tail_value = '';
        foreach($tail_keyword_arr AS $key => $value){
            if(preg_match("/".$value."/", $processed_prompt)){
                $tail_value = $value;
                break;
            }
        }
        if($tail_value != ''){
            $result["prompt"]["tail"] = $tail_value;
            $process_flag++;
        }

        $result["prompt"]["process_flag"] = $process_flag;
        if($process_flag > 0 && $result["prompt"]["tail"] != ""){
            $result["prompt"]["prompt"] = "네 지금 생성해 드릴께요.";
        }else{
            $result["prompt"]["process_flag"] = 0;
            $result["prompt"]["prompt"] = "생성 조건에 맞지 않는 내용이네요.";
        }
    }
}

if($linkType == "file_upload"){
    $result["data"] = print_r($_FILES, 1);

    // file unlink
    //$result["unlink"] = get_del_file_list($table[$m.'upload']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
        $maxFileSize = 2 * 1024 * 1024; // 2MB

        $saveDir = '/files/docData/';
        // 업로드 디렉토리 없는 경우 추가 
        if(!is_dir($_SERVER['DOCUMENT_ROOT'] . $saveDir)){
            mkdir($_SERVER['DOCUMENT_ROOT'] . $saveDir,0707);
            @chmod($_SERVER['DOCUMENT_ROOT'] . $saveDir,0707);
        }
        $saveDir = '/files/docData/'.$my['uid'].'/';
        if(!is_dir($_SERVER['DOCUMENT_ROOT'] . $saveDir)){
            mkdir($_SERVER['DOCUMENT_ROOT'] . $saveDir,0707);
            @chmod($_SERVER['DOCUMENT_ROOT'] . $saveDir,0707);
        }
        $saveDir = '/files/docData/'.$my['uid'].'/'.$botuid.'/';
        if(!is_dir($_SERVER['DOCUMENT_ROOT'] . $saveDir)){
            mkdir($_SERVER['DOCUMENT_ROOT'] . $saveDir,0707);
            @chmod($_SERVER['DOCUMENT_ROOT'] . $saveDir,0707);
        }
        $savePath1	= substr($date['today'],0,4);
        $savePath2	= $savePath1.'/'.substr($date['today'],4,2);
        $savePath3	= $savePath2.'/'.substr($date['today'],6,2);
        $folder		= $savePath3; //saveDir.substr($date['today'],0,4).'/'.substr($date['today'],4,2).'/'.substr($date['today'],6,2);
        // 업로드 디렉토리 없는 경우 추가 
        for ($i = 1; $i < 4; $i++){
            if (!is_dir($_SERVER['DOCUMENT_ROOT'] . $saveDir . ${'savePath'.$i})){
                mkdir($_SERVER['DOCUMENT_ROOT'] . $saveDir . ${'savePath'.$i},0707);
                @chmod($_SERVER['DOCUMENT_ROOT'] . $saveDir . ${'savePath'.$i},0707);
            }
        }

        if($_FILES['file']['size'] > $maxFileSize){
            $result['error']=true;
            $result['message']= "File size is too large. / ".$_FILES['file']['size'];
        }else{
            $file = $_FILES['file'];
            $fileData = file_get_contents($file['tmp_name']);
            if (!$_SESSION['upsescode']) $_SESSION['upsescode'] = str_replace('.','',$g['time_start']);
            $sescode = $_SESSION['upsescode'];
            $mingid = getDbCnt($table[$m.'upload'],'min(gid)','');
            $gid = $mingid ? $mingid - 1 :  100000000;
            $fileName = strtolower($file['name']);
            $fileExt = getExt($fileName);
            $fileExt = $fileExt == 'jpeg' ? 'jpg' : $fileExt;
            $type = getFileType($fileExt);
            $tmpname = md5($fileName).substr($date['totime'],8,14);
            $tmpname = $type == 2||$type == 6 ? $tmpname.'.'.$fileExt : $tmpname;
            $hidden = $type == 2||$type == 6 ? 1 : 0;
            $fserver = 0;

            // 파일 저장 경로와 파일명 설정
            $filepath = $_SERVER['DOCUMENT_ROOT'] . $saveDir . $folder . '/' . $tmpname; // $fileName
            $urlpath = $g['url_root'] . $saveDir . $folder . '/' . $tmpname; // $fileName

            // 파일 저장
            if (!is_file($saveFile)) {
                if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                    $error = error_get_last();
                    $result['error']=true;
                    $result['message']= "File upload failed. : ".$error['message'];
                }else{
                    $QDATA = array(
                        "gid" => $gid,
                        "hidden" => $hidden,
                        "tmpcode" => $sescode,
                        "vendor" => $vendor,
                        "bot" => $botuid,
                        "mbruid" => $my['uid'],
                        "type" => $type,
                        "ext" => $fileExt,
                        "fserver" => $fserver,
                        "url" => $saveDir,
                        "folder" => $folder,
                        "name" => $fileName,
                        "tmpname" => $tmpname,
                        "thumbname" => '',
                        "size" => $file['size'],
                        "width" => 0,
                        "height" => 0,
                        "caption" => 'statgpt',
                        "down" => 0,
                        "d_regis" => $date['totime'],
                        "d_update" => date('YmdHis', strtotime('+7 days', strtotime($date['totime'])))
                    );

                    foreach($QDATA AS $key => $value){
                        $QKEYZIP[] = $key;
                        $QVALZIP[] = "'".$value."'";
                    }
                    $QKEY = implode(",", $QKEYZIP);
                    $QVAL = implode(",", $QVALZIP);

                    getDbInsert($table[$m.'upload'],$QKEY,$QVAL);
                    $last_uid = getDbCnt($table[$m.'upload'],'max(uid)','');

                    if ($gid == 100000000) db_query("OPTIMIZE TABLE ".$table[$m.'upload'],$DB_CONNECT); 

                    // 파일 저장 결과 반환
                    $result['message'] = "File saved successfully!";
                    $result['path'] = $urlpath;
                    $result['realpath'] = $filepath;
                    $result['urlpath'] = $saveDir . $folder;
                    $result['last_uid'] = $last_uid;
                }
            }else{
                $error = error_get_last();
                $result['error']=true;
                $result['message']= "File already exists. : ".$error['message'];
            }
        }
    } else {
        $result['error']=true;
        $result['message']= "Invalid request! / ".$_FILES['file'];
    }
}

if($linkType == "get_download_filelist"){
    global $g, $table;

    // bottalks에서는 mbruid로 디렉토리 설정
    $_R = getDbData('rb_chatbot_bot', "uid='".$botuid."'", 'mbruid');

    $query = "
        (
            SELECT *
            FROM (
                SELECT *
                FROM `rb_chatbot_upload`
                WHERE STR_TO_DATE(`d_regis`, '%Y%m%d%H%i%s') <= NOW() 
                    AND `caption` = 'statgpt'
                    AND `vendor` = '".$vendor."' 
                    AND `bot` = '".$botuid."' 
                    AND `mbruid` = '".$_R['mbruid']."'
                    AND `type` = '6'
                ORDER BY `gid` ASC
                LIMIT 1
            ) AS result UNION
                SELECT *
                FROM `rb_chatbot_upload`
                WHERE STR_TO_DATE(`d_regis`, '%Y%m%d%H%i%s') >= DATE_SUB(NOW(), INTERVAL 1 DAY) 
                    AND `caption` = 'statgpt'
                    AND `vendor` = '".$vendor."' 
                    AND `bot` = '".$botuid."' 
                    AND `mbruid` = '".$_R['mbruid']."'
                    AND `type` = '6'
        )
        ORDER BY `gid` ASC
    ";
    $result["query"] = $query;
    $RCD = db_query($query, $DB_CONNECT);

    while($R = db_fetch_assoc($RCD)){
        $data[] = $R;
    }
    $result["filelist"] = $data;
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
exit;

?>