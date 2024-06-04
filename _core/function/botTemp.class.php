<?php
// bot 템플릿 컨트롤(복사/삭제)
/*
   추가 : apiList, apiReq, apiReqParam,botSettings, dialogResApiOutput,
          dialogResApiParam
*/

class botTemp{
    public $vendor;
    public $delBotTable;
    public $allBotTable;
    public $module;
    public $dbTargetMod;
    public $dbTargetServer;
    public $sysDB;
    private $sys_conn;
    private $sys_host;
    private $sys_user;
    private $sys_pass;
    private $sys_dbName;
    private $sys_port;
    private $sys_fatal;

    public function __construct($data){
        global $table;

        $m = 'chatbot';

        // table 세팅
        $this->tbl_bot = $table[$m.'bot'];
        $this->tbl_dialog = $table[$m.'dialog'];
        $this->tbl_node = $table[$m.'dialogNode'];
        $this->tbl_resGroup = $table[$m.'dialogResGroup'];
        $this->tbl_resItem = $table[$m.'dialogResItem'];
        $this->tbl_resItemOC = $table[$m.'dialogResItemOC'];
        $this->tbl_intent = $table[$m.'intent'];
        $this->tbl_intentEx = $table[$m.'intentEx'];
        $this->tbl_entity = $table[$m.'entity'];
        $this->tbl_entityVal = $table[$m.'entityVal'];
        $this->tbl_entityData = $table[$m.'entityData'];
        $this->tbl_botSettings = $table[$m.'botSettings'];
        $this->tbl_channelSettings = $table[$m.'channelSettings'];
        $this->tbl_moniteringFA = $table[$m.'moniteringFA'];
        $this->tbl_tempData = $table[$m.'tempData'];

        // api : legacy 페이지에서 등록한 api
        $this->tbl_apiList = $table[$m.'apiList'];
        $this->tbl_apiReq = $table[$m.'apiReq'];
        $this->tbl_apiReqParam = $table[$m.'apiReqParam'];
        // legacy > response > 사용되는 파라미터
        $this->tbl_resApiOutput = $table[$m.'dialogResApiOutput'];
        $this->tbl_resApiParam = $table[$m.'dialogResApiParam'];
        $this->tbl_keywordInfo = $table[$m.'keywordInfo'];
        $this->tbl_upload = $table[$m.'upload'];

        // 삭제시 사용 : 관계형/계층 관계 테이블 때문에 별도로 분리
        $this->delBotTable = array(
            "apiList"=>"bot",
            // "apiReq"=>"api",
            // "apiReqParam"=>"req",
            "botChatLog"=>"bot",
            "botSettings"=>"bot",
            "channelSettings"=>"botid",
            "chatLog"=>"bot",
            "chatStsLog"=>"bot",
            "chatStsRelation"=>"bot",
            "chatWordLog"=>"bot",
            "chatWordRelation"=>"bot",
            "counter"=>"botuid",
            "dcounter"=>"botuid",
            "dialog"=>"bot",
            "dialogNode"=>"bot",
            "dialogResApiOutput"=>"bot", // itemOC
            // "dialogResApiParam"=>"itemOC",
            "dialogResGroup"=>"bot",
            // "dialogResItem"=>"bot",
            // "dialogResItemOC"=>"item", //
            "entity"=>"bot",
            "entityData"=>"bot",
            "entityVal"=>"bot",
            "intent"=>"bot",
            "intentEx"=>"bot",
            "moniteringFA"=>"bot",
            "tempData"=>"bot",
            "token"=>"bot",
            "unknown"=>"bot",
            "upload"=>"bot",
        );

        // 전체 테이블
        $_tmp = array(
            "apiReq"=>"api",
            "apiReqParam"=>"req",
            "dialogResApiParam"=>"itemOC",
            "dialogResItem"=>"bot",
            "dialogResItemOC"=>"item",
            "vendor"=>"uid",
        );

        // bot 관련 전체 테이블
        $this->allBotTable = array_merge($this->delBotTable,$_tmp);

        $this->module='chatbot';

        // sys DB 정보 초기화 
        //$this->sysDb = new sysDB();
        $this->sys_conn = false;
        $this->sys_host = '10.10.0.243'; //hostname
        $this->sys_user = 'syscloud'; //username
        $this->sys_pass = 'syscloud5279!!'; //password
        $this->sys_dbName = 'syscloud'; //name of your database
        $this->sys_port = '3306';
        $this->sys_debug = true;

    }

    function changeVendor($data){
        global $table,$DB_CONNECT;

        $vendor = $data['vendor'];

        $m = $this->module;

        $allTable = $this->allBotTable;

        foreach($allTable as $name => $key) {
            $tbl = $table[$m.$name];
            getDbUpdate($tbl,'vendor='.$vendor,'uid>0'); 
        }
    }
   
    function initBotTable(){
        global $table,$DB_CONNECT;

        $m = $this->module;

        $allTable = $this->allBotTable;

        foreach($allTable as $name => $key) {
            $tbl = $table[$m.$name];
            db_query("ALTER TABLE ".$tbl." AUTO_INCREMENT=1",$DB_CONNECT);
        }
    }

    // 레거시 apiList, apiReq, apiReqParam 데이타 삭제
    function delLegacyApi($data){

        $tbl_apiList = $this->tbl_apiList;
        $tbl_apiReq = $this->tbl_apiReq;
        $tbl_apiReqParam = $this->tbl_apiReqParam;

        $bot = $data['bot'];

        $ACD = getDbSelect($tbl_apiList,'bot='.$bot,'uid'); // api 추출
        while($A = db_fetch_array($ACD)){
            $RCD = getDbSelect($tbl_apiReq,'api='.$A['uid'],'uid'); // req 추출
            while($R = db_fetch_array($RCD)){
                $PCD = getDbSelect($tbl_apiReqParam,'req='.$R['uid'],'uid'); // uid 추출
                while($P = db_fetch_array($PCD)){
                    getDbDelete($tbl_apiReqParam,'uid='.$P['uid']);
                }
                getDbDelete($tbl_apiReq,'uid='.$R['uid']);
            }
            getDbDelete($tbl_apiList,'uid='.$A['uid']);
        }
    }

    // 레거시 output 삭제 > dialogResApiOutput, dialogResApiParam
    function delResApiOutput($data){

        $tbl_resApiOutput = $this->tbl_resApiOutput;
        $tbl_resApiParam = $this->tbl_resApiParam;

        $bot = $data['bot'];

        $OCD = getDbSelect($tbl_resApiOutput,'bot='.$bot,'uid,itemOC'); // api 추출
        while($O = db_fetch_array($OCD)){
            $PCD = getDbSelect($tbl_resApiParam,'itemOC='.$O['itemOC'],'uid'); // uid 추출
            while($P = db_fetch_array($PCD)){
                getDbDelete($tbl_resApiParam,'uid='.$P['uid']);
            }
            getDbDelete($tbl_resApiOutput,'uid='.$O['uid']);
        }
    }

