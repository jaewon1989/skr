<?php
// 대화상자 메뉴 처리 전용 클래스

class nodeMenu extends Chatbot{
    public $vendor;
    public $bot;
    public $botId;
    public $dialog;
    public $nodeId;
    public $nodeId_copied;
    public $copyCell_type;
    public $nodeParent;
    public $act;
    public $nodeTbl;
    public $db;

     // _graph_script.php > a.do_dialogPanelAction.php 호출하는 함수 
    public function doNodeMenu($data){
        global $table;
        $this->db=new DB();
        $this->tbl_apiList = $table[$this->module.'apiList']; // api 리스트  
        $this->tbl_node = $table[$this->module.'dialogNode']; // node 테이블 
        $this->tbl_resGroup = $table[$this->module.'dialogResGroup']; // 응답그룹 테이블 
        $this->tbl_resItem = $table[$this->module.'dialogResItem']; // 응답그룹 > 응답 아이템 
        $this->tbl_resItemOC = $table[$this->module.'dialogResItemOC']; // 응답그룹 > 응답 아이템 > 조건 아이템 
        $this->tbl_upload = $table[$this->module.'upload']; // 파일(이미지) 업로드 테이블 
        $this->act = $data['menuAct'];
        $this->vendor = $data['vendor'];
        $this->bot = $data['bot'];
        $this->botId = $data['botId'];
        $this->dialog = $data['dialog'];
        $this->nodeId = $data['nodeId'];
        $this->nodeId_copied = $data['nodeId_copied']?$data['nodeId_copied']:false; // 복사한 node id 
        $this->copyCell_type = $data['copyCell_type'];
        $this->nodeParent = $data['nodeParent'];         

        $result = array();
        $result['error'] = false;

        // 타겟 node 정보 
        $R = $this->getNodeInfo($data);
        
        // 추가정보 세팅 
        $data['depth'] = $R['depth']; // depth 
        $data['gid'] = $R['gid']; // 순서값  

        if($this->act =='M2A' || $this->act =='M2B'){
            $Ex = $this->exChangeGid($data); // gid 교환 함수 호출
            if($this->act =='M2A'){
                if(!$Ex['M2A']){ 
                    $result['error'] = true;
                    $result['msg'] = '위로 이동할 수 없습니다.';
                } 
            }else if($this->act =='M2B'){
                if(!$Ex['M2B']){ 
                    $result['error'] = true;
                    $result['msg'] = '아래로 이동할 수 없습니다.';
                } 
            }  
        }  
        else{
            if($this->act =='A2A' || $this->act=='A2B'){ // 아래/위 에 추가 
                $data['nodeName'] = $data['newNodeName']; // 신규 저장되는 node 이름    
         
            }else if($this->act =='C2A'|| $this->act =='C2B'){ // 아래/위 에 붙여넣기 
                $data['nodeId'] = $data['nodeId_copied']; // 복사한 node id 
                $CR = $this->getNodeInfo($data); // 복사한 node 정보 
                $data['nodeName'] = $CR['name']; // 복사된 node 이름 > 기준 node 이름 + _ 
            }

            // copy, add 모두 대화상자 추가 
            $this->addNode($data); 

            
        }
        // 최종 리턴값 graph 
        $result['dialogNodeJson'] = $this->getNodeTreeJson($data); 
     
        return $result;
    }
    
