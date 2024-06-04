<?php
// bot 템플릿 컨트롤(복사/삭제)
/*
   추가 : apiList, apiReq, apiReqParam,botSettings, dialogResApiOutput,
          dialogResApiParam
*/

class topicTemp{
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

    public function __construct(){
        global $table, $_db_sys; 

        $this->module='chatbot';
        $m = $this->module;

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
        $this->tbl_upload = $table[$m.'upload'];

      
        /// sys DB 정보 초기화 
        //$this->sysDb = new sysDB();         
        $this->sys_conn = false;
        $this->sys_host = $_db_sys['host']; //'10.10.0.115'; //hostname
        $this->sys_user = $_db_sys['name']; //'syscloud'; //username
        $this->sys_pass = $_db_sys['pass']; //'syscloud5279!!'; //password
        $this->sys_dbName = $_db_sys['name']; //'syscloud'; //name of your database
        $this->sys_port = $_db_sys['port']; //'3306';
        $this->sys_debug = true;
    }

    
    // resGroup 삭제 > resGroup, resItem, resItemOC
    function delResGroup($data){

        $tbl_resGroup = $this->tbl_resGroup;
        $tbl_resItem = $this->tbl_resItem;
        $tbl_resItemOC = $this->tbl_resItemOC;

        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $dialog = $data['dialog'];

        $resG_wh ='vendor='.$vendor.' and bot='.$bot.' and dialog='.$dialog; 

        $GCD = getDbSelect($tbl_resGroup,$resG_wh,'*'); // resGroup 추출

        while($G = db_fetch_array($GCD)){
            $RCD = getDbSelect($tbl_resItem,$resG_wh." and resGroupId='".$G['id']."' ",'*'); // req 추출
            while($R = db_fetch_array($RCD)){

                $OCD = getDbSelect($tbl_resItemOC,'item='.$R['uid'],'*'); // uid 추출            
                while($O = db_fetch_array($OCD)){
                    
                    if($O['resType']=='img'){
                        $data['img_url'] = $O['varchar_val'];
                        $this->delResImg($data);   
                    }
                    
                    // resItemOC 삭제 
                    getDbDelete($tbl_resItemOC,'uid='.$O['uid']);
                }

                // resItem > img 삭제 
                if($R['img_url']){
                    $data['img_url'] = $R['img_url'];
                    $this->delResImg($data); 
                }

                // resItem 삭제 
                getDbDelete($tbl_resItem,'uid='.$R['uid']);
            }
            
            // resGroup 삭제 
            getDbDelete($tbl_resGroup,'uid='.$G['uid']);
        }
    }

    // 업로드 테이블 및 폴더 지우기
    function delResImg($data){
        global $g;

        $tbl = $this->tbl_upload;

        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $dialog = $data['dialog'];

        $img_url = $data['img_url'];

        $ia = explode('/',$img_url);
        $tmpname = end($ia);
        
        $R = getDbData($tbl,"vendor='".$vendor."' and bot='".$bot."' and dialog='".$dialog."' and tmpname='".$tmpname."' ",'*');
        
        unlink('.'.ltrim($R['url'],'.').$R['folder'].'/'.$R['tmpname']);
        getDbDelete($tbl,'uid='.$R['uid']);     
 

    }

    function deleteTopic($data){
        global $table;

        $tbl_dialog = $this->tbl_dialog;
        $tbl_node = $this->tbl_node;
        
        // dialog 기준 vendor, bot 추출 
        
        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $dialog = $data['topic'];

        // dialogNode 에 연결된 use_topic 값 > 0 처리 
        getDbUpdate($tbl_node,'use_topic=0','use_topic='.$dialog);

        // dialogNode 에서 node 삭제 
        $del_wh ='vendor='.$vendor.' and bot='.$bot.' and dialog='.$dialog;
        
        // node 테이블에서 삭제 
        getDbDelete($tbl_node,$del_wh);        

        // dialog 테이블에서 삭제 
        getDbDelete($tbl_dialog,'uid='.$dialog); 
        
        // 응답 내용 지우기 : resGroup, resItem, resItemOC
        $data['dialog'] = $dialog;
        $this->delResGroup($data);
        
  
    }
    // ############################### < 아래 > 복사 프로세스 시작 ###########################

     // ############################ start of system 