    // resGroup 삭제 > resGroup, resItem, resItemOC
    function delResGroup($data){

        $tbl_resGroup = $this->tbl_resGroup;
        $tbl_resItem = $this->tbl_resItem;
        $tbl_resItemOC = $this->tbl_resItemOC;

        $vendor = $data['vendor'];
        $bot = $data['bot'];

        $GCD = getDbSelect($tbl_resGroup,'vendor='.$vendor.' and bot='.$bot,'uid,id'); // resGroup 추출
        while($G = db_fetch_array($GCD)){
            $RCD = getDbSelect($tbl_resItem,"vendor='".$vendor."' and bot='".$bot."' and resGroupId='".$G['id']."'",'uid'); // req 추출
            while($R = db_fetch_array($RCD)){
                $OCD = getDbSelect($tbl_resItemOC,'item='.$R['uid'],'uid'); // uid 추출
                while($O = db_fetch_array($OCD)){
                    getDbDelete($tbl_resItemOC,'uid='.$O['uid']);
                }
                getDbDelete($tbl_resItem,'uid='.$R['uid']);
            }
            getDbDelete($tbl_resGroup,'uid='.$G['uid']);
        }
    }

    // 업로드 테이블 및 폴더 지우기
    function delResUpload($data){
        $tbl_upload = $this->tbl_upload;

        $vendor = $data['vendor'];
        $bot = $data['bot'];

        $RCD = getDbSelect($tbl_upload,'vendor='.$vendor.' and bot='.$bot,'*');
        while($R = db_fetch_array($RCD)){
            unlink('.'.ltrim($R['url'],'.').$R['folder'].'/'.$R['tmpname']);
            getDbDelete($tbl_upload,'uid='.$R['uid']);            
        }

    }

    function deleteBot($data){
        global $table;

        $m = $this->module;

        $tbl_bot = $this->tbl_bot;

        $vendor = $data['vendor'];
        $bot = $data['bot'];

        $B = getDbData($table[$m.'bot'],'uid='.$bot,'id');
        $botid = $B['id'];

        $tbl_arr = $this->delBotTable;
        foreach ($tbl_arr as $name => $key) {
            if($name =='apiList') $this->delLegacyApi($data);
            else if($name =='dialogResApiOutput') $this->delResApiOutput($data);
            else if($name =='dialogResGroup') $this->delResGroup($data);
            else if($name =='upload') $this->delResUpload($data);
            else{
                $tbl = $table[$m.$name];

                // if($name =='dcounter'){
                //     echo $tbl;
                //     echo $key;
                //     echo $bot;
                //     print_r($data);
                //     exit;
                // }
                if($key=='bot') getDbDelete($tbl,'bot='.$bot);
                else if($key =='botuid') getDbDelete($tbl,'botuid='.$bot);
                else if($key =='botid') getDbDelete($tbl,"botid='".$botid."'");
            }
        }

        getDbDelete($tbl_bot,'uid='.$bot);
    }
    // ############################### < 아래 > 복사 프로세스 시작 ###########################

     // ############################ start of system 

    // system DB 연결 
    function sysConnect(){
        if (!$this->sys_conn) {
            $this->sys_conn = mysql_connect($this->sys_host, $this->sys_user, $this->sys_pass); 
            mysql_select_db($this->sys_dbName, $this->sys_conn); 
            mysql_set_charset('utf8',$this->sys_conn);
            
            if (!$this->sys_conn) {
                $this->sys_fatal = true;
                echo 'Connection BDD failed';
                die();
            } 
            else {
                $this->sys_fatal = false;
            }
        }
 
        return $this->sys_conn;
    }

    // system table 컬럼 얻기  
    function getSysCols($tbl){

        $con= $this->sysConnect();
        $cols=array();
        $result = mysql_query("SHOW COLUMNS FROM ".$tbl,$con); 
        while ($r=mysql_fetch_array($result))
        {
           if($r["Field"]!='uid') $cols[]= $r["Field"];     
        }  
        return $cols;   

    }
    function getSysDbSelect($table,$where,$data){
        $sql = 'select '.$data.' from '.$table.($where?' where '.$this->getSqlFilter($where):'');
        $result = $this->sys_db_query($sql);
    
        return $result;
    }

    function getSysDbData($table,$where,$data){
        $row = mysql_fetch_array($this->getSysDbSelect($table,$this->getSqlFilter($where),$data));
       
        return $row;
    }

    function sys_db_query($sql){
        $con = $this->sysConnect();
        mysql_query('set names utf8',$con);
        mysql_query('set sql_mode=\'\'',$con);

        return mysql_query($sql,$con);
    }

    //SQL필터링
    private function getSqlFilter($sql){

        return preg_replace("( union| update| insert| delete| drop|\/\*|\*\/|\\\|\;)",'',$sql);
    }

    // ############################ end of system 
    
    function getColumList($data){
        $cols='';
        foreach ($data as $key => $col) {
            $cols.= $col.','; 
        }
        $result = rtrim($cols,',');
        return $result;
    }   

    // cloud DB 테이블과 필드 비교 출력 함수 
    function getColsReport(){
        global $table;
        
        $m = $this->module;

        $allTable = $this->allBotTable;
        
        $html ='<table style="width:100%">';
        $html.='<tr><td>테이블명</td><td style="width:80%">필드명</td></tr>';
        foreach($allTable as $name => $key) {
            $tbl = $table[$m.$name];
            $sysCols = $this->getSysCols($tbl);
            $cols = $this->getDbCols($tbl);
            $html.='<tr style="border: solid 1px #333;padding:3px 0;">';
            $html.='<td>'.$tbl.'</td>';
            $html.='
            <td style="width:80%">
                <div>'.$this->getColumList($sysCols).'</div>
                <div>'.$this->getColumList($cols).'</div>
            </td>';
            $html.='</tr>';
        }
        $html.='</table>';

        return $html;
    }

    // 테이블 칼럼 얻기
    function getCols($tbl){
        
        if($this->dbTargetMod =='sys') $result = $this->getSysCols($tbl);
        else $result = $this->getDbCols($tbl);

        return $result;
    }

    function getDbCols($tbl){
        global $DB_CONNECT;
        
        $cols = array();

        $result = db_query("SHOW COLUMNS FROM ".$tbl,$DB_CONNECT);
        while ($r=db_fetch_array($result))
        {
            if($r["Field"]!='uid') $cols[]= $r["Field"]; // uid 제외한 모든 컬럼 지정
        }

        return $cols;
    }