    // gid 교환 함수 
    private function exChangeGid($data){
        $targetNode = $this->nodeId; // 기준 node 

        $base_wh = $this->getDialogBaseQry($data); // module.class.php 에 있음 
        $parent = $data['nodeParent'];
        $wh = $base_wh.' and parent='.$parent;

        $result = array();
        $result['M2A'] = true;
        $result['M2B'] = true;
            
        // 우선 node gid 재조정 (1,2,3,4....) 
        $this->resetNodeOrder($data);
        
        // targetNode 정보 
        $TR = $this->getNodeInfo($data);
        $TGID = $TR['gid']; // targetNode gid 

        if($this->act =='M2A'){
            if($TGID==1) $result['M2A'] = false; // target node gid 가 1 이면 더이상 위로 이동할 수 없음  
            else{
                $AGID = $TGID-1; // 상위 gid 
                $AR_wh = $base_wh.' and parent='.$parent.' and gid='.$AGID;
                $AR = getDbData($this->tbl_node,$AR_wh,'*'); // target node 보다 위에 있는 node 정보 
                
                // 상위 node gid --> targetNode gid 변경 
                getDbUpdate($this->tbl_node,'gid='.$TGID,'uid='.$AR['uid']);

                // 타겟 node gid --> 상위 node gid 변경 
                getDbUpdate($this->tbl_node,'gid='.$AGID,'uid='.$TR['uid']);
            }
        }else if($this->act =='M2B'){
             $BGID = $TGID+1; // 상위 gid 
             $BR_wh = $base_wh.' and parent='.$parent.' and gid='.$BGID;
             $BR = getDbData($this->tbl_node,$BR_wh,'*'); // target node 보다 위에 있는 node 정보 
             
             if($BR['uid'] && !$BR['is_unknown']){ // targetNode 가 99999 인경우 대비 
                // 하위 node gid --> targetNode gid 변경 
                 getDbUpdate($this->tbl_node,'gid='.$TGID,'uid='.$BR['uid']);

                 // 타겟 node gid --> 하위 node gid 변경 
                 getDbUpdate($this->tbl_node,'gid='.$BGID,'uid='.$TR['uid']);
             }else{
                $result['M2B'] = false;
             }   
             
        }

        return $result;
    } 

    // node 정보 
    private function getNodeInfo($data){
        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $dialog = $data['dialog'];
        $id = $data['nodeId'];
        $base_wh = $this->getDialogBaseQry($data); // module.class.php 에 있음 
        $wh = $base_wh.' and id='.$id;       
    
        $R = getDbData($this->tbl_node,$wh,'*');

        return $R;

    }
    
    // node 순서 조정 
    private function changeNodeOrder($data){
   
        $depth = $data['depth']; 
        $targetGid = $data['gid']; // 기준 node gid 값 
        $data['nodeId'] = $data['last_id'];
        
        // 신규 node 정보 
        $LR = $this->getNodeInfo($data);
        $last_uid = $LR['uid'];
        
        $base_wh = $this->getDialogBaseQry($data);
        $parent = $data['nodeParent'];
        $wh = $base_wh.' and parent='.$parent;
            
 
        $query = sprintf("SELECT uid,gid FROM %s WHERE %s ORDER BY gid ASC", $this->tbl_node,$wh);
        $rows = $this->getAssoc($query);
        
        if($this->act =='A2B' || $this->act =='C2B'){ // 아래에 추가/붙여넣기 
           
            // targetGid 보다 큰 gid 값 조정(+1) 
            foreach ($rows as $row) {
                $uid = $row['uid'];
                $gid = $row['gid'];
                
                if($targetGid<$gid){
                    $changedGid = $gid+1;
                    getDbUpdate($this->tbl_node,'gid='.$changedGid,'uid='.$uid);
                }
            }

            // 최신 node gid 값  조정 (targetGid-1)
            $changedGid = $targetGid+1;
            getDbUpdate($this->tbl_node,'gid='.$changedGid,'uid='.$last_uid); 

        }
        else if($this->act=='A2A' || $this->act=='C2A'){ // 위에 추가/붙여넣기 
            
            // 최신 node gid 값  조정 (targetGid)
            getDbUpdate($this->tbl_node,'gid='.$targetGid,'uid='.$last_uid);  

            // targetGid 이상인 gid 값 조정(+1) 
            foreach ($rows as $row) {
                $uid = $row['uid'];
                $gid = $row['gid'];
                
                // target gid 보다 작거나 같은 것은 무조건 +1
                if($gid >= $targetGid && $uid <> $last_uid){
                    $changedGid = $gid+1;
                    getDbUpdate($this->tbl_node,'gid='.$changedGid,'uid='.$uid);    
                }
            }                          
         
        }

        // gid 값 리셋( 중간에 빠진것 정리 및 is_unknown gid 값은 무조건 100000) 
        $this->resetNodeOrder($data);

    }