    // system DB 연결 
    function sysConnect(){
        if (!$this->sys_conn) {
            $this->sys_conn = mysqli_connect($this->sys_host, $this->sys_user, $this->sys_pass, $this->sys_dbName, $this->sys_port); 
            
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
        $result = mysqli_query($con, "SHOW COLUMNS FROM ".$tbl); 
        while ($r=mysqli_fetch_array($result))
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
        $row = mysqli_fetch_array($this->getSysDbSelect($table,$this->getSqlFilter($where),$data));
       
        return $row;
    }

    function sys_db_query($sql){
        $con = $this->sysConnect();
        mysqli_query($con, 'set names utf8');
        mysqli_query($con, 'set sql_mode=\'\'');

        return mysqli_query($con, $sql);
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
              $insertSQL .= "'".addslashes(trim($val))."',";
        }
        $insertSQL= rtrim($insertSQL,',').')'; // 마지막 쉼표 제거

        db_query($insertSQL,$DB_CONNECT);

        $last_uid = getDbCnt($table,'max(uid)','');

        return $last_uid;

    }
    
    
    // Copy Topic
    /*
         소유정보 : vendor, bot, (topic)name, (intro)
         타겟정보 : targetBot
    */ 
    function copyTopic($data){
        global $date;

        // 테이블 지정
        $tbl = $this->tbl_dialog;

        // 타겟
        $targetBot = $data['targetBot'];


        $R = $this->getDbData($tbl,'bot='.$targetBot,'*');

        $data['targetVendor'] = $R['vendor'];
        $data['targetDialog'] = $R['uid'];

        // 소유
        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $name = $data['name'];
        $intro = $data['intro']?$data['intro']:$name;

        // 소유자 dialog 체크 > gid 추출 
        $owner_wh = 'vendor='.$vendor.' and bot='.$bot; 
        $MAXG = getDbCnt($tbl,'max(gid)',$owner_wh);
        $gid = $MAXG+1;

        // 테이블 컬럼 추출
        $cols = $this->getCols($tbl);

        $insertData = array();
        // insert value 세팅
        foreach ($cols as $col){
            if($col == 'type') $insertData[$col] = 'T';
            else if($col == 'is_temp') $insertData[$col] = 0;
            else if($col =='gid') $insertData[$col] = $gid;
            else if($col =='name') $insertData[$col] = $name;
            else if($col =='intro') $insertData[$col] = $intro;
            else if($col=='vendor') $insertData[$col] = $vendor; // 복사하는 vendor
            else if($col == 'bot') $insertData[$col] = $bot;
            else if($col == 'd_regis') $insertData[$col] = $date['totime'];
            // bottalks에서는 o_botuid를 $targetBot으로 설정
            // 해당 토픽의 유효기간에 따른 활성 비활성화를 위해
            else if($this->dbTargetMod=='sys' && $col == 'o_botuid') $insertData[$col] = $targetBot;
            
            else $insertData[$col] = $R[$col]; // 나머지는 기존 bot 데이타값
        }
        $dd = array();
        $dd['table'] = $tbl;
        $dd['cols'] = $cols;
        $dd['insertData'] = $insertData;
        $lastDialog = $this->insert($dd);
        if($lastDialog){
            $data['dialog'] = $lastDialog; //  소유자 신규  dialog         
            $this->VBDcopy($data,$this->tbl_node);
            $this->VBDcopy($data,$this->tbl_resGroup);
            $this->VBDcopy($data,$this->tbl_resItem);
            $this->copyREC($data); // node, resItem 조건의 인텐트/엔터티 uid 값 변경 > 필요시 entity,intent,context 추가 
        }
        return $lastDialog;
    }

    
    // vendor, bot, dialog 값만 변경해서 카피하기
    function VBDcopy($data,$tbl){

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

        while($R = db_fetch_array($RCD)){            
            // insert value 세팅
            foreach ($cols as $col){
                if($col=='vendor') $insertData[$col] = $vendor; // 복사하는 vendor
                else if($col == 'bot') $insertData[$col] = $bot;
                else if($col == 'dialog') $insertData[$col] = $dialog;
                else if($col == 'img_url'){
                    $data['img_url'] = $R['img_url'];
                    $insertData[$col] = $this->getCopyImgUrl($data);  
                }
                else if($col=='varchar_val' && $R['resType']=='img'){
                    $data['img_url'] = $R['varchar_val'];
                    $insertData[$col] = $this->getCopyImgUrl($data);   
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
    
    public function getLogWrite($chLog) {
        $logDir = $_SERVER['DOCUMENT_ROOT']."/api/botAPI/log";
        if(file_exists($logDir)){
            $fp = fopen($logDir."/topicTemp_".date("Ymd").".log","a+");
            fputs($fp,"[".date("Y-m-d H:i:s")."] ".$chLog."\n");
            fclose($fp);
        }
    }

    public function copyFile($oldFile, $newFile) {
        global $g;
        
        if($this->dbTargetServer) $baseDirectory = '/data/'.$this->dbTargetServer.'/www';
        else $baseDirectory = $_SERVER['DOCUMENT_ROOT'];
        
        $userBaseDirectory = $_SERVER['DOCUMENT_ROOT'].'/files/';
        
        $oldFile = $baseDirectory.str_replace('./files/', '/files/', $oldFile);
        $newFile = str_replace('/files/','',$newFile);
        $aNewPath = explode('/', $newFile);
        array_pop($aNewPath);
        
        if(file_exists($oldFile)) {
            $newPath = '';
            foreach($aNewPath as $path) {
                if($path != '') {
                    $newPath .=$path.'/';
                    if (!is_dir($userBaseDirectory.$newPath)){
                        $oldmask = umask(0);
                        mkdir($userBaseDirectory.$newPath,0707);
                        umask($oldmask);
                        $this->getLogWrite("[mkdir] ".$userBaseDirectory.$newPath);
                    }
                }
            }
            @copy($oldFile, $userBaseDirectory.$newFile);
            $this->getLogWrite("[copyfile] oldFile : ".$oldFile.", newFile : ".$userBaseDirectory.$newFile);
        }
    }

    
    // 이미지 url 복사 / upload 테이블 입력 / 실제 폴더 이동  > 최종 copyUrl 리턴  
    private function getCopyImgUrl($data){
        global $g,$date,$my;

        $m = $this->module;
        $tbl = $this->tbl_upload;

        $target_url = $data['img_url'];
        
        if($target_url!=''){
            
            // target_url > tmpname 추출             
            $img_arr = explode('/',$target_url);
            $tmpname = end($img_arr);

            // 소유 정보 
            $bot = $data['bot'];
            $vendor = $data['vendor'];
            $dialog = $data['dialog'];
            $mbruid = $my['uid'];

            // 타겟정보 
            $targetBot = $data['targetBot'];
            $targetVendor = $data['targetVendor'];

            $tmpcode = str_replace('.','',$g['time_start']);
            $url = '/files/'.$m.'/'.$mbruid.'/'.$bot.'/'.$dialog.'/'; // url 컬럼에 저장 
            
            $upfolder = substr($date['today'],0,8); 
            $dir_year  = substr($upfolder,0,4);
            $dir_month  = substr($upfolder,4,2);
            $dir_day = substr($upfolder,6,2);
            $folder = $dir_year.'/'.$dir_month.'/'.$dir_day;// folder 컬럼에 저장 
      
            // 신규 폴더(url 값) 세팅 
            $mingid = getDbCnt($tbl,'min(gid)','');
            $gid = $mingid ? $mingid - 1 : 100000000;    


            $R = $this->getDbData($tbl,"vendor='".$targetVendor."' and bot='".$targetBot."' and tmpname='".$tmpname."' ",'*');    

            // 테이블 컬럼 추출
            $cols = $this->getCols($tbl);
            $insertData = array();

            // insert value 세팅
            foreach ($cols as $col){
                if($col=='gid') $insertData[$col] = $gid; 
                else if($col=='tmpcode') $insertData[$col] = $tmpcode;
                else if($col=='vendor') $insertData[$col] = $vendor; // 복사하는 vendor
                else if($col=='bot') $insertData[$col] = $bot;
                else if($col=='dialog') $insertData[$col] = $dialog;
                else if($col=='url') $insertData[$col] = $url; // 신규 url 
                else if($col=='folder') $insertData[$col] = $folder; // 신규 폴더
                else if($col=='mbruid') $insertData[$col] = $my['uid'];
                else if($col=='d_regis') $insertData[$col] = $date['totime'];
                else $insertData[$col] = $R[$col]; // 나머지는 기존 bot 데이타값
            }

            $dd = array();
            $dd['table'] = $tbl;
            $dd['cols'] = $cols;
            $dd['insertData'] = $insertData;
            // if($tmpname=='83756eb8ee20293903960d72e42fb77d095054.jpg'){
            //    print_r($dd);
            //    exit;
            // }
            $this->insert($dd);

            // 폴더/파일 복사 함수 호출
            $new_url = $url.$folder.'/'.$tmpname;
            $this->copyFile($target_url,$new_url);
         
         }else{
            $new_url ='';
        }
        

        return $new_url; 

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
                    $insertData[$col] = $this->getCopyImgUrl($data);   
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

        }

    }

     

    // 해당 토픽 intent 만 가져오기  
    function getCopyRecIntent($data){
        global $table,$s;
         
        $tbl = $this->tbl_intent;
        $tbl_ex = $this->tbl_intentEx;

        // 타겟 
        $targetVendor = $data['targetVendor'];
        $targetBot = $data['targetBot'];

        // 소유
        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $dialog = $data['dialog'];

        $cond_data = explode('|',$data['cond_unit']); // and|#|16|체험단상품배송|#체험단상품배송 
        $andOr = $cond_data[0]; // and, or, not
        $filter = $cond_data[1]; // #, @, $
        $uid = $cond_data[2]; // target intent uid
        $name = $cond_data[3];
        $type = $cond_data[4]; // S or V
        $label = $cond_data[5];// @엔터티 or @엔터티:벨류

        if($type =='V'){

            // 소유 bot 기준 > 해당 인텐트가 있는지 체크 
            $o_wh = "type='".$type."' and vendor='".$vendor."' and bot='".$bot."' and name='".$name."'";
            $OR = getDbData($tbl,$o_wh,'uid'); // 소유 bot 기준 추출   
           
            if($OR['uid']){ // 소유 bot 에 있는 경우 
               
                // 예문 체크 > 타겟 예문 중 소유 예문에 없는 것 추가    
                $TCD = $this->getDbSelect($tbl_ex,'vendor='.$targetVendor.' and bot='.$targetBot.' and intent='.$uid,'content'); 
                while($T = db_fetch_array($TCD)){
                    $content = $T['content'];

                    $ex_wh = "type='".$type."' and vendor='".$vendor."' and bot='".$bot."' and content='".$content."'";               
                    $has_ex = getDbData($tbl_ex,$ex_wh,'uid');
                    
                    // 소유 예문에 타겟 예문이 없는 것 추가 
                    if(!$has_ex['uid']){
                        $intnet = $OR['uid'];

                        // 신규 소유 bot intent 로 추가  
                        $QKEY = "type,vendor,bot,dialog,intent,hidden,content";
                        $QVAL = "'$type','$vendor','$bot','$dialog','$intent,'$hidden','$content'";
                        getDbInsert($tbl_ex,$QKEY,$QVAL);    
                    }                 
                }

                $result = $andOr.'|'.$filter.'|'.$OR['uid'].'|'.$name.'|'.$type.'|'.$label; // 기존 소유 bot intent uid 사용 

            }else{ // 소유 bot 에 없는 경우 

                // 인텐트 추가 
                $_sql ="type='".$type."' and vendor='".$vendor."' and bot='".$bot."'";
                $MAXG = getDbCnt($tbl,'max(gid)',$_sql);
                $gid = $MAXG+1;
                      
                $QKEY = "gid,type,site,vendor,bot,hidden,name";
                $QVAL = "'$gid','$type','$s','$vendor','$bot','$hidden','$name'";
                getDbInsert($tbl,$QKEY,$QVAL); 

                $last_intent = getDbCnt($tbl,'max(uid)','');

                // 타겟 bot 예문 추출해서 소유 bot 예문으로 저장 
                $TCD = $this->getDbSelect($tbl_ex,'vendor='.$targetVendor.' and bot='.$targetBot.' and intent='.$uid,'content'); // 외부 데이터 참조 고려
                
                while($T = db_fetch_array($TCD)){
                    $content = $T['content'];
                    if($content){
                        // 신규 소유 bot intent(last_intent) 로 저장 
                        $QKEY = "type,vendor,bot,dialog,intent,hidden,content";
                        $QVAL = "'$type','$vendor','$bot','$dialog','$last_intent','$hidden','$content'";
                        getDbInsert($tbl_ex,$QKEY,$QVAL); 
                    }                
                }

                $result = $andOr.'|'.$filter.'|'.$last_intent.'|'.$name.'|'.$type.'|'.$label; // 신규 소유 bot intent uid 적용 
                
            }
        }else if($type=='S'){
            // 시스템 인텐트 인경우 그대로 돌려준다. 
            $result = $data['cond_unit'];
        }    

        return $result;


    }   

    // 해당 토픽 entity 만 가져오기  
    function getCopyRecEntity($data){
        global $table,$s;
         
        $tbl = $this->tbl_entity;
        $tbl_ex = $this->tbl_entityVal;
        $hidden =0;

        // 타겟 
        $targetVendor = $data['targetVendor'];
        $targetBot = $data['targetBot'];

        // 소유
        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $dialog = $data['dialog'];

        $cond_data = explode('|',$data['cond_unit']); // and|#|16|체험단상품배송|#체험단상품배송 
        $andOr = $cond_data[0]; // and, or, not
        $filter = $cond_data[1]; // #, @, $
        $uid = $cond_data[2]; // target intent uid
        $name = $cond_data[3];
        $type = $cond_data[4]; // S or V
        $label = $cond_data[5];// @엔터티 or @엔터티:벨류
        $operator = $cond_data[6];// :, !:, ::...
        $op_val = $cond_data[7];// operator 벨류

        if($type =='V'){
            // 소유 bot 기준 > 해당 엔터티가 있는지 체크 
            $OR = getDbData($tbl,"type='".$type."' and vendor='".$vendor."' and bot='".$bot."' and name='".$name."'",'uid'); // 소유 bot 기준 추출   
           
            if($OR['uid']){ // 소유 bot 에 entity 있는 경우 
               
                // 예문 체크 > 타겟 예문 중 소유 예문에 없는 것 추가    
                $TCD = $this->getDbSelect($tbl_ex,'vendor='.$targetVendor.' and bot='.$targetBot.' and entity='.$uid,'name,synonyms'); 

                while($T = db_fetch_array($TCD)){
                    $entityValName = $T['name'];
                    $synonyms = trim($T['synonyms']);

                    $ex_wh = "type='".$type."' and vendor='".$vendor."' and bot='".$bot."' and name='".$entityValName."'"; // entity value 가 있는지 먼저 체크               
                    $has_ex = getDbData($tbl_ex,$ex_wh,'uid,name,synonyms');
                    
                    // 소유 예문에 타겟 예문이 없는 것 추가 
                    if(!$has_ex['uid']){
                        $entity = $OR['uid'];  

                        // 신규 소유 bot intent 로 추가  
                        $QKEY = "type,vendor,bot,dialog,entity,hidden,name,synonyms";
                        $QVAL = "'$type','$vendor','$bot','$dialog','$entity,'$hidden','$entityValName','$synonyms'";
                        getDbInsert($tbl_ex,$QKEY,$QVAL);    

                    }else{ // entity 벨류 name 이 있는 경우 유사어 체크 
                        
                        $owner_syn = rtrim(trim($has_ex['synonyms']),','); // 소유 entityVal 유사어 값 
                        $tSys_arr = explode(',',trim($T['synonyms'])); // 타겟 synonyms 배열 처러 
                        $add_syn = ''; 
                        
                        // 타겟 유사어가 소유 유사어에 있는지 체크 > 없으면 추가 > 업데이트 
                        foreach ($tSys_arr as $val) {
                            if(!trim($val)) continue;
                            if(!strstr($owner_syn,$val)){
                                $add_syn.=','.$val;
                            }    
                        }
                        
                        // synonyms 업데이트 
                        $new_owner_syn = $owner_syn.$add_syn;
                        getDbUpdate($tbl_ex,"synonyms='".$new_owner_syn."'",'uid='.$has_ex['uid']);

                    }                 
                }

                // 최종 리턴되는 값 > operator 있는지 여부에 따라 분기 , uid 값은 기존 소유한 값 사용 
                if($operator) $result = $andOr.'|'.$filter.'|'.$OR['uid'].'|'.$name.'|'.$type.'|'.$label.'|'.$operator.'|'.$op_val;  
                else $result = $andOr.'|'.$filter.'|'.$OR['uid'].'|'.$name.'|'.$type.'|'.$label;  

            }else{ // 소유 bot 에 없는 경우 

                // 엔터티 추가 
                $_sql ="type='".$type."' and vendor='".$vendor."' and bot='".$bot."'";
                $MAXG = getDbCnt($tbl,'max(gid)',$_sql);
                $gid = $MAXG+1;
                      
                $QKEY = "gid,type,site,vendor,bot,hidden,name";
                $QVAL = "'$gid','$type','$s','$vendor','$bot','$hidden','$name'";
                getDbInsert($tbl,$QKEY,$QVAL); 

                $last_entity = getDbCnt($tbl,'max(uid)','');

                // 타겟 bot 예문 추출해서 소유 bot 예문으로 저장 
                $TCD = $this->getDbSelect($tbl_ex,'vendor='.$targetVendor.' and bot='.$targetBot.' and entity='.$uid,'name,synonyms'); // 외부 데이터 참조 고려
                
                while($T = db_fetch_array($TCD)){
                    $entityValName = $T['name'];
                    $synonyms = trim($T['synonyms']);
                    if($entityValName){
                        // 신규 소유 bot entity(last_entity) 로 저장 
                        $QKEY = "type,vendor,bot,dialog,entity,hidden,name,synonyms";
                        $QVAL = "'$type','$vendor','$bot','$dialog','$last_entity','$hidden','$entityValName','$synonyms'";
                        getDbInsert($tbl_ex,$QKEY,$QVAL); 
                    }                
                }

                // 최종 리턴되는 값 > operator 있는지 여부에 따라 분기 , uid 값은 기존 소유한 값 사용
                if($operator)  $result = $andOr.'|'.$filter.'|'.$last_entity.'|'.$name.'|'.$type.'|'.$label.'|'.$operator.'|'.$op_val;
                else $result = $andOr.'|'.$filter.'|'.$last_entity.'|'.$name.'|'.$type.'|'.$label; // 신규 소유 bot intent uid 적용 
                
            }
        }else if($type=='S'){
            // 시스템 엔터티인 경우 그대로 돌려준다. 
            $result = $data['cond_unit'];
        }    

        return $result;


    }     

    // node 조건구문 변경
    function copyRecCondition($data){

        $cond_arr = explode(',',$data['recCondition']); 
        $new_rec='';

        // 각 조건 배열별로 처리 
        foreach ($cond_arr as $cond_unit){           
            $cond_data = explode('|',$cond_unit); // and|#|16|체험단상품배송|#체험단상품배송 
            $cond_type  = $cond_data[1]; // #, @, $ 
            $data['cond_unit'] = $cond_unit;

            // 각 조건 타입별로 분기 처리
            if($cond_type =='#') $new_rec.= $this->getCopyRecIntent($data).',';
            else if($cond_type =='@') $new_rec.= $this->getCopyRecEntity($data).',';
            else if($cond_type =='$') $new_rec.= $cond_unit.',';// $ 컨텍스트인 경우 그래로 리턴
        }

        $result = rtrim($new_rec,',');

        return $result;
    }

    // node 조건의 인텐트/엔터티 uid 값 변경
    // 여기서는 무조건 자체 DB 에서 select 한다.(이미 node copy 가 된 상태여서) 
    function copyREC($data){
        $tbl_node = $this->tbl_node;        
        $tbl_resItem = $this->tbl_resItem;

        // 소유
        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $dialog = $data['dialog'];
        $_wh = 'vendor='.$vendor.' and bot='.$bot.' and dialog='.$dialog;
        // tbl_node 
        $RCD = getDbSelect($tbl_node,$_wh,'*');
        while($R = db_fetch_array($RCD)){
            $data['recCondition'] = $R['recCondition'];
            $rec = $this->copyRecCondition($data);
            getDbUpdate($tbl_node,"recCondition='".$rec."'",'uid='.$R['uid']);
        }

        // tbl_resItem 
        $RCD = getDbSelect($tbl_resItem,$_wh,'*');
        while($R = db_fetch_array($RCD)){
            $data['recCondition'] = $R['recCondition'];
            $rec = $this->copyRecCondition($data);
            getDbUpdate($tbl_resItem,"recCondition='".$rec."'",'uid='.$R['uid']);
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

}