    // 특정 테이블에 특정 값 입력  
    function insert($data){
        global $DB_CONNECT;

        $table = $data['table'];
        $cols = $data['cols'];
        $insertData = $data['insertData'];

        $insertSQL = "insert into ".$table." (".implode(", ",$cols).") VALUES (";

         // Value 세팅
        foreach($insertData as $val){
              $insertSQL .= "'".$val."',";
        }
        $insertSQL= rtrim($insertSQL,',').')'; // 마지막 쉼표 제거

        db_query($insertSQL,$DB_CONNECT);

        $last_uid = getDbCnt($table,'max(uid)','');

        return $last_uid;

    }
    
    // 봇 아바타 복사 
    function copyAvatarImg($data){
        global $g;

        $m = $this->module;

        // 타겟
        $targetVendor = $data['targetVendor'];
        $targetBot = $data['targetBot'];
       
        // 소유
        $vendor = $data['vendor'];
        $bot = $data['bot'];

        
        // 이미지 url 복사  
        $avatar_img_url = $this->copyImgUrl($data);
        
        // ############ 이미지 폴더 복사 #####################################################
        // $newF = $g['path_file'].$m.'/'.$vendor.'/'; 

        // if($this->dbTargetServer) $oldF = '/home/users/'.$this->dbTargetServer.'/www/files/'.$m.'/'.$targetVendor.'/';
        // else $g['path_file'].$m.'/'.$targetVendor.'/'.$targetBot.'/';

       
        // if (!is_dir($newF)){
        //     mkdir($newF,0707);//
        //     @chmod($newF,0707);
        // }
        
        // // 폴더 복사 함수 호출 
        // $this->copyDir($oldF,$newF);

        // ############ 이미지 폴더 복사 #####################################################

        return $avatar_img_url;
    }

    // 봇 아이디 문자 추출
    function getBotIdString($lengh=15) {
        return substr(md5(uniqid(mt_rand(), true)), 0, $lengh);
    }

    // bot copy & insert
    /*
        targetBot : 복사 대상 bot uid
        vendor : 소유 vendor uid
        mbruid : 소유 member uid

        bot, resItem, resItemOC 테이블에 각각 o_uid 추가하고 copy 할때 저장해준다.
    */
    function copyBot($data){
        global $date;

        $targetBot = $data['targetBot']; // 복사대상 bot uid
        $B = $this->getDbData($this->tbl_bot,'uid='.$targetBot,'*');
        $data['targetVendor'] = $B['vendor'];

        $vendor = $data['vendor']; // 소유 vendor uid
        $mbruid = $data['mbruid']; // 소유 member uid
        $name = $B['name']; // 챗봇명
        $intro = $B['intro']?$B['intro']:$name; // 챗봇소개

        $botId = $data['botId']?$data['botId']:$this->getBotIdString();
        $mingid = getDbCnt($this->tbl_bot,'min(gid)','');
        $gid = $mingid ? $mingid-1 : 1000000000;

        // 테이블 컬럼 추출
        $cols = $this->getCols($this->tbl_bot);

        $insertData = array();
        // insert value 세팅
        foreach ($cols as $col){
            if($col == 'gid') $insertData[$col] = $gid;
            else if($col=='vendor') $insertData[$col]= $vendor; // 복사하는 vendor
            else if($col == 'id') $insertData[$col] = $botId; // 신규 아이디
            else if($col == 'name') $insertData[$col] = $name;
            else if($col == 'intro') $insertData[$col] = $intro;
            else if($col == 'mbruid') $insertData[$col] = $mbruid;
            else if($col == 'hit') $insertData[$col] = 0;
            else if($col == 'likes') $insertData[$col] = 0;
            else if($col == 'report') $insertData[$col] = 0;
            else if($col == 'point') $insertData[$col] = 0;
            else if($col == 'is_temp') $insertData[$col] = $data['is_temp']? 1:0;
            else if($col == 'd_regis') $insertData[$col] = $date['totime'];
            else if($col =='avatar'){
                $data['img_url'] = $B[$col];
                $insertData[$col] = $this->copyAvatarImg($data);
            } 
            else if($col == 'o_uid') $insertData[$col] = $B['uid'];
            else $insertData[$col] = $B[$col]; // 나머지는 기존 bot 데이타값
        }

        $dd = array();
        $dd['table'] = $this->tbl_bot;
        $dd['cols'] = $cols;
        $dd['insertData'] = $insertData;
        
        if(isset($data['vendor']) && isset($data['mbruid'])){
            $lastBot = $this->insert($dd);
            if($lastBot){
                // 순서 중요 
                $data['bot'] = $lastBot; // 신규  bot uid 추출 
                $data['botid'] = $botId; // 신규 bot id  
                $data['targetBotId'] = $B['id']; // 복사 대상 봇 id  
                $this->VBcopy($data,$this->tbl_apiList); // apiList, apiReq, apiParam
                $lastDialog = $this->copyDialog($data);
                $data['dialog'] = $lastDialog; // 신규 dialog uid 추출 
                $this->copyEntity($data);
                $this->copyIntent($data);
                $this->copyEntityData($data); // entityData 테이블 카피
                $this->copyREC($data); // node, resItem 조건의 인텐트/엔터티 uid 값 변경
                $this->VBcopy($data,$this->tbl_botSettings);
                $this->VBcopy($data,$this->tbl_moniteringFA);
                $this->VBcopy($data,$this->tbl_tempData);
                $this->VbIcopy($data,$this->tbl_channelSettings);
                $this->copyUpload($data); //                 

            }
        }
        return $lastBot;

    }

    // Copy Dialog 
    function copyDialog($data){
        global $date;

        // 테이블 지정
        $tbl = $this->tbl_dialog;

        // 타겟
        $targetBot = $data['targetBot'];
        $targetVendor = $data['targetVendor'];
        $RCD = $this->getDbSelect($tbl,'vendor='.$targetVendor.' and bot='.$targetBot,'*');

        // 소유
        $vendor = $data['vendor'];
        $bot = $data['bot'];

        // 테이블 컬럼 추출
        $cols = $this->getCols($tbl);

        while($R = db_fetch_array($RCD)){
            $insertData = array();
            // insert value 세팅
            foreach ($cols as $col){
                if($col=='vendor') $insertData[$col] = $vendor; // 복사하는 vendor
                else if($col == 'bot') $insertData[$col] = $bot;
                else if($col == 'd_regis') $insertData[$col] = $date['totime'];
                else $insertData[$col] = $R[$col]; // 나머지는 기존 bot 데이타값
            }
            $dd = array();
            $dd['table'] = $tbl;
            $dd['cols'] = $cols;
            $dd['insertData'] = $insertData;
            $lastDialog = $this->insert($dd);
            if($lastDialog){
                $data['dialog'] = $lastDialog; //  신규 dialog
                $data['targetDialog'] = $R['uid'];// 기존 dialog
                $this->VBDcopy($data,$this->tbl_node);
                $this->VBDcopy($data,$this->tbl_resGroup);
                $this->VBDcopy($data,$this->tbl_resItem);
            }
        }

        return $lastDialog;
    }