    // gid 값 리셋( 중간에 빠진것 정리 및 is_unknown gid 값은 무조건 100000) 
    private function resetNodeOrder($data){
        
        $base_wh = $this->getDialogBaseQry($data); 
        $parent = $data['nodeParent'];
        $wh = $base_wh.' and parent='.$parent;

        $query = sprintf("SELECT uid,gid,is_unknown FROM %s WHERE %s ORDER BY gid ASC", $this->tbl_node,$wh);
        $rows=$this->getAssoc($query);
        foreach ($rows as $index=>$row) {
            $uid = $row['uid'];
            
            if($row['is_unknown']) $gid = 100000; // is_unknown(답변 못한 것) 은 무조건 100000 으로 세팅 
            else $gid = $index+1;
            
            getDbUpdate($this->tbl_node,'gid='.$gid,'uid='.$uid);
        }  
    }
    

    // node 신규 추가 (아래에/위체 추가) 
    private function addNode($data){

        // node 추가 > Chatbot() 클래스 재사용
        $last_id = $this->regisNode($data); // 해당 함수에서 리턴 
        
        // 순서 조정
        $data['last_id'] = $last_id; 
        $this->changeNodeOrder($data);
        
        // 붙여 넣기인 경우 업데이트 및 추가 데이터(응답) 복사  
        if($this->act =='C2A'|| $this->act =='C2B'){
             $this->updateCopiedNode($data);
        }
    }
    
    // 복사한 node 추가 업데이트 
    private function updateCopiedNode($data){
        
        // 복사한 node 정보
        $data['nodeId'] = $data['nodeId_copied'];        
        $TR = $this->getNodeInfo($data);
        $recCondition = $TR['recCondition']; // input 조건
        $context = $TR['context']; // 컨텍스트 
        $track_flag = $TR['track_flag']; // 트래킹 여부 > 기본 1
        $node_action = $TR['node_action']; // 대화상자 액션 
        $jumpTo_node = $TR['jumpTo_node']; // 대화상자 이동 값 
        $use_topic = $TR['use_topic']; // 토픽 사용여부
        //$name = $TR['name'].$data['last_id']; // 신규 이름  
        $name = $TR['name'].'-복사'; // 신규 이름
        
        // 복사된 node 정보 
        $data['nodeId'] = $data['last_id'];
        $CR = $this->getNodeInfo($data);
        
        // 복사한 node 정보 ---> 복사된 node 정보에 업데이트 
        $upQry = "name='".$name."',recCondition='".$recCondition."',context='".$context."',node_action='".$node_action."',jumpTo_node='".$jumpTo_node."',use_topic='".$use_topic."'";
        getDbUpdate($this->tbl_node,$upQry,'uid='.$CR['uid']);
        
        
        // 응답 복사 
        if($this->copyCell_type=='with_res'){
            $data['targetVendor'] = $data['vendor'];
            $data['targetBot'] = $data['bot'];
            $data['targetDialog'] = $data['dialog'];
            $data['targetNode'] = $data['nodeId_copied']; // target node
            $data['node'] = $data['last_id'];// 신규 node 

            $this->VBDNCopy($data,$this->tbl_resGroup);
            $this->VBDNCopy($data,$this->tbl_resItem);
        }         
    }

    // ####################################  응답 그룹 복사 시작 #################################################
    