    // entityData 테이블 카피
    function copyEntityData($data){

        // 테이블 지정
        $tbl = $this->tbl_entityData;
        $tbl_entity = $this->tbl_entity;
        $tbl_entityVal = $this->tbl_entityVal;
        $tbl_intent = $this->tbl_intent;


        // 타겟
        $targetBot = $data['targetBot'];
        $targetVendor = $data['targetVendor'];
        $RCD = $this->getDbSelect($tbl,"vendor='".$targetVendor."' and bot='".$targetBot."'",'*');

        // 소유
        $vendor = $data['vendor'];
        $bot = $data['bot'];

        // 테이블 컬럼 추출
        $cols = $this->getCols($tbl);
        $insertData = array();

        while($R = db_fetch_array($RCD)){
            $NE = getDbData($tbl_entity,'o_uid='.$R['entity'],'uid');
            $NEV = getDbData($tbl_entityVal,'o_uid='.$R['entityVal'],'uid');
            $NI = getDbData($tbl_intent,'o_uid='.$R['intent'],'uid');
            
            // insert value 세팅
            foreach ($cols as $col){
                if($col=='vendor') $insertData[$col] = $vendor; // 복사하는 vendor
                else if($col == 'bot') $insertData[$col] = $bot;
                else if($col == 'entity') $insertData[$col] = $NE['uid']; // New Entity uid 값
                else if($col == 'intent') $insertData[$col] = $NI['uid']; // New Intent uid 값
                else if($col == 'entityVal') $insertData[$col] = $NEV['uid']; // New EntityVal uid 값
                else $insertData[$col] = $R[$col]; // 나머지는 기존 bot 데이타값
            }
            $dd = array();
            $dd['table'] = $tbl;
            $dd['cols'] = $cols;
            $dd['insertData'] = $insertData;
            $this->insert($dd);
        }
    }

    function copyIntent($data){

        // 테이블 지정
        $tbl = $this->tbl_intent;

        // 타겟
        $targetBot = $data['targetBot'];
        $targetVendor = $data['targetVendor'];
        $RCD = $this->getDbSelect($tbl,"type='V' and vendor='".$targetVendor."' and bot='".$targetBot."'",'*');

        // 소유
        $vendor = $data['vendor'];
        $bot = $data['bot'];

        // 테이블 컬럼 추출
        $cols = $this->getCols($tbl);
        $insertData = array();

        while($R = db_fetch_array($RCD)){            
            // insert value 세팅
            foreach ($cols as $col){
                if($col=='vendor') $insertData[$col] = $vendor; // 복사하는 vendor
                else if($col == 'bot') $insertData[$col] = $bot;
                else if($col == 'o_uid') $insertData[$col] = $R['uid']; // original uid 값
                else $insertData[$col] = $R[$col]; // 나머지는 기존 bot 데이타값
            }
            $dd = array();
            $dd['table'] = $tbl;
            $dd['cols'] = $cols;
            $dd['insertData'] = $insertData;
            $lastIntent = $this->insert($dd);
            if($lastIntent){
                $data['intent'] = $lastIntent; //  신규 intent uid
                $data['targetIntent'] = $R['uid'];// 기존 intent uid
                $this->copyIntentEx($data);
            }

        }
    }

    function copyIntentEx($data){

        // 테이블 지정
        $tbl = $this->tbl_intentEx;

        // 타겟
        $targetVendor = $data['targetVendor'];
        $targetBot = $data['targetBot'];
        $targetIntent = $data['targetIntent'];
        $_wh = "type='V' and vendor='".$targetVendor."' and bot='".$targetBot."' and intent='".$targetIntent."'";
        $RCD = $this->getDbSelect($tbl,$_wh,'*');

        // 소유
        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $intent = $data['intent'];

        // 테이블 컬럼 추출
        $cols = $this->getCols($tbl);        
        $insertData = array();

        while($R = db_fetch_array($RCD)){            
            // insert value 세팅
            foreach ($cols as $col){
                if($col=='vendor') $insertData[$col] = $vendor; // 복사하는 vendor
                else if($col == 'bot') $insertData[$col] = $bot;
                else if($col == 'intent') $insertData[$col] = $intent;
                else $insertData[$col] = $R[$col]; // 나머지는 기존 bot 데이타값
            }
            $dd = array();
            $dd['table'] = $tbl;
            $dd['cols'] = $cols;
            $dd['insertData'] = $insertData;
            $this->insert($dd);
        }
    }

    function copyEntity($data){

        // 테이블 지정
        $tbl = $this->tbl_entity;

        // 타겟
        $targetBot = $data['targetBot'];
        $targetVendor = $data['targetVendor'];
        $RCD = $this->getDbSelect($tbl,"type='V' and vendor='".$targetVendor."' and bot='".$targetBot."'",'*');

        // 소유
        $vendor = $data['vendor'];
        $bot = $data['bot'];

        // 테이블 컬럼 추출
        $cols = $this->getCols($tbl);
        $insertData = array();

        while($R = db_fetch_array($RCD)){            
            // insert value 세팅
            foreach ($cols as $col){
                if($col=='vendor') $insertData[$col] = $vendor; // 복사하는 vendor
                else if($col == 'bot') $insertData[$col] = $bot;
                else if($col == 'o_uid') $insertData[$col] = $R['uid']; // original uid 값
                else $insertData[$col] = $R[$col]; // 나머지는 기존 bot 데이타값
            }
            $dd = array();
            $dd['table'] = $tbl;
            $dd['cols'] = $cols;
            $dd['insertData'] = $insertData;
            $lastEntity = $this->insert($dd);
            if($lastEntity){
                $data['entity'] = $lastEntity; //  신규 entity uid
                $data['targetEntity'] = $R['uid'];// 기존 entity  uid
                $this->copyEntityVal($data);
            }

        }
    }

    function copyEntityVal($data){

        // 테이블 지정
        $tbl = $this->tbl_entityVal;

        // 타겟
        $targetVendor = $data['targetVendor'];
        $targetBot = $data['targetBot'];
        $targetEntity = $data['targetEntity'];
        $_wh = "type='V' and vendor='".$targetVendor."' and bot='".$targetBot."' and entity='".$targetEntity."'";
        $RCD = $this->getDbSelect($tbl,$_wh,'*');

        // 소유
        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $entity = $data['entity'];

        // 테이블 컬럼 추출
        $cols = $this->getCols($tbl);
        $insertData = array();

        while($R = db_fetch_array($RCD)){            
            // insert value 세팅
            foreach ($cols as $col){
                if($col=='vendor') $insertData[$col] = $vendor; // 복사하는 vendor
                else if($col == 'bot') $insertData[$col] = $bot;
                else if($col == 'dialog') $insertData[$col] = 0;
                else if($col == 'entity') $insertData[$col] = $entity;
                else if($col == 'o_uid') $insertData[$col] = $R['uid']; // original uid 값
                else $insertData[$col] = $R[$col]; // 나머지는 기존 bot 데이타값
            }
            $dd = array();
            $dd['table'] = $tbl;
            $dd['cols'] = $cols;
            $dd['insertData'] = $insertData;
            $this->insert($dd);
        }
    }

    // intent 구문 변경
    function copyRecIntent($mat){
        $tbl = $this->tbl_intent;
        $vendor = $this->owner_vendor;
        $bot = $this->owner_bot;
        $intentName = $mat[2];

        // $o_uid = $mat[1];
        // $R = getDbData($tbl,'o_uid='.$o_uid,'uid');
        $wh = "vendor='".$vendor."' and bot='".$bot."' and name='".$intentName."'";
        $R = getDbData($tbl,$wh,'uid');

        return '|#|'.$R['uid'].'|'.$intentName.'|';

    }

    // entity 구문 변경
    function copyRecEntity($mat){
        $tbl = $this->tbl_entity;
        $vendor = $this->owner_vendor;
        $bot = $this->owner_bot;
        $entityName = $mat[2];

        $wh = "vendor='".$vendor."' and bot='".$bot."' and name='".$entityName."'";
        $R = getDbData($tbl,$wh,'uid');

        return '|@|'.$R['uid'].'|'.$entityName.'|';
    }

    // node 조건구문 변경
    function copyRecCondition($data){
        $rec = $data['rec'];

        $this->owner_vendor = $data['vendor'];
        $this->owner_bot = $data['bot'];

        // if($data['tbl']==$this->tbl_resItem && $data['rec']!=''){
        //     print_r($data);
        //     exit;
        // }

        $rec1 = preg_replace_callback('/\|\#\|(\d+)\|(([\xEA-\xED][\x80-\xBF]{2}|[a-zA-Z])+)\|/','self::copyRecIntent',$rec);
        $rec2 = preg_replace_callback('/\|\@\|(\d+)\|(([\xEA-\xED][\x80-\xBF]{2}|[a-zA-Z])+)\|/','self::copyRecEntity',$rec1);

        return $rec2;
    }

    // node 조건의 인텐트/엔터티 uid 값 변경
    // 여기서는 무조건 자체 DB 에서 select 한다.
    function copyREC($data){
        $tbl_node = $this->tbl_node;        
        $tbl_resItem = $this->tbl_resItem;

        // 소유
        $vendor = $data['vendor'];
        $bot = $data['bot'];
        
        // tbl_node 
        $RCD = getDbSelect($tbl_node,"vendor='".$vendor."' and bot='".$bot."'",'*');
        while($R = db_fetch_array($RCD)){
            $data['rec'] = $R['recCondition'];
            $rec = $this->copyRecCondition($data);
            getDbUpdate($tbl_node,"recCondition='".$rec."'",'uid='.$R['uid']);
        }

        // tbl_resItem 
        $RCD = getDbSelect($tbl_resItem,"vendor='".$vendor."' and bot='".$bot."'",'*');
        while($R = db_fetch_array($RCD)){
            $data['rec'] = $R['recCondition'];
            $rec = $this->copyRecCondition($data);
            getDbUpdate($tbl_resItem,"recCondition='".$rec."'",'uid='.$R['uid']);
        }
    }
    
    // 이미지 복사 
    private function copyImgUrl($data){
     
        $m = $this->module;

        $img_url = $data['img_url'];
        
        if($img_url!=''){
            $bot = $data['bot'];
            $vendor = $data['vendor'];
            $mbruid = $data['mbruid'];

            $ia = explode('/',$img_url);
            if($ia[1]=='files'){
                //if($ia[8]) $new_url = '/files/'.$m.'/'.$vendor.'/'.$bot.'/'.$ia[5].'/'.$ia[6].'/'.$ia[7].'/'.$ia[8];
                //else $new_url = '/files/'.$m.'/'.$vendor.'/'.$ia[4].'/'.$ia[5].'/'.$ia[6].'/'.$ia[7]; // bot 이 없는 경우  
                
                //aramjo - 이미지 디렉토리 (기존 vendor uid에서 mbr_uid > bot uid 값으로 변경
                if($ia[8]) $new_url = '/files/'.$m.'/'.$mbruid.'/'.$bot.'/'.$ia[5].'/'.$ia[6].'/'.$ia[7].'/'.$ia[8];
                else $new_url = '/files/'.$m.'/'.$mbruid.'/'.$bot.'/'.$ia[4].'/'.$ia[5].'/'.$ia[6].'/'.$ia[7]; // bot 이 없는 경우
                
                //aramjo - 개별 이미지 복사
                $this->copyFile($img_url, $new_url);
            }else{
                // /layouts/...인 경우 
                $new_url = $img_url;
            }
               
        }else{
            $new_url ='';
        }
        

        return $new_url; 

    }
    
    // aramjo 파일 개별 복사 ($oldFile : /files/chatbot/vendor_uid, $newFile : /files/chatbot/mbr_uid/bot_uid)
    private function copyFile($oldFile, $newFile) {
        global $g;
        
        if($this->dbTargetServer) $baseDirectory = '/cloud/'.$this->dbTargetServer.'/www';
        else $baseDirectory = $g['path_file'].$m.'/'.$targetVendor.'/'.$targetBot.'/';
        
        $oldFile = $baseDirectory.str_replace('./files/', '/files/', $oldFile);;
        $newFile = str_replace('/files/','',$newFile);
        $aNewPath = explode('/', $newFile);
        array_pop($aNewPath);
        
        if(file_exists($oldFile)) {
            $newPath = '';
            foreach($aNewPath as $path) {
                if($path != '') {
                    $newPath .=$path.'/';
                    if (!is_dir($g['path_file'].$newPath)){
                        mkdir($g['path_file'].$newPath,0707);//
                        @chmod($g['path_file'].$newPath,0707);
                    }
                }
            }
            @copy($oldFile, $g['path_file'].$newFile);
        }
    }