    // Vendor, Bot, Dialog, Node 값 기준 copy 
    private function VBDNcopy($data,$tbl){

        // 타겟
        $targetVendor = $data['targetVendor'];
        $targetBot = $data['targetBot'];
        $targetDialog = $data['targetDialog'];
        $targetNode = $data['targetNode'];


        $_wh = 'vendor='.$targetVendor.' and bot='.$targetBot.' and dialog='.$targetDialog.' and node='.$targetNode;
        $RCD = getDbSelect($tbl,$_wh,'*');
        
      
        // 소유
        $node = $data['node'];

        // 테이블 컬럼 추출
        $cols = $this->getCols($tbl);
        $insertData = array();

        while($R = db_fetch_array($RCD)){            
            // insert value 세팅
            foreach ($cols as $col){
                if($col=='node') $insertData[$col] = $node; // node 값 변경 
                else if($col == 'img_url'){
                    $data['img_url'] = $R['img_url'];
                    $insertData[$col] = $this->getCopyImgUrl($data);  
                }
                else if($col=='varchar_val' && $R['resType']=='img'){
                    $data['img_url'] = $R['varchar_val'];
                    $insertData[$col] = $this->getCopyImgUrl($data);   
                }
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
    
    private function copyResItemOC($data){

        // 테이블 지정
        $tbl = $this->tbl_resItemOC;

        // 타겟
        $targetItem = $data['targetItem'];

        // 소유
        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $item = $data['item'];

        $_wh = 'item='.$targetItem;
        $RCD = getDbSelect($tbl,$_wh,'*');

        // 테이블 컬럼 추출
        $cols = $this->getCols($tbl);
        $insertData = array();

        while($R = db_fetch_array($RCD)){
            // insert value 세팅
            foreach ($cols as $col){
                if($col=='item') $insertData[$col] = $item;
                else if($col=='varchar_val' && $R['resType']=='img'){
                    $data['img_url'] = $R['varchar_val'];
                    $insertData[$col] = $this->getCopyImgUrl($data);   
                }else if($col =='varchar_val' && $R['resType']=='api'){
                    $data['api'] = $R['varchar_val'];
                    $A = getDbData($this->tbl_apiList,"o_uid='".$data['api']."'",'uid');
                    $insertData[$col] = $A['uid'];
                }
                else $insertData[$col] = $R[$col]; // 나머지는 기존 bot 데이타값
            }


            $dd = array();
            $dd['table'] = $tbl;
            $dd['cols'] = $cols;
            $dd['insertData'] = $insertData;
            $lastItemOC = $this->insert($dd);

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

            // 타겟정보 
            $targetBot = $data['targetBot'];
            $targetVendor = $data['targetVendor'];

            $tmpcode = str_replace('.','',$g['time_start']);
            $url = '/files/'.$m.'/'.$vendor.'/'.$bot.'/'.$dialog.'/'; // url 컬럼에 저장 
            
            $upfolder = substr($date['today'],0,8); 
            $dir_year  = substr($upfolder,0,4);
            $dir_month  = substr($upfolder,4,2);
            $dir_day = substr($upfolder,6,2);
            $folder = $dir_year.'/'.$dir_month.'/'.$dir_day;// folder 컬럼에 저장 
      
            // 신규 폴더(url 값) 세팅 
            $mingid = getDbCnt($tbl,'min(gid)','');
            $gid = $mingid ? $mingid - 1 : 100000000;    


            $R = getDbData($tbl,"vendor='".$targetVendor."' and bot='".$targetBot."' and tmpname='".$tmpname."' ",'*');    

            // 테이블 컬럼 추출
            $cols = $this->getCols($tbl);
            $insertData = array();

            // insert value 세팅
            foreach ($cols as $col){
                if($col=='gid') $insertData[$col] = $gid; 
                else if($col=='tmpcode') $insertData[$col] = $tmpcode;
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

    private function copyFile($oldFile, $newFile) {
        global $g;
        
        $baseDirectory = $_SERVER['DOCUMENT_ROOT'];
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
                        mkdir($userBaseDirectory.$newPath,0707);
                        @chmod($userBaseDirectory.$newPath,0707);
                    }
                }
            }
            @copy($oldFile, $userBaseDirectory.$newFile);
    
        }
    }

    // 특정 테이블 컬럼 추출 
    private function getCols($tbl){
        
        $cols = array();
        
        $query = sprintf("SHOW COLUMNS FROM %s", $tbl);
        $rows = $this->getAssoc($query);


        foreach($rows as $r){
            if($r["Field"]!='uid') $cols[]= $r["Field"]; // uid 제외한 모든 컬럼 지정
        }

        return $cols;
    }

    // 특정 테이블에 특정 값 입력  
    private function insert($data){
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

        $this->db->query($insertSQL);

        $last_uid = getDbCnt($table,'max(uid)','');

        return $last_uid;

    } 

    // ####################################  응답 그룹 복사 끝 #################################################
   

}