    // vendor, bot, dialog 값만 변경해서 카피하기
    function VBDcopy($data,$tbl){

        // recConditon 복사할때 필요 
        $this->owner_vendor = $data['vendor'];
        $this->owner_bot = $data['bot'];

        // 타겟
        $targetVendor = $data['targetVendor'];
        $targetBot = $data['targetBot'];
        $targetDialog = $data['targetDialog'];
        $_wh = 'vendor='.$targetVendor.' and bot='.$targetBot.' and dialog='.$targetDialog;
        $RCD = $this->getDbSelect($tbl,$_wh,'*');

        // 소유
        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $dialog = $data['dialog'];

        // 테이블 컬럼 추출
        $cols = $this->getCols($tbl);
        $insertData = array();

        // //if($tbl==$this->tbl_resItem){
        //     print_r($this);
        //     exit;
        // //}

        while($R = db_fetch_array($RCD)){            
            // insert value 세팅
            foreach ($cols as $col){
                if($col=='vendor') $insertData[$col] = $vendor; // 복사하는 vendor
                else if($col == 'bot') $insertData[$col] = $bot;
                else if($col == 'dialog') $insertData[$col] = $dialog;
                else if($col == 'img_url'){
                    $data['img_url'] = $R['img_url'];
                    $insertData[$col] = $this->copyImgUrl($data);  
                } 
                else if($col == 'o_uid') $insertData[$col] = $R['uid'];
                else $insertData[$col] = $R[$col]; // 나머지는 기존 bot 데이타값
            }

            $dd = array();
            $dd['table'] = $tbl;
            $dd['cols'] = $cols;
            $dd['insertData'] = $insertData;
            $lastItem = $this->insert($dd);

            if($tbl == $this->tbl_resItem && $lastItem){
                $data['item'] = $lastItem; //  신규 resItem uid
                $data['targetItem'] = $R['uid'];// 기존 resItem uid
                $this->copyResItemOC($data);                
            }

        }

    }
    
    function copyResItemOC($data){

        // 테이블 지정
        $tbl = $this->tbl_resItemOC;

        // 타겟
        $targetItem = $data['targetItem'];

        // 소유
        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $item = $data['item'];

        $_wh = 'item='.$targetItem;
        $RCD = $this->getDbSelect($tbl,$_wh,'*');

        // 테이블 컬럼 추출
        $cols = $this->getCols($tbl);
        $insertData = array();

        while($R = db_fetch_array($RCD)){
            // insert value 세팅
            foreach ($cols as $col){
                if($col=='vendor') $insertData[$col] = $vendor;
                else if($col=='bot') $insertData[$col] = $bot;
                else if($col=='item') $insertData[$col] = $item;
                else if($col=='varchar_val' && $R['resType']=='img'){
                    $data['img_url'] = $R['varchar_val'];
                    $insertData[$col] = $this->copyImgUrl($data);   
                }else if($col =='varchar_val' && $R['resType']=='api'){
                    $data['api'] = $R['varchar_val'];
                    $A = getDbData($this->tbl_apiList,"o_uid='".$data['api']."'",'uid');
                    $insertData[$col] = $A['uid'];
                }
                else if($col=='o_uid') $insertData[$col] = $R['uid'];
                else $insertData[$col] = $R[$col]; // 나머지는 기존 bot 데이타값
            }


            $dd = array();
            $dd['table'] = $tbl;
            $dd['cols'] = $cols;
            $dd['insertData'] = $insertData;
            $lastItemOC = $this->insert($dd);

            // ResApiOutput
            if($R['resType'] =='api'){
                $data['targetItemOC'] = $R['uid'];
                $data['itemOC'] = $lastItemOC;
                $this->copyResApiOutput($data);    
            }     
        }

    }

    private function copyDir($odir,$ndir) { 
        if(filetype($odir) === 'dir') { 
           clearstatcache(); 

           if($fp = @opendir($odir)) { 
                while(false !== ($ftmp = readdir($fp))){ 
                    if(($ftmp !== ".") && ($ftmp !== "..") && ($ftmp !== "")) { 
                        if(filetype($odir.'/'.$ftmp) === 'dir') { 
                            clearstatcache(); 
   
                            //aramjo - 신규 디렉토리 생성 시 707 권한 설정
                            @mkdir($ndir.'/'.$ftmp, 0707); 
                            @chmod($ndir.'/'.$ftmp,0707);
                            //echo ($ndir.'/'.$ftmp."<br />\n"); 
                            set_time_limit(0); 
                            $this->copyDir($odir.'/'.$ftmp,$ndir.'/'.$ftmp); 
                        }else{ 
                            copy($odir.'/'.$ftmp,$ndir.'/'.$ftmp); 
                        } 
                    } 
                } 
           } 
           if(is_resource($fp)){ 
                 closedir($fp); 
           } 
        }else{ 
            //echo $ndir."<br />\n";      
            //exec("cp -r $odir $ndir");
            @copy($odir,$ndir); 
        } 
    } // end func 

    // upload file 복사하기 
    private function copyUpload($data){
        global $g;

        $m = $this->module;

        $tbl = $this->tbl_upload;

        // 타겟
        $targetVendor = $data['targetVendor'];
        $targetBot = $data['targetBot'];
        $_wh = 'vendor='.$targetVendor.' and bot='.$targetBot;
        $RCD = $this->getDbSelect($tbl,$_wh,'*');

        // 소유
        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $mbruid = $data['mbruid'];

        // 신규 폴더(url 값) 세팅 
        //$new_url = $g['path_file'].$m.'/'.$vendor.'/'; 
        
        //aramjo - 이미지 디렉토리 기존 vendor uid에서 mbruid > bot uid 값으로 변경
        $new_url = '/files/'.$m.'/'.$mbruid.'/'.$bot.'/'; 
      
        // 테이블 컬럼 추출
        $cols = $this->getCols($tbl);
        $insertData = array();

        while($R = db_fetch_array($RCD)){

            $mingid = getDbCnt($tbl,'min(gid)','');
            $gid = $mingid ? $mingid - 1 : 100000000;            
            
            // insert value 세팅
            foreach ($cols as $col){
                if($col=='gid') $insertData[$col] = $gid; 
                else if($col=='vendor') $insertData[$col] = $vendor; // 복사하는 vendor
                else if($col == 'bot') $insertData[$col] = $bot;
                else if($col == 'url') $insertData[$col] = $new_url; // 신규 폴더 
                else $insertData[$col] = $R[$col]; // 나머지는 기존 bot 데이타값
            }

            $dd = array();
            $dd['table'] = $tbl;
            $dd['cols'] = $cols;
            $dd['insertData'] = $insertData;
            $this->insert($dd);
            
            //aramjo - vendor 디렉토리 복사가 아닌 이미지 개별 파일 복사
            $old_path = $R['url'].$R['folder'].'/'.$R['name'];
            $new_path = $new_url.$R['folder'].'/'.$R['name'];
            
            $this->copyFile($old_path, $new_path);
        }

        /*
        // 실제 폴더 복사
        //if($this->dbTargetServer) $oldF = '/home/users/'.$this->dbTargetServer.'/www/files/'.$m.'/'.$targetVendor.'/';
        //else $g['path_file'].$m.'/'.$targetVendor.'/'.$targetBot.'/';
        
        //aramjo - syschatbot 디렉토리 변경
        if($this->dbTargetServer) $oldF = '/cloud/'.$this->dbTargetServer.'/www/files/'.$m.'/'.$targetVendor.'/';
        else $g['path_file'].$m.'/'.$targetVendor.'/'.$targetBot.'/';

        $newF = $new_url ;
        if (!is_dir($newF)){
            mkdir($newF,0707);//
            @chmod($newF,0707);
        }
        
        // 폴더 복사 함수 호출 
        $this->copyDir($oldF,$newF);
        */

    }

    // vendor, botid, 값만 변경해서 카피하기
    function VbIcopy($data,$tbl){

        // 타겟
        $targetVendor = $data['targetVendor'];
        $targetBotId = $data['targetBotId'];
        $_wh = "vendor='".$targetVendor."' and botid='".$targetBotId."'";
        $RCD = $this->getDbSelect($tbl,$_wh,'*');

        // 소유
        $vendor = $data['vendor'];
        $botid = $data['botid'];
      
        // 테이블 컬럼 추출
        $cols = $this->getCols($tbl);
        $insertData = array();

        while($R = db_fetch_array($RCD)){            
            // insert value 세팅
            foreach ($cols as $col){
                if($col=='vendor') $insertData[$col] = $vendor; // 복사하는 vendor
                else if($col == 'botid') $insertData[$col] = $botid;
                else $insertData[$col] = $R[$col]; // 나머지는 기존 bot 데이타값
            }

            $dd = array();
            $dd['table'] = $tbl;
            $dd['cols'] = $cols;
            $dd['insertData'] = $insertData;
            $lastUid = $this->insert($dd);
        }
    }

    // vendor, bot, 값만 변경해서 카피하기
    function VBcopy($data,$tbl){

        // 타겟
        $targetVendor = $data['targetVendor'];
        $targetBot = $data['targetBot'];
        $_wh = 'vendor='.$targetVendor.' and bot='.$targetBot;
        $RCD = $this->getDbSelect($tbl,$_wh,'*');

        // 소유
        $vendor = $data['vendor'];
        $bot = $data['bot'];
      
        // 테이블 컬럼 추출
        $cols = $this->getCols($tbl);
        $insertData = array();

        while($R = db_fetch_array($RCD)){            
            // insert value 세팅
            foreach ($cols as $col){
                if($col=='vendor') $insertData[$col] = $vendor; // 복사하는 vendor
                else if($col == 'bot') $insertData[$col] = $bot;
                else if($col == 'o_uid') $insertData[$col] = $R['uid'];
                else $insertData[$col] = $R[$col]; // 나머지는 기존 bot 데이타값
            }

            $dd = array();
            $dd['table'] = $tbl;
            $dd['cols'] = $cols;
            $dd['insertData'] = $insertData;
            $lastUid = $this->insert($dd);
            if($lastUid){
                // apiReq, apiParam
                if($tbl == $this->tbl_apiList){
                   $data['api'] = $lastUid; //  신규 uid
                   $data['targetApi'] = $R['uid'];// 기존 uid
                   $this->OKcopy($data,$this->tbl_apiReq);     
                }                
            }
        }
    }
    
    // Only Key copy (apiReq, apiReqParam)
    function OKcopy($data,$tbl){
    
        if($tbl == $this->tbl_apiReq) $_wh = 'api='.$data['targetApi'];
        else if($tbl == $this->tbl_apiReqParam) $_wh = 'req='.$data['targetReq'];

        $RCD = $this->getDbSelect($tbl,$_wh,'*');

        // 테이블 컬럼 추출
        $cols = $this->getCols($tbl);
        $insertData = array();

        while($R = db_fetch_array($RCD)){
            // insert value 세팅
            if($tbl == $this->tbl_apiReq){
                foreach ($cols as $col){
                    if($col=='api') $insertData[$col] = $data['api']; // 타겟 api 
                    else if($col=='o_uid') $insertData[$col] = $R['uid'];
                    else $insertData[$col] = $R[$col]; // 나머지는 기존 데이타값
                }    
            }else if($tbl == $this->tbl_apiReqParam){
                foreach ($cols as $col){
                    if($col=='api') $insertData[$col] = $data['api'];
                    else if($col=='req') $insertData[$col] = $data['req'];
                    else $insertData[$col] = $R[$col]; // 나머지는 기존 데이타값
                } 
            }
            $dd = array();
            $dd['table'] = $tbl;
            $dd['cols'] = $cols;
            $dd['insertData'] = $insertData;
            $lastUid = $this->insert($dd);

            if($lastUid){
                // apiReq, apiParam
                if($tbl == $this->tbl_apiReq){
                   $data['api'] = $data['api']; 
                   $data['req'] = $lastUid; //  신규 uid
                   $data['targetReq'] = $R['uid'];// 기존 uid
                   $this->OKcopy($data,$this->tbl_apiReqParam);     
                }                
            }
        }

    }

    function getNewApiReq($data){

        $old_req = $data['req'];

        $tbl= $this->tbl_apiReq;
        $R = getDbData($tbl,'o_uid='.$old_req,'*');

        return array("new_api"=>$R['api'],"new_req"=>$R['uid']);
    }

    // ResApiOutput, ResApiParam 테이블 2 개 복사 
    function copyResApiOutput($data){

        // 테이블 지정
        $tbl_output = $this->tbl_resApiOutput;
        $tbl_param = $this->tbl_resApiParam;

        // target 
        $targetItemOC = $data['targetItemOC'];

        // 소유
        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $itemOC = $data['itemOC'];

        // 조건 
        $_wh = 'itemOC='.$targetItemOC;
        
        // ############ output 복사 ##########################################
        $OCD = $this->getDbSelect($tbl_output,$_wh,'*');

        // 테이블 컬럼 추출
        $cols = $this->getCols($tbl_output);
        $insertData = array();

        while($R = db_fetch_array($OCD)){
            // insert value 세팅
            foreach ($cols as $col){
                if($col=='vendor') $insertData[$col] = $vendor;
                else if($col=='bot') $insertData[$col] = $bot;
                else if($col=='itemOC') $insertData[$col] = $itemOC;
                else $insertData[$col] = $R[$col]; // 나머지는 기존 bot 데이타값
            }

            $dd = array();
            $dd['table'] = $tbl_output;
            $dd['cols'] = $cols;
            $dd['insertData'] = $insertData;
            $this->insert($dd);
        }

        // ############ param 복사 ########################################## 
        $PCD = $this->getDbSelect($tbl_param,$_wh,'*');

        // 테이블 컬럼 추출
        $cols = $this->getCols($tbl_param);
        $insertData = array();

        while($R = db_fetch_array($PCD)){
            $GNAR = $this->getNewApiReq($R);
            $new_api = $GNAR['new_api'];
            $new_req = $GNAR['new_req'];
            
            // insert value 세팅
            foreach ($cols as $col){
                if($col=='itemOC') $insertData[$col] = $itemOC;
                else if($col=='api') $insertData[$col] = $new_api;
                else if($col=='req') $insertData[$col] = $new_req;
                else if($col=='o_uid') $insertData[$col] = $R['uid'];
                else $insertData[$col] = $R[$col]; // 나머지는 기존 bot 데이타값
            }

            $dd = array();
            $dd['table'] = $tbl_param;
            $dd['cols'] = $cols;
            $dd['insertData'] = $insertData;
            $this->insert($dd);
        }
    }
    
    // getDbSelect 바인딩 
    function getDbSelect($table,$where,$data){

        if($this->dbTargetMod=='sys') $result = $this->getSysDbSelect($table,$where,$data); // sysDB
        else $result = getDbSelect($table,$where,$data); // 기본 DB 
       
        return $result;
    }

     // getDbData 바인딩 
    function getDbData($table,$where,$data){

        if($this->dbTargetMod=='sys') $result = $this->getSysDbData($table,$where,$data); // sysDB
        else  $result = getDbData($table,$where,$data); // 기본 DB 
       
        return $result;
    }

    // getRemoteBotList
    function getRemoteBotList($data){
        global $table;
        
        $result = array();        
        $server = $data['targetServer'];
        if($server=='cloud'){
            $where = 'hidden=0';
            $tbl = $table[$this->module.'bot'];
            $RCD = $this->getSysDbSelect($tbl,$where,'uid,name');
            while($R = db_fetch_array($RCD)){
                $result[] = array("uid"=>$R['uid'],"name"=>$R['name']);
            }

        }

        return $result;
    }
    
    // 템플릿 봇 리스트 추출 
    function getTemplateBotList($data){
        global $table;

        $this->dbTargetMod=='sys';
        
        $result = array();        
        $where = 'hidden=0';
        $tbl = $table[$this->module.'bot'];
        $RCD = $this->getSysDbSelect($tbl,$where,'uid,name');
        while($R = db_fetch_array($RCD)){
            $result[] = array("uid"=>$R['uid'],"name"=>$R['name']);
        }

        return $result;
    }
     
    // Start == ######################## system 리소스(entity, intent, topic) 업데이트 ######################
    function updateSysEntityVal($data){

        $tbl = $this->tbl_entityVal;
        $vendor = $data['vendor'];
        $entity = $data['entity'];
        $targetEntity = $data['targetEntity'];

        // 테이블 컬럼 추출  
        $cols = $this->getDbCols($tbl); // local DB
        $insertData = array();
        
        $where = "type='S' and bot=0 and entity='".$targetEntity."'"; // system entity 조건 
        $RCD = $this->getSysDbSelect($tbl,$where,'*');
        
        while($R = db_fetch_array($RCD)){
            $o_uid = $R['uid'];
            $is_row_wh = $where.' and o_uid='.$o_uid;
            $is_row = getDbData($tbl,$is_row_wh,'uid');
            
            // 이미 있는 경우 > name,synonyms,patterns 값만 수정 
            if($is_row['uid']){
                $update_q = "name='".$R['name']."',synonyms='".$R['synonyms']."',patterns='".$R['patterns']."'";
                getDbUpdate($tbl,$update_q,'uid='.$is_row['uid']);
            }else{
                // insert value 세팅
                foreach ($cols as $col){
                    if($col =='vendor') $insertData[$col] = $vendor;
                    else if($col =='entity') $insertData[$col] = $entity;
                    else if($col == 'o_uid') $insertData[$col] = $o_uid;
                    else $insertData[$col] = $R[$col]; // 나머지는 기존 bot 데이타값
                }

                $dd = array();
                $dd['table'] = $tbl;
                $dd['cols'] = $cols;
                $dd['insertData'] = $insertData;
                $lastEntityVal = $this->insert($dd);
            }
            
        }
    
    }

    function updateSysEntity($data){
        global $table;
        
        $tbl = $this->tbl_entity;
        $vendor = $data['vendor'];

        // 테이블 컬럼 추출  
        $cols = $this->getDbCols($tbl); // local DB
        $insertData = array();
        
        $where = "type='S' and bot=0"; // system entity 조건 
        $RCD = $this->getSysDbSelect($tbl,$where,'*');
        
        while($R = db_fetch_array($RCD)){
            $o_uid = $R['uid'];
            $is_row_wh = $where.' and o_uid='.$o_uid;
            $is_row = getDbData($tbl,$is_row_wh,'*');
            
            // 이미 있는 경우 > name 값만 수정 
            if($is_row['uid']){
                getDbUpdate($tbl,'name='.$R['name'],'uid='.$is_row['uid']);
            }else{
                // insert value 세팅
                foreach ($cols as $col){
                    if($col =='vendor') $insertData[$col] = $vendor;
                    else if($col == 'o_uid') $insertData[$col] = $o_uid;
                    else $insertData[$col] = $R[$col]; // 나머지는 기존 bot 데이타값
                }

                $dd = array();
                $dd['table'] = $tbl;
                $dd['cols'] = $cols;
                $dd['insertData'] = $insertData;
                $lastEntity = $this->insert($dd);
                if($lastEntity){
                    $data['entity'] = $lastEntity;
                    $data['targetEntity'] = $R['uid'];
                    $this->updateSysEntityVal($data);                
                }

            }
            
        }

    }
    
    //DB데이터 ARRAY
    function getSysDbArray($table,$where,$data,$sort,$orderby,$recnum,$p) 
    {
        $sql = 'select '.$data.' from '.$table.($where?' where '.getSqlFilter($where):'').' order by '.$sort.' '.$orderby.($recnum?' limit '.(($p-1)*$recnum).', '.$recnum:''); 
        $rcd = $this->sys_db_query($sql);
        
        return $rcd;
    }

   
    // 시스템 리소스 업데이트 (user DB 최초 생성 or 업데이트시 사용 ) 
    // $data['vendor'] = 사용자 vendor 적용 
    function updateSysResource($data){
        $this->updateSysEntity($data);
    } 
    // End == ######################## system 리소스 업데이트 ######################
    
    // 사용 보류 

    // 템플릿을 복사한 Vendor 관리자 페이지 > 응답설정 페이지에 출력될 내용     
    // function getTempResData($data){
    //     global $table;

    //     $tempBot = $data['tempBot']; // 복사 대상 bot uid = 템플릿 uid  
    //     $tbl = $this->tbl_tempData;
    //     $wh = "bot='".$tempBot."' and active=1";
    //     $RCD = $this->getSysDbArray($tbl,$wh,'*','gid','asc','',1);
        
    //     return $RCD;
    // }
}