<?php
include_once __DIR__.'/chatgpt.class.php';

class Chatbot extends Module_base{
	public $db;					// Database Property
    public $recnum =10;
    public $vendor;
    public $botid;
    public $botuid;
    public $dialog;
    public $input;
    public $user_input;
    public $theme_name;
    public $q_StartTime;
    public $r_qTime; // 답변시 질문시작 시간
    public $r_currentTime; // 답변시 현재시간
    public $r_diff; // 답변시 현재시간-질문시작 시간
    public $r_SP; // 유사도
    public $cmod;// cs(웹) or 영화추천(앱)
    public $roomToken;
    public $last_chat; // 최근 사용자 입력값 Log uid ( chatLog 테이블 uid , botChatLog 테이블 chat 값)
    public $match_mod; // 매칭 모드 : 어느단계에서 매칭이 되었는가?  basic,search...
    public $showTimer;
    public $qry_token;
    public $debug; // 디버그시 체크 값
    public $context = array(); // array(캠퍼스명,과정명,학과명) 저장
    public $mbruid;
    public $intentTrainData; // intent 학습 데이타 init chatBox 시 세팅
    public $callIntent = '인텐트';
    public $callEntity = '엔터티';
    public $callContext = '컨텍스트';
    public $NFO = ':!,:,::!,::,<=,>=,>,<,!='; // Node Filter Operator
    public $if_MultiMenu_guideText = '본 조건 해당시 액션 및 컨텍스트 할당';
    public $MultiMenu_guideText = '본 메뉴 선택시 액션 및 컨텍스트 할당';
    public $botks = 'botks';
    public $TDNL = array(); // tracking dialog node log
    public $pesonlp = "/usr/local/bin/pesonlp";
    public $intentMV = 0.6;
    public $intentTokenType = 'morph';
    public $mecab_dic = "/usr/local/lib/mecab/dic/mecab-ko-dic";
    public $faqMV = 0.6;
    public $userId;
    public $bot_skin;
    public $nThumbWidth = 800;
    public $channel;
    public $bottype;
    public $botActive;
    public $accessToken;
    public $fromPhone;
    public $cgroup;
    public $aSysIntentKeyword = array(
        '시스템-반복'=>array('다시', '한번', '한 번', '말해', '뭐라', '안들'),
        '시스템-통화종료'=>array('종료', '그만', '끊을', '끊어', '끊겠', '괜찮'),
        '시스템-날짜시간변경'=>array('바꿔', '바꿀', '날짜', '변경', '시간', '요일', '다른', '가능'),
        '시스템-시간문의'=>array('오전', '오후', '가능', '시간', '안되', '안돼', '몇시')
    );
    public $shopApiVendor = array(
        'cafe24' => array('client_id'=>'iUcEHnFTDhShRccWMNFKaD', 'client_secret'=>'nznYgO1AEdJviB6UDQ0leE'),
        'godo' => array('client_id'=>'JThGJTdGJTA3JUNDJUM0JThFJUU3JUY0')
    );
    public $csChatAPIs = array(
        'nexus' => array('name'=>'넥서스', 'url'=>'http://dev.bottalks.co.kr/api/v1'),
    );

    function console_log($data){
        print_r($data);
    }

    // 외부 url 리턴
    function getExUrl($access_mod){
        global $g;

        $url_array = array(
            "mediExamAdm" => "http://medical.bottalks.co.kr",
            "compManual" => "http://emanual.bottalks.co.kr",
            "reserveAdm" => "http://erpbottalks.co.kr/chatbotApi/auth", // 병원 예약 관리
            //"ondaAdm" => "https://web.hpms.tport.dev",
            "ondaAdm" => "https://app.wave.onda.me",
            "addChatbot" => $g['front_host']."/?r=&m=service&front=mypage&page=mypay",
            "userInfo"=> $g['front_host']."/?r=bts&m=service&front=mypage&page=profile",
        );
        return $url_array[$access_mod];
    }
    function getAPIUrl($access_mod){
        $url_array = array(
            "erpbottalks" => "erpbottalks.co.kr/botApi/reserve",
            "onda" => "https://api.hpms.onda.me",
        );
        return $url_array[$access_mod];
    }

    // 챗봇 계약정보
    function getBotContractInfo($data){
        $id = $data['id'];
        $bot_array = $_SESSION['bot_info'];
        $this_bot = array();
        foreach($bot_array as $bot){
            if($bot['botid'] == $id){
                $this_bot = $bot;
                break;
            }
        }
        return $this_bot;
    }

    function getRoleTypeName($name){
        $roleType = $_SESSION['roleType'];

        if($roleType=='topic') $result = str_replace('챗봇','토픽',$name);
        else if($_SESSION['bottype'] == 'call') $result = str_replace('챗봇','콜봇',$name);
        else $result = $name;
        return $result;
    }

    function controlTopic($data){
        global $table,$date;

        $d_regis = $date['totime'];

        $tbl = $table[$this->module.'dialog'];
        $tbl_bot = $table[$this->module.'bot'];
        $tbl_node = $table[$this->module.'dialogNode'];

        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $act = $data['act']?$data['act']:'get-topic';
        $name = $data['name'];
        $uid = $data['uid'];
        $addMethod = $data['addMethod']; // 추가방식

        $result = array();
        $result['error'] = false;
        $result['act'] = $act;
        $base_wh = 'vendor='.$vendor.' and bot='.$bot;

        if($act =='add-topic'){
            if($addMethod=='temp'){
                $topic = new topicTemp();

                // aramjo bottalks 시스템일 경우 적용
                $topic->dbTargetMod = 'sys';
                $topic->dbTargetServer = 'sys.chatbot';

                $data['targetBot'] = $data['tempUid'];
                $topic->copyTopic($data);

            }else{
                $intro = $data['intro']?$data['intro']:'';
                $MAXG = getDbCnt($tbl,'max(gid)',$base_wh);
                $gid = $MAXG+1;
                $type = 'T'; // topic 의미

                $QKEY="type,gid,name,intro,active,vendor,bot,d_regis, o_botuid";
                $QVAL="'$type','$gid','$name','$intro','1','$vendor','$bot','$d_regis','-1'";
                getDbInsert($tbl,$QKEY,$QVAL);
            }

            $result['last_topic'] = getDbCnt($tbl,'max(uid)','');

            // 봇 자체 토픽 생성일 경우 Welcome, 시작 노드 등록
            if($addMethod == 'blank') {
                $QKEY = "gid,isson,parent,depth,id,name,vendor,bot,dialog";
                $QVAL = "1,1,0,0,1,'Welcome','$vendor','$bot','".$result['last_topic']."'";
                getDbInsert($tbl_node,$QKEY,$QVAL);

                $QKEY = "gid,isson,parent,depth,id,name,vendor,bot,dialog";
                $QVAL = "1,0,1,1,2,'시작','$vendor','$bot','".$result['last_topic']."'";
                getDbInsert($tbl_node,$QKEY,$QVAL);
            }

            // 토픽 추가 시 모델 재학습
            $this->getTrainIntentPesoNLP($data);

        }else if($act =='delete-topic'){
            $topic = new topicTemp();
            $topic->deleteTopic($data);

            // 토픽 삭제 시 모델 재학습
            $this->getTrainIntentPesoNLP($data);

        }else if($act =='get-topic'){
            $RCD = getDbArray($tbl,$base_wh,'*','gid','asc','',1);
            $result['topicArray'] = array();
            while ($R = db_fetch_array($RCD)) {
                $gid = $R['gid'];
                if($R['type'] == 'D') {
                    $name = $R['name'] ? $R['name'] : '메인 그래프';
                } else {
                    $name = $R['name'] ? $R['name']:'토픽 '.($gid+1);
                }
                $name = ($R['type']=='T' && $R['active']==0 ? $name. '(기간만료)' : $name);
                $dialogType = $R['type']=='D'?'default':'topic';

                $topic_url = $R['active'] ? '/adm/graph?dialog='.$R['uid'].'&type='.$dialogType : '#';

                if($R['active']){
                    $topic_hiddenClass = '';
                    $state_label = 'show';
                    $showHide_label = '숨김';
                }else{
                    $topic_hiddenClass = 'topic-hidden';
                    $state_label = 'hide';
                    $showHide_label = '노출';
                }
                $result['topicArray'][] = array(
                    "uid"=>$R['uid'],
                    "gid"=>$gid,
                    "type"=>$R['type'],
                    "name"=>trim($name),
                    "topic_hidden"=>$topic_hiddenClass,
                    "topic_url"=>$topic_url,
                    "topic_readonly"=>($R['active'] ? '' : 'readonly'),
                    "state_label"=>$state_label,
                    "showHide_label"=>$showHide_label,
                    "act"=>$act,
                    "deletable"=>($R['o_botuid'] ? 1 : 0),
                );
            }

            // 토픽 템플릿
            $RCD = getDbSelect($tbl_bot,"role='topic' and is_temp=1 and active=1",'uid,vendor,name');
            while($R = db_fetch_array($RCD)){
                $result['topicTemp'][] = array("bot"=>$R['uid'],"vendor"=>$R['vendor'],"name"=>$R['name']);
            }

        }else if($act=='update-topicOrder'){
            $dialog_arr = $data['dialog_arr'];
            $i = 0;
            foreach ($dialog_arr as $dialog) {
                getDbUpdate($tbl,'gid='.$i,'uid='.$dialog);
                $i++;
            }
        }else if($act=='update-topicName'){
            $name = $data['name'];
            $dialog = $data['dialog'];
            getDbUpdate($tbl,"name='".$name."'",'uid='.$dialog);

        }else if($act =='get-temp'){
            $RCD = getDbSelect($tbl_bot,"role='topic' and is_temp=1 and active=1",'uid,vendor,name');
            while($R = db_fetch_array($RCD)){
                $result[] = array("uid"=>$R['uid'],"vendor"=>$R['vendor'],"name"=>$R['name']);
            }
        }
        return $result;
    }

    // 레거시 api, req 삭제
    function deleteLegacy($data){
        global $table;

        $m = $this->module;

        $delType = $data['delType'];

        $tbl_apiList = $table[$m.'apiList'];
        $tbl_apiReq = $table[$m.'apiReq'];
        $tbl_apiReqParam = $table[$m.'apiReqParam'];

        if($delType=='api'){
            $api = $data['api'];
            $ACD = getDbSelect($tbl_apiList,'uid='.$api,'uid'); // api 추출
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
        }else if($delType=='req'){
            $req = $data['req'];
            $RCD = getDbSelect($tbl_apiReq,'uid='.$req,'uid'); // req 추출
            while($R = db_fetch_array($RCD)){
                $PCD = getDbSelect($tbl_apiReqParam,'req='.$R['uid'],'uid'); // uid 추출
                while($P = db_fetch_array($PCD)){
                    getDbDelete($tbl_apiReqParam,'uid='.$P['uid']);
                }
                getDbDelete($tbl_apiReq,'uid='.$R['uid']);
            }
        }
    }

    // 가이드 텍스트 추출
    function getGuideTxt($data,$key){
        global $g,$d,$TMPL;

        require_once $g['path_module'].$this->module.'/var/guide.txt.php';

        // guide text 세팅
        $page = $data['page'];
        $TMPL['guide_txt'] = $d['guide_'.$page][$key];

        if($key=='common') $guide_markup = new skin('vendor/guide_common');
        else if($key=='needName') $guide_markup = new skin('vendor/guide_warning');
        else $guide_markup = new skin('vendor/guide_default');

        $result = $guide_markup->make();
        return $result;
    }

    function controlTagFA($data){
        global $table;

        $tbl = $table[$this->module.'moniteringFA'];

        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $act = $data['act'];
        $tagName = $data['tagName'];
        $uid = $data['uid'];

        $result = array();
        $result['error'] = false;
        $result['act'] = $act;
        $base_wh = 'vendor='.$vendor.' and bot='.$bot;

        if($act =='add-tagFA'){
            $R = getDbData($tbl,"fa='".$tagName."'",'uid');
            if($R['uid']){
                $result['error'] = true;
                $result['error_msg'] = '이미 등록되었습니다.';
            }else{
                $MAXG = getDbCnt($tbl,'max(gid)',$base_wh);
                $gid = $MAXG+1;

                $QKEY="gid,vendor,bot,fa,hit";
                $QVAL ="'$gid','$vendor','$bot','$tagName','1'";
                getDbInsert($tbl,$QKEY,$QVAL);

                $result['uid'] = getDbCnt($tbl,'max(uid)','');
            }

        }else if($act =='del-tagFA'){
            getDbDelete($tbl,'uid='.$uid);
        }else if($act =='get-tagFA'){
            $RCD = getDbArray($tbl,$base_wh,'uid,fa','gid','desc','',1);
            $result['listArray'] = array();
            while ($R = db_fetch_array($RCD)) {
                $result['listArray'][] = array("uid"=>$R['uid'], "tagName"=>$R['fa']);
            }
        }
        return $result;
    }

    // 응답 힌트 출력 : 사용자 입력문장 기준 해당 온톨로지 검색결과 출력
    function getResponseHint($data){
        global $table;
        $m = $this->module;

        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $msg = $data['clean_input'];

        $E = $this->getSentenceEntity($data);

        if($E['has_entity']){
            $entityArray = $E['entity_val'];
            $UL_open ='<ul class="list-group">';
            $hintList = '';
            foreach ($entityArray as $entityData) {
                $entityVal_name = $entityData[3];
                $entityName = $entityData[5];

                $_wh = "vendor='".$vendor."' and bot='".$bot."' and resType='text' and content like '%".$entityVal_name."%'";
                $RCD = getDbSelect($table[$m.'dialogResItem'],$_wh,'content');
                while($R = db_fetch_array($RCD)){
                    $hintList.= '<li class="list-group-item">'.$R['content'].'</li>';
                }

                $_wh = "vendor='".$vendor."' and bot='".$bot."' and resType='text' and text_val like '%".$entityVal_name."%'";
                $RCD = getDbSelect($table[$m.'dialogResItemOC'],$_wh,'text_val');
                while($R = db_fetch_array($RCD)){
                    $hintList.= '<li class="list-group-item">'.$R['text_val'].'</li>';
                }
            }
            $UL_end='</ul>';
        }

        if($hintList!=''){
            $result = $UL_start.$hintList.$UL_end;
        }
        else $result = '<p>응답 힌트를 찾지 못했습니다.</p>';
        return $result;
    }


    /*
        dialog 기준 저장된 context 추출
        1) dialogNode > context 필드
        2) dialogResItemOC > resType = context 인 text_val 필드
    */
    function getDialogContextSet($data){
        $ctx_array = explode(',',$data['ctx_string']);
        $result = array();
        foreach ($ctx_array as $ctx_set){
            $ctx_arr = explode('|',$ctx_set);
            $result[] = $ctx_arr[0];
        }
        return $result;
    }

    function getContextData($data){
        global $table;

        $ctx_arr =array();
        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $dialog = $data['dialog'];

        $m = $this->module;
        $tbl_node = $table[$m.'dialogNode'];
        $tbl_item = $table[$m.'dialogResItem'];
        $tbl_itemOC = $table[$m.'dialogResItemOC'];
        $tbl_botSet = $table[$m.'botSettings'];

        $_wh = 'vendor='.$vendor.' and bot='.$bot.' and dialog='.$dialog;
        $NCD = getDbSelect($tbl_node,$_wh,'context');
        while($N = db_fetch_array($NCD)){
            if($N['context']){
                $_dd = array("ctx_string"=>$N['context']);
                $ctx_set = $this->getDialogContextSet($_dd);
                array_push($ctx_arr,$ctx_set);// key|val,key1|val1...
            }
        }

        $_table = $tbl_item." A left join ".$tbl_itemOC." B on B.item = A.uid and B.resType = 'context' ";
        $_where = "A.vendor='".$vendor."' and A.bot='".$bot."' and A.dialog='".$dialog."' and (B.text_val is not null and B.text_val <> '')";
        $OCD = getDbSelect($_table,$_where,'A.uid, B.text_val');
        while($O = db_fetch_array($OCD)){
            if($O['text_val']){
                $_dd = array("ctx_string"=>$O['text_val']);
                $ctx_set = $this->getDialogContextSet($_dd);
                array_push($ctx_arr,$ctx_set);// key|val,key1|val1...
            }
        }

         // botSettings 에 기본 컨텍스트값 추출
        $_dwh ="vendor='".$vendor."' and bot='".$bot."' and name='default_context'";
        $DC = getDbData($tbl_botSet,$_dwh,'value');
        $ctxArray = explode(',',$DC['value']);

        $_dd = array("ctx_string"=>rtrim($DC['value'],','));
        $ctx_set = $this->getDialogContextSet($_dd);
        array_push($ctx_arr,$ctx_set);// key|val,key1|val1...

        if(count($ctx_arr)>0) $result['content'] = $ctx_arr;
        else $result['content'] = array(array('데이터 없음'));

        return $result;
    }

    // access token 으로 bot 데이타 리턴
    function getBotDataFromAT($data){
        global $table;

        $access_mod = $data['access_mod'];
        $at_arr = explode('_',$data['access_token']);

        $result = array();
        $_now = time();
        $live_q = "access_mod='".$access_mod."' and access_token='".$data['access_token']."' and expire>'".$_now."'";
        $T = getDbData($table[$this->module.'token'], $live_q, '*');

        if($T['uid']){
            $bot = $T['bot'];

            $query = "Select A.name as botname, A.id as bot_id, B.name as company, B.email, C.value as domain From ".$table[$this->module.'bot']." A ";
            $query .="left join ".$table[$this->module.'vendor']." B on A.vendor = B.uid ";
            $query .="left join ".$table[$this->module.'botSettings']." C on A.vendor = C.vendor and A.uid = C.bot and C.name = 'reserve_domainkey' ";
            $query .="Where A.uid = '".$bot."' ";
            $content = $this->getAssoc($query);
            $content[0]['company'] = $content[0]['company'] ? $content[0]['company'] : $content[0]['botname'];

            $result['status'] = 'success';
            $result['result'] = array("type"=>"object","content"=>$content);
        }else{
            $result['status'] = 'fail';
            $result['result'] = array("type"=>"text","content"=>"잘못된 접근입니다.");
        }
        return $result;
    }

    // bot access token 등록
    function setBotAccessToken($data){
        global $table, $g;

        $tbl = $table[$this->module.'token'];
        $bot = $data['bot'];
        $access_mod = $data['access_mod'];

        $_data = array();
        $_data['src'] = 'AN';
        $_data['len'] =  100;
        $ranString = $this->getRandomString($_data);

        $_hostenc = str_replace("=", "", base64_encode(($g['https_on'] ? "https" : "http")."://".$_SERVER['HTTP_HOST']));
        $access_token = $ranString.'_'.$_hostenc.'_'.($_SESSION['mbr_uid'] ? $_SESSION['mbr_uid'] : '0');

        $_now = time();
        $expire = time()+60*60; // 1시간

        // 유효기간 지난것 삭제
        $del_q = "bot='".$bot."' and access_mod='".$access_mod."' and expire<'".$_now."'";
        getDbDelete($tbl,$del_q);

        $is_q = "bot='".$bot."' and access_mod='".$access_mod."' and expire>'".$_now."'";
        $is_token = getDbData($tbl,$is_q,'uid');
        $update_q = "access_token='$access_token',expire='$expire'";

        if($is_token['uid']) getDbUpdate($tbl,$update_q,'uid='.$is_token['uid']);
        else{
            // 신규 토큰 저장
            $QKEY ="bot,access_mod,access_token,expire";
            $QVAL ="'$bot','$access_mod','$access_token','$expire'";
            getDbInsert($tbl,$QKEY,$QVAL);
        }
        return $access_token;
    }

    function getMethodLabel($method){
        $result = array(
            "GET"=>"primary",
            "POST"=>"success",
            "DEL"=>"danger",
            "PUT"=>"warning",
        );
        return $result[$method];
    }

    // vendor 관리자 페이지 > 템플릿 데이타 응답 UI
    function getVendorResponseHtml($R){
        global $table;

        // vendor 정보
        $data = $R['data'];
        $vendor = $data['vendor'];
        $bot = $data['bot'];

        // template 정보
        $resType = $R['resType'];
        $item_type = $R['item_type'];
        $tbl_name = $item_type =='RI'?'dialogResItem':'dialogResItemOC'; // OC or
        $o_uid = $R['item_uid'];

        $wh = 'vendor='.$vendor.' and bot='.$bot.' and uid='.$o_uid;
        $tbl = $table[$this->module.$tbl_name];
        $row = getDbData($tbl,$wh,'*');

        $html ='';

        $data_group = 'data-uid="'.$row['uid'].'" data-itemtype="'.$item_type.'" data-resType="'.$resType.'"';

        if($resType =='text' || $resType=='link' || $resType=='tel'){
            if($item_type =='RI') $content = $row['content']; // dialogResItem
            else if($item_type =='OC'){
                if($resType =='text') $content = $row['text_val']; // dialogResItemOC
                else $content = $row['varchar_val']; // dialogResItemOC
            }

            $html.='
            <input type="text" name="vendorResponse[]" value="'.$content.'" '.$data_group.' class="form-control form-control-line">
            ';
        }else if($resType =='img'){
            if($item_type =='RI') $img_url = $row['img_url']; // dialogResItem
            else $img_url = $row['varchar_val']; // dialogResItemOC

            if($img_url) $res_backimg = 'style="background: url('.$img_url.') no-repeat center top;background-size: cover"';
            else $res_backimg = 'style="background: url('.$img_url.') no-repeat center top;"';

            $html.='
            <div class="card">
               <input type="hidden" name="vendorResponse[]" data-role="img_url" value="'.$img_url.'" '.$data_group.'>
               <div class="card-img-wrapper" data-role="self-uploadImg" '.$res_backimg.' ></div>
            </div>
            ';
        }
        return $html;
    }

    // 레거시 api 파라미터 세팅
    function saveLegacyApiParam($data){
        global $table;

        $row = $data['row'];
        $prefix = $data['prefix'];
        $paramName = $row[$prefix.'ParamName'];
        $paramVal = $row[$prefix.'ParamVal'];
        $paramUid = $row[$prefix.'ParamUid'];
        $req = $data['req'];
        $mod = $data['mod'];
        $api = $data['api'];
        $itemOC = $data['itemOC'];
        $tbl_param = $data['tbl_param'];
        $prefixToPS = array("q"=> "query","p"=> "path","h"=> "header","f"=> "form");

        $_data = array();
        $_data['api'] = $api;

        // 파라미터 업데이트
        foreach ($paramName as $index => $name) {
            $varchar_val = $paramVal[$index];
            $uid = $paramUid[$index];
            $_wh = 'uid='.$uid.' ';
            $_wh .=($itemOC ? 'and itemOC='.$itemOC : '');
            $is_param = getDbRows($tbl_param,$_wh);
            if($uid && $is_param){
                $QVAL="name='$name',varchar_val='$varchar_val'";
                if($name) getDbUpdate($tbl_param,$QVAL,'uid='.$uid);

            }else{
                $_data['req'] = $req;
                $_data['name'] = $name;
                $_data['position'] = $prefixToPS[$prefix];//'header';
                $_data['varchar_val'] = $varchar_val;
                $_data['tbl_param'] = $tbl_param;
                $_data['itemOC'] = $itemOC;

                // 파라미터 신규 등록
                if($name) $this->regisApiParam($_data);
            }
        }
    }

    // 각 response 와 상관 없이 관리자 > legacy 페이지에서 저장된 req
    function getApiReqParamData($data){
        global $table;

        $tbl_api = $table[$this->module.'apiList'];
        $tbl_req = $table[$this->module.'apiReq'];
        $tbl_param = $table[$this->module.'apiReqParam'];

        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $api = $data['api'];
        $req = $data['req'];

        $RQ = getDbData($tbl_req,'uid='.$req,'*'); // apiReq data
        $RCD = getDbArray($tbl_param,'req='.$req,'*','uid','asc','',1);

        $result = array();
        $result['api'] = $api;
        $result['req'] = $req;
        $result['name'] = $RQ['name'];
        $result['description'] = $RQ['description'];
        $result['base_path'] = $RQ['base_path'];
        $result['method'] = $RQ['method'];
        $result['bodyType'] = $RQ['bodyType'];

        if($RQ['bodyType'] =='text'){
            $b_wh = "api='".$api."' and req='".$req."' and position='body'";
            $BD = getDbData($tbl_param,$b_wh,'uid,text_val');
            $result['bodyVal'] = $BD['text_val'];
            $result['bodyUid'] = $BD['uid'];
        }

        $result['qParamName'] = array();
        $result['qParamVal'] = array();
        $result['qParamUid'] = array();
        $result['hParamName'] = array();
        $result['hParamVal'] = array();
        $result['hParamUid'] = array();
        $result['pParamName'] = array();
        $result['pParamVal'] = array();
        $result['pParamUid'] = array();
        $result['fParamName'] = array();
        $result['fParamVal'] = array();
        $result['fParamUid'] = array();

        // 파라미터 세팅
        while($R = db_fetch_assoc($RCD)){
            $ps = $R['position'];

            if($ps == 'query'){
                $result['qParamName'][] = $R['name'];
                $result['qParamVal'][] = $R['varchar_val'];
                $result['qParamUid'][] = $R['uid'];
            }else if($ps=='header'){
                $result['hParamName'][] = $R['name'];
                $result['hParamVal'][] = $R['varchar_val'];
                $result['hParamUid'][] = $R['uid'];
            }else if($ps=='path'){
                $result['pParamName'][] = $R['name'];
                $result['pParamVal'][] = $R['varchar_val'];
                $result['pParamUid'][] = $R['uid'];
            }else if($ps=='form'){
                $result['fParamName'][] = $R['name'];
                $result['fParamVal'][] = $R['varchar_val'];
                $result['fParamUid'][] = $R['uid'];
            }
        }
        return $result;
    }


    // api output 처리 (저장/수정)
    function controlApiOutput($data){
        global $table;

        $tbl = $table[$this->module.'dialogResApiOutput'];

        $apiData = $data['apiData'];
        $_tmp = stripcslashes($apiData);
        $row = json_decode($_tmp,true);
        $act = $data['act'];
        $mod = $data['mod'];
        $itemOC = $row['itemOC'] ? $row['itemOC'] : $data['itemOC'];

        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $base_wh = 'vendor='.$vendor.' and bot='.$bot.' and itemOC='.$itemOC;
        // 텍스트 답변 저장
        if(isset($row['apiTextVal'])){
            $resType = 'text';
            $text_val = $row['apiTextVal'];
            $text_wh = $base_wh." and resType='text'";
            $R = getDbData($tbl,$text_wh,'uid');

            if($R['uid']){
                $QVAL="text_val='$text_val'";
                getDbUpdate($tbl,$QVAL,'uid='.$R['uid']);
            }else{
                $g_wh = $base_wh." and resType='.$resType.'";
                $MAXG = getDbCnt($tbl,'max(gid)',$g_wh);
                $gid = $MAXG+1;
                $QKEY ="vendor,bot,itemOC,gid,resType,text_val,o_uid";
                $QVAL ="'$vendor','$bot','$itemOC','$gid','$resType','$text_val','$o_uid'";
                getDbInsert($tbl,$QKEY,$QVAL);
            }
        }
        // 콘텍스트 답변 저장
        $aContextVal_uid = array();
        if(isset($row['apiContextName']) && isset($row['apiContextVal'])){
            for($i=0,$nCnt=count($row['apiContextName']); $i<$nCnt; $i++) {
                $contextName = trim($row['apiContextName'][$i]) ? trim($row['apiContextName'][$i]) : '';
                $contextVal = trim($row['apiContextVal'][$i]) ? trim($row['apiContextVal'][$i]) : '';
                if($contextName && $contextVal) {
                    $context_val = $contextName."|".$contextVal;
                    $_wh = $base_wh." and resType='context' and text_val like '".$contextName."|'";
                    $R = getDbData($tbl, $_wh, 'uid');
                    if($R['uid']) {
                        $aContextVal_uid[] = $R['uid'];
                        getDbUpdate($tbl, "text_val='".$context_val."'", "uid=".$R['uid']);
                    } else {
                        $QKEY ="vendor,bot,itemOC,gid,resType,text_val,o_uid";
                        $QVAL ="'$vendor','$bot','$itemOC','1','context','$context_val','$o_uid'";
                        getDbInsert($tbl,$QKEY,$QVAL);
                        $uid=getDbCnt($tbl,'max(uid)','');
                        $aContextVal_uid[] = $uid;
                    }
                }
            }
        }
        if(count($aContextVal_uid) > 0) {
            $aContextVal_uid = implode(",", $aContextVal_uid);
            $_wh = $base_wh." and resType='context' and uid not in (".$aContextVal_uid.")";
            getDbDelete($tbl,$_wh);
        } else {
            $_wh = $base_wh." and resType='context'";
            getDbDelete($tbl,$_wh);
        }
    }

    // vendor 관리자 페이지 > 레거시 > api 추가/수정
    function controlLegacyApiData($data){
        global $table;

        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $tempBot = $data['tempBot'];
        $act = $data['act'];
        $mod = $data['mod'];
        $itemOC = $data['itemOC']; // 대화상자 > api 설정 버튼 클릭한 경우

        // apiData 값 추출
        /*
          만약, dialog 모드에서 param 값을 추가할 수 있게 하면
          $apiData = $data['apiData'] 즉, 해당 페이지에서 생성된 값으로 해야 한다.

          dialog > 답변 설정시 param 부분에 @엔터티, $컨텍스트를 대입할 수 있게 하기 위해서
          테스트는 기존 저장된 내용 사용하고 실제 답변 출력시 @,#,$ 적용된 파라미터로 api 진행.
        */
        if($mod=='dialog' && $act !='save'){
            $row = $this->getApiReqParamData($data); // dialog 모드에서 테스트할때
        }
        else{
            // admin > legacy 페이지에서 최초 등록할때
            $apiData = $data['apiData'];
            $_tmp = stripcslashes($apiData);
            $row = json_decode($_tmp,true);
        }
        $api = $data['api']?$data['api']:$row['api'];
        $req = $data['req']?$data['req']:$row['req'];
        $name = $row['name'];
        $description = $row['description'];
        $base_path = $row['base_path'];
        $method = $row['method'];
        $statusCode = $row['statusCode'];
        $bodyType = $row['bodyType'];
        $bodyVal = $row['bodyVal'];
        $bodyUid = $row['bodyUid'];

        // 테이블 정의
        $tbl_api = $table[$this->module.'apiList'];
        $tbl_req = $table[$this->module.'apiReq'];
        // tbl_param 선택 : dialog 모드에서는 dialogResApiParam 테이블에 있는지 먼저 체크
        if($act=='get'){
            if($mod == 'dialog' && $itemOC){
                $is_apply = getDbRows($table[$this->module.'dialogResApiParam'],'itemOC='.$itemOC.' and api='.$api.' and req='.$req);
                if($is_apply) $tbl_param = $table[$this->module.'dialogResApiParam'];
                else $tbl_param = $table[$this->module.'apiReqParam'];
            }
            else $tbl_param = $table[$this->module.'apiReqParam'];
        }else if($act=='test'){
            $tbl_param = $table[$this->module.'apiReqParam'];
        }else if($act =='save'){
            if($mod =='dialog') $tbl_param = $table[$this->module.'dialogResApiParam'];
            else $tbl_param = $table[$this->module.'apiReqParam'];
        }else if($act=='delete') {
            $tbl_param = $table[$this->module.'apiReqParam'];
        }

        // adm > legacy
        if($act=='getApiListData'){
            $_wh = 'a.vendor='.$vendor.' and a.bot='.$bot;

            $_query = "SELECT r.uid,r.api,r.name FROM %s as r left join %s as a on r.api=a.uid WHERE %s GROUP BY r.uid ORDER BY r.uid ASC";
            $query = sprintf($_query,$tbl_req,$tbl_api,$_wh);
            $rows = $this->getAssoc($query);
            $result = array();
            foreach ($rows as $row) {
                $result[] = array("uid"=>$row['uid'],"api"=>$row['api'],"name"=>$row['name']);
            }

        }else if($act == 'get'){

            $req = $data['req'];
            $result = array();
            $result['apiReq'] = array(); // apiReq 테이블 데이타
            $result['apiOutput'] = array(); // dialogResApiOutput 테이블 데이타
            $result['header'] = array();
            $result['query'] = array();
            $result['path'] = array();
            $result['form'] = array();
            $result['body'] = array();

            // req
            $ACD = getDbSelect($tbl_req,'uid='.$req,'*'); // apiReq data
            while($A=db_fetch_assoc($ACD)){
                $result['apiReq'][] = $A;
            }

            // param
            $RCD = getDbArray($tbl_param,'req='.$req,'*','uid','asc','',1);
            while($R = db_fetch_assoc($RCD)){
                $result[$R['position']][]= $R;
            }

            //output
            $opt_wh = 'vendor='.$vendor.' and bot='.$bot.' and itemOC='.$itemOC;
            $tbl_output = $table[$this->module.'dialogResApiOutput'];
            $OCD = getDbArray($tbl_output,$opt_wh,'*','uid','asc','',1);
            while($O = db_fetch_assoc($OCD)){
                $result['apiOutput'][]= $O;
            }

        }else if($act =='save'){
            $_data = array();
            $_data['api'] = $api;

             // 각 position 별 save/update
            $dd = array();
            $dd['row'] = $row;
            $dd['mod'] = $mod;
            $dd['api'] = $api;
            $dd['tbl_param'] = $tbl_param;
            $dd['itemOC'] = $itemOC;

            // api output 저장
            $this->controlApiOutput($data);

            if($req){
                //if($mod=='dialog'){
                    // apiReq 업데이트
                    $QVAL="name='$name',description='$description',base_path='$base_path',method='$method',statusCode='$statusCode',bodyType='$bodyType'";
                    getDbUpdate($tbl_req,$QVAL,'uid='.$req);
                //}

                $dd['req'] = $req;

                // header 파라미터 업데이트
                $dd['prefix'] = "h";
                $this->saveLegacyApiParam($dd);

                // query 파라미터 업데이트
                $dd['prefix'] = "q";
                $this->saveLegacyApiParam($dd);

                // path 파라미터 업데이트
                $dd['prefix'] = "p";
                $this->saveLegacyApiParam($dd);

                // form 파라미터 업데이트
                $dd['prefix'] = "f";
                $this->saveLegacyApiParam($dd);

                // body 값 저장
                if($bodyVal) {
                    if($bodyUid) {
                        $is_bodyParam = getDbRows($tbl_param,'uid='.$bodyUid);
                        if($is_bodyParam){
                            $QVAL="text_val='$bodyVal'";
                            if($bodyVal) getDbUpdate($tbl_param,$QVAL,'uid='.$bodyUid);
                        }
                    }else{
                        $_data['req'] = $req;
                        $_data['name'] = 'body';
                        $_data['position'] = 'body';
                        $_data['text_val'] = $bodyVal;
                        $_data['tbl_param'] = $tbl_param;
                        $_data['itemOC'] = $itemOC;

                        // 파라미터 신규 등록
                        if($bodyVal) $this->regisApiParam($_data);
                    }
                }

                $result['mod'] = 'update';
                $result['req'] = $req;

            }else{
                // apiReq 등록
                $QKEY = "api,name,description,base_path,method,statusCode,bodyType";
                $QVAL = "'$api','$name','$description','$base_path','$method','$statusCode','$bodyType'";
                getDbInsert($tbl_req,$QKEY,$QVAL);

                $req = getDbCnt($tbl_req,'max(uid)','');

                $dd['req'] = $req;

                // header 파라미터 업데이트
                $dd['prefix'] = "h";
                $this->saveLegacyApiParam($dd);

                // query 파라미터 업데이트
                $dd['prefix'] = "q";
                $this->saveLegacyApiParam($dd);

                // path 파라미터 업데이트
                $dd['prefix'] = "p";
                $this->saveLegacyApiParam($dd);

                // form 파라미터 업데이트
                $dd['prefix'] = "f";
                $this->saveLegacyApiParam($dd);

                // body 값 저장
                $_data['req'] = $req;
                $_data['name'] = 'body';
                $_data['position'] = 'body';
                $_data['text_val'] = $bodyVal;
                $_data['tbl_param'] = $tbl_param;
                $_data['itemOC'] = $itemOC;

                // 파라미터 신규 등록
                if($bodyVal) $this->regisApiParam($_data);

                $result['mod'] = 'new';
                $result['req'] = $req;
            }

        }else if($act=='delete'){
            $uid = $data['uid'];
            getDbDelete($tbl_param,'uid='.$uid);

        }else if($act=='test'){
            $_data = array();
            $_data['contentType'] = 'json';
            $_data['data'] = $row;
            $_data['resType'] = 'body';
            $result = $this->getLegacyApiResult($_data);
        }
        return $result;
    }

    // Legacy Api 전송 함수
    function getLegacyApiResult($data){
        global $g;

        require_once $g['dir_module'].'lib/guzzel/autoloader.php';

        $row = $data['data'];
        $base_path = $row['base_path'];
        $method = $row['method'];
        $qParamName = $row['qParamName']; // 쿼리 파라미터 key
        $qParamVal = $row['qParamVal']; // 쿼리 파라미터 val
        $hParamName = $row['hParamName']; // 헤더 파라미터 key
        $hParamVal = $row['hParamVal']; // 헤더 파라미터 val
        $fParamName = $row['fParamName']; // 폼 파라미터 key
        $fParamVal = $row['fParamVal']; // 폼 파라미터 val
        $pParamName = $row['pParamName']; // 패스 파라미터 key
        $pParamVal = $row['pParamVal']; // 패스 파라미터 val

        $bodyVal = $row['bodyVal']; // body

        $result = array();
        $result['error'] = false;

        $options = array();
        $options['headers'] = array();
        $options['query'] = array();
        $options['form_params'] = array();

        $options['http_errors'] = false;

        // query 추가
        foreach ($qParamName as $index => $name) {
            $varchar_val = $qParamVal[$index];
            if($name) $options['query'][$name] = trim($varchar_val);
        }

        // form param 추가
        foreach ($fParamName as $index => $name) {
            $varchar_val = $fParamVal[$index];
            if($name) $options['form_params'][$name] = trim($varchar_val);
        }

        // header 추가
        foreach ($hParamName as $index => $name) {
            $varchar_val = $hParamVal[$index];
            if($name) $options['headers'][$name] = $varchar_val;
        }

        // path 추가
        $ex_path ='';
        foreach ($pParamName as $index => $name) {
            $value = $pParamVal[$index];
            if($name) $ex_path.= $name.'/'.$value;
        }

        // path 추가
        if($ex_path!=''){
            $base_path = $base_path.$ex_path;
        }

        // echo $base_path;
        // print_r($data);
        // exit;

        // body 추가
        if($bodyVal){
            //$options['body'] = $bodyVal;
            $options['json'] = json_decode($bodyVal);
        }

        $client = new \GuzzleHttp\Client();
        $RQ = $client->request($method,$base_path,$options);

        $result['body'] = $RQ->getBody();
        $result['headers'] = $RQ->getHeaders();
        $result['statusCode'] = $RQ->getStatusCode();
        return $result;
    }


    // 레거시 > 파라미터 등록
    function regisApiParam($data){
        global $table;

        $tbl_param = $data['tbl_param'];//$table[$this->module.'apiReqParam'];
        $api = $data['api'];
        $req = $data['req'];
        $name = $data['name'];
        $itemOC = $data['itemOC'];
        $position = $data['position'];
        $text_val = $data['text_val'];
        $varchar_val = $data['varchar_val'];

        if($tbl_param ==$table[$this->module.'apiReqParam']){
            $QKEY = "api,req,name,position,text_val,varchar_val";
            $QVAL = "'$api','$req','$name','$position','$text_val','$varchar_val'";
        }else if($tbl_param ==$table[$this->module.'dialogResApiParam']){
            $QKEY = "api,itemOC,req,name,position,text_val,varchar_val";
            $QVAL = "'$api','$itemOC','$req','$name','$position','$text_val','$varchar_val'";
        }
        getDbInsert($tbl_param,$QKEY,$QVAL);
    }

    // vendor 관리자 페이지 > 템플릿 데이타 관리
    function controlVendorResponse($data){
        global $table;

        $botTemp = new botTemp();

        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $tempBot = $data['tempBot'];
        $act = $data['act'];
        if($act == 'get'){
            $tbl = $table[$this->module.'tempData'];
            $wh = "vendor='".$vendor."' and bot='".$bot."' and active=1";
            $RCD = getDbArray($tbl,$wh,'*','node asc,','gid asc','',1);
            $html='';
            while($R = db_fetch_array($RCD)){
                $tb_name = $R['item_type'] == 'RI' ? $table[$this->module.'dialogResItem'] : $table[$this->module.'dialogResItemOC'];
                $is_item = getDbRows($tb_name, "vendor=".$vendor." and bot=".$bot." and uid=".$R['item_uid']);
                if($is_item == 0) continue;

                $R['data'] = $data;
                $html.='
                    <div class="form-group">
                        <label class="col-md-12 input resLabel">'.$R['label'].'</label>
                        <div class="col-md-12">
                              '.$this->getVendorResponseHtml($R).'
                        </div>
                    </div>';
            }

            $result = $html;
        }else if($act =='save'){
            foreach ($data['resArray'] as $val) {
                $resItem = stripcslashes($val);
                $row = json_decode($resItem,true);
                $resType = $row['restype'];
                $itemType = $row['itemtype'];
                $uid = $row['uid'];

                $tbl_name = $itemType=='RI'?'dialogResItem':'dialogResItemOC';
                $tbl = $table[$this->module.$tbl_name];
                if($resType == 'img' && strpos($row['content'], '/_tmp/upload') !== false) {
                    $data['file_url'] = $row['content'];
                    $row['content'] = $this->setFileTempToSave($data);
                }
                if($itemType =='RI'){
                    if($resType =='text') $QVAL= "content='".addslashes($row['content'])."'";
                    else if($resType =='img') $QVAL= "img_url='".addslashes($row['content'])."'";

                }else if($itemType =='OC'){
                    if($resType =='text') $QVAL= "text_val='".addslashes($row['content'])."'";
                    else if($resType =='img' || $resType=='link') $QVAL= "varchar_val='".addslashes($row['content'])."'";

                }
                getDbUpdate($tbl,$QVAL,'uid='.$uid);
            }
            $result ='OK';
        }
        return $result;
    }

    // 슈퍼관리자 페이지 > 템플릿 데이타 관리
    function controlTempData($data){
        global $table;

        $linkType = $data['linkType'];
        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $item_type = $data['itemtype']=='resItem'?'RI':'OC';
        $item_uid = $data['itemuid'];
        $label = $data['label'];
        $resType = $data['restype'];
        $node = $data['node'];
        $gid = $data['gid'] ? $data['gid'] : 0;

        $tbl = $table[$this->module.'tempData'];
        $wh = "vendor='".$vendor."' and bot='".$bot."' and item_type='".$item_type."' and item_uid='".$item_uid."'";
        $R = getDbData($tbl,$wh,'*');

        if($linkType == 'addTempData' || $linkType =='editTempData'){

            if($R['uid']){
                $QVAL = "active=1,label='$label',gid='$gid',node='$node'";
                getDbUpdate($tbl,$QVAL,'uid='.$R['uid']);
            }else{
                $_wh = 'vendor='.$vendor.' and bot='.$bot;
                $MAXG = getDbCnt($tbl,'max(gid)',$_wh);
                $gid = $MAXG+1;

                $QKEY ="gid,vendor,bot,active,item_type,item_uid,label,resType,node";
                $QVAL ="'$gid','$vendor','$bot','1','$item_type','$item_uid','$label','$resType','$node'";
                getDbInsert($tbl,$QKEY,$QVAL);

                $last_uid = getDbCnt($tbl,'max(uid)','');
            }

            $result = $R['uid']?$R['uid']:$last_uid;

        }else if($linkType == 'delTempData'){
            $QVAL = "active=0";
            getDbUpdate($tbl,$QVAL,'uid='.$R['uid']);

           $result = $R['uid'];

        }else if($linkType =='getTempData'){
            $result = array();
            if($R['uid']){
                $result['active'] = $R['active']?true:false;
                $result['is_temp'] = true;
                $result['data'] = $R;
            }else{
                $result['is_temp'] = false;
            }
        }else if($linkType =='getTempLabelList'){
            $wh = 'vendor='.$vendor.' and bot='.$bot;
            $RCD = getDbArray($tbl,$wh,'*','node asc,','gid asc','',1);
            $list ='';
            while($R = db_fetch_array($RCD)){
                $label_location = $this->getTempDataLabelLocation($R);
                $list.='
                <li class="dd-item dd3-item" data-uid="'.$R['uid'].'" >
                    <div class="dd-handle dd3-handle"></div>
                    <div class="dd3-content">
                        <span class="label-name">'.$R['label'].'</span>
                        <span class="position">'.$label_location.'</span>
                    </div>
                </li>';
            }

            $result = $list;
        }else if($linkType =='changeTempLabelOrder'){
            foreach ($data['uid_arr'] as $index=>$uid) {
                $QVAL = "gid='$index'";
                getDbUpdate($tbl,$QVAL,'uid='.$uid);
            }

            $result = 'OK';
        }
        return $result;
    }

    function getTempDataLabelLocation($data){
        global $table;

        $item_uid = $data['item_uid'];
        $item_type = $data['item_type'];
        if($item_type =='OC'){
            $OC = getDbData($table[$this->module.'dialogResItemOC'],'uid='.$item_uid,'item');
            $uid = $OC['item'];
        }else if($item_type =='RI'){
            $uid = $item_uid;
        }

        $R = getDbData($table[$this->module.'dialogResItem'],'uid='.$uid,'dialog,node');
        $data['dialog'] = $R['dialog'];
        $data['node'] = $R['node'];

        $nodeCat = $this->getDialogNodeCat($data);
        return $nodeCat;
    }

    // dialog 대화상자 카테고리 추출
    function getDialogNodeCat($data){
        global $table;

        $base_wh = $this->getDialogBaseQry($data);
        $wh = $base_wh.' and id='.$data['node'];
        $R = getDbData($table[$this->module.'dialogNode'],$wh,'parent,name');
        if($R['parent']){
            $data['node'] = $R['parent'];
            $parentNode = $this->getDialogNodeCat($data);
            $result = $parentNode.' > '.$R['name'];
        }
        else $result= $R['name'];
        return $result;
    }

    // 템플릿봇 데이타 추출
    function getBotTempData($data){
        global $table;

        $m = $this->module;
        $vendor = $data['vendor'];
        $_wh = 'is_temp=1';
        $RCD = getDbSelect($table[$m.'bot'],$_wh,'*');
        $result = array();
        while($R = db_fetch_assoc($RCD)){
             $result[] = $R;
        }
        return $result;
    }

    function verify_apiKey($data){
        global $table;

        $headers = $data['headers'];
        $auth = $headers['Authorization'];
        $auth2 = str_replace("==","",str_replace("Basic ","",$auth));
        $id_pw = explode(':',base64_decode($auth2));
        $client_id = $id_pw[0];
        $client_secret = $id_pw[1];
        $access_token = $headers['X-Bottalks-Token'];
        $botId = $data['botId'];
        $tbl = $table[$this->module.'channelSettings'];

        $result = array();
        $result['valid'] = false;
        $result['client_id'] = $client_id;
        $result['client_secret'] = $client_secret;
        $result['access_token'] = $access_token;

        $_wh = "botid='".$botId."' and channel='".$this->botks."'";
        $id_wh = $_wh." and name='client_id' and value='".$client_id."'";
        $secret_wh = $_wh." and name='client_secret' and value='".$client_secret."'";
        $token_wh = $_wh." and name='access_token' and value='".$access_token."'";

        $check_id = getDbRows($tbl,$id_wh);
        if($check_id){
            $check_secret = getDbRows($tbl,$secret_wh);
            if($check_secret){
                 $check_token = getDbRows($tbl,$token_wh);
                 if($check_token) $result['valid'] = true;
            }
        }
        return $result;
    }

     // 랜덤 스트링 추출
    function getRanString($length){
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        return substr( str_shuffle( $chars ), 0, $length );
    }

    // 랜덤 스트링 추출2
    function getRandomString($data){
        $type = $data['type'];
        $src = $data['src'];
        $len= $data['len'];
        $num = "0123456789";

        $res = "";
        if($src == "N") {
            for($i=0;$i<6;$i++) $res .=$num.time();
            $res = str_shuffle($res);
        } else {
            for($i=0; $i<10; $i++) $res .=md5(uniqid(mt_rand(), true));
        }

        if($type == "console_id") {
            $result = $data['botid']."-".substr($res,8,4)."-".substr($res,12,4)."-".substr($res,16,4)."-".substr($res,20,12);
        } else {
            $result = substr($res,0,$len);
        }
        return $result;
    }

    // 챗봇 카테고리 리턴
    function getBotCategory($data){
        global $table;

        $m = $this->module;
        $tbl = $table[$m.'category'];
        $cat = array();
        $RCD = getDbArray($tbl,'uid>1','uid,name','gid','asc','',1);
        while($R = db_fetch_array($RCD)) {
            $cat[] = array("uid"=>$R['uid'],"name"=>$R['name']);
        }
        return $cat;
    }

    // 외부 sns 연동시 .. 음악, 동영상, 오디오, 사진 등 이벤트트에 대한 응답
    function getMediaEventMsg($data){

        $mediaArray = array("image"=>"이미지","audio"=>"오디오","sticker"=>"스티커");
        $mediaType = $mediaArray[$data['type']];
        $msg = $mediaType.' 인식기능은 아직 못배웠어요..ㅠㅠ';
        return $msg;
    }

    // api 로 접근해오는 입력에 대한 응답
    function getApiResponse($data){
        global $g, $table;

        // botId 로 관련 데이타 추출
        $B = $this->getBotDataFromId($data['botId']);

        if(($data['userId'] && $data['chatType'] && $data['chatType'] != 'Q') && $data['roomToken'] == '') {
            $query = "Select roomToken From ".$table[$this->module.'chatLog']." Where vendor='".$B['vendor']."' and bot='".$B['bot']."' and userId='".$data['userId']."' ";
            $query .="and chatType='".$data['chatType']."' Order by uid DESC limit 1";
            $row=$this->getAssoc($query);
            $data['roomToken'] = $row[0]['roomToken'] ? $row[0]['roomToken'] : substr(str_shuffle(md5(uniqid(mt_rand()))),0,20);
        }

        $dt = array();
        $dt['clean_input'] = $data['msg'];
        $dt['botUid'] = $this->botuid = $B['bot'];
        $dt['botid'] = $this->botid = $data['botId'];
        $dt['vendor'] = $this->vendor = $B['vendor'];
        $dt['dialog'] = $this->dialog = $B['dialog'];
        $dt['botActive'] = $this->botActive = $B['botActive'];
        $dt['roomToken'] = $this->roomToken = $data['roomToken'];
        $dt['userId'] = $this->userId = $data['userId'];
        $dt['channel'] = $this->channel = $data['channel'];
        $dt['bottype'] = $this->bottype = $B['bottype'];
        $dt['cgroup'] = $this->cgroup = $B['cgroup'];
        $dt['bot'] = $B['bot'];
        $dt['botId'] = $data['botId'];
        $dt['msg_type'] = $data['msg_type'] ? $data['msg_type'] : 'text';
        $dt['api'] = true;
        $dt['botks_api'] = isset($data['botks_api'])?isset($data['botks_api']):false;
        $dt['cmod'] = $data['cmod'] ? $data['cmod'] : 'channel';
        $dt['node'] = isset($data['node'])?$data['node']:''; // 특정 노드가 지정된 경우
        $dt['chatType'] = $data['chatType'] ? $data['chatType'] : '';

        // 20230712 aramjo chatgpt
        $_SESSION['S_UseChatGPT'] = $B['use_chatgpt'] == 'on' ? true : false;

        // gsitm
        if($dt['cgroup'] == 'gsitm') {
            include_once $g['dir_module'] . "includes/gsitm.class.php";
            $objGSITM = new GSITM($this);
            $dt['objGSITM'] = $objGSITM;

            $response = $objGSITM->getGSITMCheckTokenSendResponse($dt);
            if($response) {
                $result[]= array("type"=>'text',"content"=>$response, "unknown"=>0, "res_end"=>0);
                return $result;
            }
        }

        // aramjo context load
        $this->getBotContext($dt);

        $result = array();

        if(isset($data['node'])){
            $response = $this->getNodeRespond($dt);
        }else{
            $process = $this->ProcessInput($dt);
            $response = $process['response'];
        }
        $unknown = $process['unknown'] ? 1 : 0;
        $res_end = $process['res_end'] ? 1 : 0;

        if(is_array($response)){
            foreach ($response as $i => $resItem){
                //$bargein = getSearchArrayValByKey($resItem, 'bargein');
                //$bargein = $bargein ? true : false;
                //callbot--
                if(is_array($resItem[0])){
                    $itemType = $resItem[0][0];
                    $itemCont = $resItem[0][1];
                    $itemBargein = $resItem[0]['bargein'];
                    $itemNextStatus = $resItem[0]['next_status'];
                    $itemRData = $resItem[0]['r_data'];
                    $result[] = array("type"=>$itemType,"content"=>$itemCont, "bargein"=>$itemBargein, "next_status"=>$itemNextStatus, "r_data"=>$itemRData, "unknown"=>$unknown, "res_end"=>$res_end);
                }else{
                    $itemType = $resItem[0];
                    $itemCont = $resItem[1];
                    $result[] = array("type"=>$itemType,"content"=>$itemCont);
                }
            }
        }else{
            $result[]= array("type"=>'text',"content"=>$response, "unknown"=>$unknown, "res_end"=>$res_end);
        }

        // aramjo context save
        $this->setBotContext($dt);

        return $result;
    }

    // 업체 관리자 페이지 > 레거시 데이타 컨트롤
    function controlLegacyData($data){
        global $table;

        $botId = $data['botId'];
        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $linkType = $data['linkType'];
        $uid = $data['uid'];
        $name = $data['name'];
        $description = $data['description'];
        $url = $data['url'];
        $type = $data['is_adm']?'S':'V';

        $tbl = $table[$this->module.'apiList'];

        if($linkType == 'save-legacySettings'){

            $is_row = getDbData($tbl,'uid='.$uid,'uid');
            if($uid && $is_row['uid']){

                $QVAL ="name='$name',description='$description',url='$url'";
                getDbUpdate($tbl,$QVAL,'uid='.$uid);

                $last_uid = $uid;

            }else{

               $QKEY = "name,hidden,description,type,vendor,bot,botId,url,version";
               $QVAL = "'$name','$hidden','$description','$type','$vendor','$bot','$botId','$url','$version'";
               getDbInsert($tbl,$QKEY,$QVAL);

               $last_uid = getDbCnt($tbl,'max(uid)','');
            }
            $result = $last_uid;

        }else if($linkType =='get-legacyList'){
            $_wh = "hidden=0 and vendor='".$data['vendor']."' and bot='".$data['bot']."'";
            $query = sprintf("SELECT * FROM `%s` WHERE %s GROUP BY `uid` ORDER BY `uid` ASC", $tbl,$_wh);
            $rows=$this->getAssoc($query);

            $result= array();
            foreach ($rows as $row) {
               $result[] = $row;
            }
        }
        return $result;
    }

    // 업체 관리자 페이지 > 채널 데이타 컨트롤
    function controlChannelData($data){
        global $table, $g;

        $act = $data['act'];
        $channel = $data['channel'];
        $botId = $data['botId'];
        $B = $this->getBotDataFromId($botId);
        $vendor = $data['vendor']?$data['vendor']:$B['vendor'];

        $tbl = $table[$this->module.'channelSettings'];
        $wh = "botid='".$botId."' and vendor='".$vendor."' and channel='".$channel."'";

        if($act == 'getData' && $botId){
            $cName = array(
                "ntok"=>"chatapi_navertalk",
                "kakao"=>"chatapi_kakao",
                "line"=>"chatapi_line",
                "fb"=>"chatapi_facebook",
            );

            // 자동추출값 : return url, 인증코드
            if($channel == 'kakao') {
                $return_url = 'http://'.$_SERVER['HTTP_HOST'].'/'.$cName[$channel].'/'.$botId;
            } else {
                $return_url = 'https://'.$botId.'.'.$g['chatbot_host'].'/'.$cName[$channel];
            }
            $verify_token = 'BVT_'.substr($botId,0,6);
            $RCD = getDbArray($tbl,$wh,'name,value','uid','asc','',1);
            $result = array();
            while($R = db_fetch_array($RCD)){
                $result[$R['name']] = $R['value'];
            }
            $result['return_url'] = $return_url;
            $result['verify_token'] = $verify_token;

            return $result;
        }else if($act == 'saveData' && $botId){
            $nameArray = $data['nameArray'];
            foreach ($nameArray as $name=>$value) {

                $is_wh = $wh." and name='".$name."'";
                $is_row = getDbData($tbl,$is_wh,'uid');
                if($is_row['uid']){
                   getDbUpdate($tbl,"value='".$value."'",'uid='.$is_row['uid']);
                }else{
                   $QKEY = "botid,vendor,channel,name,value";
                   $QVAL = "'$botId','$vendor','$channel','$name','$value'";
                   getDbInsert($tbl,$QKEY,$QVAL);
                }
            }
           $result = 'OK';
        }
        return $result;
    }

    // 업체 관리자 페이지 dialog 추출함수
    function getVendorAdmDialog($data){
        global $table,$date;

        $tbl = $table[$this->module.'dialog'];
        $tbl_node = $table[$this->module.'dialogNode'];
        $d_regis = $date['totime'];
        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $roleType = $_SESSION['roleType']; // bot ? or topic : 봇인지 토픽인지

        if($bot){
            $_WHERE='vendor='.$vendor.' and bot='.$bot.' and gid=0 and active=1';
            $_WHERE.=" and type='D'";
            $R = getDbData($tbl,$_WHERE,'uid');

            if($R['uid']) {
                $dialog = $R['uid'];

                // 기존 노드 1 검색
                $welcome = getDbData($tbl_node, "vendor=".$vendor." and bot=".$bot." and dialog=".$dialog." and id=1 and is_unknown=0", 'uid, gid');
                if($welcome['uid']) {
                    if($welcome['gid'] != 1) {
                        getDbUpdate($tbl_node, "gid=1, isson=1, parent=0", "uid=".$welcome['uid']);
                    }
                } else {
                    $QKEY = "gid,isson,parent,depth,id,name,vendor,bot,dialog";
                    $QVAL = "1,1,0,1,0,'Welcome','$vendor','$bot','$dialog'";
                    getDbInsert($tbl_node,$QKEY,$QVAL);
                }

                // 기존 노드 2 검색
                $start = getDbData($tbl_node, "vendor=".$vendor." and bot=".$bot." and dialog=".$dialog." and id=2 and is_unknown=0", 'uid');
                if(!$start['uid']) {
                    $QKEY = "gid,isson,parent,depth,id,name,vendor,bot,dialog";
                    $QVAL = "1,0,1,1,2,'시작','$vendor','$bot','$dialog'";
                    getDbInsert($tbl_node,$QKEY,$QVAL);
                }

                // 기존 is_unknown 검색
                $is_UN_wh = 'vendor='.$vendor.' and bot='.$bot.' and dialog='.$dialog.' and is_unknown=1';
                $_unknown = getDbData($tbl_node,$is_UN_wh, 'uid, gid, id, name');
                if($_unknown['uid']) {
                    if($_unknown['gid'] != 100000 || $_unknown['name'] != 'Fallback' || $_unknown['id'] <= 2) {
                        getDbUpdate($tbl_node, "gid=100000, isson=0, parent=1, depth=1, name='Fallback', track_flag=0, is_unknown=1", "uid=".$_unknown['uid']);
                    }
                } else {
                    if($roleType=='bot'){
                        $max_id = getDbCnt($tbl_node, "max(id)", "vendor=".$vendor." and bot=".$bot." and dialog=".$dialog);

                        $QKEY = "gid,isson,parent,depth,id,name,vendor,bot,dialog,track_flag,is_unknown";
                        $QVAL = "100000,0,1,1,".($max_id+1).",'Fallback','$vendor','$bot','$dialog',0,1";
                        getDbInsert($tbl_node,$QKEY,$QVAL);
                    }
                }
            }else{
                // dialog 가 없는 경우 최초 접속시 dialog/node 를 기본으로 저장해준다.
                $QKEY = "gid,name,active,vendor,bot,d_regis";
                $QVAL = "'0','메인 그래프','1','$vendor','$bot','$d_regis'";
                getDbInsert($tbl,$QKEY,$QVAL);
                $dialog = getDbCnt($tbl,'max(uid)','');

                // node 저장
                $QKEY = "gid,isson,parent,depth,id,name,vendor,bot,dialog";
                $QVAL = "1,1,0,0,1,'Welcome','$vendor','$bot','$dialog'";
                getDbInsert($tbl_node,$QKEY,$QVAL);

                $QKEY = "gid,isson,parent,depth,id,name,vendor,bot,dialog";
                $QVAL = "1,0,1,1,2,'시작','$vendor','$bot','$dialog'";
                getDbInsert($tbl_node,$QKEY,$QVAL);

                if($roleType=='bot'){
                    $QKEY = "gid,isson,parent,depth,id,name,vendor,bot,dialog,track_flag,is_unknown";
                    $QVAL = "100000,0,1,1,3,'Fallback','$vendor','$bot','$dialog',0,1";
                    getDbInsert($tbl_node,$QKEY,$QVAL);
                }
            }

            // 의도 디폴트 기준 점수 등록
            $bs = getDbData($table[$this->module.'botSettings'], "vendor='".$vendor."' and bot='".$bot."' and name='intentMV'", 'uid, value');
            if(!$bs['uid'] && !$bs['value']) {
                getDbInsert($table[$this->module.'botSettings'], "vendor,bot,name,value", "'$vendor','$bot','intentMV','".$this->intentMV."'");
            } else if($bs['uid'] && !$bs['value']) {
                getDbUpdate($table[$this->module.'botSettings'], "value='".$this->intentMV."'", 'uid='.$bs['uid']);
            }
            // FAQ 디폴트 기준 점수 등록
            $bs = getDbData($table[$this->module.'botSettings'], "vendor='".$vendor."' and bot='".$bot."' and name='faqMV'", 'uid, value');
            if(!$bs['uid'] && !$bs['value']) {
                getDbInsert($table[$this->module.'botSettings'], "vendor,bot,name,value", "'$vendor','$bot','faqMV','".$this->faqMV."'");
            } else if($bs['uid'] && !$bs['value']) {
                getDbUpdate($table[$this->module.'botSettings'], "value='".$this->faqMV."'", 'uid='.$bs['uid']);
            }

        }else{
            $dialog ='';
        }
        return $dialog;
    }

    // botSettings 테이블 데이타 관리 : 기본정보외 추가정보 관리
    function updateBotSettings($data){
        global $table;

        $vendor = $data['data']['vendor'];
        $bot = $data['data']['bot'];

        $tbl = $table[$this->module.'botSettings'];
        foreach($data['nameArray'] as $name=>$value){
            $wh = "vendor='".$vendor."' and bot='".$bot."' and name='".$name."'";
            $is_row = getDbData($tbl,$wh,'uid');
            if($is_row['uid']){
                $QVAL = "value='$value'";
                getDbUpdate($tbl,$QVAL,'uid='.$is_row['uid']);
            }else{
                $QKEY = "vendor,bot,name,value";
                $QVAL ="'$vendor','$bot','$name','$value'";
                getDbInsert($tbl,$QKEY,$QVAL);
            }
        }
    }

    // 봇 정보 업데이트
    function updateBotData($data){
        global $table, $g;

        $m = $this->module;
        $name = $data['name'];
        $service = $data['service'];
        $intro = $data['intro'];
        $website = $data['website'];
        $avatar = $data['avatar'];
        $active = $data['active'];
        $monitering_fa = $data['monitering_fa'];
        $induCat = $data['induCat'];

        if($data['page'] =='adm/config'){
            if($avatar && strpos($avatar, 'bot_avatar_blank2.png') === false) {
                $_bot = getDbData($table[$m.'bot'], 'uid='.$data['uid'], 'avatar');
                if($_bot['avatar'] && strpos($_bot['avatar'], 'bot_avatar_blank2.png') === false) {
                    $data['file_url'] = $_bot['avatar'];
                    $this->deleteBotFile($data);
                }

                $data['file_url'] = $avatar;
                $uploadFile = $this->setFileTempToSave($data);
            } else {
                $uploadFile = $avatar;
            }

            $upSql = "active='$active',induCat='$induCat',name='$name',service='$service',intro='$intro',website='$website',avatar='$uploadFile'";
            getDbUpdate($table[$m.'bot'],$upSql,'uid='.$data['uid']);
        }

        if($data['page'] =='adm/monitering'){
            $upSql = "monitering_fa='".$monitering_fa."'";
            getDbUpdate($table[$m.'bot'],$upSql,'uid='.$data['uid']);
        }

        // 문진봇 사용여부 업데이트
        if(isset($data['use_mediExam'])){
            $_data = array();
            $_data['nameArray'] = array();
            $_data['nameArray']['use_mediExam'] = $data['use_mediExam'];
            $_data['data'] = $data;
            $this->updateBotSettings($_data);
        }

        // 메뉴얼 사이트 사용여부 업데이트
        if(isset($data['use_compManual'])){
            $_data = array();
            $_data['nameArray'] = array();
            $_data['nameArray']['use_compManual'] = $data['use_compManual'];
            $_data['data'] = $data;
            $this->updateBotSettings($_data);
        }

        // 인터페이스 값  업데이트
        if(isset($data['interface'])){
            $_data = array();
            $_data['nameArray'] = array();
            $_data['nameArray']['interface'] = $data['interface'];
            if(isset($data['use_bargein'])) $_data['nameArray']['use_bargein'] = $data['use_bargein'];
            $_data['data'] = $data;
            $this->updateBotSettings($_data);
        }

        // 채팅 기능 사용여부 업데이트
        if(isset($data['use_chatting'])){
            $_data = array();
            $_data['nameArray'] = array();
            $_data['nameArray']['use_chatting'] = $data['use_chatting'];
            $_data['nameArray']['callBotName'] = $data['callBotName'];
            $_data['data'] = $data;
            $this->updateBotSettings($_data);
        }

       // 기본 컨텍스트 값 업데이트
        if(isset($data['default_context'])){
            $_data = array();
            $_data['nameArray'] = array();
            $_data['nameArray']['default_context'] = $data['default_context'];
            $_data['data'] = $data;
            $this->updateBotSettings($_data);
        }

        // 의도 추천 최소 점수
        if(isset($data['intentMV'])){
            $_data = array();
            $_data['nameArray'] = array();
            $_data['nameArray']['intentMV'] = $data['intentMV'];
            $_data['data'] = $data;
            $this->updateBotSettings($_data);
        }

        // FAQ 추천 최소 점수
        if(isset($data['faqMV'])){
            $_data = array();
            $_data['nameArray'] = array();
            $_data['nameArray']['faqMV'] = $data['faqMV'];
            $_data['data'] = $data;
            $this->updateBotSettings($_data);
        }

        // 채팅창 스킨 업데이트
        if(isset($data['chatSkin'])){
            $_data = array();
            $_data['nameArray'] = array();
            $_data['nameArray']['chatSkin'] = $data['chatSkin'];
            $_data['data'] = $data;
            $this->updateBotSettings($_data);
        }

        // 챗봇버튼 스킨 업데이트
        if(isset($data['chatBtn'])){
            if($data['chatBtn'] != '/_core/skin/images/btn_chatbot.png') {
                $_info = getDbData($table[$m.'botSettings'], "vendor='".$data['vendor']."' and bot='".$data['bot']."' and name='chatBtn'", 'value');
                if($_info['value']) {
                    $data['file_url'] = $_info['value'];
                    $this->deleteBotFile($data);
                }

                $data['file_url'] = $data['chatBtn'];
                $uploadFile = $this->setFileTempToSave($data);
            } else {
                $uploadFile = $data['chatBtn'];
            }

            $_data = array();
            $_data['nameArray'] = array();
            $_data['nameArray']['chatBtn'] = $uploadFile;
            $_data['data'] = $data;
            $this->updateBotSettings($_data);
        }

        // 챗봇버튼 위치 업데이트
        if(isset($data['pc_btn_bottom'])){
            $_data = array();
            $_data['nameArray'] = array();
            $_data['nameArray']['pc_btn_bottom'] = $data['pc_btn_bottom'];
            $_data['data'] = $data;
            $this->updateBotSettings($_data);
        }
        if(isset($data['pc_btn_right'])){
            $_data = array();
            $_data['nameArray'] = array();
            $_data['nameArray']['pc_btn_right'] = $data['pc_btn_right'];
            $_data['data'] = $data;
            $this->updateBotSettings($_data);
        }
        if(isset($data['m_btn_bottom'])){
            $_data = array();
            $_data['nameArray'] = array();
            $_data['nameArray']['m_btn_bottom'] = $data['m_btn_bottom'];
            $_data['data'] = $data;
            $this->updateBotSettings($_data);
        }
        if(isset($data['m_btn_right'])){
            $_data = array();
            $_data['nameArray'] = array();
            $_data['nameArray']['m_btn_right'] = $data['m_btn_right'];
            $_data['data'] = $data;
            $this->updateBotSettings($_data);
        }

        // 예약 사용여부 업데이트
        if(isset($data['use_reserve'])){
            $_data = array();
            $_data['nameArray'] = array();
            $_data['nameArray']['use_reserve'] = $data['use_reserve'];
            $_data['nameArray']['reserve_category'] = $data['reserve_category'];
            $_data['nameArray']['reserve_manage'] = $data['reserve_manage'];
            $_data['nameArray']['reserve_api'] = $data['reserve_api'];
            if($data['reserve_category'] == 'hospital' && $data['reserve_manage'] == 'erpbottalks' && $data['reserve_domainkey'] == '') {
                $reserve_domainkey = $this->getReserveDomainKey();
                $_data['nameArray']['reserve_domainkey'] = $reserve_domainkey;
            }
            if($data['reserve_category'] == 'hotel' && $data['reserve_manage'] == 'onda') {
                $_data['nameArray']['reserve_onda_suburl'] = $data['reserve_onda_suburl'];
            }
            $_data['data'] = $data;
            $this->updateBotSettings($_data);
        }

        // shop api
        if(isset($data['use_shopapi'])){
            $_data = array();
            $_data['nameArray'] = array();
            $_data['nameArray']['use_shopapi'] = $data['use_shopapi'];
            $_data['nameArray']['shopapi_vendor'] = $data['shopapi_vendor'];
            $_data['nameArray']['shopapi_domain'] = $data['shopapi_domain'];

            if($data['shopapi_mall_id']) $_data['nameArray']['shopapi_mall_id'] = $data['shopapi_mall_id'];
            if($data['shopapi_access_token']) $_data['nameArray']['shopapi_access_token'] = $data['shopapi_access_token'];
            if($data['shopapi_access_token_expire']) $_data['nameArray']['shopapi_access_token_expire'] = $data['shopapi_access_token_expire'];
            if($data['shopapi_refresh_token']) $_data['nameArray']['shopapi_refresh_token'] = $data['shopapi_refresh_token'];
            if($data['shopapi_refresh_token_expire']) $_data['nameArray']['shopapi_refresh_token_expire'] = $data['shopapi_refresh_token_expire'];

            if($data['shopapi_mall_type']) $_data['nameArray']['shopapi_mall_type'] = $data['shopapi_mall_type'];
            if($data['shopapi_client_key']) $_data['nameArray']['shopapi_client_key'] = $data['shopapi_client_key'];

            $_data['data'] = $data;
            $this->updateBotSettings($_data);
        }

        // system checkup
        if(isset($data['use_syscheckup'])){
            $_data = array();
            $_data['nameArray'] = array();
            $_data['nameArray']['use_syscheckup'] = $data['use_syscheckup'];
            $_data['nameArray']['syscheckup_start'] = $data['syscheckup_start'];
            $_data['nameArray']['syscheckup_end'] = $data['syscheckup_end'];
            $_data['nameArray']['syscheckup_msg'] = $data['syscheckup_msg'];
            $_data['data'] = $data;
            $this->updateBotSettings($_data);
        }
        // 주소봇 사용여부 업데이트
        if(isset($data['use_jusobot'])){
            $_data = array();
            $_data['nameArray'] = array();
            $_data['nameArray']['use_jusobot'] = $data['use_jusobot'];
            $_data['data'] = $data;
            $this->updateBotSettings($_data);
        }

        // 채팅상담 사용여부 업데이트
        if(isset($data['use_cschat'])){
            $_data = array();
            $_data['nameArray'] = array();
            $_data['nameArray']['use_cschat'] = $data['use_cschat'];
            $_data['nameArray']['cschat_api'] = $data['cschat_api'];
            $_data['nameArray']['cschat_userinfo'] = $data['cschat_userinfo'];
            $_data['data'] = $data;
            $this->updateBotSettings($_data);
        }

        //------------------------------------------------
        // 인트로 데이터
        if(isset($data['intro_use'])){
            $_data = array();
            $_data['nameArray'] = array();
            $_data['nameArray']['intro_use'] = $data['intro_use'];
            $_data['data'] = $data;
            $this->updateBotSettings($_data);
        }
        if(isset($data['intro_profile'])){
            $_data = array();
            $_data['nameArray'] = array();
            $_data['nameArray']['intro_profile'] = $data['intro_profile'];
            $_data['data'] = $data;
            $this->updateBotSettings($_data);
        }
        if(isset($data['intro_img']) && is_array($data['intro_img'])){
            foreach($data['intro_img'] as $img) {
                $_data = array();
                $_data['nameArray'] = array();
                $_data['nameArray']['intro_img_'.sprintf('%05d',mt_rand(1000,99999))] = $img;
                $_data['data'] = $data;
                $this->updateBotSettings($_data);
            }
        }

        // callbot tts
        if(isset($data['tts_vendor']) && isset($data['tts_audio']) && isset($data['tts_pitch']) && isset($data['tts_speed'])){
            $_wh = "bot=".$data['bot']." and name like 'tts_%'";
        	$RCD = getDbArray($table[$this->module.'botSettings'], $_wh, 'name, value', 'uid', 'asc', '', 1);
        	$is_change = false;
        	while ($R = db_fetch_array($RCD)) {
        	    if($R['name'] == 'tts_vendor' && $R['value'] != $data['tts_vendor']) {
        	        $is_change = true; break;
        	    } else if($R['name'] == 'tts_audio' && $R['value'] != $data['tts_audio']) {
        	        $is_change = true; break;
        	    } else if($R['name'] == 'tts_pitch' && $R['value'] != $data['tts_pitch']) {
        	        $is_change = true; break;
        	    } else if($R['name'] == 'tts_speed' && $R['value'] != $data['tts_speed']) {
        	        $is_change = true; break;
        	    }
        	}

            $_data = array();
            $_data['nameArray'] = array();
            $_data['nameArray']['tts_vendor'] = $data['tts_vendor'];
            $_data['nameArray']['tts_audio'] = $data['tts_audio'];
            $_data['nameArray']['tts_pitch'] = $data['tts_pitch'];
            $_data['nameArray']['tts_speed'] = $data['tts_speed'];
            $_data['data'] = $data;
            $this->updateBotSettings($_data);

            // CTI쪽으로 정보 전송
            if($is_change) {
                $access_mod = "callbot_option";
                $accessToken = "";
                while (strlen($accessToken) < 64) $accessToken .= chr(mt_rand(0, 255));
                $accessToken = bin2hex($accessToken).md5(uniqid(mt_rand()));

                $RToken = getDbData($table[$this->module.'token'], "bot='".$data['bot']."' and access_mod='".$access_mod."'", 'uid');
                if($RToken['uid']) {
                    $QVAL = "access_token='".$accessToken."'";
                    getDbUpdate($table[$this->module.'token'],$QVAL,'uid='.$RToken['uid']);
                } else {
                    $_QKEY = "bot, access_mod, access_token, roomToken, userId, expire";
                    $_QVAL = "'".$data['bot']."','".$access_mod."','".$accessToken."', '', '', '0'";
                    getDbInsert($table[$this->module.'token'],$_QKEY,$_QVAL);
                }

                $apiURL = $g['cti_api_host'];
                $aPostVal = array();
                $aPostVal['mode'] = $access_mod;
                $aPostVal['botId'] = $data['botId'];
                $aPostVal['accessToken'] = $accessToken;
                $aPostVal['ttsVendor'] = $data['tts_vendor'];
                $aPostVal['ttsWave'] = $data['tts_audio'];
                $aPostVal['ttsPitch'] = $data['tts_pitch'];
                $aPostVal['ttsSpeed'] = $data['tts_speed'];
                $postData = json_encode($aPostVal, JSON_UNESCAPED_UNICODE);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $apiURL);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSLVERSION,1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($ch);
                $aResInfo = curl_getinfo($ch);
                curl_close ($ch);

                $_result = 0;

                if($aResInfo['http_code'] == 200 && $response) {
                    $apiResult = json_decode($response, true);

                    if($apiResult['result'] == true) {
                        getDbDelete("rb_chatbot_token", "access_mod='".$aPostVal['mode']."' and access_token='".$aPostVal['accessToken']."'");
                        $_result = 1;
                    }
                }
            }
        }

        // 챗봇창 타이틀
        if(isset($data['chatTop']) && isset($data['chatLogo'])){
            $_data = array();
            $_data['nameArray'] = array();
            $_data['nameArray']['chatTop'] = $data['chatTop'];

            if($data['chatLogo']) {
                $_logo = getDbData($table[$this->module.'botSettings'], "vendor='".$data['vendor']."' and bot='".$data['bot']."' and name='chatLogo'", 'value');
                if($_logo['value'] != $data['chatLogo']) {
                    $data['file_url'] = $_logo['value'];
                    $this->deleteBotFile($data);

                    $data['file_url'] = $data['chatLogo'];
                    $chatLogo = $this->setFileTempToSave($data);
                    $_data['nameArray']['chatLogo'] = $chatLogo;
                }
            }
            $_data['data'] = $data;
            $this->updateBotSettings($_data);
        }

        // ChatGPT use
        if(isset($data['use_chatgpt'])){
            $_data = array();
            $_data['nameArray'] = array();
            $_data['nameArray']['use_chatgpt'] = trim($data['use_chatgpt']);
            $_data['data'] = $data;
            $this->updateBotSettings($_data);
        }
        return $data['uid'];
    }

    // 병원예약을 위한 도메인키 생성(클라우드에서는 전체 DB 조회)
    function getReserveDomainKey($length=9) {
        global $table, $DB_CONNECT;

        $domainKey = substr(md5(uniqid(mt_rand(), true)), 0, $length);

        $RCD = db_query("Select schema_name From information_schema.schemata Where schema_name like 'bot_user%'", $DB_CONNECT);
        while($R = db_fetch_assoc($RCD)){
            $chDBName = $R['SCHEMA_NAME'];
            if (getDbRows($chDBName.".".$table[$this->module.'botSettings'], "name = 'reserve_domainkey' and value = '".$domainKey."'") > 0) {
                $domainKey = $this->getReserveDomainKey();
            }
        }
        return $domainKey;
    }

    function deleteResItem($data){
        global $table;

        $tbl_item = $table[$this->module.'dialogResItem'];
        $tbl_itemOC = $table[$this->module.'dialogResItemOC'];
        getDbDelete($tbl_item,'uid='.$data['uid']);
        getDbDelete($tbl_itemOC,'item='.$data['uid']);
        return;
    }

    function getVendorBotRow($row){
        global $TMPL,$g, $my;
        $m = $this->module;

        $mod = $row['mod'];
        $role = $row['role'];
        $tempColor = $role=='topic'?'danger':'primary';

        // 231005 sys check
        if($GLOBALS['_cloud_'] === true) {
            $contract = $row['contract']; // 계약정보
            $d_remain = $contract['d_remain']; // 서비스 유효일
        }

        $TMPL['uid'] = $row['uid'];
        $TMPL['name'] = $row['name'];
        $TMPL['service'] = $row['service'];
        $TMPL['uid'] = $row['uid']; // bot
        $TMPL['vendor'] = $row['vendor'];
        $TMPL['roleType'] = $row['role'];
        if($row['bottype'] == 'call' && $row['callno']) {
            $aCallNo = explode(',', $row['callno']);
            $callno = '';
            foreach($aCallNo as $no) {
                $callno .=getStrToPhoneFormat($no).', ';
            }
            $callno = rtrim($callno, ', ');
            $TMPL['call_no'] = '<h2 class="dash-callNo">콜봇번호 : '.$callno.'</h2>';
        }

        if($mod=='vendor') $TMPL['link'] = $g['chatbot_reset'].'/dialog&bot='.$row['uid'];
        else if($mod=='adm'){
            // 231005 sys check
            if($GLOBALS['_cloud_'] === true) {
                if($d_remain>=0){
                    $TMPL['link'] = $g['s'].'/adm/dashboard?bot='.$row['uid'];
                    $TMPL['state_mark'] = ' <span class="label label-info">유효기간 : '.$d_remain.'일  </span>';
                }else{
                    $TMPL['link'] = $this->getExUrl('addChatbot');
                    $TMPL['state_mark'] = ' <span class="label label-danger">유효기간 만료</span>';
                }
            } else {
                if($row['role'] == 'bot') $TMPL['link'] = $g['s'].'/adm/dashboard?bot='.$row['uid'];
                else if($row['role'] == 'topic') $TMPL['link'] = $g['s'].'/adm/config?bot='.$row['uid'];
            }
        }

        // 231005 sys check
        if($GLOBALS['_cloud_'] === true) {
            // 템플릿 여부 적용
            if($row['is_temp']){
                $TMPL['temp_action']='cancel-temp';
                $TMPL['temp_actName'] = '템플릿 취소';
                $TMPL['temp_class'] = ' bot-temp';
                $TMPL['temp_mark'] =' <span class="label label-'.$tempColor.'">T</span>';
            }else{
                $TMPL['temp_action']='make-temp';
                $TMPL['temp_actName'] = '템플릿 지정';
                $TMPL['temp_class'] = '';
                $TMPL['temp_mark'] ='';
            }
        }

        if($GLOBALS['_cloud_'] === true) {
            $skin = new skin('vendor/bot_row');
        } else {
            if($my['super']) $skin = new skin('vendor/bot_row_adm');
            else $skin = new skin('vendor/bot_row');
        }
        $result = $skin->make();
        return $result;
    }

    function getVendorBotList($data){
        global $table,$TMPL,$my;

        $vendor = $data['vendor'];
        $tbl = $table[$this->module.'bot'];

        $_wh = "vendor='".$vendor."' and bottype='".$data['bottype']."'";
        $query=sprintf("SELECT * FROM `%s` WHERE %s ORDER BY hidden DESC, nrank ASC, uid DESC", $tbl,$_wh);
        $rows = $this->getAssoc($query);

        $bot_rows='';
        $i=0;
        foreach ($rows as $row) {
            if ($GLOBALS['_cloud_'] === true && !array_key_exists($row['id'], $_SESSION['mbr_bot'])) continue;
            if (!in_array($row['uid'], $my['mybot'])) continue;

            $row['mod'] = $data['mod'];

            // 231005 sys check
            if($GLOBALS['_cloud_'] === true) {
                $row['contract'] = $this->getBotContractInfo($row);
            }
            $bot_rows.= $this->getVendorBotRow($row);
            $i++;
        }

        $TMPL['bot_rows'] = $bot_rows;

        $skin = new skin('vendor/botList');
        $list = $skin->make();

        return array("list"=>$list,"qty"=>$i);
    }

    function saveEntityData($data){
        global $table;

        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $intent = $data['intent'];
        $entity = $data['entity'];
        $entityVal = $data['entityval'];
        $value = $data['value'];

        $tbl = $table[$this->module.'entityData'];
        $_wh = 'hidden=0 and vendor='.$vendor.' and bot='.$bot.' and intent='.$intent.' and entity='.$entity;
        if($entityVal){
            $_wh.=' and entityVal='.$entityVal;
        }
        $query=sprintf("SELECT `uid` FROM `%s` WHERE %s ORDER BY `uid` DESC", $tbl,$_wh);
        $row = $this->getArray($query);
        $R = $row[0];

        if($R['uid']) getDbUpdate($tbl,"value='".$value."'",'uid='.$R['uid']);
        else{
            if($value){
                $type='T';
                $hidden = 0;
                $QKEY = "type,vendor,bot,entity,entityVal,intent,hidden,value";
                $QVAL = "'$type','$vendor','$bot','$entity','$entityVal','$intent','$hidden','$value'";
                getDbInsert($tbl,$QKEY,$QVAL);
            }
        }
    }

     // 인텐트 & 엔터티 테이블 관련 : entityVal 추출
    function getEIentityVal($data){
        $data['mod'] = 'query';

        $query = $this->getEntityExData($data);
        $rows = $this->getAssoc($query);
        $total = $this->getRows($query);
        return array("total"=>$total,"rows"=>$rows);
    }

    function getEIdata($data){
        global $table;

        $tbl = $table[$this->module.'entityData'];
        $vendor = $data['vendor']?$data['vendor']:$this->vendor;
        $bot = $data['bot']?$data['bot']:$this->botuid;
        $uid = $data['uid'];
        $intent = $data['intent'];
        $entity = $data['entity'];
        $entityVal = $data['entityVal'];
        $_wh = "hidden=0 and vendor='".$vendor."' and bot='".$bot."' and intent='".$intent."' and entity='".$entity."' and value<>''";

        if($entityVal) $_wh2=$_wh.' and entityVal='.$entityVal;
        else $_wh2 = $_wh;

        $R = getDbData($tbl,$_wh2,'type,value');
        return $R['value']?$R['value']:'';
    }

    // entity * intent 데이타/td 추출
    function getEIdataTd($data){
        global $table;
        $m = $this->module;

        $uid = $data['uid'];
        $mod = $data['mod'];
        if($mod=='entityVal'){
            $entity = $data['entity'];
            $entityVal = $data['uid'];
        }else if($mod=='entity'){
            $entity = $data['entity'];
            $entityVal = '';
        }

        $html ='';
        foreach ($data['intentData'] as $intent){
            $data['intent'] = $intent['uid'];
            $data['entityVal'] = $entityVal;
            $EI_data = $this->getEIdata($data);
            $E = getDbData($table[$m.'entity'],'uid='.$entity,'name');
            $EV = getDbData($table[$m.'entityVal'],'uid='.$entityVal,'name');
            $placeholder = '#'.$intent['name'].'@'.$E['name'].':'.$EV['name'];
            $html.='
            <td class="dT-td">
               <textarea class="form-control" data-role="input-EI" data-intent="'.$intent['uid'].'" data-entity="'.$entity.'" data-entityVal="'.$entityVal.'" placeholder="'.$placeholder.'">'.$EI_data.'</textarea>
            </td>';
        }
        return $html;
    }

    // 인텐트 & 엔터티 테이블 추출 함수
    /*
          $data['vendor'], $data['bot']
    */
    function getEIdataTable($data){
        $data['vendorOnly'] = true;

        $get_intentData = $this->getIntentData($data);
        $get_entityData = $this->getEntityData($data);

        $intentData = $get_intentData['content'];
        $entityData = $get_entityData['content'];

        $data['intentData'] = $intentData;

        $html='';

        $header='<table class="table table-bordered dT-table"><thead>';

        // entity 테이블 :  절대위치
        $header2='<table class="table table-bordered dT-table dTTable-Entity"><thead>';

        // intent tr 추출
        $gubun='<tr class="intent-tr"><th colspan="2"><span class="gubun">구분</span></th>';
        $gubun2='<tr class="intent-tr"><th colspan="2"><span class="gubun">구분</span></th>';

        $html.= $header.$gubun;

        $intent_tr ='';
        foreach ($intentData as $intent) {
            $intent_tr.= '<th><span>#'.$intent['name'].'</span></th>';
        }
        $intent_tr.='</tr></thead>';

        // 엔턴티 tr 추출
        $entity_tr ='';
        $entity_tr2='';
        foreach ($entityData as $entity) {
            $data['entity'] = $entity['uid'];
            $EV = $this->getEIentityVal($data); // entity value 데이타
            if($EV['total']){
                $rowspan = $EV['total']+1;
                $entity_tr.='<tr><td class="entity-td" rowspan="'.$rowspan.'">@'.$entity['name'].'</td></tr>';
                $entity_tr2.='<tr><td class="entity-td" rowspan="'.$rowspan.'">@'.$entity['name'].'</td></tr>';

                $data['mod'] ='entityVal';
                foreach ($EV['rows'] as $row) {
                    $data['uid'] = $row['uid'];
                    $EI_data_td = $this->getEIdataTd($data);
                    $entity_tr.= '<tr><td class="entityVal-td">:'.$row['name'].'</td>'.$EI_data_td.'</tr>';
                    $entity_tr2.='<tr><td class="entityVal-td">:'.$row['name'].'</td></tr>';
                }
            }else{
                $data['mod'] = 'entity';
                $data['uid'] = $row['uid'];
                $EI_data_td = $this->getEIdataTd($data);
                $entity_tr.='<tr><td class="entityVal-td">@'.$entity['name'].'</td>'.$EI_data_td.'</tr>';
                $entity_tr2.='<tr><td class="entityVal-td">@'.$entity['name'].'</td>';
            }
        }

        // entity 테이블 : scroll-x 시 position: absolute 로 고정시키기 위함
        $entityTable = $header2.$gubun2;
        $entityTable.='<tbody>';
        $entityTable.= $entity_tr2;
        $entityTable.='</tbody></table>';

        $html.= $intent_tr;
        $html.='<tbody>';
        $html.= $entity_tr;
        $html.='</tbody></table>';
        return array("defaultTable"=>$html,"entityTable"=>$entityTable);
    }

    // 네이버 지식인
    function getNaverIn($data){
        $qty = $data['qty']?$data['qty']:100; // 건수 : 최대 100건
        $sort = $data['sort']?$data['sort']:'sim'; // sim (유사도순), date (날짜순), point(평점순)
        $str = $data['str']; // 검색 엔터티
        $start = $data['start']?$data['start']:1;

        $keyword = urlencode($str);
        $param ='?query='.$keyword.'&display='.$qty.'&start='.$start.'&sort='.$sort;
        $_url='https://openapi.naver.com/v1/search/kin.json';
        $url = $_url.$param;
        $client_id = 'U5PycZ0eIK5fpwf64Hvs';
        $client_secret = 'CJEsQEGHwl';
        // $param_arr = array($mod_to_key[$mod]=>$send_data);
        // $fields=http_build_query($param_arr);
        $headers = [
            'X-Naver-Client-Id: '.$client_id,
            'X-Naver-Client-Secret: '.$client_secret
        ];

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
        curl_setopt($ch, CURLOPT_SSLVERSION,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $ch_result = curl_exec($ch);
        curl_close($ch);

        $search_json = json_decode($ch_result,true);
        return $search_json;
    }

    // 네이버 검색 전체
    function getNaverSearch($data){
        $search_json = $this->getNaverIn($data);
        $str ='';
        foreach($search_json['items'] as $item){
            $str.=$item['title'].$item['description']; // 제목 & 설명 텍스트만 추출

            if($data['insert']){
                $title= $item['title'];
                $desc = $item['description'];
                $QKEY = "keyword,result_title,result_des";
                $QVAL = "'$str','$title','$desc'";
                getDbInsert('bg_naverIn',$QKEY,$QVAL);
            }
        }

        $_data['type'] = $data['type'];
        $_data['sentence'] = $str;

        $result = $this->getPSFromSentence($_data);
        return $result;
    }

    // LC 용 / 임시
    function getNaverSearchLC($data){
        $search_json = $this->getNaverIn($data);
        $result = array();
        foreach($search_json['items'] as $item){
            $str = $item['title'];//$item['description'],0,15); // 제목 & 설명 텍스트만 추출
            $result[] = preg_replace('(<b>|</b>)','',$str);
        }
        return $result;
    }

    // 문장에서 품사(명사/동사) 추출
    function getPSFromSentence($data){
        $sentence = $data['sentence']; // 타겟 문장
        $type = $data['type']; // 추천타입 : entity, intent
        $result = array();
        if(extension_loaded('mecab')) {
            $t = new \MeCab\Tagger(array('-d', $this->mecab_dic));
            $node = $t->parseToNode($sentence);

            foreach($node as $m) {
                $feature = $m->getFeature();
                $feature_arr = explode(',',$feature);
                $PS = $feature_arr[0]; // 품사

                if(strpos($PS,"BOS") === false && strpos($PS,"EOS") === false) {
                    if($type=='entity'){
                        if($PS=='NNG'){
                            $entity = $m->getSurface();
                            if(strlen($entity)>3){ // 한글 1글자 3byte
                                if(array_key_exists($entity,$result)) $result[$entity]++;
                                else $result[$entity] = 1;
                            }
                        }
                    }else if($type=='intent'){
                        if($PS=='VV'|| ($PS=='VV'&& ($n_pos=='EF'||$n_pos=='EC'))||($PS=='VA'&& $n_pos=='EF')|| $PS=='VV+EC' || $PS=='VV+EF' || $PS=='VV+ETM'){
                            $intent = $m->getSurface();
                            if(strlen($intent)>3){ // 한글 1글자 3byte
                                if(array_key_exists($intent,$result)) $result[$intent]++;
                                else $result[$intent] = 1;
                            }
                        }
                    }
                }
            }
        } else {
            $cmd = "echo '".$sentence."' | ".$this->mecab_exe." -d ".$this->mecab_dic;
            exec($cmd, $aResult, $return);
            if($retrun == 0 && count($aResult) > 0) {
                foreach($aResult as $node) {
                    $m = explode("\t", $node);
                    if(strpos($m[0],'BOS') !== false || strpos($m[0],'EOS') !== false) continue;
                    $word = $m[0];
                    $aInfo = explode(",", $m[1]);
                    $PS = $aInfo[0];
                    if($type=='entity'){
                        if($PS=='NNG'){
                            $entity = $word;
                            if(strlen($entity)>3){ // 한글 1글자 3byte
                                if(array_key_exists($entity,$result)) $result[$entity]++;
                                else $result[$entity] = 1;
                            }
                        }
                    }else if($type=='intent'){
                        if($PS=='VV'|| ($PS=='VV'&& ($n_pos=='EF'||$n_pos=='EC'))||($PS=='VA'&& $n_pos=='EF')|| $PS=='VV+EC' || $PS=='VV+EF' || $PS=='VV+ETM'){
                            $intent = $word;
                            if(strlen($intent)>3){ // 한글 1글자 3byte
                                if(array_key_exists($intent,$result)) $result[$intent]++;
                                else $result[$intent] = 1;
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

    function deleteIntent($data){
        global $table;

        $tbl = $table[$this->module.'intent'];
        $tblEx = $table[$this->module.'intentEx'];
        $intent = $data['uid'];
        getDbDelete($tbl,'uid='.$intent);

        // Ex 도 삭제
        getDbDelete($tblEx,'intent='.$intent);
    }

    function deleteEntity($data){
        global $table;

        $tbl = $table[$this->module.'entity'];
        $tblEx = $table[$this->module.'entityVal'];
        $entity = $data['uid'];
        getDbDelete($tbl,'uid='.$entity);

        // Ex 도 삭제
        getDbDelete($tblEx,'entity='.$entity);
    }

    function regisIntent($data){
        global $table,$s,$date;

        $data = getRemoveBackslash($data);

        $tbl = $table[$this->module.'intent'];
        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $dialog = $data['dialog'];
        $name = preg_replace('/#/','',trim($data['intentName']));
        // 수정
        if($data['intent']){
            $B = getDbData($tbl, "uid=".$data['intent'], 'type');
            $data['type'] = $B['type'];
            if($data['type'] == 'S') $name = '시스템-'.str_replace('시스템-', '', $name);
            $QVAL ="name='".$name."'";
            getDbUpdate($tbl,$QVAL,'uid='.$data['intent']);

            $last_intent = $data['intent'];
        }else{
            if(trim($name) == '') {
                echo json_encode(array(-1, '의도명을 입력해주세요.'));
                exit;
            }

            if($data['sys_intent'] == true) {
                $name = '시스템-'.str_replace('시스템-', '', $name);
                $gid = $vendor = $bot = 0;
                $type = 'S';
            } else {
                $_sql ="type='V' and vendor='".$vendor."' and bot='".$bot."'";
                $MAXG = getDbCnt($tbl,'max(gid)',$_sql);
                $gid = $MAXG+1;
                $type = 'V';
            }
            $hidden = 0;
            $data['type'] = $type;
            $d_regis = $date['totime'];

            $QKEY = "gid,type,site,vendor,bot,hidden,name,d_regis";
            $QVAL = "'$gid','$type','$s','$vendor','$bot','$hidden','$name','$d_regis'";
            getDbInsert($tbl,$QKEY,$QVAL);

            $last_intent = getDbCnt($tbl,'max(uid)','');
        }

        $data['intent'] = $last_intent;
        $this->regisIntentEx($data);

        // 의도 자동 학습
        $this->getTrainIntentPesoNLP($data);
        return array("intent_uid"=>$last_intent,"intent_name"=>$data['intentName']);
    }

    function regisIntentEx($data){
        global $table;

        $tbl = $table[$this->module.'intentEx'];
        $iEx_uid = $data['iEx_uid'];
        $iEx_val = $data['iEx_val'];
        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $dialog = $data['dialog'];
        $intent = $data['intent'];
        $hidden = 0;
        $type = $data['type'];
        if($type == 'S') {
            $vendor = $bot = $dialog = 0;
        }
        foreach ($iEx_val as $i => $content) {
            $content = trim($content);
            $uid = $iEx_uid[$i];
            // 수정 모드
            if($uid){
                getDbUpdate($tbl,"content='".$content."'",'uid='.$uid);
            }else{
                if($content){
                    $QKEY = "type,vendor,bot,dialog,intent,hidden,content";
                    $QVAL = "'$type','$vendor','$bot','$dialog','$intent','$hidden','$content'";
                    getDbInsert($tbl,$QKEY,$QVAL);
                }
            }
        }
        return $data;
    }

    function regisEntity($data){
        global $table,$s,$date;

        $data = getRemoveBackslash($data);

        $tbl = $table[$this->module.'entity'];
        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $dialog = $data['dialog'];
        $name = preg_replace('/@/','',trim($data['entityName']));
        // 수정
        if($data['entity']){
            $QVAL ="name='".$name."'";
            getDbUpdate($tbl,$QVAL,'uid='.$data['entity']);

            $last_entity = $data['entity'];
        }else{
            if(trim($name) == '') {
                echo json_encode(array(-1, '엔터티명을 입력해주세요.'));
                exit;
            }

            $_sql ="type='V' and vendor='".$vendor."' and bot='".$bot."'";
            $MAXG = getDbCnt($tbl,'max(gid)',$_sql);
            $gid = $MAXG+1;
            $type = 'V';
            $hidden = 0;
            $d_regis = $date['totime'];

            $QKEY = "gid,type,site,vendor,bot,hidden,name,d_regis";
            $QVAL = "'$gid','$type','$s','$vendor','$bot','$hidden','$name','$d_regis'";
            getDbInsert($tbl,$QKEY,$QVAL);

            $last_entity = getDbCnt($tbl,'max(uid)','');
        }

        $data['entity'] = $last_entity;
        $this->regisEntityEx($data);
        return array("entity_uid"=>$last_entity,"entity_name"=>$data['entityName']);
    }

    function regisEntityEx($data){
        global $table;

        $tbl = $table[$this->module.'entityVal'];
        $iEx_uid = $data['iEx_uid'];
        $iEx_val = $data['iEx_val'];
        $iEx_syn = $data['iEx_syn'];
        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $dialog = $data['dialog'];
        $entity = $data['entity'];
        $hidden = 0;
        $type ='V';

        foreach ($iEx_val as $i => $name) {
            $uid = $iEx_uid[$i];
            $synonyms = trim($iEx_syn[$i]);
            // 수정 모드
            if($uid){
                getDbUpdate($tbl,"name='".$name."',synonyms='".$synonyms."'",'uid='.$uid);
            }else{
                if($name){
                    $_sql ="type='V' and vendor='".$vendor."' and bot='".$bot."'";
                    $MAXG = getDbCnt($tbl,'max(gid)',$_sql);
                    $gid = $MAXG+1;
                    $QKEY = "type,vendor,bot,dialog,entity,hidden,name,synonyms";
                    $QVAL = "'$type','$vendor','$bot','$dialog','$entity','$hidden','$name','$synonyms'";
                    getDbInsert($tbl,$QKEY,$QVAL);
                }
            }
        }
        return $data;
    }

    function getIntentExRow($row){
        global $TMPL;

        $TMPL['iEx_uid'] = $row['uid'];
        $TMPL['iEx_val'] = trim($row['content']);

        $skin = new skin('dialog/intentEx_row');
        $result = $skin->make();
        return $result;
    }

    // 인텐트 예문 출력
    function getIntentEx($data){
        global $table,$TMPL;

        $tbl = $table[$this->module.'intentEx'];
        $intent = $data['uid'];
        $base_wh = $this->getDialogBaseQry($data);
        $bot = $data['bot'];

        $B = getDbData($table[$this->module.'intent'], "uid=".$data['uid'], 'type');
        if($B['type'] == 'S') {
            $_wh = 'hidden=0 and intent='.$intent;
        } else {
            $_wh = 'hidden=0 and bot='.$bot.' and intent='.$intent;
        }
        $query=sprintf("SELECT * FROM `%s` WHERE %s ORDER BY `uid` DESC", $tbl,$_wh);
        $rows = $this->getAssoc($query);
        $total = $this->getRows($query);

        if($total){
            $intentEx_rows = '';
            foreach ($rows as $row) {
                $intentEx_rows.= $this->getIntentExRow($row);
            }
            $TMPL['intentEx_rows'] = $intentEx_rows;
            $skin = new skin('dialog/intentEx_list');
            $list = $skin->make();

        }else{
           $TMPL['noData_msg'] = '등록된 예문이 없습니다.';
           $noData = new skin('dialog/no_data');
           $list = $noData->make();
        }

        if($data['reqType'] =='rows') return $rows;
        else return $list;
    }

    function getEntityExRow($row){
        global $TMPL;

        $TMPL['iEx_uid'] = $row['uid'];
        $TMPL['iEx_val'] = $row['name']; // 항목명
        $TMPL['iEx_syn'] = $row['synonyms']; // 유사어
        $TMPL['callEntity'] = $this->callEntity;

        $skin = new skin('dialog/entityEx_row');
        $result = $skin->make();
        return $result;
    }

    // 엔터티 Value 출력( data, query 구분)
    function getEntityExData($data){
        global $table;

        $tbl = $table[$this->module.'entityVal'];
        $entity = $data['entity'];

        $_wh = 'hidden=0 and entity='.$entity;
        $query=sprintf("SELECT * FROM `%s` WHERE %s ORDER BY `uid` DESC", $tbl,$_wh);
        $rows = $this->getAssoc($query);
        $entityVal = array();
        foreach ($rows as $row) {
           $entityVal[] = array("uid"=>$row['uid'],"name"=>$row['name']);
        }

        $result = array();
        $result['query'] = $query;
        $result['data'] = $entityVal;
        return $result[$data['mod']];
    }

    // 엔터티 Value 출력(html 포함)
    function getEntityEx($data){
        global $TMPL;

        // entityExData 호출
        $_data = $data;
        $_data['entity'] = $data['uid'];
        $_data['mod'] = 'query';
        $query = $this->getEntityExData($_data);

        $rows = $this->getAssoc($query);
        $total = $this->getRows($query);

        if($total){
            $entityEx_rows = '';
            foreach ($rows as $row) {
                $entityEx_rows.= $this->getEntityExRow($row);
            }
            $TMPL['entityEx_rows'] = $entityEx_rows;
            $skin = new skin('dialog/entityEx_list');
            $list = $skin->make();

        }else{
           $TMPL['noData_msg'] = '등록된 예시단어가 없습니다.';
           $noData = new skin('dialog/no_data');
           $list = $noData->make();
        }

        if($data['reqType'] =='rows') return $rows;
        else return $list;
    }

    // 노드 기본 쿼리 추출함수
    function getDialogBaseQry($data){
        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $dialog = $data['dialog'];
        $query = 'vendor='.$vendor.' and bot='.$bot.' and dialog='.$dialog;
        return $query;
    }

    // 다이얼로그 노드 삭제
    function deleteDialogNode($data){
        global $table;

        $m = $this->module;
        $base_wh = $this->getDialogBaseQry($data);
        $_wh = $base_wh.' and id='.$data['node'];
        $_wh2 = $base_wh.' and node='.$data['node'];

        // resItem & resItemOC
        $RCD = getDbSelect($table[$m.'dialogResItem'],$_wh2,'uid,img_url');
        while($R = db_fetch_array($RCD)){
            // 파일 삭제
            if($R['img_url']) {
                $data['file_url'] = $R['img_url'];
                $this->deleteBotFile($data);
            }
            $__wh = "item=".$R['uid']." and ((resType='img' and varchar_val<>'') or (resType='api' and varchar_val<>''))";
            $RCD2 = getDbSelect($table[$m.'dialogResItemOC'],$__wh,"uid,resType,varchar_val");
            while($R2 = db_fetch_array($RCD2)){
                if($R2['resType']=='img') {
                    $data['file_url'] = $R2['varchar_val'];
                    $this->deleteBotFile($data);
                } else if($R2['resType']=='api') {
                    getDbDelete($table[$m.'dialogResApiOutput'],"itemOC='".$R2['uid']."'");
                    getDbDelete($table[$m.'dialogResApiParam'],"itemOC='".$R2['uid']."'");
                }
            }
            // 응답설정 삭제
            $__wh = "vendor=".$vendor." and bot=".$bot." and item=".$R['uid'];
            $RCD2 = getDbSelect($table[$m.'dialogResItemOC'],$__wh,'uid');
            while($R2 = db_fetch_array($RCD2)){
                getDbDelete($table[$m.'tempData'],"vendor=".$vendor." and bot=".$bot." and item_type='OC' and item_uid=".$R2['uid']);
            }

            getDbDelete($table[$m.'dialogResItemOC'],"vendor=".$vendor." and bot=".$bot." and item=".$R['uid']);

            // 응답설정 삭제
            getDbDelete($table[$m.'tempData'],"vendor=".$vendor." and bot=".$bot." and item_type='RI' and item_uid=".$R['uid']);
        }
        getDbDelete($table[$this->module.'dialogResItem'],$_wh2);

        // resGroup 테이블
        getDbDelete($table[$m.'dialogResGroup'],$_wh2);

        // node 테이블
        getDbDelete($table[$m.'dialogNode'],$_wh);
    }

    // node 조건 label 상세쿼리 추출
    function getFilterLabelDetail($labelData){

        $NFO = str_replace(',','|',$this->NFO); // ':,::,:!,::!,<=,>=,>,<'
        preg_match('/('.$NFO.')/', $labelData,$match);
        if($match[0]){
            $label_arr = explode($match[0],$labelData); // @엔터티:벨류
            $operator = $match[0]; // :
            $term = $label_arr[1]; // 벨류
            $result = $labelData.'|'.$operator.'|'.$term;
        }
        else $result = $labelData;
        return $result;
    }

    // node 조건 쿼리문 배열로 세팅
    function getNodeRecognizeCondition($data){

        $andOr_arr = $data['andOr'];
        $filterData_arr = $data['recognize'];
        $filterLabel_arr = $data['filterLabel'];
        $qry ='';
        foreach ($filterLabel_arr as $index=>$labelData) {
            $im = $index-1;
            $andOr = $andOr_arr[$im];
            $filterData = $filterData_arr[$index];
            if($filterData && $labelData){
                $filterLabel = $this->getFilterLabelDetail($labelData); // :, !:, =, !=,>,<,>=
                if($index=='0') {
                    $qry .=($andOr_arr[0]?$andOr_arr[0]:'and').'|'.$filterData.'|'.$filterLabel.','; // 첫번째 and/or 는 두번째 and/or 값과 같게 한다.
                }
                else $qry .= $andOr.'|'.$filterData.'|'.$filterLabel.',';
            }
        }
        $result = rtrim($qry,',');
        return $result;
    }

    // node 조건 쿼리문 배열로 세팅
    function getNodeContextCondition($data){

        $contextName_arr = $data['contextName'];
        $contextValue_arr = $data['contextValue'];
        $qry ='';
        foreach ($contextName_arr as $index=>$contextName) {
            $contextValue = $contextValue_arr[$index];
            if($contextName) $qry .= $contextName.'|'.$contextValue.',';
            else $qry.='';
        }
        $result = rtrim($qry,',');
        return $result;
    }

    // 노드 저장 함수
    function regisNode($data){
        global $table,$date;

        $tbl = $table[$this->module.'dialogNode'];
        $base_wh = $this->getDialogBaseQry($data);
        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $dialog = $data['dialog'];
        $name = $data['nodeName'];
        $parent = $data['nodeParent']?$data['nodeParent']:0;
        $node_action = $data['nodeAction'];
        $jumpTo_node = $data['jumpTo_node']?$data['jumpTo_node']:0; // node_action 이 2인 경우
        $is_unknown = $data['is_unknown'];// 대화상자 못찾은 경우 응답하는 대화상자 여부값
        $use_topic = $data['use_topic']; // 연결한 토픽(dialog) uid

        $p_wh = $base_wh." and id='".$parent."'";
        $P = getDbData($tbl,$p_wh,'depth');
        $p_depth = $P['depth'];
        $depth = $p_depth+1;

        if($is_unknown){
            $_wh = $base_wh." and is_unknown=1 and track_flag=0";
            $track_flag = 0;
        }
        else{
            $_wh = $base_wh." and id='".$data['node']."'";
            $track_flag =1;
        }

        $query=sprintf("SELECT `uid` FROM `%s` WHERE %s", $tbl,$_wh);
        $is_row = $this->getRows($query);

        $recCondition = $this->getNodeRecognizeCondition($data);
        $context = $this->getNodeContextCondition($data);

        if($is_row){
           $upQry = "name='".$name."',recCondition='".$recCondition."',context='".$context."',recQry='".$recQry."',node_action='".$node_action."',jumpTo_node='".$jumpTo_node."',use_topic='".$use_topic."'";
           getDbUpdate($tbl,$upQry,$_wh);

           $last_id = $data['node'];

        }else{
            if($vendor && $bot && $dialog){
                $_wh = 'vendor='.$vendor.' and bot='.$bot.' and dialog='.$dialog;
                if($depth) $_wh.=' and depth='.$depth;

                $MAXC = getDbCnt($tbl,'max(gid)',$_wh);
                $gid = $MAXC+1;

                $_wh2 = 'vendor='.$vendor.' and bot='.$bot.' and dialog='.$dialog;
                $MAXID = getDbCnt($tbl,'max(id)',$_wh2);
                $id = $MAXID+1;
                $d_regis = $date['totime'];

                $QKEY ="gid,parent,depth,hidden,id,name,vendor,bot,dialog,recCondition,context,recQry,track_flag,node_action,jumpTo_node,is_unknown,use_topic,d_regis";
                $QVAL ="'$gid','$parent','$depth','$hidden','$id','$name','$vendor','$bot','$dialog','$recCondition','$context','$recQry','$track_flag','$node_action','$jumpTo_node','$is_unknown','$use_topic','$d_regis'";

                getDbInsert($tbl,$QKEY,$QVAL);

                $last_id = $id;

                // parent 있는 경우
                if($parent){
                    getDbUpdate($tbl,'isson=1',"vendor=".$vendor." and bot=".$bot." and dialog=".$dialog." and id='".$parent."'");
                }
            }else{
                $last_id = '';
            }
        }
        return $last_id;
    }

    // Node 응답그룹 Header 저장
    function regisResGroupHeader($data){
        global $table;

        $tbl = $table[$this->module.'dialogResGroup'];
        $base_wh = $this->getDialogBaseQry($data);
        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $dialog = $data['dialog'];
        $node = $data['node'];
        $aResGroupID = array();

        if(!isset($data['frontInput']) || $data['frontInput']=='') {
            foreach ($data['resGroupHeader'] as $val) {
                $val_arr = explode('-',$val);
                $id = $val_arr[1];
                if($id && !in_array($id, $aResGroupID)) $aResGroupID[] = $id;
            }
            if(count($aResGroupID) > 0) {
                $aResGroupID = implode("','", $aResGroupID);
                $_wh = $base_wh." and node='".$node."' and id not in ('".$aResGroupID."')";
                getDbDelete($table[$this->module.'dialogResGroup'], $_wh);

                $_wh = $base_wh." and node='".$node."' and resGroupId not in ('".$aResGroupID."')";
                $RCD = getDbSelect($table[$this->module.'dialogResItem'],$_wh,'uid,img_url');
                while($R = db_fetch_array($RCD)){
                    // 파일 삭제
                    if($R['img_url']) {
                        $data['file_url'] = $R['img_url'];
                        $this->deleteBotFile($data);
                    }
                    $_wh2 = "vendor=".$vendor." and bot=".$bot." and item=".$R['uid']." and ((resType='img' and varchar_val<>'') or (resType='api' and varchar_val<>''))";
                    $RCD2 = getDbSelect($table[$this->module.'dialogResItemOC'],$_wh2,"uid,resType,varchar_val");
                    while($R2 = db_fetch_array($RCD2)){
                        if($R2['resType']=='img') {
                            $data['file_url'] = $R2['varchar_val'];
                            $this->deleteBotFile($data);
                        } else if($R2['resType']=='api') {
                            getDbDelete($table[$this->module.'dialogResApiOutput'],"itemOC='".$R2['uid']."'");
                            getDbDelete($table[$this->module.'dialogResApiParam'],"itemOC='".$R2['uid']."'");
                        }
                    }
                    // 응답설정 삭제
                    getDbDelete($table[$this->module.'tempData'],"vendor=".$vendor." and bot=".$bot." and item_type='RI' and item_uid=".$R['uid']);
                    //---------------------
            	    getDbDelete($table[$this->module.'dialogResItemOC'],"vendor=".$vendor." and bot=".$bot." and item=".$R['uid']);
            	}
            	getDbDelete($table[$this->module.'dialogResItem'],$_wh);
            }
        }

        $i=1;
        foreach ($data['resGroupHeader'] as $val) {
            $val_arr = explode('-',$val);
            $resType = $val_arr[0];
            $id = $val_arr[1];
            $hidden = $val_arr[2] && $val_arr[2] == 'hide' ? 1 : 0;

            $_wh = $base_wh." and node='".$node."' and id='".$id."'";
            $R = getDbData($tbl,$_wh,'*');

            if($R['uid']){
               $upQry = 'gid='.$i.', hidden='.$hidden;
               getDbUpdate($tbl,$upQry,'uid='.$R['uid']);
            }else{
                $QKEY ="gid,hidden,id,vendor,bot,dialog,node,resType";
                $QVAL ="'$i','$hidden','$id','$vendor','$bot','$dialog','$node','$resType'";
                getDbInsert($tbl,$QKEY,$QVAL);

            }
            $i++;
        }
    }

    // and =>&, or=>|, not => <>, : => = , != => !=,
    function convertStringToSql($str){
        $convertString = array("and"=>"&","or"=>"|","not"=>"<>");
        $result = $convertString[$str]?$convertString[$str]:$str;
        return $result;
    }

    function convertSqlToString($str){
        $convertSql =array("&"=>"and","|"=>"or","<>"=>"not");
        $result = $convertSql[$str]?$convertSql[$str]:$str;
        return $result;
    }

    // Node 응답그룹 Body 저장
    function regisResGroupBody($data){
        global $table;

        $tbl = $table[$this->module.'dialogResItem'];
        $base_wh = $this->getDialogBaseQry($data);
        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $dialog = $data['dialog'];
        $node = $data['node'];
        $aResItemUID = array();

        if(!isset($data['frontInput']) || $data['frontInput']=='') {
            foreach ($data['resGroupBody'] as $val){
                $resGroupBody = stripcslashes($val);
                $item = json_decode($resGroupBody,true);
                $item_uid = $item['item_uid'];
                if($item_uid && !in_array($item_uid, $aResItemUID)) $aResItemUID[] = $item_uid;
            }
            if(count($aResItemUID) > 0) {
                $aResItemUID = implode(",", $aResItemUID);
                $_wh = $base_wh." and node='".$node."' and uid not in (".$aResItemUID.")";
                $RCD = getDbSelect($table[$this->module.'dialogResItem'],$_wh,'uid,img_url');
                while($R = db_fetch_array($RCD)){
                    // 파일 삭제
                    if($R['img_url']) {
                        $data['file_url'] = $R['img_url'];
                        $this->deleteBotFile($data);
                    }
                    $_wh2 = "vendor=".$vendor." and bot=".$bot." and item=".$R['uid']." and ((resType='img' and varchar_val<>'') or (resType='api' and varchar_val<>''))";
                    $RCD2 = getDbSelect($table[$this->module.'dialogResItemOC'],$_wh2,"uid,resType,varchar_val");
                    while($R2 = db_fetch_array($RCD2)){
                        if($R2['resType']=='img') {
                            $data['file_url'] = $R2['varchar_val'];
                            $this->deleteBotFile($data);
                        } else if($R2['resType']=='api') {
                            getDbDelete($table[$this->module.'dialogResApiOutput'],"itemOC='".$R2['uid']."'");
                            getDbDelete($table[$this->module.'dialogResApiParam'],"itemOC='".$R2['uid']."'");
                        }
                    }
                    //---------------------
            	    // 응답설정 삭제
                    $_wh2 = "vendor=".$vendor." and bot=".$bot." and item=".$R['uid'];
                    $RCD2 = getDbSelect($table[$this->module.'dialogResItemOC'],$_wh2,'uid');
                    while($R2 = db_fetch_array($RCD2)){
                        getDbDelete($table[$this->module.'tempData'],"vendor=".$vendor." and bot=".$bot." and item_type='OC' and item_uid=".$R2['uid']);
                    }
            	    getDbDelete($table[$this->module.'dialogResItemOC'],'vendor='.$vendor.' and bot='.$bot.' and item='.$R['uid']);
            	}
                getDbDelete($table[$this->module.'dialogResItem'],$_wh);
            }
        }

        $i=0;
        $apiItemOC = '';
        $resImgVal = array();

        foreach ($data['resGroupBody'] as $val){
            $resGroupBody = stripcslashes($val);
            $item = json_decode($resGroupBody,true);

            $item_uid = $item['item_uid'];
            $resGroupId = $item['group_id'];
            $resType = $item['resType'];
            $id = $item['item_id'];
            $title = $item['title'];
            $summary = $item['summary'];
            $content = addslashes($item['content']);
            $img_url = $item['img_url'];
            $link1 = $item['link1'];
            $link2 = $item['link2'];
            $link3 = $item['link3'];
            $ctiaction = $item['cti_action'];
            $ctx_init = $item['ctx_init'] ? $item['ctx_init'] : 0;

            if($resType=='if'){
                $if_recQry = $item['if_recQry']; // query 용
                $if_recCondition = $item['if_recCondition']; // uid 포함값
                $if_recLabel = $item['if_recLabel']; // 입력된 값

                $_recQry ='';
                $_recCondition ='';
                foreach ($if_recQry as $index=>$str) {
                    $recCondition = $if_recCondition[$index]; // #|76|항공
                    $recConArray = explode('|',$recCondition);
                    $_recQry.= $this->convertStringToSql($str).$recConArray[0].$recConArray[1].',';
                    $filterLabel = $this->getFilterLabelDetail($if_recLabel[$index]); // :, !:, =, !=,>,<,>=
                    if($if_recCondition[$index]&&$filterLabel) $_recCondition.= $str.'|'.$if_recCondition[$index].'|'.$filterLabel.',';
                    else $_recCondition.='';
                }

                $recQry = rtrim($_recQry,',');
                $recCondition = rtrim($_recCondition,',');
            }

            if($resType =='text') $_wh = $base_wh." and node='".$node."' and resType='".$resType."' and resGroupId='".$resGroupId."'";
            else $_wh = $base_wh." and node='".$node."' and resType='".$resType."' and resGroupId='".$resGroupId."' and id='".$id."'";

            $query=sprintf("SELECT * FROM `%s` WHERE %s", $tbl,$_wh);
            $rows = $this->getAssoc($query);
            $R = array();
            $R = $rows[0];

            $uploadFile = '';

            if($R['uid']){
                if($img_url) {
                    $_info = getDbData($tbl, 'uid='.$R['uid'], 'img_url');
                    if($_info['img_url'] && $_info['img_url'] != $img_url) {
                        $data['file_url'] = $_info['img_url'];
                        $this->deleteBotFile($data);
                    }
                    $data['file_url'] = $img_url;
                    $uploadFile = $this->setFileTempToSave($data);
                }

                $upQry ='gid='.$i;
                if($title) $upQry.=",title='".$title."'";
                if($summary) $upQry.=",summary='".$summary."'";
                if($content) $upQry.=",content='".$content."'";
                if($uploadFile) $upQry.=",img_url='".$uploadFile."'";
                if($link1) $upQry.=",link1='".$link1."'";
                if($link2) $upQry.=",link2='".$link2."'";
                if($link3) $upQry.=",link3='".$link3."'";
                if($recCondition) $upQry.=",recCondition='".$recCondition."'";
                if($recQry) $upQry.=",recQry='".$recQry."'";
                if($ctiaction) $upQry.=",ctiaction='".$ctiaction."'";
                $upQry.=",ctx_init='".$ctx_init."'";

                getDbUpdate($tbl,$upQry,'uid='.$R['uid']);
                $item_uid = $R['uid'];

            }else{
                if($img_url) {
                    $data['file_url'] = $img_url;
                    $uploadFile = $this->setFileTempToSave($data);
                }

                $QKEY ="gid,vendor,bot,dialog,node,resGroupId,resType,id,title,summary,content,img_url,link1,link2,link3,recQry,recCondition,ctx_init,ctiaction";
                $QVAL ="'$i','$vendor','$bot','$dialog','$node','$resGroupId','$resType','$id','$title','$summary','$content','$uploadFile','$link1','$link2','$link3','$recQry','$recCondition',";
                $QVAL.="'$ctx_init','$ctiaction'";

                getDbInsert($tbl,$QKEY,$QVAL);
                $item_uid = getDbCnt($tbl,'max(uid)','');
            }

            // 변경된 이미지 적용을 위해 저장
            if($uploadFile) {
                $file = substr($uploadFile, (strrpos($uploadFile, "/")+1));
                $resImgVal[] = array('uid'=>$item_uid, 'file'=>$file, 'img'=>$uploadFile);
            }

            // 메뉴 아이템 응답그룹 저장 or 업데이트
            if($item['itemResGroup']){
                $j=0;
                foreach ($item['itemResGroup'] as $type => $uid_val){
                    $_data = array();
                    $_data['item'] = $item_uid;
                    $_data['item_id'] = $id;
                    $_data['vendor'] = $vendor;
                    $_data['bot'] = $bot;
                    $_data['dialog'] = $dialog;
                    $_data['resType'] = $type;
                    $_data['uid'] = $uid_val['uid'];
                    $_data['val'] = $uid_val['val'];
                    $_data['ctiaction'] = $uid_val['cti_action'];
                    $_data['resApiData'] = $data['resApiData'];
                    $_data['gid'] = $j;

                    $result = $this->regisItemResGroup($_data);
                    if(!$apiItemOC && $result['apiItemOC']) $apiItemOC = $result['apiItemOC'];

                    // 변경된 이미지 적용을 위해 저장
                    if(isset($result['res-img']) && count($result['res-img']) > 0) $resImgVal[] = $result['res-img'];

                   $j++;
                }
            }
            $i++;
        }

        $result = array('apiItemOC'=>$apiItemOC, 'resImgVal'=>$resImgVal);
        return $result;
    }

    // 메뉴 아이템 응답그룹 저장
    function regisItemResGroup($data){
        global $table;
        $tbl = $table[$this->module.'dialogResItemOC'];
        $item = $data['item'];
        $uid = $data['uid'];
        $resType = $data['resType'];
        $val = addslashes(rtrim($data['val'],','));
        $gid = $data['gid'];
        $vendor = $data['vendor'];
        $bot = $data['bot'];

        $uploadFile = '';

        // 수정
        if($uid){
            if($resType == 'img') {
                $_info = getDbData($tbl, 'uid='.$uid, 'varchar_val');
                if($_info['varchar_val'] && $_info['varchar_val'] != $val) {
                    $data['file_url'] = $_info['varchar_val'];
                    $this->deleteBotFile($data);

                    if($val) {
                        $data['file_url'] = $val;
                        $uploadFile = $this->setFileTempToSave($data);
                        $val = $uploadFile;
                    }
                }
            }

            $QVAL = 'gid='.$gid;
            if($resType=='text' || $resType=='context' || $resType =='form') $QVAL .= ",text_val='".$val."'";
            else $QVAL .= ",varchar_val='".$val."'";

            $QVAL .=",ctiaction='".$data['ctiaction']."'";

            getDbUpdate($tbl,$QVAL,'uid='.$uid);
        }else{
            if($resType == 'img' && $val) {
                $data['file_url'] = $val;
                $uploadFile = $this->setFileTempToSave($data);
                $val = $uploadFile;
            }

            if($resType=='text' || $resType=='context' || $resType =='form') $text_val = $val;
            else $varchar_val = $val;

            // 해당 row 가 없는 경우
            $is_row_q = "vendor='".$vendor."' and bot='".$bot."' and item='".$item."' and resType='".$resType."'";
            $is_row = getDbData($tbl,$is_row_q,'uid');
            if(!$is_row['uid']){
                $QKEY = "vendor,bot,item,gid,resType,text_val,varchar_val,ctiaction";
                $QVAL = "'$vendor','$bot','$item','$gid','$resType','$text_val','$varchar_val','".$data['ctiaction']."'";
                getDbInsert($tbl,$QKEY,$QVAL);

                $uid = getDbCnt($tbl,'max(uid)',$is_row_q);
            }
        }

        // resType이 api일 경우 응답설정 저장
        $result = array();
        if($resType=='api' && $data['resApiData']) {
            $apiData = json_decode(stripcslashes($data['resApiData']), true);
            if($data['item_id'] == $apiData['itemID']) {
                $_data = array();
                $_data['vendor'] = $data['vendor'];
                $_data['bot'] = $data['bot'];
                $_data['mod'] = 'dialog';
                $_data['act'] = 'save';
                $_data['itemOC'] = $uid;
                $_data['apiData'] = $data['resApiData'];
                $_data['val'] = $val;
                $this->controlLegacyApiData($_data);

                $result['apiItemOC'] = $_data['itemOC'];
            }
        }

        // 변경된 이미지 적용을 위해 저장
        if($uploadFile) {
            $file = substr($uploadFile, (strrpos($uploadFile, "/")+1));
            $result['res-img'] = array('uid'=>$uid, 'file'=>$file, 'img'=>$uploadFile);
        }
        return $result;
    }

    // dialog graph 에러 있는지 여부 체크
    function isDialogGraphValid($data){
        $graph = $data['graph'];
        preg_match('/(errors:|error)/',$graph,$match);

        if($match[0]) return false;
        else return true;
    }

    // 다이얼로그 저장
    function regisDialog($data){
        global $table,$date;

        $tbl = $table[$this->module.'dialog'];
        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $dialog = $data['dialog'];
        $graph = $data['graph'];
        $name = $data['name'];
        $gid = $data['dialog_gid'];
        $_today = $date['totime'];

        $isDialogGraphValid = $this->isDialogGraphValid($data);

        $R = getDbData($tbl,'uid='.$dialog.' and gid='.$gid,'uid');

        if($R['uid']){
            // graph valid 체크
            if($isDialogGraphValid) getDbUpdate($tbl,"graph='$graph',d_update='$_today'",'uid='.$R['uid']);

            $last_dialog = $R['uid'];
        } else{
            $_wh = 'vendor='.$vendor.' and bot='.$bot;
            $MAXG = getDbCnt($tbl.'max(gid)',$_wh);
            $gid = $MAXG+1;
            $QKEY = "gid,active,vendor,bot,graph";
            $QVAL = "'$gid',1,'$vendor','$bot','$graph'";

            // graph valid 체크
            if($isDialogGraphValid){
                getDbInsert($tbl,$QKEY,$QVAL);

                $_sql = 'vendor='.$vendor.' and bot='.$bot;
                $last_dialog = getDbCnt($tbl,'max(uid)',$_sql);
            }
            else $last_dialog = 0;
        }
        return $last_dialog;
    }

    // 노드 데이타 저장/업데이트 함수
    function saveNodeData($data){
        $data = getRemoveBackslash($data);

        // 다이얼로그 graph 업데이트
        $data['dialog'] = $this->regisDialog($data);

        // 다이얼로그 Node 저장/수정
        $node = $this->regisNode($data);
        $apiItemOC = '';
        $resImgVal = array();

        if($node) {
            $data['node'] = $node;
            // 대화상자 못찾은 경우 응답하는 대화상자 여부값
            if($data['is_unknown'] && !$data['node']){
                $data['node'] = $node;
            }

            // 응답 Header 처리 (순서 중요)
            $this->regisResGroupHeader($data);

            // 응답 Body 처리
            $resultBody = $this->regisResGroupBody($data);
            $apiItemOC = $resultBody['apiItemOC'];
            $resImgVal = $resultBody['resImgVal'];
        }

        //aramjo
        $result = array('node'=>$node, 'apiItemOC'=>$apiItemOC, 'resImgVal'=>$resImgVal);
        return $result;
    }

    function importDataByFile($data){
        global $g,$date,$table;

        $m = $this->module;
        $importType = $data['type'];

        // 파일 타입 체크, 용량 체크
        $fcheck = getUploadFileCheck($data['file'], 'excel', (2*1024*1024));
        if($fcheck !== true) {
            $result = array(-1, $fcheck);
            echo json_encode($result);
            exit;
        }

        $saveDir = $g['path_tmp'].'cache';

        // 1시간 전 파일 삭제
        $nNowTime = time();
        $aDir = dir($saveDir);
        while ($chFileName = $aDir->read()) {
            if ($chFileName != "." && $chFileName != "..") {
                $nFileTime = fileatime($saveDir."/".$chFileName);
                if (($nNowTime - 1800) > $nFileTime) {
    				unlink($saveDir."/".$chFileName);
    			}
            }
        }

        //선택한 파일 정보 값
        $Upfile = $data['file']; // 선택한 파일
        $tmpname = $Upfile['tmp_name']; // 임시파일
        $realname = $Upfile['name']; // 실제 파일
        $fileExt    = strtolower(getExt($realname)); // 확장자 얻기

        if (is_uploaded_file($tmpname)) { // 파일이 업로드되었다 가 참이면....
            $newname =$g['time_start'].'_'.rand(10000, 99999).'_'.$realname; // 년월일_파일명.확장자
            $saveFile = $saveDir.'/'.$newname;
            if ($Overwrite == 'true' || !is_file($saveFile)) {
                move_uploaded_file($tmpname,$saveFile);
                @chmod($saveFile,0644); // 새로 들어왔으니 권한 신규 부여
            }
        } // 파일업로드 체크

        include_once $g['dir_module'] . "lib/phpExcel/PHPExcel.php";
        include_once $g['dir_module'] . "lib/phpExcel/PHPExcel/IOFactory.php";

        $CPHPExcel = new PHPExcel();
        $objReader = PHPExcel_IOFactory::createReaderForFile($saveFile);
        $objReader->setReadDataOnly(true);
        $CPHPExcel = $objReader->load($saveFile);
        $CPHPExcel->setActiveSheetIndex(0);
        $objWorksheet = $CPHPExcel->getActiveSheet();

        $nMaxColumn = $objWorksheet->getHighestColumn();
        $nMaxRow = $objWorksheet->getHighestRow();

        $rows = array();
        foreach ($objWorksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cells = array();
            foreach ($cellIterator as $cell) {
                $cells[] = getRemoveBackslash($cell->getValue());
            }
            $rows[] = $cells;
        }
        $data['rows'] = $rows;

        if($importType=='intent') $result = $this->importIntentData($data);
        else if($importType=='entity') $result = $this->importEntityData($data);
        else if($importType=='sysEntity'){
            $data['sys'] = true; // 시스템-엔터티
            $result = $this->importEntityData($data);
        } else if($importType=='chat_TSTest') $result = $data['rows']; // TS 테스트

        return $result;
    }

    function importIntentData($data){
        global $g,$date,$table;

        $m = $this->module;
        $rows = $data['rows']; // 엑셀 rows
        $hidden = 0;
        $sys = isset($data['sys']) && $data['sys'] == 'true' ? true : false;
        $type = $sys ? 'S' : 'V';
        $vendor = $sys ? 0 : $data['vendor'];
        $bot = $sys ? 0 : $data['bot'];

        $start_y = 1;

        for($y = $start_y, $nCnt=count($rows); $y < $nCnt; $y++) {
            $name = trim($rows[$y][0]); // 인텐트명
            if($type == 'S') {
                $name = '시스템-'.str_replace('시스템-', '', $name);
            }
            $content = trim($rows[$y][1]);// 예문

            if($name){
                $is_sql = "vendor='".$vendor."' and bot='".$bot."' and name='".$name."'";
                $is_row = getDbData($table[$m.'intent'],$is_sql,'uid');

                if($is_row['uid']){
                    $intent = $is_row['uid'];
                }else{
                    $QKEY = "type,vendor,bot,name";
                    $QVAL = "'$type','$vendor','$bot','$name'";
                    getDbInsert($table[$m.'intent'],$QKEY,$QVAL);

                    $intent = getDbCnt($table[$m.'intent'],'max(uid)','');
                }
                $is_ex_sql = "vendor='".$vendor."' and bot='".$bot."' and intent='".$intent."' and content='".$content."'";
                $is_ex = getDbRows($table[$m.'intentEx'],$is_ex_sql,'uid');

                if(!$is_ex['uid']){
                    $QKEY = "type,vendor,bot,intent,content";
                    $QVAL = "'$type','$vendor','$bot','$intent','$content'";
                    getDbInsert($table[$m.'intentEx'],$QKEY,$QVAL);
                }
            }
        }

        // 의도 자동 학습
        $this->getTrainIntentPesoNLP($data);

        $result = array();
        $result['msg'] ='OK';
    }

    function getUniqString($split,$str){
        $str_arr = explode($split,$str);
        $uniq_arr = array_unique($str_arr);
        $result = implode($split,$uniq_arr);
        return $result;
    }

    function importEntityData($data){
        global $g,$date,$table;

        $m = $this->module;
        $rows = $data['rows'];

        $tbl_ex = $table[$m.'entityVal'];

        $hidden = 0;
        $type = $data['sys']?'S':'V'; // 시스템 엔터티 vs 벤더 엔터티
        $vendor = $data['vendor']?$data['vendor']:0;
        $bot = $data['bot']?$data['bot']:0;

        $start_y = 1;

        for($y = $start_y, $nCnt=count($rows); $y <$nCnt; $y++) {
            $name = $rows[$y][0]; // 엔터티명
            $entityVal = $rows[$y][1];// 엔터티 벨류
            $syn_str = $rows[$y][2];// 유사어
            $synonyms = $this->getUniqString(',',$syn_str);

            if($name){
                if($data['sys']) $is_sql = "name='".$name."'";
                else $is_sql = "vendor='".$vendor."' and bot='".$bot."' and name='".$name."'";
                $is_row = getDbData($table[$m.'entity'],$is_sql,'uid');

                if($is_row['uid']){
                    $entity = $is_row['uid'];
                }else{
                    $QKEY = "type,vendor,bot,name";
                    $QVAL = "'$type','$vendor','$bot','$name'";
                    getDbInsert($table[$m.'entity'],$QKEY,$QVAL);

                    $entity = getDbCnt($table[$m.'entity'],'max(uid)','');
                }

                $is_ex_sql = "vendor='".$vendor."' and bot='".$bot."' and entity='".$entity."' and name='".$entityVal."'";
                $is_ex = getDbData($tbl_ex,$is_ex_sql,'uid,synonyms');

                if($is_ex['uid']){
                    $syn_old = $this->getUniqString(',',$is_ex['synonyms']);
                    $syn_total = rtrim($syn_old,',').','.$synonyms;
                    $new_syn = $this->getUniqString(',',$syn_total);
                    getDbUpdate($tbl_ex,"synonyms='".$new_syn."'",'uid='.$is_ex['uid']);
                }else{
                    $QKEY = "type,vendor,bot,entity,name,synonyms";
                    $QVAL = "'$type','$vendor','$bot','$entity','$entityVal','$synonyms'";
                    getDbInsert($tbl_ex,$QKEY,$QVAL);
                }
            }
        }

        $result = array();
        $result['msg'] ='OK';
    }

    function uploadFile($data){
        global $g,$date,$table,$my;

        $saveDir = $g['path_tmp'].'upload';

        // 1시간 이전 temp 파일 삭제
        $nNowTime = time();
        $aDir = dir($saveDir);
        while ($chFileName = $aDir->read() ) {
			if ($chFileName != "." && $chFileName != "..") {
			    $nFileTime = fileatime($saveDir."/".$chFileName);
				if (($nNowTime - 1800) > $nFileTime) {
				    unlink($saveDir."/".$chFileName);
				}
			}
		}

        // 파일 타입 체크, 용량 체크
        $fcheck = getUploadFileCheck($data['file'], 'image', (2*1024*1024));
        if($fcheck !== true) {
            $result = array(-1, $fcheck);
            echo json_encode($result);
            exit;
        }

        if(!$data['sescode']){
            $code='200';
            $msg='정상적인 접근이 아닙니다.';
            $result=array($code,$msg);
            echo json_encode($result);
            exit;
        }
        $url  = str_replace('.','',$saveDir);
        $name  = strtolower($data['file']['name']);
        $fileExt = getExt($name);
        $fileExt = $fileExt == 'jpeg' ? 'jpg' : $fileExt;
        $type  = getFileType($fileExt);
        $tmpname = $_tempname_ = md5($name).substr($date['totime'],8,14).rand(1000,9999);
        $tmpname = $type == 2 ? $tmpname.'.'.$fileExt : $tmpname;
        $hidden  = $type == 2 ? 1 : 0;

        move_uploaded_file($data['file']['tmp_name'], $saveDir.'/'.$tmpname);
        // 리사이즈
        $aSize = getimagesize($saveDir.'/'.$tmpname);
        if (strpos($aSize['mime'], 'image') !== false && $aSize[0] > $this->nThumbWidth) {
            $_rename = substr($_tempname_, 0, (strlen($_tempname_)-4)).rand(1000,9999).'.'.$fileExt;
            $_tmpname = getCreateThumb($saveDir, $tmpname, $_rename, $this->nThumbWidth, 0);
            @unlink($saveDir."/".$tmpname);
            $tmpname = $_tmpname;
        }

        $sourcePath = $url.'/'.$tmpname;
        $code='100';
        $src=$saveFile;
        $file_name = $_LU['name'];

        $result=array($code,$sourcePath,0,$name); // 이미지 path 및 이미지 uid 값
        return $result;
    }

    function setFileTempToSave($data) {
        global $g, $table, $date, $my;

        $userBaseDir = $_SERVER['DOCUMENT_ROOT'];

        if($data['file_url'] && file_exists($userBaseDir.$data['file_url'])) {
            // vendor,bot 추가
            $vendor = $data['vendor'];
            $bot = $data['bot'];
            $dialog = $data['dialog'];
            $mbruid  = $my['uid'];
            $paths = explode('/', $data['file_url']);
            $name = array_pop($paths);

            $m = $this->module;
            //aramjo - 이미지 디렉토리 vendor uid 값이 아닌 mbr_uid > bot_uid 로 변경
            // kiere - dialog 폴더 추가 & dialog 값 여부 체크 (2019.8.15)
            if($dialog && $dialog != 'null') $saveDir = $g['path_file'].$m.'/'.$mbruid.'/'.$bot.'/'.$dialog.'/';
            else $saveDir = $g['path_file'].$m.'/'.$mbruid.'/'.$bot.'/'; // avatar 등

            $url  = str_replace('.','',$saveDir);
            $size = $width = $height = $down = 0;
            $d_regis = $date['totime'];
            $d_update = '';
            $fileExt = getExt($name);
            $type  = getFileType($fileExt);
            $hidden  = $type == 2 ? 1 : 0;
            $tmpname = $name;

            $folder  = substr($date['today'],0,4).'/'.substr($date['today'],4,2).'/'.substr($date['today'],6,2);
            $savePath = $saveDir.$folder;
            $path_arr = explode('/',$savePath);
            $newPath = '';
            foreach($path_arr as $path) {
                if($path != '') {
                    $newPath .=$path.'/';
                    if (!is_dir($userBaseDir.'/'.$newPath)){
                        mkdir($userBaseDir.'/'.$newPath,0707);
                        @chmod($userBaseDir.'/'.$newPath,0707);
                    }
                }
            }

            $saveFile = $savePath.'/'.$tmpname;
            @rename($userBaseDir.$data['file_url'], $saveFile);
            if ($type == 2) {
                $IM = getimagesize($saveFile);
                $width = $IM[0];
                $height= $IM[1];
                $size = filesize($saveFile);
            }
            @chmod($saveFile,0644);

            $mingid = getDbCnt($table[$m.'upload'],'min(gid)','');
            $gid = $mingid ? $mingid - 1 : 100000000;

            $QKEY = "gid,hidden,tmpcode,vendor,bot,dialog,mbruid,type,ext,fserver,url,folder,name,tmpname,thumbname,size,width,height,caption,d_regis";
            $QVAL = "'$gid','$hidden','$tmpcode','$vendor','$bot','$dialog','$mbruid','$type','$fileExt','$fserver','$url','$folder','$name','$tmpname','$thumbname','$size','$width','$height','$caption','$d_regis'";
            getDbInsert($table[$m.'upload'],$QKEY,$QVAL);

            if ($gid == 100000000) db_query("OPTIMIZE TABLE ".$table[$m.'upload'],$DB_CONNECT);

            $lastuid= getDbCnt($table[$m.'upload'],'max(uid)','');
            $_LU=getUidData($table[$m.'upload'],$lastuid);
            $sourcePath=$_LU['url'].$_LU['folder'].'/'.$_LU['tmpname'];
            return $sourcePath;
        } else {
            return false;
        }
    }

    // template html 추출
    function getDialogTemplate($fileName){
        $tpl = new skin($fileName);
        //if(substr($fileName,7,11)=='inputFilter') $result = $tpl->make();
        $result = $tpl->make2(); //{$~} 도 불러와서 치환
        return $result;
    }

    // 조건 Html 추출 (node,if 통합 )
    function getRecognizeHtml($rec,$mod,$i){
        global $TMPL;

        $rec_arr = explode('|',$rec);
        if($rec_arr[0]=='or'){
            $TMPL['or_selected'] = 'selected';
            $TMPL['and_selected'] = '';
        }else if($rec_arr[0]=='and'){
            $TMPL['and_selected'] = 'selected';
            $TMPL['or_selected'] = '';
        }
        $TMPL['input_order'] = $i;
        $TMPL['filter_label'] = $rec_arr[5];
        $TMPL['filter_val'] = $rec_arr[1].'|'.$rec_arr[2].'|'.$rec_arr[3].'|'.$rec_arr[4];

        if($mod=='if'){
            $skin = new Skin('dialog/if_inputFilter_inputBox');
            $html = $skin->make();

        }else if($mod=='node'){
            $html='';
            if($i>1){
                $andOr_skin = new Skin('dialog/inputFilter_andOr');
                $html.= $andOr_skin->make();
            }
            $input_skin = new Skin('dialog/inputFilter_inputBox');
            $html.= $input_skin->make();
        }
        return $html;
    }

    // 컨텍스트 List html 추출
    function getContextListHtml($data){
        global $TMPL;
        if($data['contextSet']){
            $contextSet = $data['contextSet'];
            $contextArray = explode('|',$contextSet);
            $TMPL['contextName'] = $contextArray[0]?$contextArray[0]:'';
            $TMPL['contextValue'] = $contextArray[1]?$contextArray[1]:'';
        }else{
            $TMPL['contextName'] = '';
            $TMPL['contextValue'] = '';
        }

        $TMPL['contextOrder'] = $data['order']?$data['order']:1;
        $TMPL['callContext'] = $this->callContext;
        $TMPL['group_id'] = $data['group_id'];
        $TMPL['item_id'] = $data['item_id'];

        if($data['multiMenu']){
            $addBtn = new Skin('dialog/multiMenuContextRow_addBtn');
            $TMPL['addBtn'] = $addBtn->make();

            $skin = new Skin('dialog/multiMenuContextForm_row');
        }
        else $skin = new Skin('dialog/contextForm_row');

        $html = $skin->make();
        return $html;
    }

    // node list html
    function getNodeListHtml($data){
        $nodeList= $this->getNodeList($data);
        $nodeList_html = '
           <select class="form-control" name="jumpTo_node" data-role="nodeAction-nodeList">
           '.$nodeList['option'].'
           </select>';

         return $nodeList_html;
    }

    // 노드 action html 추출
    function getNodeActionHtml($R){
        $nodeAction_html ='<select class="form-control" data-role="select-nodeAction" name="node_action" data-node ="'.$R['uid'].'">';
        if(isset($R['uid'])){
            $nodeAction_html.='
               <option value="1" '.($R['node_action']==1?'selected':'').'>사용자 입력 대기</option>
               <option value="2" '.($R['node_action']==2?'selected':'').'>대화상자 이동 </option>';
        }else{
            $nodeAction_html.='
               <option value="1" >사용자 입력 대기</option>
               <option value="2" >대화상자 이동</option>';
        }
        $nodeAction_html.='</select>';

        if(isset($R['uid']) && $R['jumpTo_node']){
            $R['varchar_val'] = $R['jumpTo_node']; // 대화상자값에 대입
            $nodeList_html = $this->getNodeListHtml($R);
        }else{
            $nodeList_html ='';
        }
        return $nodeAction_html.$nodeList_html;
    }

    // 노드 데이타 추출
    function getNodeData($data){
        global $table,$TMPL;

        $m = $this->module;
        $id = $data['node'];
        $base_wh = $this->getDialogBaseQry($data);
        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $dialog = $data['dialog'];
        $node = $data['node'];
        $act = $data['act'];// 일반 Node (openSetNodePanel) or 대화상자 못찾음(openUKNodePanel)

        if($act =='openSetNodePanel') $_wh = $base_wh." and id='".$node."'";
        else if($act =='openUKNodePanel') $_wh = $base_wh." and track_flag=0 and is_unknown=1"; // UK Node 조건

        $R = getDbData($table[$m.'dialogNode'],$_wh,'*');

        $R['tmod'] = $data['tmod']; // 템플릿 관리자 모드에서는 다르게 출력하기 위함
        $R['data'] = $data;

        $result = array();

        if($R['uid']){
             if($act =='openUKNodePanel'){
                $result['id'] = $R['id'];
                $result['node'] = $R['id'];
                $result['nodeParent'] = $R['parent'];
                $result['is_unknown'] = true;
            }else{
                $result['is_unknown'] = false;
            }

            // 대화상자 조건
            if($R['recCondition']){
               $recConditon = explode(',',$R['recCondition']);
               $inputFilterHtml = '';
               $i=1;
               foreach ($recConditon as $recognize) {
                   $inputFilterHtml .= $this->getRecognizeHtml($recognize,'node',$i);
                   $i++;
               }
               $result['inputFilterHtml'] = $inputFilterHtml;
            }else{
               $result['inputFilterHtml'] = false;
            }

            // 대화상자 컨텍스트
            if($R['context']){
               $contextArray = explode(',',$R['context']);
               $contextListHtml = '';
               $_context = array();
               $i=1;
               foreach ($contextArray as $context) {
                   $_context['contextSet'] = $context;
                   $_context['order'] = $i;
                   $contextListHtml .= $this->getContextListHtml($_context);
                   $i++;
               }
               $result['contextListHtml'] = $contextListHtml;
            }else{
               $result['contextListHtml'] = false;
            }

            // respond Header 가져오기 with group_id
            $RGH = $this->getResGroupHeaderHtml($R);
            $result['resGroupHeaderHtml'] = $RGH['html']; // 응답그룹 헤더 html
            $result['resHeaderSql'] = $RGH['sql'];

            // respond Body 가져오기
            $RGB ='';
            foreach ($RGH['resGroup'] as $index=>$resGroup) {
                if($data['tmod']){
                    if($index==0) $TMPL['res_active'] =' active';
                    else $TMPL['res_active'] ='';
                }
                $RGB.= $this->getResGroupBodyHtml($R,$resGroup);
            }

            // respond Body 가져오기
            $result['resGroupBodyHtml'] = $RGB;
            $result['resGroup'] = $RGH['resGroup']; // api 용도
            //$result['resBodySql'] = $RGB['sql'];

            // node action html 가져오기
            $result['nodeActionHtml'] = $this->getNodeActionHtml($R);

            // 해당 챗봇 토픽 데이타
            $data['act'] = 'get-topic';
            $result['topicData'] = $this->controlTopic($data);

            // 토픽 연결값 추가
            $result['use_topic'] = $R['use_topic'];

        }else{
            $result['inputFilterHtml'] = false;
            $result['resGroupHeaderHtml'] = false;
            $result['resGroupBodyHtml'] = false;
            $result['contextListHtml'] = false;
            $result['topicData']['topicArray'] = array();

             // node action html 가져오기
            $R = array(); // 빈 함수
            $result['nodeActionHtml'] = $this->getNodeActionHtml($R);

            // 해당 챗봇 토픽 데이타
            $data['act'] = 'get-topic';
            $result['topicData'] = $this->controlTopic($data);

            // 토픽 연결값 추가
            $result['use_topic'] = $R['use_topic'];
        }
        return $result;
    }

    // respond Group Header 추출 함수
    function getResGroupHeaderHtml($R){
        global $table,$TMPL;

        $m = $this->module;
        $tbl = $table[$m.'dialogResGroup'];
        $base_wh = $this->getDialogBaseQry($R);
        $node = $R['id'];
        $_wh = $base_wh." and node='".$node."'";
        $sql = sprintf("SELECT * FROM `%s` WHERE %s ORDER BY `gid` ASC", $tbl,$_wh);
        $rows = $this->getAssoc($sql);
        $html ='';
        $resGroup = array();
        $i=0;
        foreach ($rows as $key=>$row) {
            $type_label = new Skin('dialog/respond_'.$row['resType'].'_label');
            $resHeaderMenuIcon = new Skin('dialog/respond_header_menuIcon');

            // 템플릿 모드인 경우
            if($R['tmod']){
                if($key==0) $TMPL['res_active'] = 'active';
                else $TMPL['res_active'] = '';

                $TMPL['resHeaderMenuIcon'] = $resHeaderMenuIcon->make();
            }else{
                $TMPL['resHeaderMenuIcon'] = $resHeaderMenuIcon->make();
            }

            $TMPL['group_id'] = $row['id'];
            $TMPL['resType'] = $row['resType'];
            $TMPL['group_uid'] = $row['uid']; // 삭제시 체크용도 : uid 가 없으면 바로 삭제 가능

            // 숨김 처리
            $TMPL['hidden_class'] = $row['hidden'] ? 'tab-hidden' : '';
            $TMPL['state_class'] = $row['hidden'] ? 'hide' : 'show';
            $TMPL['showHide_label'] = $row['hidden'] ? '노출' : '숨김';
            $TMPL['showStateIcon'] = $row['hidden'] ? 'eye' : 'eye-slash';

            $TMPL['type_label'] = $type_label->make();
            $skin = new Skin('dialog/respond_header');
            $html.= $skin->make();

            $resGroup[$i] = array("resType"=>$row['resType'],"id"=>$row['id'],"gid"=>$row['gid'],"uid"=>$row['uid']);

            $i++;
        }
        return array("sql"=>$sql,"resGroup"=>$resGroup,"html"=>$html);
    }

    // respond Group Body 추출 함수
    function getResGroupBodyHtml($R,$resGroup){
        global $table,$TMPL, $g;

        $m = $this->module;
        $tbl = $table[$m.'dialogResItem'];
        $base_wh = $this->getDialogBaseQry($R);
        $node = $R['id'];
        $resGroupId = $resGroup['id'];
        $resType = $resGroup['resType'];

        // Barge in
        $aBargein = $this->getSysBargein($R);
        $use_bargein = $aBargein['bargein'];
        $use_ctiaction = $aBargein['ctiaction'];

        $_wh = $base_wh." and node='".$node."' and resGroupId='".$resGroupId."'";
        $sql = sprintf("SELECT * FROM `%s` WHERE %s ORDER BY `gid` ASC", $tbl,$_wh);
        $rows = $this->getAssoc($sql);
        $items ='';

        $TMPL['group_id'] = $resGroupId;

        foreach ($rows as $index=>$row) {
            $row['tmod'] = $R['tmod']; // 템플릿 관리자 모드
            $row['bot'] = $R['bot'];
            $row['data'] = $R['data'];
            $row['use_bargein'] = $use_bargein && ($row['resType'] == 'text' || $row['resType'] == 'if') ? true : false;
            $row['use_ctiaction'] = $use_ctiaction && ($row['resType'] == 'text' || $row['resType'] == 'if') ? true : false;

            //$TMPL['group_id'] = $row['resGroupId'];
            $TMPL['item_uid'] = $row['uid']; // 삭제시 체크용도 : uid 가 없으면 바로 삭제 가능
            $TMPL['item_id'] = $row['id']; // item id
            $TMPL['res_title'] = $row['title'];
            $TMPL['res_summary'] = $row['summary'];
            $TMPL['res_imgUrl'] = $row['img_url'];
            $TMPL['res_backImg'] = 'style="background:url('.$row['img_url'].') no-repeat center top;background-size:cover;"';
            $TMPL['res_content'] = $row['content'];
            $TMPL['res_link1'] = $row['link1'];
            $TMPL['res_link2'] = $row['link2'];
            $TMPL['res_link3'] = $row['link3'];
            $TMPL['ctx_init_check'] = $row['ctx_init'] ? 'checked' : '';

            // 템플릿 모드인 경우 textarea rows 값을 2 로 조정
            if($R['tmod']){
                $dd = array();
                $dd['linkType'] ='getTempData';
                $dd['itemtype'] = 'resItem';
                $dd['itemuid'] = $row['uid'];
                $dd['vendor'] = $R['vendor'];
                $dd['bot'] = $R['bot'];
                $TD = $this->controlTempData($dd); // 템플릿 데이타 설정되었는지 체크
                if($TD['is_temp']){
                    $TMPL['TD_checked'] = $TD['active']?'checked':'';
                    $TMPL['TD_label'] = $TD['data']['label'];
                }else{
                    $TMPL['TD_checked'] ='';
                    $TMPL['TD_label'] = '';
                }
                $TMPL['TD_resType'] = $row['resType'];
                $TMPL['TD_item_uid'] = $row['uid'];
                $TMPL['TD_item_bot'] = $R['bot'];
                $TMPL['TD_item_node'] = $node;
                $TMPL['TD_item_gid'] = $index;
                $checkbox_dLabel = new skin('dialog/tempData_checkBox');
                $TMPL['ta_rows'] = '2';
                $TMPL['TD_item_type'] = 'resItem';
                $TMPL['checkbox_dLabel'] = $checkbox_dLabel->make();

                // Barge in
                if($row['use_bargein']) {
                    $TMPL['BI_item_type'] = 'item';
                    $TMPL['BI_item_uid'] = $row['uid'];
                    $TMPL['BI_checked'] = $row['bargein'] ? 'checked' : '';
                    $checkbox_bargein = new skin('dialog/bargein_checkBox');
                    $TMPL['checkbox_bargein'] = $checkbox_bargein->make();
                }

                // CTI Action
                if($row['use_ctiaction']) {
                    $aCtiAction = explode('|', $row['ctiaction']);
                    $_action = $aCtiAction[0] == '' ? 'recognize' : $aCtiAction[0];
                    $_value = isset($aCtiAction[1]) ? $aCtiAction[1] : '';
                    $_value_skill = isset($aCtiAction[2]) ? $aCtiAction[2] : '';

                    $TMPL['CTI_item_type'] = 'item';
                    $TMPL['CTI_item_uid'] = $row['uid'];
                    $TMPL['CTI_select_'.$_action] = 'selected';
                    $TMPL['CTI_view_'.$_action] = 'view_option';
                    $TMPL['CTI_value_'.$_action] = $_value;
                    $TMPL['CTI_value_'.$_action.'_skill'] = $_value_skill;
                    $checkbox_ctiaction = new skin('dialog/ctiAction_checkBox');
                    $TMPL['checkbox_ctiaction'] = $checkbox_ctiaction->make();
                }
            }else{
                $TMPL['ta_rows'] = '10';
                $TMPL['checkbox_dLabel'] = $TMPL['checkbox_bargein'] = $TMPL['checkbox_ctiaction'] = '';
            }

            // 벤더 시스템일 경우 응답설정 미표시
            if($g['chatbot_host'] || $use_bargein) $TMPL['checkbox_dLabel'] ='';

            // 선택형 메뉴(버튼)인 경우 해당 액션값 세팅
            if($row['resType']=='hMenu'||$row['resType']=='if'){
                // if: 조건 관련 부분
                if($row['resType']=='if'){
                    if($row['recCondition']){
                        $recConditon = explode(',',$row['recCondition']);
                        $inputFilterBox = '';
                        $i=1;
                        foreach ($recConditon as $recognize){
                           $inputFilterBox .= $this->getRecognizeHtml($recognize,'if',$i);
                           $i++;
                       }
                        $TMPL['inputFilterBox'] = $inputFilterBox;
                    }else{
                        $skin = new skin('dialog/if_inputFilter_inputBox');
                        $TMPL['inputFilterBox'] = $skin->make();
                    }
                }

                $TMPL['menuItem_resGroup'] = $this->getMenuItemResGroup($row); // $row['tmod']
            }
            $skin = new Skin('dialog/respond_'.$resType.'_item');
            $items.= $skin->make();
        }

        $TMPL['resItem'] = $items;
        $resBody = new skin('dialog/respond_'.$resType.'_body');
        $html = $resBody->make();
        return $html;//array("sql"=>$sql,"html"=>$html);
    }

    // 메뉴 아이템 응답그룹 추출 함수
    function getMenuItemResGroup($row){
        global $table,$TMPL;
        $TMPL['group_id'] = $row['resGroupId'];
        $TMPL['item_id'] = $row['id'];

        // 데이타 추출 및 TMPL에 이전
        $data = $this->getMultiMenuResData($row); // $row['tmod']

        foreach ($data as $key=>$val) {
            $TMPL[$key] = $val;
        }

        // 가이드 문구
        if($row['resType']=='hMenu') $TMPL['guideText'] = $this->MultiMenu_guideText;// '본 메뉴 선택시 액션';
        else if($row['resType']=='if') $TMPL['guideText'] =$this->if_MultiMenu_guideText;// '본 조건 해당시 액션';

        // 전체 세팅
        $resGroup = new skin('dialog/respond_menuItem_resGroup');
        $result = $resGroup->make();
        return $result;
    }

    // 멀티메뉴 헤더 데이타 추출
    function getMultiMenuHeaderData($data){
        $result = array(
            "text"=>array("텍스트",'<i class="fa fa-file-text-o"></i>'),
            "link"=>array("링크이동",'<i class="fa fa-link"></i>'),
            "tel"=>array("전화연결",'<i class="fa fa-phone"></i>'),
            "img"=>array("이미지",'<i class="fa fa-picture-o"></i>'),
            "context"=>array($this->callContext,'<i class="fa fa-dollar-sign"></i>'),
            "node"=>array("대화상자",'<i class="fa fa-sitemap"></i>'),
            "api"=>array("API",'<i class="fa fa-plug"></i>'),
            "form"=>array("폼",'<i class="fa fa-th-list"></i>'),
            "hform"=>array("HTML양식",'<i class="fa fa-list-alt"></i>')
        );
        return $result;
    }

    // 멀티메뉴 클릭시 응답 헤더 추출
    /*
      row = dialogResItem 테이블 row
    */
    function getMultiMenuResData($row){
        global $table,$TMPL, $g;

        $dt = $row['data'];
        $NAMT = explode('-',$dt['nowActiveMultiMenuTab']);
        $now_group = $NAMT[0];
        $now_item = $NAMT[1];
        $now_tab = $NAMT[2];
        $header_html ='';
        $data = array();
        $HeaderData = $this->getMultiMenuHeaderData($data);

        $TMPL['TD_item_bot'] = $row['bot'];

        // form 데이터 누락된 것 강제 입력
        $_R = getDbData($table[$this->module.'dialogResItemOC'], "item=".$row['uid']." and resType='form'", 'uid');
        if(!$_R['uid']) {
            $QKEY = "vendor,bot,item,gid,resType,text_val,varchar_val";
            $QVAL = "'".$row['vendor']."','".$row['bot']."','".$row['uid']."','8','form','|||','$varchar_val'";
            getDbInsert($table[$this->module.'dialogResItemOC'],$QKEY,$QVAL);
        }

        $tbl = $table[$this->module.'dialogResItemOC'];
        $RCD = getDbArray($tbl,'item='.$row['uid'],'*','gid','asc','',1);
        $i=1;
        while ($R = db_fetch_array($RCD)) {
            $resType = $R['resType'];
            $header = $HeaderData[$R['resType']];
            $TMPL['group_id'] = $row['resGroupId']; // resGroupItem 에서 넘어온 값
            $TMPL['item_id'] = $row['id']; // resGroupItem 에서 넘어온 값
            $TMPL['resType'] = $resType;
            $TMPL['label'] = $header[0];
            $TMPL['label_icon'] = $header[1];

            // multi tab active 처리
            if($dt['nowActiveMultiMenuTab'] && ($now_group == $row['resGroupId']) && ($now_item == $row['id'])){
                if($now_tab==$resType){
                    $TMPL['true_false'] = 'true';
                    $TMPL[$resType.'_active'] = ' active';
                    $TMPL['active'] = ' active';
                }else{
                    $TMPL[$resType.'_active'] = '';
                    $TMPL['active'] = '';
                    $TMPL['true_false'] = 'false';
                }
            }else{
                if($i==1){
                    $TMPL['true_false'] = 'true';
                    $TMPL[$resType.'_active'] = ' active';
                    $TMPL['active'] = ' active';
                }else{
                    $TMPL[$resType.'_active'] = '';
                    $TMPL['active'] = '';
                    $TMPL['true_false'] = 'false';
                }
            }

            $TMPL['is_response'] = '';
            if($resType=='form'){
                $TMPL['is_response'] = trim($R['text_val']) && trim($R['text_val']) != '|||' ? 'is_response' : '';
            } else {
                $TMPL['is_response'] = trim($R['text_val']) || trim($R['varchar_val']) ? 'is_response' : '';
            }

            $skin = new skin('dialog/respond_menuItem_resHeader');
            $header_html.= $skin->make();

            $tempData_checkBox = new skin('dialog/tempData_checkBox');
            $bargein_checkBox = new skin('dialog/bargein_checkBox');
            $ctiaction_checkBox = new skin('dialog/ctiAction_checkBox');

            if($row['tmod']){
                $TMPL['TD_item_type'] = 'resItemOC';
                $TMPL['TD_item_uid'] = $R['uid'];
                $TMPL['TD_item_gid'] = $row['gid'];
                $dd = array();
                $dd['linkType'] ='getTempData';
                $dd['itemtype'] = 'resItemOC';
                $dd['itemuid'] = $R['uid'];
                $dd['vendor'] = $row['vendor'];
                $dd['bot'] = $row['bot'];
                $TD = $this->controlTempData($dd); // 템플릿 데이타 설정되었는지 체크
                if($TD['is_temp']){
                    $TMPL['TD_checked'] = $TD['active']?'checked':'';
                    $TMPL['TD_label'] = $TD['data']['label'];
                }else{
                    $TMPL['TD_checked'] ='';
                    $TMPL['TD_label'] = '';
                }

                $TMPL['TD_resType'] = $resType;

                $TMPL['BI_item_type'] = 'itemoc';
                $TMPL['BI_item_uid'] = $R['uid'];
                $TMPL['BI_checked'] = $R['bargein'] ? 'checked' : '';

                $aCtiAction = explode('|', $R['ctiaction']);
                $_cti_action = $aCtiAction[0] == '' ? 'recognize' : $aCtiAction[0];
                $_cti_value = isset($aCtiAction[1]) ? $aCtiAction[1] : '';

                $TMPL['CTI_item_type'] = 'itemoc';
                $TMPL['CTI_item_uid'] = $R['uid'];
                $TMPL['CTI_select_'.$_cti_action] = 'selected';
                $TMPL['CTI_view_'.$_cti_action] = 'view_option';
                $TMPL['CTI_value_'.$_cti_action] = $_cti_value;
            }

            // body 데이타
            if($resType=='text'){
                $data['resText_uid'] = $R['uid'];
                $data['res_text'] = $R['text_val'];
                if($row['tmod'] && !$g['chatbot_host'] && !$row['use_bargein']) $TMPL['text_checkbox_dlabel'] = $tempData_checkBox->make();
                if($row['tmod'] && $row['use_bargein']) $TMPL['text_checkbox_bargein'] = $bargein_checkBox->make();
                if($row['tmod'] && $row['use_ctiaction']) $TMPL['text_checkbox_ctiaction'] = $ctiaction_checkBox->make();

            }else if($resType=='link'){
                $data['resLink_uid'] = $R['uid'];
                $data['res_link'] = $R['varchar_val'];
                if($row['tmod'] && !$g['chatbot_host'] && !$row['use_bargein']) $TMPL['link_checkbox_dlabel'] = $tempData_checkBox->make();

            }else if($resType=='tel'){
                $data['resTel_uid'] = $R['uid'];
                $data['res_tel'] = $R['varchar_val'];
                if($row['tmod'] && !$g['chatbot_host'] && !$row['use_bargein']) $TMPL['tel_checkbox_dlabel'] = $tempData_checkBox->make();

            }else if($resType=='context'){
                $data['resContext_uid'] = $R['uid'];
                $_data = array();
                $_data['group_id'] = $row['resGroupId'];
                $_data['item_id'] = $row['id'];
                $_data['multiMenu'] = true;
                $data['res_context'] ='';
                if($R['text_val']){
                    $context_arr = explode(',',$R['text_val']);
                    $i=1;
                    foreach ($context_arr as $contextSet) {
                        $_data['contextSet'] = $contextSet;
                        $_data['order'] = $i;
                        $data['res_context'].= $this->getContextListHtml($_data);
                        $i++;
                    }
                }
                else{
                    $data['res_context'].= $this->getContextListHtml($_data);
                }

            }else if($resType=='img'){
                $data['resImg_uid'] = $R['uid'];
                $data['res_imgUrl'] = $R['varchar_val'];
                if($data['res_imgUrl']) $data['resItem_backImg'] = 'style="background:url('.$R['varchar_val'].') no-repeat center top;background-size:cover;"';
                else $data['resItem_backImg'] ='';

                if($row['tmod'] && !$g['chatbot_host'] && !$row['use_bargein']) $TMPL['img_checkbox_dlabel'] = $tempData_checkBox->make();

            }else if($resType=='node'){
                $data['resNode_uid'] = $R['uid'];
                $data['res_nodeSelect'] = $this->getNodeSelect($R,'option'); // 해당 노드 id

            }else if($resType=='api'){
                $RQ = getDbData($table[$this->module.'apiReq'],'uid='.$R['varchar_val'],'api');
                $_dd = array();
                $_dd['itemRow'] = $row;
                $_dd['itemOCRow'] = $R;
                $_dd['listType'] = 'selectOption';
                $data['resApi_uid'] = $R['uid'];
                $data['req_uid'] = $R['varchar_val'];
                $data['api_uid'] = $RQ['api'];
                $data['res_apiSelect'] = $this->getLegacyApiList($_dd);

            }else if($resType=='form'){
                $data['resForm_uid'] = $R['uid'];
                $R['group_id'] = $row['resGroupId'];
                $R['item_id'] = $row['id'];
                $data['res_form'] =  $this->getDialogFormResHtml($R);
                if($row['tmod'] && $row['use_bargein']) $TMPL['form_checkbox_bargein'] = $bargein_checkBox->make();
                if($row['tmod'] && $row['use_ctiaction']) $TMPL['form_checkbox_ctiaction'] = $ctiaction_checkBox->make();

            }else if($resType=='hform'){
                $data['resHForm_uid'] = $R['uid'];
                $data['res_hformSelect'] = $this->getHtmlFormList($R,'option');
            }

            $TMPL['CTI_select_'.$_cti_action] = '';
            $TMPL['CTI_view_'.$_cti_action] = '';
            $TMPL['CTI_value_'.$_cti_action] = '';

            $i++;
        }

        // html 양식 응답 추가
        if(!isset($data['resHForm_uid'])){
            $header = $HeaderData['hform'];
            $TMPL['group_id'] = $row['resGroupId']; // resGroupItem 에서 넘어온 값
            $TMPL['item_id'] = $row['id']; // resGroupItem 에서 넘어온 값
            $TMPL['resType'] = 'hform';
            $TMPL['label'] = $header[0];
            $TMPL['label_icon'] = $header[1];
            $skin = new skin('dialog/respond_menuItem_resHeader');
            $header_html.= $skin->make();

            $data['resHForm_uid'] = null;
            $data['res_hformSelect'] = $this->getHtmlFormList($row,'option');
        }

        // api header & body 추가
        if(!isset($data['resApi_uid'])){
            $header = $HeaderData['api'];
            $TMPL['group_id'] = $row['resGroupId']; // resGroupItem 에서 넘어온 값
            $TMPL['item_id'] = $row['id']; // resGroupItem 에서 넘어온 값
            $TMPL['resType'] = 'api';
            $TMPL['label'] = $header[0];
            $TMPL['label_icon'] = $header[1];
            $skin = new skin('dialog/respond_menuItem_resHeader');
            $header_html.= $skin->make();

            $_dd = array();
            $_dd['itemRow'] = $row;
            $_dd['itemOCRow'] = $R;
            $_dd['listType'] = 'selectOption';
            $data['resApi_uid'] = null;
            $data['res_apiSelect'] = $this->getLegacyApiList($_dd);
        }

        if(!isset($data['resTel_uid'])){
            $header = $HeaderData['tel'];
            $TMPL['group_id'] = $row['resGroupId']; // resGroupItem 에서 넘어온 값
            $TMPL['item_id'] = $row['id']; // resGroupItem 에서 넘어온 값
            $TMPL['resType'] = 'tel';
            $TMPL['label'] = $header[0];
            $TMPL['label_icon'] = $header[1];
            $skin = new skin('dialog/respond_menuItem_resHeader');
            $header_html.= $skin->make();
        }

        $data['resGroup_header'] = $header_html;
        return $data;
    }

    // 폼 형식 답변 html
    function getDialogFormResHtml($R){
        global $TMPL;

        if($R['text_val']){
            $_arr = explode('|',$R['text_val']);
            $TMPL['form_ques'] = $_arr[0];
            $TMPL['form_rec'] = $_arr[1];
            $TMPL['form_contextName'] = $_arr[2];
            $TMPL['form_contextValue'] = $_arr[3];

        }else{
            $TMPL['form_ques'] ='';
            $TMPL['form_rec'] ='';
            $TMPL['form_contextName'] = '';
            $TMPL['form_contextValue'] = '';
        }
        $TMPL['callContext'] = $this->callContext;
        $TMPL['group_id'] = $R['group_id'];
        $TMPL['item_id'] = $R['item_id'];

        $skin = new Skin('dialog/multiMenuForm_row');
        $html = $skin->make();
        return $html;
    }

    // api list 추출 전용 함수
    /*
       what : 요청 데이터
    */
    function getLegacyApiList($data){
        global $TMPL;

        $itemRow = $data['itemRow'];
        $itemOCRow = $data['itemOCRow'];
        $listType = $data['listType'];
        $result = array();
        $_data = array();
        $_data['vendor'] = $itemRow['vendor'];
        $_data['bot'] = $itemRow['bot'];
        $_data['act'] = 'getApiListData';
        $apiListData = $this->controlLegacyApiData($_data);// apiReq 리스트
        $reqList ='';
        $legacy_reqOption = new skin('dialog/legacy_reqOption');
        foreach ($apiListData as $req) {
            if(isset($itemOCRow['varchar_val'])) $TMPL['check_selected'] = $itemOCRow['varchar_val']==$req['uid']?' selected':'';
            else $TMPL['check_selected'] = '';
            $TMPL['legacy_reqUid'] = $req['uid'];
            $TMPL['legacy_reqApi'] = $req['api'];
            $TMPL['legacy_reqName'] = $req['name'];
            $reqList.= $legacy_reqOption->make();
            //$reqList .='<option value="'.$req['uid'].'" data-api="'.$req['api'].'" '.$check_selected.'>'.$req['name'].'</option>';
        }

        $result['selectOption'] = $reqList;
        return $result[$listType];
    }

    public function getNodeList($data){
        global $table;

        $tbl_node = $table[$this->module.'dialogNode'];
        $base_wh = $this->getDialogBaseQry($data);
        $_wh = $base_wh.' and hidden=0';
        $RCD = getDbArray($tbl_node,$_wh,'*','gid','asc','',1);
        $option = '';
        $nodeData = array();
        while($R = db_fetch_array($RCD)){
            $nodeData[] = array("id"=>$R['id'],"name"=>$R['name']);
            if(isset($data['varchar_val'])) $check_selected = $data['varchar_val']==$R['id']?' selected':'';
            else $check_selected = '';
            $option .='<option value="'.$R['id'].'"'.$check_selected.'>! '.$R['name'].'</option>';
        }

        $result['option'] = $option;
        $result['data'] = $nodeData;
        return $result;
    }

    // node select option 추출함수
    function getNodeSelect($row,$mod){
        global $table;

        $tbl_item = $table[$this->module.'dialogResItem'];

        $item = getDbData($tbl_item,'uid='.$row['item'],'*');
        $data = array();
        $data['vendor'] = $item['vendor'];
        $data['bot'] = $item['bot'];
        $data['dialog'] = $item['dialog'];
        $data['varchar_val'] = $row['varchar_val'];
        $getNodeList = $this->getNodeList($data);

        if($mod=='option') $result = $getNodeList['option'];
        return $result;
    }

    function getLiveAccessUser($data){
        global $table;

        $_now = time();
        $tbl = $table['s_token'];
        $live_q = "expire>'".$_now."'";
        $LCD = getDbSelect($tbl,$live_q,'memberuid');
        $NUM = getDbRows($tbl,$live_q);

        $result = array();

        $result['list'] = '';
        $result['num'] = $NUM;
        return $result[$data];
    }

    // 모바일 view 페이지 출력 함수
    function getMobileView($uid){
        global $TMPL,$table,$g;

        $R=getUidData($table[$this->module.'bot'],$uid);

        $TMPL['category'] =$R['induCat'];
        $default_header = new skin('view/default_header');

        // build & regis 구분
        if($R['type']==1){
            $TMPL['registed_mark']='';
            $TMPL['btn_excute_hidden'] =' cb-hidden';
            $TMPL['copyUrlBox_hidden'] ='';
       } else if($R['type']==2){
            $TMPL['registed_mark']='<span class="registed-mark">외부</span>';
            $TMPL['btn_chat_hidden'] =' cb-hidden';
            $TMPL['copyUrlBox_hidden'] =' cb-hidden';
        }

        // 봇 베너
        if($R['upload']){
            $TMPL['view_banner'] = $this->getBotUpload($R,'src');
            $TMPL['view_logo'] = '';
        }else{
            $TMPL['view_banner'] = $g['img_layout'].'/bg_bar.png';
            $TMPL['view_logo'] = '<img src="'.$g['img_layout'].'/detail_m_logo.png'.'" alt="bottalks" class="view-logo"/>';
        }
        $TMPL['default_header'] = $default_header->make();
        $TMPL['chat_url'] = $this->getChatUrl($R);
        $TMPL['bot_avatar'] = $this->getBotAvatarSrc($R);
        $TMPL['bot_service'] = $R['service'];
        $TMPL['bot_intro'] = $R['intro'];
        $TMPL['bot_uid'] = $R['uid'];
        $TMPL['bot_id'] = $R['id'];
        $TMPL['vendor'] = $R['vendor'];
        $TMPL['recommendedList'] = $this->getCatBotList($R['induCat'],'',$where,4,1);
        $TMPL['website'] = $this->getWebUrlText('웹사이트',$R['website']);

        // 챗 박스
        $chatBox = $this->getChatBox($R['id']);
        $TMPL['chat_box'] = $chatBox->make();

        $view_markup = new skin('view/bot_view');
        return $view_markup;
    }

    // chat Box 추출
    function getChatBox($bot_id){
        global $TMPL,$my;

        $botData = $this->getBotDataFromId($bot_id);
        // bot data 파싱
        foreach ($botData as $key=>$value) {
           $TMPL[$key] = $value;
        }

        // 챗 로그 출력
        $TMPL['chat_rows'] = $this->getChatLog($bot_id,1,20);
        $TMPL['bot_id'] = $bot_id;
        $TMPL['user_avatar_src'] = $this->getUserAvatar($my['uid'],'src');

        // 이모티콘 리스트
        $TMPL['emoticon_list'] = $this->getEmoticonList($bot_id);

        // 언어 변환 버튼
        $btn_lang = new skin('chat/btn_lang');
        $TMPL['btn_lang'] = $btn_lang->make();

        $TMPL['inputBox_style'] = 'style="display:none;"';

        // 챗봇 박스 출력
        $chatbox = new skin('chat/chat_box');
        //$chatbox_markup = $chatbox->make();

        return $chatbox;
    }

    // 사용자별 chat Box 추출
    function getUserChatBox($data){
        global $TMPL,$my,$table;

        $m = $this->module;

        $bot = $data['bot'];
        $user = $data['userUid'];

        if($user) $M = getUidData($table['s_mbrid'],$user);
        $B = getUidData($table[$m.'bot'],$bot);
        $data['vendor'] = $B['vendor'];

        $bot_id = $B['id'];

        $botData = $this->getBotDataFromId($bot_id);
        // bot data 파싱
        foreach ($botData as $key=>$value) {
           $TMPL[$key] = $value;
        }

        // 챗 로그 출력
        $TMPL['chat_rows'] = $this->getUserChatLog($data);
        $TMPL['bot_id'] = $bot_id;
        if($M['uid']) $TMPL['user_avatar_src'] = $this->getUserAvatar($M['uid'],'src');

        // 이모티콘 리스트
        $TMPL['emoticon_list'] = $this->getEmoticonList($bot_id);

        // 언어 변환 버튼
        $btn_lang = new skin('chat/btn_lang');
        $TMPL['btn_lang'] = $btn_lang->make();

        $TMPL['inputBox_style'] = 'style="display:none;"';

        // 챗봇 박스 출력
        $chatbox = new skin('chat/chat_box');
        //$chatbox_markup = $chatbox->make();

        return $chatbox;
    }

    // 언어 번역 함수
    function transLang($in_lang,$out_lang,$text){
        $trans = new GoogleTranslate();
        $result = $trans->translate($in_lang,$out_lang,$text);
        return $result;
    }

    // 사용자 input 필터링
    function verifyUserInput($user_input) {
        $clean_input = $this->cleanInput($user_input);
        $this->input = $clean_input;
        return $clean_input;
    }

    function cleanInput($tmp){
        global $g,$movie;

        $cmod = $this->cmod;
        $tmp = strip_tags($tmp);

        //remove puncutation except full stops
        $tmp = preg_replace('/\.+/', '.', $tmp);
        $tmp = preg_replace('/\,+/', '', $tmp);
        $tmp = str_replace("'", " ", $tmp);
        $tmp = str_replace("\"", " ", $tmp);
        $tmp = preg_replace('/\s\s+/', ' ', $tmp);
        //replace more than 2 in a row occurances of the same char with two occurances of that char
        $tmp = preg_replace('/ㄱㄱ+/', 'ㄱㄱ', $tmp);
        $tmp = trim($tmp);
        return $tmp;
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
          "UNKNOWN"=>'UNKNOWN',
        );

        $PM_arr=explode('+',$PM);
        $pm_name='';
        for($i=0;$i<count($PM_arr);$i++) {
            $pm_name .= $pm_to_han[$PM_arr[$i]].'+';
        }
        return rtrim($pm_name,'+');
    }

    function getMopList($mop_array, $bHtml=true){
        $html='';
        $mop = $mop_array['mop'];
        $mopArray = explode(' ',$mop);
        foreach ($mopArray as $mopData) {
            $mop_arr = explode('(*|*)',$mopData);
            $mop = $mop_arr[0];
            $ps = $this->get_PM_to_Han($mop_arr[1]);
            if($bHtml) {
                $html.='<tr><th>'.$mop.'</th><td>'.$ps.'</td></tr>';
            } else {
                $html .=$mop.', ';
            }
        }
        return rtrim($html, ', ');
    }

    // entity 추출내역 분석 : 기본값 or 유사어
    //array($entity,$entityVal_uid,$DT_entityMatch,$entityVal_name,$entityVal_type,$DT_entityValue)
    function getEntityLogList($entityData, $bHtml=true){
        global $table;

        $qty = count($entityData);
        $html = '';
        if($bHtml) {
            $html .='
            <tr>
                <th class="eLog-left eLog-30">'.$this->callEntity.'명</th>
                <th class="eLog-right eLog-30">대표단어</th>
                <th class="eLog-right eLog-30">감지단어</th>
            </tr>';
        }
        for ($i=0;$i<$qty;$i++) {
            if(!$entityData[$i]) continue;
            $entity = $entityData[$i][0];
            $entityName = $entityData[$i][5]; //엔터티명
            $entityVal = $entityData[$i][3]; //대표단어
            $entityType = $entityData[$i][4]; //타입
            $entityWord = $entityData[$i][2]; //감지단어
            $entityWord .=($entityType=='S' && $entityData[$i][6] ? '('.$entityData[$i][6].')' : '');

            if($bHtml) {
                $html.='
                <tr>
                    <td class="eLog-left eLog-30">@'.$entityName.'</td>
                    <td class="eLog-left eLog-30">'.$entityVal.'</td>
                    <td class="eLog-right eLog-30">'.$entityWord.'</td>
                </tr>';
            } else {
                $html .=$entityName.': '.$entityWord.', ';
            }
        }
        return rtrim($html, ', ');
    }

    // intent 추출과정 : 각 예문을 통해서 얻은 score 리스트
    function getIntentScoreList($data){
        global $table;

        $html='<tr><th>'.$this->callIntent.'명</th><th>Score</th></tr>';
        foreach ($data as $intent => $score) {
            $I = getDbData($table[$this->module.'intent'],'uid='.$intent,'name');
             $html.='<tr><th>#'.$I['name'].'</th><th>'.$score.'</th></tr>';
        }
        return $html;
    }

    // context object 에서 값 가져오기
    function getContextVal($data){
        $find = trim($data['find']);
        $result ='';

        if(isset($this->context[$find])){
            $result = $this->context[$find];
        }
        return $result;
    }

    // 컨텍스트 값 얻기
    function getContextFileVal($data){
        global $g;
        $cfile = file($g['dir_module'].'includes/sns_log/api_context.txt');
        $find = $data['find'];

        $alpa = array();

        foreach ($cfile as $key=>$line) {
            $d = json_decode($line);
            if(isset($d->{$find})){
                $alpa[$key] = $d->{$find};
            }
        }
        $beta = array_reverse($alpa);
        return $beta[0];
    }

    function updateContext($context){
        global $g;
        foreach ($context as $key => $val) {
            $this->context[$key] = $val;
        }
    }

    // 전 단계에서 bot 이 질문했는지 여부 체크
    function getCheckPrevBotQ($data){
        global $table;
        $m = $this->module;

        $last_botQ = $this->context["last_botQ"];
        $botQ_arr = explode('|',$last_botQ);
        $q_node = $botQ_arr[0]; // 질문했던 node
        $q_text = $botQ_arr[1]; // 질문내용
        $r_rec = $botQ_arr[2]; // 유저 답변에서 찾아야할 값 (#주문,#긍정<>#부정)
        $r_ctxName = $botQ_arr[3]; // 찾아야 할 값이 있는 경우 저장할 context name
        $r_ctxVal = $botQ_arr[4]; // 찾아야 할 값이 있는 경우 저장할 context value

        $rec_Arr = explode('<>',$r_rec);
        $rec_go = $rec_Arr[0]; // 긍정 인식 : 해당 프로세스를 진행할 수 있게 하는 정보
        $rec_stop = $rec_Arr[1]; // 부정 인식 : 해당 프로세스를 중지할 수 있게 하는 정보

        // 긍정 배열
        $recArray_go = explode(',',$rec_go);
        $recArray_intent_go = array(); // 인텐트_go 배열
        $recArray_entity_go = array(); // 엔터티_go 배열
        foreach ($recArray_go as $val) {
            $mark = substr(trim($val),0,1);
            $name = substr(trim($val),1);
            if($mark =='#') array_push($recArray_intent_go,$name);
            else if($mark =='@') array_push($recArray_entity_go,$name);
        }

        // 부정 배열
        $recArray_stop = explode(',',$rec_stop);
        $recArray_intent_stop = array(); // 인텐트_stop 배열
        $recArray_entity_stop = array(); // 엔터티_stop 배열
        foreach ($recArray_stop as $val) {
            $mark = substr(trim($val),0,1);
            $name = substr(trim($val),1);
            if($mark =='#') array_push($recArray_intent_stop,$name);
            else if($mark =='@') array_push($recArray_entity_stop,$name);
        }

        $result =  array();
        $result['find_go'] = false;
        $result['find_stop'] = false;

        // 문장에서 엔터티 찾기
        $E = $this->getSentenceEntity($data);
        if($E['has_entity']){
            foreach ($E['entity_val'] as $entity_arr) {
                $entityName = $entity_arr[5];
                if(in_array($entityName,$recArray_entity_go)) $result['find_go'] = true;
                else if(in_array($entityName,$recArray_entity_stop)) $result['find_stop'] = true;
            }
            $result['entityData'] = $E['entity_val'];
        }

        $I = $this->getSimilarityIntent($data);
        if($I['intent']){
            $intent = $I['intent'];
            $score = $I['score'];
            $scoreList = $I['scoreList'];
        } else{
            $I = $this->getSentenceIntent($data); //array("intent"=>$intent,"score"=>$score,"scoreList"=>$scoreList)
            $intent = $I['intent'];
            $score = $I['score'];
            $scoreList = $I['scoreList'];
        }
        if($intent){
            $R = getDbData($table[$m.'intent'],'uid='.$intent,'name');
            $intentName = $R['name'];

            if($score >= $this->intentMV) {
                if(in_array($intentName,$recArray_intent_go)) $result['find_go'] = true;
                else if(in_array($intentName,$recArray_intent_stop)) $result['find_stop'] = true;
            }

            $result['intentName'] = $intentName;
            $result['intentScore'] = $score;
            $result['intentScoreList'] = $scoreList;
        }

        // find_go = true 인 경우 만 context 추가
        if($result['find_go']){
            $data['node'] = $q_node;
            $data['entityData'] = $E['entity_val'];
            $this->setNodeContext($data);

            $_dd = array();
            $_dd['entityData'] = $E['entity_val'];
            $_dd['contextVal'] = $r_ctxVal;
            $_contextVal = $this->setContextVal($_dd);
            if($_contextVal){
                $this->context[$r_ctxName] = $_contextVal;//$r_ctxVal;
            }
        }

        $result['jump'] = $q_node;
        $result['entityResult'] = $E;
        $result['intent_uid'] = $intent;

        $base_wh = $this->getDialogBaseQry($data);
        $R = getDbData($table[$this->module.'dialogNode'], $base_wh." and id='".$q_node."'", 'name');
        $result['nodeName'] = $R['name'];
        return $result;
    }

    function getFaqResponse($data) {
        global $table, $DB_CONNECT;

        $bs = getDbData($table[$this->module.'botSettings'], "vendor='".$data['vendor']."' and bot='".$data['botUid']."' and name='faqMV'", 'value');
        $this->faqMV = $bs['value'] ? $bs['value'] : $this->faqMV;

        $answer = array();
        $faq_rows = getDbRows($table[$this->module.'faq'],'vendor='.$data['vendor'].' and bot='.$data['bot']);
        if($faq_rows > 0 && $data['clean_input']) {
            // 특수문자, 부호 제거
            $input = getMorphStrReplace($data['clean_input']);
            $_match = "match(question) against('".$input."')";

            $query ="Select ";
            $query .="  uid, question, answer, ".$_match." as score ";
            $query .="From ".$table[$this->module.'faq']." ";
            $query .="Where vendor=".$data['vendor']." and bot=".$data['bot']." ";
            $query .="and ".$_match." ";
            $query .="Order by score DESC Limit 1";
            $R = db_fetch_assoc(db_query($query, $DB_CONNECT));
            if($R['question'] && $R['answer']) {
                // 특수문자, 부호 제거
                $target = getMorphStrReplace($R['question']);

                similar_text($input, $target, $score);
                $score = number_format(($score/100), 6);
                if($score >= $this->faqMV) {
                    $answer['res'] = $R['answer'];
                    $answer['score'] = $score;
                }
            }
        }
        return $answer;
    }

    function ProcessInput($data) {
        global $g,$table,$d;

        // 언어값 체크
        $lang = $data['lang']?$data['lang']:$this->lang;
        $cmod = $data['cmod']?$data['cmod']:$this->cmod;

        $botid = $data['botId'];
        $clean_input = $data['clean_input'];
        $msg_type = $data['msg_type'];
        $botUid = $data['botUid'];
        $roomToken = $data['roomToken'];
        $botActive = $data['botActive'];

        $result = array();
        $userChat = array();
        $botChat = array();
        $userChatInfo = array();

        // roomToken , botActive 값
        $userChat['roomToken'] = $botChat['roomToken'] = $roomToken;
        $userChat['botActive'] = $botChat['botActive'] = $botActive;

        // 입력값 세팅
        $this->user_input = $clean_input;

        // 시스템 점검
        if($cmod != 'TS') {
            $sysCheckMsg = $this->getSysCheckup($data);
            if($sysCheckMsg) {
                // 사용자 인풋 log 저장
                if($msg_type == 'say_hello') {
                    $userChat['printType'] ='W';
                    $userChat['content'] = 'say_hello';
                } else {
                    $userChat['printType'] ='T';
                    $userChat['content'] = $clean_input;
                }
                $userLastChat = $this->addChatLog($userChat);

                $_data = array();
                $_data['parse'] = false;
                $_data['api'] = $data['api'];
                $_data['text'] = $sysCheckMsg;
                $botMsg = $this->getBotTextMsg($_data);
                $result['response'] = array(array("text",$botMsg));
                $result['res_type'] ='mix';
                $result['res_end'] = true;

                // 챗봇 아웃풋 log 저장
                $botChat['printType'] ='M';
                $botChat['content'] = $result['response'];
                $botChat['last_chat'] = $userLastChat['last_chat'];
                $this->addBotChatLog($botChat);
                return $result;
            }
        }

        if($msg_type=='emoticon'){
           $emoticon_path = $g['url_root'].'/modules/'.$this->module.'/lib/emoticon';
           $user_msg = '<span class="emoticon_wrap"><img src="'.$emoticon_path.'/emo_'.$clean_input.'.png" /></span>';

           // 사용자 이모티콘채팅 저장

           $userChat['printType'] ='E';
           $userChat['content'] = $user_msg;
           $userLastChat = $this->addChatLog($userChat);

           // 답메세지 리턴
           $ran_emo = $this->getRanEmoticon($clean_input); // emoticon 단어에 따라 랜덤 이모티콘 얻기
           $response = '<span class="emoticon_wrap"><img src="'.$emoticon_path.'/emo_'.$ran_emo.'.png" /></span>';

           // 챗봇 채팅 저장
           $botChat['printType'] ='E';
           $botChat['content'] = $response;
           $botChat['last_chat'] = $userLastChat['last_chat']; // 사용자 chat uid
           $this->addBotChatLog($botChat);

           $result['res_type'] ='text';
           $result['response'] = $response;
           return $result;

        }else if($msg_type=='text'){
            // 의도분류 기준 스코어
            $bs = getDbData($table[$this->module.'botSettings'], "bot='".$botUid."' and name='intentMV'", 'value');
            $this->intentMV = $bs['value'] ? $bs['value'] : $this->intentMV;

            // 잘못된 문자 체크
            $input_array = $this->getMopAndPattern($clean_input);
            $data['input_mop'] = $input_array['mop'];

            // 사용자 인풋 log 저장
            $userChat['printType'] ='T';
            $userChat['chatType'] = $data['chatType'];
            $userChat['userId'] = $data['userId'];
            $userChat['content'] = $clean_input;
            $userChat['input_mop'] = $input_array['mop'];
            $userLastChat = $this->addChatLog($userChat);

            // 텍스트 답변에 대해서 버튼/조건 타입 답변도 Log 에 저장할 수 있돌고
            $data['last_chat'] = $userLastChat['last_chat'];

            if(isset($basic_match) && $basic_match){
                $result['res_type'] ='text';
                $result['response'] = $basic_match;

                // 챗봇 아웃풋 log 저장
                $botChat['printType'] ='T';
                $botChat['content'] = $result['response'];
                $botChat['last_chat'] = $userLastChat['last_chat']; // 사용자 chat uid
                $this->addBotChatLog($botChat);

                return $result;
            } else{
                // 전 단계 턴에서 봇이 질문을 한 경우
                if($this->context["last_botQ"]){
                    // aramjo context
                    $checkBotQ = $this->getCheckPrevBotQ($data);

                    // last_botQ context 초기화
                    $this->context["last_botQ"] ='';

                    if($checkBotQ['find_go']|| $checkBotQ['find_stop']){
                        $data['jump'] = $checkBotQ['jump'];
                        $data['intent_uid'] = $checkBotQ['intent_uid'];
                        $data['entityResult'] = $checkBotQ['entityResult'];
                        $data['nodeName'] = $checkBotQ['nodeName'];

                        if($cmod=='dialog'){
                            $result['intentName'] = $checkBotQ['intentName'];
                            $result['intentScore'] = $checkBotQ['intentScore'];
                            $result['intentMV'] = $this->intentMV;
                            $result['intentScoreList'] = $this->getIntentScoreList($checkBotQ['intentScoreList']);
                            $result['entityList'] = $this->getEntityLogList($checkBotQ['entityData']); // 리스트 확인용도
                            $result['entityData'] = $checkBotQ['entityData']; //array(38,49,'안과');
                            $result['nodeName'] = $checkBotQ['nodeName'];
                            $result['node_id'] = $checkBotQ['node_id'];
                        }

                        $result['response'] = $this->getNodeRespond($data);
                        $result['res_type'] ='mix';

                        // 챗봇 아웃풋 log 저장
                        $botChat['printType'] ='M';
                        $botChat['content'] = $result['response'];
                        $botChat['last_chat'] = $userLastChat['last_chat']; // 사용자 chat uid
                        $this->addBotChatLog($botChat);

                        // 분석 정보 log 저장
                        $userChatInfo['chat_uid'] = $userLastChat['last_chat'];
                        $userChatInfo['intent'] = $checkBotQ['intentName'];
                        $userChatInfo['intentScore'] = $checkBotQ['intentScore'];
                        $userChatInfo['node'] = $checkBotQ['nodeName'];
                        $userChatInfo['entity'] = $checkBotQ['entityData'];
                        $userChatInfo['unknown'] = 0;
                        $this->addChatLog($userChatInfo);
                        return $result;
                    }
                }

                // FAQ 응답 먼저 처리
                $faq_answer = $this->getFaqResponse($data);
                if($faq_answer['res']) {
                    $_data = array();
                    $_data['parse'] = false;
                    $_data['api'] = $data['api'];
                    $_data['text'] = $faq_answer['res'];
                    $botMsg = $this->getBotTextMsg($_data);
                    $result['response'] = array(array("text",$botMsg));
                    $result['res_type'] ='mix';
                    $result['faqRes'] = true;
                    $result['faqScore'] = $faq_answer['score'];

                    if($cmod=='dialog'){
                        $result['mopData'] = $this->getMopList($input_array);
                    }

                    // 챗봇 아웃풋 log 저장
                    $botChat['printType'] ='text';
                    $botChat['findType'] ='F';
                    $botChat['score'] =$faq_answer['score'];
                    $botChat['content'] = $result['response'];
                    $botChat['last_chat'] = $userLastChat['last_chat'];
                    $this->addBotChatLog($botChat);
                    return $result;
                }

                $dialog_match = $this->getDialogMatch($data); //대화상자 인풋 체크

                if($cmod=='dialog' || $cmod=='TS'){
                    if($dialog_match['response']){
                        $if_gid = getSearchArrayValByKey($dialog_match['response'], 'if_gid');
                    }

                    $result['intentName'] = $dialog_match['intentName'];
                    $result['intentScore'] = $dialog_match['intentScore'];

                    if($cmod=='dialog'){
                        $result['mopData'] = $this->getMopList($input_array);
                        $result['intentMV'] = $this->intentMV;
                        $result['intentScoreList'] = $this->getIntentScoreList($dialog_match['intentScoreList']);
                        $result['entityList'] = $this->getEntityLogList($dialog_match['entityData']); // 리스트 확인용도
                        $result['entityData'] = $dialog_match['entityData']; //array(38,49,'안과');
                        $result['nodeName'] = $if_gid ? $dialog_match['nodeName'].' (if : '.$if_gid.'번)' : $dialog_match['nodeName'];
                        $result['nodeQuery'] = $dialog_match['nodeQuery'];
                        $result['node_id'] = $dialog_match['default_node_id'];
                    } else {
                        $result['mopData'] = $this->getMopList($input_array, false);
                        $result['entityList'] = $this->getEntityLogList($dialog_match['entityData'], false);
                        $result['nodeName'] = $dialog_match['nodeName'];
                        $result['if_gid'] = $if_gid;
                    }

                }else if($cmod=='LC'){
                    $result['intentName'] = $dialog_match['intentName'];
                    $result['entityData'] = $dialog_match['entityData']; //array(38,49,'안과');
                    $result['nodeName'] = $dialog_match['nodeName'];
                    $result['user_input'] = $clean_input;
                }

                if($dialog_match['response'] && $dialog_match['response'][0] && !isset($dialog_match['response'][0][0]['unknown'])){
                    $result['res_type'] = $dialog_match['res_type'];
                    $result['response'] = $dialog_match['response'];
                    $result['query'] = $dialog_match['query'];
                    $result['context'] = $this->context;
                    $result['dialog_match'] = $dialog_match;

                    // 챗봇 아웃풋 log 저장
                    $botChat['printType'] ='M';
                    $botChat['content'] = $result['response'];
                    $botChat['last_chat'] = $userLastChat['last_chat']; // 사용자 chat uid
                    $this->addBotChatLog($botChat);

                    // 분석 정보 log 저장
                    $userChatInfo['chat_uid'] = $userLastChat['last_chat'];
                    $userChatInfo['intent'] = $dialog_match['intentName'];
                    $userChatInfo['intentScore'] = $dialog_match['intentScore'];
                    $userChatInfo['node'] = $dialog_match['nodeName'];
                    $userChatInfo['entity'] = $dialog_match['entityData'];
                    $userChatInfo['unknown'] = 0;
                    $this->addChatLog($userChatInfo);

                    unset($_SESSION['S_Messages']);

                }else{ // 못찾을 때 답변

                    $result['unknown'] = true;

                    // aramjo context
                    if(isset($this->context['last_botQ_parse']) && $this->context['last_botQ_parse']) {
                        $data['c_ques'] = $this->context['last_botQ_parse'];
                    }

                    // is_unknown=1 대화상자 체크
                    $data['msg_type'] = 'unknown';
                    $result['context'] = $this->context;

                    // 20230712 aramjo chatgpt
                    if($_SESSION['S_UseChatGPT']) {
                        $data['msg'] = $clean_input." 60자 이내로 대답해줘";
                        $data['text'] = $this->getChatgpt($data);
                        $data['parse'] = false; // 텍스트내 데이타 파싱함
                        $_response = $this->getBotTextMsg($data);
                        $result['findType'] = 'G';

                        if($data['api']) {
                            $result['user_input'] = $clean_input;
                            $result['res_type'] = 'mix';

                            if($this->bottype == 'call') {
                                $result['response'] = array(array("text",$_response, 'bargein'=>false, 'next_status'=>array('action'=>'recognize')));
                            } else {
                                $result['response'] = $_response;
                            }
                        } else {
                            $result['response'] = array(array("text",$_response));
                            $result['res_type'] = 'mix';
                        }
                    } else {

                        $dialog_match = $this->getDialogMatch($data);

                        if($dialog_match['response']){
                            $result['res_type'] = $dialog_match['res_type'];
                            $result['response'] = $dialog_match['response'];
                            $result['query'] = $dialog_match['query'];
                            $result['context'] = $this->context;
                            $result['dialog_match'] = $dialog_match;

                        }else{
                            $data['intent_uid'] = $dialog_match['intent_uid'];
                            $data['has_entity'] = $dialog_match['has_entity'];
                            $UN = $this->printUnKnownMsg($data); // Unknown 답변
                            if($data['api']){
                                $result['user_input'] = $clean_input;
                                $result['response'] = $UN['response'];
                                $result['res_type'] = 'mix';
                            }else{
                                $result['response'] = array(array("text",$UN['response']));
                                $result['res_type'] = 'mix';
                            }
                        }
                    }

                    // 챗봇 아웃풋 log 저장
                    $botChat['findType'] = isset($result['findType']) ? $result['findType'] : '';
                    $botChat['printType'] ='M';
                    $botChat['content'] = $result['response'];
                    $botChat['last_chat'] = $userLastChat['last_chat']; // 사용자 chat uid
                    $botChat['unknown'] = true; //
                    $this->addBotChatLog($botChat);

                    // 분석 정보 log 저장
                    $userChatInfo['chat_uid'] = $userLastChat['last_chat'];
                    $userChatInfo['intent'] = $dialog_match['intentName'];
                    $userChatInfo['intentScore'] = $dialog_match['intentScore'];
                    $userChatInfo['node'] = $dialog_match['nodeName'];
                    $userChatInfo['entity'] = $dialog_match['entityData'];
                    $userChatInfo['unknown'] = 1;
                    $this->addChatLog($userChatInfo);
                }

                $result['trackHistory'] = $dialog_match['trackHistory'];
                return $result;

            }// basic match 아닌 경우

        }else if($msg_type=='say_hello'){
            // 사용자 인풋 log 저장
            $userChat['printType'] ='W';
            $userChat['userId'] = $data['userId'];
            $userChat['chatType'] = $data['chatType'];
            $userChat['content'] = 'say_hello';
            $userLastChat = $this->addChatLog($userChat);

            //대화상자 인풋 체크
            $data['msg_type'] = $msg_type;
            $dialog_match = $this->getDialogMatch($data);
            $result['context'] = $this->context;
            $result['nodeName'] = 'Welcome';

            if($dialog_match['response']){
                $result['res_type'] = $dialog_match['res_type'];
                $result['response'] = $dialog_match['response'];
                $result['query'] = $dialog_match['query'];
                $result['context'] = $this->context;
                $result['dialog_match'] = $dialog_match;
            }else{
                $_data = array();
                $_data['parse'] = false;
                $_data['api'] = $data['api'];
                $_data['text'] = '안녕하세요';
                $botMsg = $this->getBotTextMsg($_data);
                $result['response'] = array(array("text",$botMsg));
                $result['res_type'] ='mix';

                // 챗봇 아웃풋 log 저장
                $botChat['printType'] ='M';
                $botChat['content'] = $result['response'];
                $botChat['last_chat'] = 0; // 사용자 chat uid
                $this->addBotChatLog($botChat);
            }

            // 분석 정보 log 저장
            $userChatInfo['chat_uid'] = $userLastChat['last_chat'];
            $userChatInfo['node'] = $result['nodeName'];
            $userChatInfo['unknown'] = 0;
            $this->addChatLog($userChatInfo);
            return $result;

        // aramjo callbot
        } else if($msg_type == 'hangup' || $msg_type == 'noinput' || $msg_type == 'sttfail') {
            // 사용자 인풋 log 저장
            $userChat['printType'] ='T';
            $userChat['content'] = $clean_input;
            $userLastChat = $this->addChatLog($userChat);

            if($msg_type == 'hangup') exit;

            // aramjo context
            if(isset($this->context['last_botQ_parse']) && $this->context['last_botQ_parse']) {
                $data['c_ques'] = $this->context['last_botQ_parse'];
            }

            $UN = $this->printUnKnownMsg($data); // Unknown 답변
            if($data['api']){
                $result['user_input'] = $clean_input;
                $result['response'] = $UN['response'];
                $result['res_type'] = 'mix';
            }else{
                $result['response'] = array(array("text",$UN['response']));
                $result['res_type'] = 'mix';
            }

            // 챗봇 아웃풋 log 저장
            $botChat['printType'] ='M';
            $botChat['content'] = $UN['response'];
            $botChat['last_chat'] = $userLastChat['last_chat']; // 사용자 chat uid
            $this->addBotChatLog($botChat);
            return $result;

        // login from
        }else if($msg_type=='login_form'){
            // 사용자 인풋 log 저장
            $userChat['chatType'] = $data['chatType'];
            $userChat['printType'] ='W';
            $userChat['content'] = 'login_form';
            $userLastChat = $this->addChatLog($userChat);

            $result['response'] = $this->getHTMLLoginFormRespond($data);
            $result['res_type'] ='mix';

            // 챗봇 아웃풋 log 저장
            $botChat['printType'] ='M';
            $botChat['content'] = $result['response'];
            $botChat['last_chat'] = $userLastChat['last_chat']; // 사용자 chat uid
            $this->addBotChatLog($botChat);
            return $result;
        }
    }

    function getNodeRespond($data){
        global $table;

        $data['vendor'] = $data['vendor']?$data['vendor']:$this->vendor;
        $data['bot'] = $data['bot']?$data['bot']:$this->botuid;
        $data['botuid'] = $data['bot'];
        $data['dialog'] = $data['dialog']?$data['dialog']:$this->dialog;
        $data['clean_input'] = $data['clean_input']?$data['clean_input']:($data['title']?$data['title']:''); // 버튼메뉴 title 을
        if($data['channel']) $this->channel = $data['channel'];

        $tbl = $table[$this->module.'dialogResGroup'];

        // 다이얼로그 active 상태 체크(토픽 활성/비활성)
        $result = array();

        $dialogActive = getDbData($table[$this->module.'dialog'], "vendor='".$data['vendor']."' and bot='".$data['bot']."' and uid='".$data['dialog']."'", 'type, active');
        if($dialogActive['type'] == 'T' && $dialogActive['active'] == 0) {
            if($data['cmod'] == 'dialog') {
                $data['text'] = '만료된 토픽의 응답입니다.';
                $data['parse'] = false;
                $response = $this->getBotTextMsg($data);
                $result[]= array('text',$response);
            }
        } else {
            // jump 해서 오는 경우 구분
            if($data['jump']){
                $node = $data['jump'];
                $data['node'] = $node;
                // jump 해온 경우 : $data['clean_input'] 값을 세팅하고 , entity/intent 값을 다시 체크
                $E = $this->getSentenceEntity($data); //array("has_entity"=>$has_entity,"entity_val"=>$entityVal) array($entity,$entityVal_uid,$s_match[0])
                if($E['has_entity']) $data['entityData'] = $E['entity_val'];
                else $data['entityData'] = array();
            } else{
                $node = $data['node'];
            }

            $base_wh = $this->getDialogBaseQry($data);
            $wh = $base_wh.' and node='.$node.' and hidden=0';

            if(!$data['nodeName']) {
                $R = getDbData($table[$this->module.'dialogNode'], $base_wh.' and id='.$node, 'name');
                $data['nodeName'] = $R['name'];
            }

            $RCD = getDbArray($tbl,$wh,'*','gid','asc','',1);
            while ($R = db_fetch_array($RCD)) {
                $data['resGroupId'] = $R['id'];
                $data['resType'] = $R['resType'];
                $result[] = $this->getDialogResItem($data);
            }

            // aramjo - jump해서 오는 경우이고 node가 1일 경우 응답 설정이 없을 때 기본 인사말 응답
            if($data['jump'] && $node == 1 && count($result) == 0) {
                $data['text'] = '안녕하세요';
                $data['parse'] = false;
                $response = $this->getBotTextMsg($data);
                $result[]= array('text',$response);
            }
        }

        // 쇼핑몰 api
        $shopApiResult = $this->getShopAPIRespond($data);
        if(is_array($shopApiResult) && count($shopApiResult) > 0) {
            $result = array();
            foreach($shopApiResult as $apiResult) {
                $result[] = $apiResult;
            }
        }
        return $result;
    }

    function getDialogResItem($data){
        global $table,$TMPL;

        //리턴값
        $result = array();

        $tbl = $table[$this->module.'dialogResItem'];
        $base_wh = $this->getDialogBaseQry($data);
        $node = $data['node'];
        $resType = $data['resType'];
        $wh = $base_wh.' and node='.$node;
        $wh.=" and resGroupId='".$data['resGroupId']."'";

        if($resType=='text'){
            $R = getDbData($tbl,$wh,'content,uid,bargein,ctiaction'); // 텍스트 답변

            $msg_txt = $R['content'];
            $data['text'] = $msg_txt ? $msg_txt : ($node == 1 ? '안녕하세요' : '죄송합니다. 답변 준비중입니다.');
            $data['parse'] = true; // 텍스트내 데이타 파싱함
            $response = $this->getBotTextMsg($data);

            if($this->bottype == 'call' && $data['api']) {
                $aBargein = $this->getSysBargein($data);
                $use_bargein = $aBargein['bargein'];
                $bargein = $use_bargein && $R['bargein'] ? true : false;

                //callbot--
                $aCtiAction = explode('|', $R['ctiaction']);
                $next_status = array();
                $next_status['action'] = $aCtiAction[0] == '' ? 'recognize' : $aCtiAction[0];
                if(isset($aCtiAction[1]) && $aCtiAction[1]) {
                    $next_status['value'] = $aCtiAction[1];
                }
                if($next_status['action'] == 'routing' && isset($aCtiAction[2]) && $aCtiAction[2]) {
                    $next_status['skill'] = $aCtiAction[2];
                }
                $result[]= array('text',$response,'bargein'=>$bargein, 'next_status'=>$next_status);
            } else {
                $result[]= array('text',$response);
            }

        }else if($resType=='hMenu'||$resType=='card'||$resType=='img'){
            $RCD = getDbArray($tbl,$wh,'*','gid','asc','',1);
            $rows='';
            $resArray = array();
            $hMenu_length = array();
            $total_length = 0;
            while ($R = db_fetch_array($RCD)) {
                if($data['api']){
                   $R['api'] = $data['api']; // api 인 경우 리턴값 형태를 json 형태로 하기 위해서
                   $R['botks_api'] = $data['botks_api']; // bottalks api 전용
                   $resArray[] = $this->getDialogMultiItemRow($R);
                }else{
                   $rows.= $this->getDialogMultiItemRow($R);
                }

                // hMenu 길이 체크
                if($resType =='hMenu'){
                    $word_length = strlen($R['title']);
                    $total_length += $word_length;
                }
                if($R['ctx_init']) $this->context = array(); // 221012 context init
            }

            if($resType=='hMenu'){
                if($totla_length >90) $slidePerView = '1';//$total_length/37;//'2.5';
                else if($total_length>70 && $total_length<90) $slidePerView = '2.8';
                else $slidePerView = '2.5';

            }
            else $slidePerView = '1.5';

            if($resType=='card') {
                $is_mobile = $this->is_mobile();
                $TMPL['card_freemode'] = $is_mobile ? 'true' : 'false';
                $TMPL['card_loop'] = $is_mobile ? 'true' : 'false';
                $TMPL['card_button'] = $is_mobile ? 'false' : 'true';
                $TMPL['card_center'] = 'false';
            }

            $TMPL['slidePerView'] = $slidePerView;
            $TMPL[$resType.'_rows'] = $rows;
            $skin = new skin('chat/'.$resType.'_list');
            $list = $skin->make();

            if($data['api']) $result[]= array($resType,$resArray);
            else $result[]= array($resType,$list);

        }else if($resType=='if'){
            $query = sprintf("SELECT uid,gid,id,recCondition FROM `%s` WHERE %s GROUP BY `uid` ORDER BY `gid` ASC", $tbl,$wh);
            $rows = $this->getAssoc($query);

            // shopapi
            $_wh = "vendor=".$data['vendor']." and bot=".$data['bot']." and name = 'use_shopapi'";
            $R = getDbData($table[$this->module.'botSettings'], $_wh, 'value');
            $use_shopapi = $R['value'] == 'on' ? true : false;

            $is_match = false;
            $if_rows ='';
            $if_res = array();
            foreach ($rows as $idx=>$row) {
                // shopapi
                if(preg_match('/shopapi_order_mobile/', $row['recCondition']) && !$use_shopapi) continue;

                $data['recCondition'] = $row['recCondition'];
                $data['resType'] = $resType;
                $data['item'] = $row['uid'];
                $checkCondition = $this->checkDNCondition($data); // node 조건 체크

                if($checkCondition['is_match']){
                    $data['uid'] = $row['uid'];
                    $result['if_gid'] = ($idx+1);
                    $resArray = $this->getMenuRespond($data); // 특정 메뉴를 선택한 것과 같은 결과
                    foreach ($resArray as $_res) {
                        $if_res[]= $_res;
                    }
                    $is_match =true;
                    break;
                }
            }

            if($is_match){
                if($data['api']) $result[]= array($resType,$if_res);
                else $result[] = array($resType,$if_res);
            } else {
                $result[] = array('unknown'=>true);
            }
        }

        // 챗봇 답변 Log 저장
        $botChat = array();
        $botChat['content'] = $result;
        $botChat['unknown'] = $data['msg_type'] == 'unknown' ? true : false;
        $botChat['last_chat'] = $data['last_chat']?$data['last_chat']:$this->last_chat; // 사용자 chat uid
        $this->addBotChatLog($botChat);

        $result['hMenu_length'] = $hMenu_length;
        return $result;

    }

    // 다이얼로그 박스 설정내용 매칭 함수
    /*
        processInput -> getDialogMatch -> getDialogNode -> trackDialogNode -> checkDNCondition

    */
    function getDialogMatch($data){
        global $table;

        //리턴값
        $result = array();
        $result['nodeName'] = '';
        $result['nodeResAnal'] = array();

        $tbl = $table[$this->module.'dialogResGroup'];
        $base_wh = $this->getDialogBaseQry($data);
        $msg_type = $data['msg_type'];

        if($msg_type=='say_hello') {
            $node =1;
            $_wh = $base_wh." and id=1";
            $GDN = getDbData($table[$this->module.'dialogNode'],$_wh,'*');

            //230210
            $NLP = $this->getNlpData($data);
            $result['intent_uid'] = $GDN['intent_uid'] = $NLP['intent_uid'];
            $result['intentName'] = $GDN['intentName'] = $NLP['intentName'];
            $result['intentScore'] = $GDN['intentScore'] = $NLP['intentScore'];
            $result['intentScoreList'] = $GDN['intentScoreList'] = $NLP['intentScoreList'];
            $result['has_entity'] = $GDN['has_entity'] = $NLP['has_entity'];
            $result['entityData'] = $GDN['entityData'] = $NLP['entityData'];
            $result['entityResult'] = $GDN['entityResult'] = $NLP['entityResult'];
        }else if($msg_type=='unknown'){
            $_wh = $base_wh." and track_flag=0 and is_unknown=1";
            $R = getDbData($table[$this->module.'dialogNode'],$_wh,'*');

            $GDN = $this->getNlpData($data);
            $GDN['intent_uid'] = $GDN['intent'];
            $GDN['intentScore'] = $GDN['score'];

            $node = $GDN['node_id'] = $R['id'];
            $result['intent_uid'] = $GDN['intent_uid'];
            $result['intentName'] = $GDN['intentName'];
            $result['intentScore'] = $GDN['intentScore'];
            $result['intentScoreList'] = $GDN['intentScoreList'];
            $result['has_entity'] = $GDN['has_entity'];
            $result['entityData'] = $GDN['entityData'];
            $result['entityResult'] = $GDN['entityResult'];
            $result['nodeName'] = $R['name'] ;
            $result['node_action'] = $GDN['node_action'] = $R['node_action'];
            $result['jumpTo_node'] = $GDN['jumpTo_node'] = $R['jumpTo_node'];
            $result['trackHistory'] = $GDN['trackHistory'];
            $result['default_node_id'] = $node; //// 대화상자 로그용

        }else{
            $GDN = $this->getDialogNode($data);
            $node = $GDN['node_id'];
            $result['intent_uid'] = $GDN['intent_uid'];
            $result['intentName'] = $GDN['intentName'];
            $result['intentScore'] = $GDN['intentScore'];
            $result['intentScoreList'] = $GDN['intentScoreList'];
            $result['has_entity'] = $GDN['has_entity'];
            $result['entityData'] = $GDN['entityData'];
            $result['nodeName'] = $GDN['nodeName'];
            $result['nodeQuery'] = $GDN['nodeQuery'];
            $result['node_action'] = $GDN['node_action'];
            $result['jumpTo_node'] = $GDN['jumpTo_node'];
            $result['dialog'] = $GDN['dialog'];
            $result['trackHistory'] = $GDN['trackHistory'];
            $result['default_node_id'] = $GDN['default_node_id']; //// 대화상자 로그용
            $data['dialog'] = $GDN['dialog']; // 토픽(유동적) 값 추가
        }

        // resGroup 찾는 쿼리
        $data['node'] = $node;
        $data['intent_uid'] = $GDN['intent_uid'];
        $data['intentName'] = $GDN['intentName'];
        $data['intentScore'] = $GDN['intentScore'];
        $data['entityData'] = $GDN['entityData'];
        $data['entityResult'] = $GDN['entityResult'];
        $data['nodeName'] = $GDN['nodeName'];
        if($node){
            // node_action 이 jump 인 경우
            if($GDN['node_action'] ==2){
                $data['jump'] = $GDN['jumpTo_node'];
                $result['context'] = $this->setNodeContext($data);
            } else {
                $result['context'] = $this->setNodeContext($data);
            }
            $result['response'] = $this->getNodeRespond($data);
            $result['res_type'] ='mix';

        }else{
            $result['response'] = '';
            $result['res_type'] ='text';
        }
        return $result;
    }

    // 컨텍스트 값 세팅
    /*
        contextVal 타입을 먼저 체크한다.
        1. @,# 등 동적인 값이 세팅된 경우
            이 값이 존재하는 경우 즉, 문장에서 발견된 경우에만 리턴하고 그렇지 않은 경우 빈값 리턴

        2. 일반 텍스트(텍스트)로 된 경우
            해당 값을 그대로 리턴
    */
    function setContextVal($data){
        $contextVal = $data['contextVal'];
        $FC = substr($contextVal,0,1);
        if($FC == '@'){
            $result = ''; // 최초에 빈값으로 세팅 : context 업데이트시에 값이 있는 경우에만 업데이트되도록
            // 엔터티 체크
            if(isset($data['entityData'])){
                foreach ($data['entityData'] as $E) {
                    $type = $E[4];
                    $entityName = isset($E[5])?$E[5]:'';
                    $entityVal = isset($E[3])?$E[3]:'';
                    if($entityName == str_replace('@','',$contextVal)) $result = $entityVal;
                }
            }
        }else{
            $result = $contextVal;
        }
        return $result;
    }


    // node 에서 설정한 컨텍스트 체크
    function setNodeContext($data){
        global $table;

        $node = $data['jump'] ? $data['jump'] : $data['node'];
        $wh = $this->getDialogBaseQry($data).' and id='.$node;
        $R = getDbData($table[$this->module.'dialogNode'], $wh,'context');
        if($R['context']){
            $contextArray = explode(',',$R['context']);
            foreach ($contextArray as $val) {
                $contextSet = explode('|',$val);
                $contextName = $contextSet[0];
                $contextVal = $contextSet[1];
                $data['contextVal'] = $contextVal;
                $_contextVal = $this->setContextVal($data);
                if($_contextVal){
                    $this->context[$contextName] = $_contextVal;
                }
            }
        }
    }

    // 인텐트 예문중 입력문장이 있는지 체크
    function getSimilarityIntent($data){
        global $table, $DB_CONNECT;

        $result = array();
        $result['intent'] = false;
        $user_input = getEtcStrReplace(strtolower($data['clean_input']));

        $query = "Select A.intent, B.name From ".$table[$this->module.'intentEx']." A ";
        $query .="left join ".$table[$this->module.'intent']." B on A.intent = B.uid ";
        $query .="Where A.vendor = '".$data['vendor']."' and A.bot = '".$data['bot']."' ";
        $query .="and replace(regexp_replace(lower(A.content), '[\]\\\[!@#$%.&*`~^_{}:;<>+=/\\\|()-]+', ''), ' ', '') = '".$user_input."' ";
        $R = db_fetch_assoc(db_query($query, $DB_CONNECT));
        if($R['intent'] && $R['name']) {
            $result['intent'] = $R['intent'];
            $result['intentName'] = $R['name'];
            $result['score'] = 1;
            $result['scoreList'] = '';
        }
        return $result;
    }

    // 사용자 문장 NLP 결과 값 (엔터티,인텐트 .. )
    function getNlpData($data){
        $result = array();

        // 엔터티 체크
        $E = $this->getSentenceEntity($data); //array("has_entity"=>$has_entity,"entity_val"=>$entityVal) array($entity,$entityVal_uid,$s_match[0])
        if($E['has_entity']) $result['entityData'] = $E['entity_val'];
        else $result['entityData'] = array();

        $result['E'] = $E;
        $result['entityResult'] = $E;
        $result['has_entity'] = $E['has_entity'];

        $data['entityData'] = $result['entityData'];

        // 1. 인텐트 체크 > 입력문장이 인텐트 예문중에 있는지 체크하는 방식(SI = similarity intent)
        $IS = $this->getSimilarityIntent($data);

        if($IS['intent']){
            $result['intent'] = $IS['intent'];
            $result['intentName'] = $IS['intentName'];
            $result['score'] = $IS['score'];
            $result['scoreList'] = $IS['scoreList'];
        }else{
            // 2. 인텐트 체크 > 머신런닝 방식 (MI = ml intent)
            $I = $this->getSentenceIntent($data); //array("intent"=>$intent,"score"=>$score,"scoreList"=>$scoreList)

            $result['intent'] = $I['intent'];
            $result['intentName'] = $I['intentName'];
            $result['score'] = $I['score'];
            $result['scoreList'] = $I['scoreList'];
        }
        return $result;
    }

    // 사용자 입력문에서
    function getDialogNode($data){
        global $table;

        $m = $this->module;

        $result = array(); // 최종 리턴값 :
        $result['intentName'] =''; // intent 명
        $result['intentScore'] =''; // intent 명
        $result['entityData'] = array(); // entity 명 배열
        $result['node_id'] =''; // node uid
        $result['nodeName'] ='';

        $NLP = $this->getNlpData($data);
        $intent = $NLP['intent'];
        $intentName = $NLP['intentName'];
        $score = $NLP['score'];
        $scoreList = $NLP['scoreList'];
        $E = $NLP['E'];

        // 인텐트 발견되었을 경우
        if($data['cmod']=='LC') $intentCond = $intent && $score>-25;
        else $intentCond = $intent;

        if($intentCond){
            if($score >= $this->intentMV) {
                $data['intent_name'] = $intentName;
                $data['intent_uid'] = $intent;
            }
        }

        //node 찾기
        $data['intent_scoreList'] = $scoreList;
        $data['entityResult'] = $E;

        $TDN = $this->trackDialogNode($data);

        if($TDN['is_match']){
            $result['node_id'] = $TDN['node_id'];
            $result['nodeName'] = $TDN['nodeName'];
            $result['node_action'] = $TDN['node_action'];
            $result['jumpTo_node'] = $TDN['jumpTo_node'];
            $result['dialog'] = $TDN['dialog'];
            $result['default_node_id'] = $TDN['default_node_id'];
        }

        $result['intentName'] = $intentName;
        $result['intentScore'] = $score;
        $result['intentScoreList'] = $scoreList;
        $result['trackHistory'] = $TDN;
        $result['nodeQuery'] = $query;
        $result['entityResult'] = $E;
        $result['has_entity'] = $E['has_entity'];
        $result['intent_uid'] = $intent;
        $result['entityData'] = $NLP['entityData'];
        return $result; // intent_name, entity_name (배열), node_id
    }

    // Write tracking dialog node log
    function writeTDNLog($data){
        $this->TDNL[]= array(
           "dialog"=>$data['dialog'],
           "name"=>$data['nodeName'],
           "parent"=>$data['parent'],
           "depth"=>$data['depth'],
        );
    }

    // 트리구조 배열을 일차원 배열로 변경
    function getNodeTreeToArray($nodes, &$aNode=array()) {
        foreach ($nodes as $node) {
            if(isset($node['children'])) {
                $_node = $node;
                unset($_node['children']);
                $aNode[] = $_node;
                $this->getNodeTreeToArray($node['children'], $aNode);
            } else {
                $aNode[] = $node;
            }
        }
        return $aNode;
    }

    // node 트래킹 함수 (gid/계층 기준) 체크
    function trackDialogNode($data){
        global $table;

        $result = array();
        $result['is_match'] = false;

        $nodes = $this->getNodeTreeJson($data, false);
        $rows = $this->getNodeTreeToArray($nodes);

        foreach ($rows as $row) {
            if($row['track_flag'] != 1) continue;

            $row['id'] = $row['nodeid'];

            $data['nodeName'] = $row['name'];
            $data['recCondition'] = $row['recCondition'];
            $data['node_action'] = $row['node_action'];
            $data['jumpTo_node'] = $row['jumpTo_node'];
            $data['depth'] = $row['depth'];
            $this->writeTDNLog($data);

            $checkCondition = $this->checkDNCondition($data); // node 조건 체크

            if($checkCondition['is_match']){
                // 토픽 연결인 경우
                if($row['use_topic']>0){
                    $data['dialog'] = $row['use_topic'];
                    $data['topic_mod'] = true;
                    $data['parent'] = 0;
                    $data['checked_id'] = 0;
                    $data['default_node_id'] = $row['id'];
                    $data['default_node_name'] = $row['name'];
                    return $this->trackDialogNode($data);

                }else{
                    $result['is_match'] = true;
                    $result['node_id'] = $row['id']; // 첫번째 row node uid
                    $result['default_node_id'] = $data['default_node_id'] ? $data['default_node_id'] : $row['id'];
                    $result['nodeName'] = $data['default_node_name'] ? $data['default_node_name'] : $row['name'];
                    $result['node_action'] = $row['node_action'];
                    $result['jumpTo_node'] = $row['jumpTo_node'];
                    $result['dialog'] = $row['dialog'];
                    break;
                }
            }
        }

        $result['TDNL'] = $this->TDNL;
        return $result;
    }

    // node 조건 참/거짓 추출
    function getEvaluateCondition($data){
        $result = false;
        $type = $data['con_type']; // #, @, $
        $operator = $data['con_operator'];
        $op_val = $data['con_op_val'];
        $con_id = $data['con_id'];
        $con_name = $data['con_name'];
        $con_eType = $data['con_eType'];
        $intent = $data['intent_uid']; // 문장에서 발견된 intent
        $E = $data['entityResult'];
        $true_num = 0;
        $false_num = 0;

        if($type =='$'){
            $_cd = array("find"=>$con_name);
            $contextVal = $this->getContextVal($_cd);
            if($operator && $op_val){
                if($operator=='!='){
                   if($contextVal!='') $result = false;
                   else $result = true;
                }
            }else{
              if($contextVal!='') $result = true;
              else $result = false;
            }

        }else if($type=='#'){
            // 부정일 경우와 긍정일경우 구분 체크
            if($operator && $op_val){
                if($operator=='!='){
                   if($intent==$con_id) $result = false;
                   else $result = true;
                }
            }else{
              if($intent==$con_id) $result = true;
              else $result = false;
            }

        }else if($type=='@'){
            if($E['has_entity']){
                $entity_qty = count($E['entity_val']);

                for($i=0;$i<$entity_qty;$i++){
                    $entity_uid = $E['entity_val'][$i][0]; //문장에서 찾은 entity uid
                    $entityVal_uid = $E['entity_val'][$i][1]; //문장에서 찾은 entity 벨류 uid
                    $matched_val = $E['entity_val'][$i][2]; // 문장에서 entity 로 매칭된 값
                    $entityVal_name = $E['entity_val'][$i][3]; // 문장에서 찾은 entity 벨류 대표명
                    $entityType = $E['entity_val'][$i][4]; // 문장에서 찾은 entity 타입 (S or V)
                    if($entityType==$con_eType){
                        if($operator && $op_val){
                            if($operator==':'){
                                if(($entityVal_name == $op_val) && ($entity_uid == $con_id)){
                                    $true_num++;
                                }

                            }else if($operator==':!'){
                                if(($entityVal_name == $op_val) && ($entity_uid == $con_id)){
                                    $false_num++;
                                }
                            }else if($operator=='!='){
                                if($entity_uid==$con_id){
                                    $false_num++;
                                }

                            }else{ // <,>,>=,<=
                                if(($entityVal_name != $op_val) && ($entityVal_name .$operator.$op_val)){
                                    $true_num++;
                                }
                            }

                        }else{
                            if($entity_uid == $con_id){
                                $true_num++;
                            }
                        }
                    }
                }

                if($operator && $op_val){
                    if($operator==':!' || $operator=='!='){
                        if($false_num>0) $result = false;
                        else $result = true;
                    }else{
                        if($true_num>0) $result = true;
                        else $result = false;
                    }
                }else{
                    if($true_num>0) $result = true;
                    else $result = false;
                }

            // has_entity == true
            }else{
                if($operator && $op_val){
                    if($operator==':!' || $operator=='!=') $result = true;
                    else $result = false;

                }else{
                    $result = false;
                }
            // has_entity  == false
            }
        } // @


        if(!$type) $result = false;
        return $result;
    }

    // node 조건 체크
    /*
        $data['intent_uid'] = $I['intent'];
        $data['intent_name'] = $result['intentName'];
        $data['intent_scoreList'] = $scoreList;
        $data['entityResult'] = $E;
        $data['recCondition']

    */
    function checkDNCondition($data){
        $result = array();
        $result['is_match'] = true;

        if(!$data['recCondition']) {
            $result['is_match'] = false;
        } else {
            $total = ''; // and 기준으로 평가값 분리값
            $and_num = 0;
            $or_num = 0;
            $CD = explode(',',$data['recCondition']); // and|#|16|체험단상품배송|#체험단상품배송,and|@|16|쿠폰|@쿠폰', ...
            foreach ($CD as $index => $C){
                $con_data = explode('|',$C); // and|#|16|체험단상품배송|#체험단상품배송
                $con_andOr = $con_data[0]; // and, or, not
                $data['con_str'] = $C;
                $data['con_type'] = $con_data[1]; // #, @, $
                $data['con_id'] = $con_data[2]; // intent/entity --> uid, context --> id
                $data['con_name'] = $con_data[3];
                $data['con_eType'] = $con_data[4]; // S or V
                $data['con_label'] = $con_data[5];// @엔터티 or @엔터티:벨류
                $data['con_operator'] = $con_data[6]; // :
                $data['con_op_val'] = $con_data[7]; // 벨류

                // 조건 평가 결과
                $ev = $this->getEvaluateCondition($data);

                if($ev) $ev_text = 'true';
                else $ev_text ='false';

                if($con_andOr=='and'){
                    $total.='and'.$ev_text.',';
                    $and_num++;
                }else{
                    $total.= $ev_text.',';
                    $or_num ++;
                }
            }

            if($and_num){ // and 가 있는 경우
                $total_arr = explode('and',$total); // true,false,true
                foreach ($total_arr as $index=>$and_group) { // and_group : true,false,true
                    if($total_arr[$index]){
                        $ag_arr = explode(',',$and_group);
                        if(!in_array('true',$ag_arr)) $result['is_match'] = false;
                    }
                }

            }else{ // or 만 있는 경우
                $ag_arr = explode(',',$total);
                if(!in_array('true',$ag_arr)) $result['is_match'] = false;
            }
        }
        return $result;
    }

    // 문장에 엔터티 value 외 다른 단어가 있는지 체크
    function hasWordWithoutEntity($data){
        $input = $data['clean_input'];
        $eData = $data['entityData'];
        foreach ($eData as $entityArr) {
            $match = $entityArr[2];
            $input = str_replace($match,'', $input);
        }

        if($input=='') $result = false;
        else $result = true;
        return $result;
    }

    function getSentenceIntent($data){
        global $g, $table;

        $user_input = strtolower($data['clean_input']);

        // bottalks에서는 mbruid로 디렉토리 설정
        $R = getDbData($table[$this->module.'bot'], "uid='".$data['bot']."'", 'mbruid');

        $saveDir = $_SERVER['DOCUMENT_ROOT'].'/files/trainData/'.$R['mbruid'].'/'.$data['bot'];
        $model_file = $saveDir.'/'.$data['vendor']."_".$data['bot']."_model.bin";

        // ---------- Predict ----------------
        if(!$user_input || !file_exists($model_file)) {
            $result = array("intent"=>'',"intentName"=>'',"score"=>'',"scoreList"=>'');
        } else {
            $chSample = "";
            if($this->intentTokenType == 'morph') {
                if($data['input_mop']) {
                    $chSample = str_replace('(*|*)', '|', $data['input_mop']);

                    //형태소 분석 (불용어 제거)
                    $chSample = getRemoveStopWords($chSample);
                } else {
                    // 형태소 분석 데이터 없을 경우 mecab 실행
                    $user_input = getMorphStrReplace($user_input);

                    //형태소 분석 (불용어 제거)
                    $chSample = getRemoveStopWords(getMecabMorph($user_input, '|'));
                }
            } else {
                $chSample = getKoreanSplit($user_input);
            }

            // predict
            $aResult = array();
            $return = 0;

            $cmd = "echo '".$chSample."' | ".$this->pesonlp." predict-prob ".$model_file." - 3";
            exec($cmd, $aResult, $return);
            if($return != 0 || count($aResult) == 0) {
                $result = array("intent"=>'',"score"=>'',"scoreList"=>'');
            } else {
                $scoreList = array();

                $probResult = explode("__label__", $aResult[0]);
                foreach($probResult as $prob) {
                    if(trim($prob) == '') continue;
                    $aProb = explode(' ', trim($prob));

                    $R = getDbData($table[$this->module.'intent'], "uid=".$aProb[0],'name');
                    if(preg_match("/^시스템-/u", $R['name'])) {
                        $keywords = implode("|", $this->aSysIntentKeyword[$R['name']]);
                        if(!preg_match("/".$keywords."/iu", $user_input)) continue;
                    }

                    $scoreList[$aProb[0]] = $aProb[1];
                }

                $intent = key($scoreList);
                $score = $scoreList[$intent];

                if($intent) {
                    $R = getDbData($table[$this->module.'intent'], "uid=".$intent,'name');
                    $intentName = $R['name'];
                }

                $result = array("intent"=>$intent, "intentName"=>$intentName, "score"=>$score, "scoreList"=>$scoreList);
            }
        }
        return $result;
    }

    // 사용자 입력문에서 entity 찾기
    function getSentenceEntity($data){
        $user_input = strtolower($data['clean_input']);
        if(!$data['input_mop']) {
            $input_array = $this->getMopAndPattern($data['clean_input']);
            $data['input_mop'] = $input_array['mop'];
        }

        $data['entityTrainData'] = $this->getEntityTrainData($data);

        // 전체 entity에서 사용자 entity만 추출
        $aUserEntity = array_filter($data['entityTrainData'], function($item) {return $item[3] === 'V';});
        $aUserEntity = array_values($aUserEntity);

        $entityVal = $_result = array();
        $has_entity = false;

        $_user_input = preg_replace('/\s/','', $user_input);
        $_result = array_map(function($item) use ($_user_input) {
            $entityVal_list = preg_replace('/\s/','',str_replace('.','',strtolower($item[2])));
            $entityValList_arr = array_unique(explode(',',$entityVal_list));
            $entityVal_filter = implode("|", $entityValList_arr);
            preg_match("/(".$entityVal_filter.")/ui", $_user_input, $match);
            if(isset($match[0]) && $match[0]) {
                return [$item[0], $item[1], $match[0], $entityValList_arr[0], $item[3], $item[4]];
            }
        }, $aUserEntity);
        $_result = array_filter($_result, function($item) {return $item;});
        if(count($_result) > 0) {
            $has_entity = true;
            $entityVal = array_values($_result);
        }

        // 아래 시스템-엔터티 리턴배열형식도 위 형식과 일치시켜줘야 한다.
        // 시스템-날짜 & 시스템-시각 세팅
        $oDate = new DateParse();
        $aResultDate = $oDate->getDateParse($data);
        if(isset($aResultDate['data'])) {
            $has_entity = true;
            foreach($aResultDate['data'] as $DT) {
                if (is_array($DT) && count($DT) > 0) {
                    if ($DT['year'] && $DT['month'] && $DT['day']) {
                        $data['entityVal'] = '날짜'; // entity 값
                        $matches = array_unique($DT['match']);
                        $match = ($matches['year'] ? $matches['year'].', ' : '').($matches['month'] ? $matches['month'].', ' : '').($matches['day'] ? $matches['day'] : '');
                        $data['entityMatch'] = rtrim($match, ', '); // 감지 단어
                        $data['entityValue'] = $DT['year'].'-'.$DT['month'].'-'.$DT['day']; // 감지 단어로 인해 실제 계산된 값
                        $entityVal[] = $this->getSysEntity($data);
                    }
                    if ($DT['hour'] && $DT['minute'] && $DT['second']) {
                        $data['entityVal'] = '시각'; // entity 값
                        $matches = array_unique($DT['match']);
                        $match = ($matches['hour'] ? $matches['hour'].', ' : '').($matches['minute'] ? $matches['minute'].', ' : '').($matches['second'] ? $matches['second'] : '');
                        $data['entityMatch'] = rtrim($match, ', '); // 감지 단어
                        $data['entityValue'] = $DT['hour'].':'.$DT['minute'].':'.$DT['second']; // 감지 단어로 인해 실제 계산된 값
                        $entityVal[] = $this->getSysEntity($data);
                    }
                    if ($DT['weekday']) {
                        $data['entityVal'] = '요일'; // entity 값
                        $data['entityMatch'] = $DT['match']['weekday']; // 감지 단어
                        $data['entityValue'] = $DT['weekday']; // 감지 단어로 인해 실제 계산된 값
                        $entityVal[] = $this->getSysEntity($data);
                    }

                }
            }
        }

        // 숫자 파싱 : 날짜 파싱에서 검출된 날짜 문자열(2022년, 오월 등) 제거
        if(isset($aResultDate['matchWord']) && $aResultDate['matchWord']) {
            $data['user_input'] = trim(preg_replace("/".str_replace(" ", "|", str_replace(",", "|", $aResultDate['matchWord']))."/iu", "", $data['clean_input']));
        } else {
            $data['user_input'] = $data['clean_input'];
        }

        // 날짜 관련 문자열 제거된 문장 형태소 분석
        $data['user_input_mop'] = getRemoveStopWords(getMecabMorph($data['user_input'], '|'), "JKS");

        // aramjo 수량, 금액, 숫자
        $oNumber = new NumberParse();

        // 수량, 금액 체크
        $aResultNumber = $oNumber->getNumberParse($data);
        if($aResultNumber['is_matched']){
            foreach($aResultNumber['data'] as $_numbers) {
                $data['entityVal'] = $_numbers['unitStr']; // entity 값
                $data['entityMatch'] = $_numbers['matched']; // 감지 단어
                $data['entityValue'] = $_numbers['sum']; // 감지 단어로 인해 실제 계산된 값
                $entityVal[] = $this->getSysEntity($data);
            }
            $has_entity = true;
        }

        // 이메일 체크
        $email_pt = '([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})';
        if(preg_match('/'.$email_pt.'/',$user_input,$match)){
            if($match[0]){
                $data['entityVal'] = 'email'; // entity 값
                $data['entityMatch'] = $match[0]; // 감지 단어
                $data['entityValue'] = $match[0]; // 감지 단어로 인해 실제 계산된 값
                $entityVal[] = $this->getSysEntity($data);
            }
            $has_entity = true;
        }

        // URL 체크
        $url_pt = '\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]';
        if(preg_match('/'.$url_pt.'/',$user_input,$match)){
            if($match[0]){
                $data['entityVal'] = 'url'; // entity 값
                $data['entityMatch'] = $match[0]; // 감지 단어
                $data['entityValue'] = $match[0]; // 감지 단어로 인해 실제 계산된 값
                $entityVal[] = $this->getSysEntity($data);
            }
            $has_entity = true;
        }
        return array("has_entity"=>$has_entity,"entity_val"=>$entityVal);
    }

    // 시스템-엔터티 추출
    function getSysEntity($data){
        $result = "";

        $entityVal = $data['entityVal'];
        $entityMatch = $data['entityMatch'];
        $entityValue = $data['entityValue'];

        // 전체 entity에서 시스템 entity만 추출
        $aSysEntity = array_filter($data['entityTrainData'], function($item) {return $item[3] === 'S';});
        $aSysEntity = array_values($aSysEntity);

        $key = array_search($entityVal, array_column($aSysEntity, 2)); // 2번째 값이 대표 value
        if($key !== false) {
            $_entity = $aSysEntity[$key];
            $result = array($_entity[0], $_entity[1], $entityMatch, $entityVal, $_entity[3], $_entity[4], $entityValue);
        }
        return $result;
    }

    function getWeekDay($n){
        $result = array("일","월","화","수","목","금","토");
        return $result[$n];
    }

    // 형태소로 검색
    function applyMopFilter($sentence){
        $sentence = preg_replace('/하고(.*)싶어/', '하고 $1 싶어', $sentence);
        $sentence = preg_replace('/(\?|\.|어떻게 |었어요|어요)/', '', $sentence);
        $sentence = preg_replace('/(.*)방법/', '$1 방법', $sentence);
        return $sentence;
    }

    // 학습용 인텐트 예문 데이타
    function getIntentTrainData($data){
        global $table;

        $m = $this->module;
        $tbl_intent = $table[$m.'intent'];
        $tbl_intentEx = $table[$m.'intentEx'];

        if(!isset($this->bottype)) {
            $_bot = $this->getUidData($table[$m.'bot'], $data['bot']);
            $this->bottype = $_bot['bottype'];
        }

        $_wh = "i.vendor='".$data['vendor']."' and i.bot='".$data['bot']."' and i.type = 'V' and i.hidden = 0 ";
        if($this->bottype == "call") $_wh .="or (i.type = 'S') ";
        $_query = "SELECT i.uid as intent, i.name, e.content FROM %s i left join %s e on i.uid = e.intent and e.hidden=0 WHERE %s ORDER BY i.type ASC, e.intent ASC";
        $query = sprintf($_query,$tbl_intent,$tbl_intentEx,$_wh);
        $rows = $this->getAssoc($query);

        $trainData = array();
        foreach ($rows as $row) {
            $trainData[] = array($row['intent'], trim($row['content']), $row['name']);
        }
        return $trainData;
    }

    // 엔터티 데이타
    function getEntityTrainData($data){
        global $table;

        $m = $this->module;
        $tbl_val = $table[$m.'entityVal'];
        $tbl_entity = $table[$m.'entity'];
        $_wh = "v.hidden=0 and ((v.type='V' and v.vendor='".$data['vendor']."' and v.bot='".$data['bot']."') or (v.type='S'))";

        $_query = "SELECT v.uid,v.type,v.entity,v.name,v.synonyms,e.name as eName FROM %s as v left join %s as e on v.entity=e.uid WHERE %s GROUP BY v.uid ORDER BY v.uid ASC";
        $query = sprintf($_query,$tbl_val,$tbl_entity,$_wh,$_query);
        $rows = $this->getAssoc($query);

        $trainData = array();
        foreach ($rows as $row) {
            $val_syn = $row['name'].($row['synonyms']?','.$row['synonyms']:'');
            $val_syn = rtrim($val_syn,',');
            $val_syn = rtrim($val_syn,', ');
            $val_syn = rtrim($val_syn,' ,');
            $trainData[] = array($row['entity'],$row['uid'],$val_syn,$row['type'],$row['eName']);
        }
        return $trainData;
    }

    // 인텐트 데이타 추출
    function getIntentData($data){
        global $table;

        $m = $this->module;
        $tbl = $table[$m.'intent'];
        if($data['vendorOnly']) {
            $_wh = "hidden=0 and type='V' and vendor='".$data['vendor']."' and bot='".$data['bot']."'";
        } else {
            if(!isset($this->bottype)) {
                $_bot = $this->getUidData($table[$m.'bot'], $data['bot']);
                $this->bottype = $_bot['bottype'];
            }
            $_wh = "hidden=0 and (type='V' and vendor='".$data['vendor']."' and bot='".$data['bot']."') ";
            if($this->bottype == "call") $_wh .="or (type='S')";
        }

        $query = sprintf("SELECT `uid`,`type`,`name` FROM `%s` WHERE %s ORDER BY `type` ASC, `name` ASC", $tbl,$_wh);
        $rows=$this->getAssoc($query);

        $result= array();
        foreach ($rows as $row) {
           $result[] = array("uid"=>$row['uid'],"type"=>$row['type'],"name"=>$row['name']);
        }
        return array("content"=>$result,"query"=>$query);
    }

     // 엔터티 데이타 추출
    function getEntityData($data){
        global $table;

        $m = $this->module;
        // 쿼리 분기
        if($data['sysOnly']) $_wh = "A.hidden=0 and A.type='S' ";
        else if($data['vendorOnly']) $_wh = "A.hidden=0 and A.type='V' and A.vendor='".$data['vendor']."' and A.bot='".$data['bot']."' ";
        else $_wh = "A.hidden=0 and A.type='S' or (A.type='V' and A.vendor='".$data['vendor']."' and A.bot='".$data['bot']."') ";

        $query = "Select A.uid, A.type, A.name, B.uid as val_uid, B.name as val_name From ".$table[$m.'entity']." A ";
        $query .="left join ".$table[$m.'entityVal']." B on B.hidden = 0 and B.entity = A.uid ";
        $query .="Where ".$_wh;
        $query .="Order by A.type ASC, A.name ASC, val_uid ASC";
        $rows=$this->getAssoc($query);

        $result= array();
        foreach ($rows as $row) {
            if(!array_key_exists($row['uid'], $result)) {
                $result[$row['uid']] = array("uid"=>$row['uid'],"type"=>$row['type'],"name"=>$row['name'],"value"=>array(array("uid"=>$row['val_uid'],"name"=>$row['val_name'])));;
            } else {
                $result[$row['uid']]['value'][] = array("uid"=>$row['val_uid'],"name"=>$row['val_name']);
            }
        }
        $result = array_values($result);
        return array("content"=>$result,"query"=>$query);
    }


    // 잘 몰라요 답변
    function printUnKnownMsg($data){
        $result = array();

        // aramjo context
        if($data['c_ques']) {
            // 폼 질문이 있을 경우
            $dontKnowMsg = '죄송합니다. 다시 내용을 확인하고 답변해주세요. '.$data['c_ques'];
        } else {
            // 일반 unknown일 경우
            $dontKnowMsg = ($data['msg_type'] == 'noinput' || $data['msg_type'] == 'sttfail') ? '죄송합니다. 다시 한번 말씀해주세요.' : '아직 제가 이해를 못하는 말이네요.';
        }

        $_data = array();
        $_data['parse'] = false;
        $_data['text'] = $dontKnowMsg;
        $botMsg = $this->getBotTextMsg($_data);

        if($data['api']){
            if($data['cmod']=='LC') $result['response'] = 'NO DATA';
            else $result['response'] = $dontKnowMsg;
            $result['res_type'] = 'text';
        }else{
            $result['response'] = $botMsg;
            $result['res_type'] = 'text';
        }
        return $result;
    }

    function replaceCallbackContext($match){
        $data = array();
        $data['context_express'] = $match[1];
        return $this->getReplaceContextExpress($data);
    }

    // 쿼리에서 컨텐스트 추출
    function getReplaceQryContext($qry){
        return preg_replace_callback('/\{\$([^}]+)\}/','self::replaceCallbackContext',$qry);
    }

    function getDialogMultiItemRow($data){
        global $table,$TMPL,$g;

        $vendor = $this->vendor;
        $bot = $this->botuid;
        $dialog = $this->dialog;
        $resType = $data['resType'];
        $link = $this->getReplaceQryContext($data['link1']);

        $TMPL['uid'] = $data['uid'];
        $TMPL['vendor'] = $data['vendor'];
        $TMPL['bot'] = $data['bot'];
        $TMPL['dialog'] = $data['dialog'];
        $TMPL['node'] = $data['node'];
        $TMPL['resType'] = $data['resType'];
        $TMPL['res_title'] = $data['title'];
        $TMPL['res_summary'] = $data['summary'];
        $TMPL['res_backImg'] = 'style="background:url('.$data['img_url'].') no-repeat;background-size: cover; background-position: center top;"';
        $TMPL['res_link1'] = $link;
        $TMPL['res_cLink'] = ''; //aramjo 버튼/카드 onclick 이벤트용

        if($data['api']){
            $img_url = $g['url_host'].$data['img_url'];

            if($resType=='hMenu'){
                $uid = $data['uid'];
                $_wh = "item='".$uid."' and resType='link'";
                $r = getDbData($table[$this->module.'dialogResItemOC'],$_wh,'varchar_val');
                if($r['varchar_val']) {
                    $link = preg_replace('/\r\n|\r|\n/', '', trim($r['varchar_val']));
                    $link = $this->getReplaceQryContext($link);
                } else {
                    // 문진표 자동 링크
                    if(preg_match('/문진표/', $data['title'])) {
                        $r = getDbData($table[$this->module.'botSettings'], "vendor=".$vendor." and bot=".$bot." and name='use_mediExam'", 'value');
                        if($r['value'] == 'on') $link = $this->getReplaceQryContext($this->mediExamUrl.'/R2'.$this->botid);
                    }
                }
                $json_result = array(
                    "uid"=> $data['uid'],
                    "title"=> $data['title'],
                    "link"=> $link,
                );
            }else{
                $json_result = array(
                    "uid"=> $data['uid'],
                    "title"=> $data['title'],
                    "summary"=> $data['summary'],
                    "img_url"=> $img_url,
                    "res_link"=> $link,
                    "mobile_link"=> $link,
                );
            }
        }

        // aramjo 버튼/카드 링크 onclick 이벤트 추가
        if($resType=='card') {
            if($link) $TMPL['res_cLink']=stripos($link, 'javascript:') === false ? 'onclick="window.open(\''.$link.'\')"' : 'onclick="'.$link.'"';
        } else if($resType=='hMenu') {
            $uid = $data['uid'];
            $_wh = "item='".$uid."' and resType='link'";
            $r = getDbData($table[$this->module.'dialogResItemOC'],$_wh,'varchar_val');
            if($r['varchar_val']){
                $cLink = preg_replace('/\r\n|\r|\n/', '', trim($r['varchar_val']));
                $cLink = $this->getReplaceQryContext($cLink);
                $TMPL['res_cLink']=stripos($cLink, 'javascript:') === false ? 'onclick="window.open(\''.$cLink.'\')"' : 'onclick="'.$cLink.'"';
            } else {
                // 문진표 자동 링크
                if(preg_match('/문진표/', $data['title'])) {
                    $r = getDbData($table[$this->module.'botSettings'], "vendor=".$vendor." and bot=".$bot." and name='use_mediExam'", 'value');
                    if($r['value'] == 'on') {
                        $cLink = $this->getReplaceQryContext($this->mediExamUrl.'/R2'.$this->botid);
                        if($this->is_mobile() == 'M') {
                            $TMPL['res_cLink']='onclick="window.open(\''.$cLink.'\')"';
                        } else {
                            $TMPL['res_cLink']='onclick="window.open(\''.$cLink.'\', \'mediexam\', \'width=400,height=650,menubar=no,toolbar=no,scrollbars=yes\')"';
                        }
                    }
                }
            }
        }

        if($resType=='img'){
           // 이미지 별도 처리
            $ID = array();
            $ID['img_url'] = $data['img_url'];
            $PSMD = $this->getPSImgMarkupData($ID);
            foreach ($PSMD as $key => $val) {
                 $TMPL[$key] = $val;
            }
            $skin = new skin('chat/'.$resType.'_rowPS');
        }
        else $skin = new skin('chat/'.$resType.'_row');

        if($data['api']) $result = json_encode($json_result,JSON_UNESCAPED_UNICODE);
        else $result = $skin->make();
        return $result;
    }

    // 포토스와이프 이미지 마크업 리턴
    function getPSImgMarkupData($ID){
        global $g,$table;

        $img_url = $ID['img_url'];

        $backImg = 'style="background:url('.$img_url.') no-repeat;background-size: cover;background-position: center;"';
        $path_arr = explode('/',$img_url);
        $cnt = count($path_arr);
        $tmpname = $path_arr[($cnt-1)];
        $U = getDbData($table[$this->module.'upload'],"tmpname='".$tmpname."'",'uid,width,height');
        $aSize = getimagesize($_SERVER['DOCUMENT_ROOT'].$img_url);
        $result['img_w'] = $aSize[0];
        $result['img_h'] = $aSize[1];
        $result['img_src'] = $g['url_root'].$img_url;
        $result['back_img'] = $backImg;
        $result['img_uid'] = $U['uid'];
        return $result;
    }

    // 챗봇 메뉴타입 응답 클릭시 해당 답변 추출
    function getMenuRespond($data){
        global $table;

        // 버튼 클릭 인풋 Log 저장
        // 사용자 인풋 log 저장
        $query = "Select A.*, B.active, B.bottype, B.id as botid, C.cgroup From ".$table[$this->module.'dialogResItem']." A ";
        $query .="left join ".$table[$this->module.'bot']." B on A.bot = B.uid ";
        $query .="left join rb_s_mbrdata C on B.mbruid = C.memberuid ";
        $query .="Where A.uid = ".$data['uid']." ";
        $U = $this->getAssoc($query)[0];
        $this->botuid = $this->botuid ? $this->botuid : $U['bot'];
        $this->botid = $this->botid ? $this->botid : $U['botid'];
        $this->vendor = $this->vendor ? $this->vendor : $U['vendor'];
        $this->dialog = $this->dialog ? $this->dialog : $U['dialog'];
        $this->botActive = $this->botActive ? $this->botActive : $U['active'];
        $this->bottype = $this->bottype ? $this->bottype : $U['bottype'];
        $this->cgroup = $this->cgroup ? $this->cgroup : $U['cgroup'];
        $this->roomToken = $this->roomToken ? $this->roomToken : $data['roomToken'];

        $data['title'] = $data['title'] ? $data['title'] : $U['title'];
        if($data['title']) {
            $userChat['printType'] ='B';
            $userChat['chatType'] = $data['chatType'] ? $data['chatType'] : '';
            $userChat['userId'] = $data['userId'] ? $data['userId'] : '';
            $userChat['content'] = $data['title'].'에 대해서 문의드립니다.';
            $userChat['roomToken'] = $data['roomToken'] ? $data['roomToken'] : $this->roomToken;;
            if($data['node']) {
                $userChat['node'] = $data['node'];
            } else {
                $U = getDbData($table[$this->module.'dialogNode'],"vendor='".$U['vendor']."' and bot='".$U['bot']."' and dialog='".$U['dialog']."' and id='".$U['node']."'",'name');
                $userChat['node'] = $U['name'];
            }
            $userLastChat = $this->addChatLog($userChat);
        }

        $this->last_chat = $data['last_chat'] = $userLastChat['last_chat'] ? $userLastChat['last_chat'] : $this->last_chat;
        $this->roomToken = $data['roomToken'];
        if($data['channel']) $this->channel = $data['channel'];

        $ctx_arr = array("last_chat"=>$this->last_chat);
        $this->updateContext($ctx_arr);

        $aBargein = $this->getSysBargein($data);
        $data['use_bargein'] = $aBargein['bargein'];
        $data['use_ctiaction'] = $aBargein['ctiaction'];

        $tbl = $table[$this->module.'dialogResItemOC'];
        $uid = $data['uid'];
        $_wh = 'item='.$uid;

        $result= array();
        $query = sprintf("SELECT * FROM `%s` WHERE %s GROUP BY `uid` ORDER BY `gid` ASC", $tbl,$_wh);
        $rows = $this->getAssoc($query);
        foreach ($rows as $row) {
            if(trim($row['text_val']) || trim($row['varchar_val'])){
                if($row['resType'] == 'form' && $row['text_val'] == '|||') continue;
                $data['row'] = $row;
                $result[] = $this->getMenuRespondRow($data);
            }
        }

        // aramjo hMenu 응답이 없을 경우
        if(!$data['api']) {
            if($data['restype'] == 'hMenu' && (count($result) == 0 || !$result[0]['content'])) {
                $data['row'] = array('resType'=>'text', 'text_val'=>'답변 준비중입니다.');
                $result[] = $this->getMenuRespondRow($data);
            }
        }

        //aramjo context
        if($U['ctx_init']) $this->context = array();

        // 챗봇 답변 Log 저장
        if(isset($data['restype']) && $data['restype'] != 'if') {
            $botChat = array();
            $botChat['content'] = $result;
            $botChat['last_chat'] = $data['last_chat']?$data['last_chat']:$this->last_chat; // 사용자 chat uid
            $this->addBotChatLog($botChat);
        }
        return $result;
    }

    // context 배열 업데이트
    function updateContextArray($string){
        $ctxArray = array();
        $contextArray = explode(',',$string);
        foreach ($contextArray as $contextSet) {
            $context_arr = explode('|',$contextSet);
            $ctx_name = $context_arr[0];
            $ctx_val = $context_arr[1];
            $ctxArray[$ctx_name] = $ctx_val;
        }
        $this->updateContext($ctxArray);
    }

    function updateBotQuesCxt($data){
        $last_botQ = $data['ques_node'].'|'.$data['bot_ques'];
        if(!isset($this->context["botQ"]) || in_array($last_botQ, $this->context["botQ"])) {
            $this->context["botQ"][] = $last_botQ;
        }
        $this->context["last_botQ"] = $last_botQ;

        $aBotQues = explode("|", $data['bot_ques']);
        $this->context["last_botQ_parse"] = $this->getParseText(array('text'=>$aBotQues[0]));
    }

    function getMenuRespondRow($data){
        global $TMPL,$g,$table;

        $result = array();
        $r = $data['row'];
        $result['res_type'] = $resType = $r['resType'];
        $bargein = $data['use_bargein'] && $r['bargein'] ? 1 : 0;

        if($data['use_ctiaction'] && $r['ctiaction']) {
            $aCtiAction = explode('|', $r['ctiaction']);
            $next_status = array();
            $next_status['action'] = $aCtiAction[0] == '' ? 'recognize' : $aCtiAction[0];
            if(isset($aCtiAction[1]) && $aCtiAction[1]) {
                $next_status['value'] = $aCtiAction[1];
            }
            if($next_status['action'] == 'routing' && isset($aCtiAction[2]) && $aCtiAction[2]) {
                $next_status['skill'] = $aCtiAction[2];
            }
        }

        $apiResult = array(); // api 용 결과
        if($resType=='text'){
            $data['text'] = trim($r['text_val']);
            $data['parse'] = true;
            $parseText = $this->getBotTextMsg($data);
            $result['content'] = $parseText;
            //callbot--
            $apiResult[] = array('type'=>$resType,'content'=>$parseText,'bargein'=>$bargein, 'next_status'=>$next_status);
        }else if($resType =='form'){
            $_arr = explode('|',$r['text_val']);
            $ques = trim($_arr[0]);
            $rec = $_arr[1];
            $ctxName = $_arr[2];
            $ctxVal = $_arr[3];
            if($ques && $rec){
                $data['text'] = $ques;
                $data['parse'] = true;
                $parseText = $this->getBotTextMsg($data);
                $result['content'] = $parseText;
                //callbot--
                $apiResult[] = array('type'=>$resType,'content'=>$parseText,'bargein'=>$bargein, 'next_status'=>$next_status);

               // 폼 질문 context 에 저장
                $_dd =array();
                $_dd['bot_ques'] = $r['text_val'];
                $_dd['ques_node'] = $data['node'];
                $_dd['item'] = $data['uid'];
                $this->updateBotQuesCxt($_dd);
            }

        }else if($resType=='img'){
            $TMPL['img_uid'] = $r['uid'];
            $TMPL['img_parent'] = $r['item'];

            // 이미지 별도 처리
            $ID = array();
            $ID['img_url'] = $r['varchar_val'];
            $PSMD = $this->getPSImgMarkupData($ID);
            foreach ($PSMD as $key => $val) {
                 $TMPL[$key] = $val;
            }
            $skin = new skin('chat/'.$resType.'_rowPS');
            $row = $skin->make();

            // img list
            $TMPL[$resType.'_rows'] = $row;
            $TMPL['slidePerView'] = '1';
            $list = new skin('chat/'.$resType.'_list');
            $content = $list->make();
            $result['content'] = $content;

            // api 설정
            if($r['varchar_val']!=''){
                $img_url = $data['botks_api']?$g['url_host'].$r['varchar_val']:$r['varchar_val'];
            }
            else $img_url = '';

            $apiResult[] = array('type'=>$resType,'content'=>$img_url);

        } else if($resType =='link'|| $resType =='node'){
            if($resType =='link') {
                $content = $this->getReplaceQryContext($r['varchar_val']);
                $data['parse'] = true;
                $data['text'] = $data['api'] ? $content : '<a href="'.$content.'" target="_blank" class="tel-msg">'.$content.'</a>';
                $content = $this->getBotTextMsg($data);
                $result['content'] = $content;
            } else {
                $content = $r['varchar_val'];
                $result['content'] = $content;
            }

            $apiResult[] = array('type'=>$resType,'content'=>$content);

        } else if($resType == 'context'){
            if($r['text_val']) $this->updateContextArray($r['text_val']);

            $apiResult[] = array('type'=>$resType,'content'=>$r['text_val']);

        } else if($resType =='api'){

            // req 기본정보를 담아서 넘긴다.
            $R = getDbData($table[$this->module.'apiReq'],'uid='.$r['varchar_val'],'*');
            $data['apiReq'] = $R;
            $data['itemOC'] = $r['uid']; // itemOC 테이블 uid

            $apiSendResult = $this->getApiSendResult($data);
            $result['res_type'] = $apiSendResult['res_type'];
            $result['content'] = $apiSendResult['content'];

        } else if($resType =='tel'){
            $content = $r['varchar_val'];
            $_tmp = array();
            $_tmp['parse'] = false;
            $_tmp['text'] = '<a href="tel:'.$content.'" class="tel-msg">'.$content.'</a>';
            $msg = $this->getBotTextMsg($_tmp);
            $result['content'] = $msg;
            $apiResult[] = array('type'=>$resType,'content'=>$content);

        } else if($resType=='hform'){
            //$r['varchar_val'] => reserve_request [폼타입_문서명]
            $botData = $this->getBotDataFromId($this->botid);
            $formType = substr($r['varchar_val'], 0, strpos($r['varchar_val'], '_'));
            $docType = substr($r['varchar_val'], (strpos($r['varchar_val'], '_')+1));
            $category = $botData[$formType.'_category'];
            $data['botData'] = $botData;
            $data['formType'] = $formType;
            $data['docType'] = $docType;
            $data['category'] = $category;

            $TMPL['bot_avatar_src'] = $botData['bot_avatar_src'];
            $TMPL['bot_name'] = $botData['bot_name'];
            $TMPL['date'] = (date('a') == 'am' ? '오전 ':'오후 ').date('g').':'.date('i');
            $TMPL['category_type'] = $category;
            $TMPL['hform_type'] = $formType;

            // 예약 기능 응답
            if($formType == 'reserve') {
                $_result = $this->getReserveRespond($data);
            } else if($formType == 'jusobot') {
                $_result = $this->getJusobotRespond($data);
            } else {
                // gsitm
                $_result = $this->getHTMLFormRespond($data);
            }
            if(is_array($_result) && $_result['res_type']) {
                $result['res_type'] = $_result['res_type'];
                $result['content'] = $_result['content'];
            } else {
                $result['content'] = $_result;
            }
            //callbot--
            $apiResult[] = array('type'=>$_result['res_type'], 'content'=>$_result['content'], 'bargein'=>$_result['bargein'], 'next_status'=>$_result['next_status'], 'r_data'=>$_result['r_data']);
        }
        if($data['api']) return $apiResult;
        else return $result;
    }

    // api 전송결과
    function getApiSendResult($data){
        global $table;

        $vendor = $data['vendor'];
        $bot = $data['bot'];
        $itemOC = $data['itemOC'];

        // api result
        $sendData = array();
        $param = array(); // 전송 데이타
        $A = $data['apiReq']; // req 정보
        $method = $A['method'];
        $apiName = $A['name'];
        $req = $A['uid'];
        $api = $A['api'];
        $param['base_path'] = $A['base_path'];//'http://www.epush.co.kr/persona';
        $param['method'] = $A['method'];
        $_wh = 'itemOC='.$itemOC.' and api='.$api.' and req='.$req;
        $RCD = getDbArray($table[$this->module.'dialogResApiParam'],$_wh,'*','uid','asc','',1);

        // 파라미터 세팅
        $data['parse_mod'] = 'api';
        $data['entitySet'] = $data['entityData'][0]; // 파라미터에는 엔터티 1개만 추가 가능
        $data['parse_type'] = '@';
        $data['parse_entityData'] = 'valName'; // 기존에는 무조건 감지단어로 파싱
        while($R = db_fetch_assoc($RCD)){
            $ps = $R['position'];
            $key = $R['name'];
            $data['text'] = $R['varchar_val']; // 파라미터 값 파싱 $data 에 text 추가
            $val = $this->getParseText($data);

            if($ps == 'query'){
                $param['qParamName'][] = $key;
                $param['qParamVal'][] = $val;
            }else if($ps=='header'){
                $param['hParamName'][] = $key;
                $param['hParamVal'][] = $val;
            }else if($ps=='path'){
                $param['pParamName'][] = $key;
                $param['pParamVal'][] = $val;
            }else if($ps=='form'){
                $param['fParamName'][] = $key;
                $param['fParamVal'][] = $val;
            }
        }

        $sendData['data'] = $param;

        /*
           api send result : 아래 3가지 값 리턴

           $result['body'] = $RQ->getBody();
           $result['headers'] = $RQ->getHeaders();
           $result['statusCode'] = $RQ->getStatusCode();

        */
        $AR = $this->getLegacyApiResult($sendData); // json 타입
        $parse_data = array();
        $parse_data['response'] = json_decode($AR['body'],true);
        $this->context['last_entitySet'] = $data['entitySet'];
        $_wh2 = 'vendor='.$vendor.' and bot='.$bot.' and itemOC='.$itemOC;
        $OCD = getDbArray($table[$this->module.'dialogResApiOutput'],$_wh2,'*','uid','asc','',1);
        while($O = db_fetch_assoc($OCD)){
            if($O['resType'] =='text'){
                $parse_data['outputType'] = $O['resType'];
                $parse_data['output'] = $O['text_val'];
            }
        }

        $Output = $this->getParseApiOutput($parse_data);

        // parse result
        $result = array();

        // guzzle & api 모두 정상인 경우
        if($AR['statusCode']=='200'){
            $_tmp = array();
            $_tmp['parse'] = true;
            $_tmp['text'] = $Output;
            $_tmp['entityData'] = $data['entityData'];
            $msg = $this->getBotTextMsg($_tmp);
            $result['res_type'] = 'text';
            $result['content'] = $msg;
       }else{
            $_tmp = array();
            $_tmp['parse'] = false;
            $_tmp['text'] = '네트워크에 문제가 있네요. 잠시후에 다시 한번 해주세요';
            $msg = $this->getBotTextMsg($_tmp);
            $result['res_type'] = 'text';
            $result['content'] = $msg;
        }
        return $result;
    }

    // Api Response 데이타 추출
    private function getDataFromApiRes($match){
        $hookObj = $this->context['apiResObj'];
        $objArray = explode('.',$match[1]);
        $array =& $hookObj;

        foreach ($objArray as $i => $key) {
            $objArray[$i] = $key;
        }
        $objArray = "[ '" . join( "' ][ '", $objArray ) . "' ]";

        $result = eval("return \$array{$objArray};");
        return $result;
    }

    // Api response 패턴 파싱
    function getParseApiOutput($data){
        $outputType = $data['outputType'];
        $output = $data['output'];
        $this->context['apiResObj'] = $data['response'];

        $result = preg_replace_callback('/\[([^\]]+)\]/','self::getDataFromApiRes',$output);
        return $result;
    }

    // 링크 데이타 파싱
    private function getLinkDataFromText($text){
        preg_match('/\[LINK:([^\]]+)\]/',$text,$match); //$context = $input.keyword
        $result = array();
        if($match[1]){
            $result['is_match'] = true;
            $result['link'] = $match[1];
        }else{
            $result['is_match'] = false;
            $result['link'] = '';
        }
        return $result;
    }

    // 검색 엔터티 추출 함수
    private function getSearchKeyword($input){
        global $g,$d;
        require_once $g['path_module'].$this->module.'/var/var.php';

        $filter = $d['chatbot']['searchword'];
        $filter = str_replace(',','|',$filter);
        $result = preg_replace('/('.$filter.')/','', $input);
        return trim($result);
    }

    // 컨텍스트 표현 치환
    private function getReplaceContextExpress($data){
        $_data = array();
        $_data['find'] = $data['context_express'];
        $cVal = $this->getContextVal($_data);

        if($cVal) $result = $cVal;
        else $result = $data['resText'];
        return $result;
    }

    // 계산 item 값 얻기
    function getCalItemVal($item){
        // 엔터티 타입 체크
        preg_match('/\@(.+)/',$item,$array);

        // 컨텍스트 타입 체크
        preg_match('/\$(.+)/',$item,$array);
        if(isset($array[1])){
            $contextVal = $array[1];
            $data = array();
            $data['find'] = $contextVal;
            $item = $this->getContextVal($data);
        }
        return $item;
    }

    // 컨텍스트 값 파싱
    function getContextValFromText($match){
        $result='';

        preg_match('/\$([^\#^\$^\@]+)/',$match[1],$ctx);
        if($ctx[0]){
            $_dd = array();
            $_dd['find'] = trim($ctx[1]);
            $_dd['parse'] = 'yes';

            $result = $this->getContextVal($_dd);
        }
        return $result;
    }

     // 컨텍스트 정규식 파싱
    function getParseContext($data){
        $this->context['parse_text'] = $data['parse_text'];
        $result = preg_replace_callback('/\{([^}]+)\}/','self::getContextValFromText',$data['parse_text']);
        return $result;
    }

    /* text 에서 intent *entity 데이타 추출
       $matches : array({#연락처문의(59) @동물센터(40)}, #연락처문의(59) @동물센터(40));
    */
    private function getEIdataFromText($matches){
        global $table;

        $parse_entityData = $this->context['parse_entityData']; // 파싱할 entity 데이터
        $entitySet = $this->context['last_entitySet']; // ex) array(38,39,'안과');
        $entityUid = $entitySet[0];
        $entityVal = $entitySet[1];
        $entityMatchName = $entitySet[2];
        $entityValName = $entitySet[3];

        // 인텐트 찾기
        preg_match('/\#([^\#^\@]+)/',$matches[1],$intent); //$intent = array(#연락처문의(59),연락처문의(59))
        preg_match('/\(([0-9]+)\)/',$intent[1],$match);
        $intent_uid = $match[1];

        // 엔터티 찾기 :
        preg_match('/\@([^\#^\@]+)/',$matches[1],$entity); //$entity = array(@동물센터(40),동물센터(40))
        preg_match('/\(([0-9]+)\)/',$entity[1],$match);
        $entity_uid = $match[1];

        $entityData = $this->context['last_entityData'];

        // {} 안에  @엔터티 존재하는 경우
        if($entity_uid){
            // 입력문장에서 발견된 전체 entity 배열 검색
            foreach ($entityData as $entitySet) {
                $entityUid = $entitySet[0];
                $entityVal = $entitySet[1];
                $entityMatchName = $entitySet[2];
                $entityValName = $entitySet[3];

                // {} 안에 존재하는 @엔터티 uid 와 입력문장에서 발견된 엔터티 배열 중에 같은게 있는 경우
                if($entity_uid == $entityUid){

                    // {} 안에 #인텐트도 있는 경우
                    if($intent_uid){
                        $data = array();
                        $data['vendor'] = $this->context['vendor'];
                        $data['bot'] = $this->context['bot'];
                        $data['dialog'] = $this->context['dialog'];
                        $data['intent'] = $intent_uid;
                        $data['entity'] = $entity_uid;
                        $data['entityVal'] = $entityVal; // 상단 context['last_entitySet'] 값에서 추출한다.
                        $res = $this->getEIdata($data);
                        $result = $res?$res:'데이터 없음';
                        //$result['intent_match'] = true;

                    }else{ // {} 안에 @엔터티만 있는 경우

                        if($parse_entityData =='valName') $result = $entityValName;  // 대표 value 명
                        else {
                            if($entitySet[4] == 'S' && $entitySet[6]) $result = $entitySet[6];
                            else $result = $entityMatchName; // 감지단어
                        }
                    }
                    break;

                }// {} 안에 존재하는 @엔터티 uid 와 입력문장에서 발견된 엔터티 배열 중에 같은게 있는 경우
            }

        }else{
            // 컨텍스트 체크
            preg_match('/\$([^\#^\$^\@]+)/',$matches[1],$ctx);
            if($ctx[0]){
                $_dd = array();
                $_dd['find'] = trim($ctx[1]);
                $_dd['parse'] = 'yes';
                $result = $this->getContextVal($_dd);
            }
            else $result = '';//$matches[0]; // 원래값 리턴
        }
        return $result;
    }

    // 텍스트에 포함된 데이타 치환 함수
    function getParseText($data){
        global $table;
        $this->context['last_entitySet'] = $data['entitySet']; // 한번의 발화턴에서 추출한 entityVal 배열
        $this->context['last_entityData'] = $data['entityData']; // 한번의 발화턴에서 추출한 entityVal 배열
        $this->context['parse_entityData'] = $data['parse_entityData']; // 파싱할 데이터 지정
        if($data['parse_mod'] != 'api'){
            $this->context['last_input'] = $data['clean_input']; // 사용자 입력문장
            $this->context['resText'] = $data['text']; // 해당 답변 text
            $this->context['vendor'] = $data['vendor'];
            $this->context['bot'] = $data['bot'];
            $this->context['dialog'] = $data['dialog'];
            $this->updateContext($this->context);
        }
        $data['b_text'] = $data['b_text'] ? $data['b_text'] : $data['text'];
        $result = preg_replace_callback('/\{([^}]+)\}/','self::getEIdataFromText',$data['b_text']);
        return $result;
    }

    // bot 텍스트 메세지 출력 함수
    function getBotTextMsg($data){
        global $TMPL,$date;

        $botid = $data['botid']?$data['botid']:$this->botid;
        $sentence = $data['text'];

        if($botid) {
            $botData = $this->getBotDataFromId($botid);
            foreach ($botData as $key=>$value) {
               $TMPL[$key] = $value;
            }
        }

        if($data['parse']){ // 파싱하는 text

            // {} 로 설정된 값들 모두 찾기
            preg_match_all('/\{([^}]+)\}/',$data['text'],$matches);
            if($matches[0]){
                $bracket_arr = $matches[0]; // {}, {}, {} ....
                // {} 값을 하나씩 파싱한다.
                foreach ($bracket_arr as $key=>$bracket) {
                    $data['b_text'] = $bracket;
                    $bracket_parse = $this->getParseText($data);
                    if($bracket_parse){
                        if($key==0) $text = str_replace($bracket,$bracket_parse,$data['text']);
                        else $text = str_replace($bracket,$bracket_parse,$text); // 2 번째 값부터 이전 $text 값에 덮어쓰기
                    } else {
                        // 파싱된 값이 없을 경우 해당 {} 부분 삭제
                        $text = str_replace($bracket, '', $data['text']);
                    }
                }
            }else{
                $text = $data['text'];
            }
        }
        else $text = $data['text'];

        $TMPL['date'] = (date('a') == 'am' ? '오전 ':'오후 ').date('g').':'.date('i');
        $TMPL['response'] = '<span>'.nl2br($text).'</span>';

        // 챗봇 메세지 풍선 출력
        $skin = new skin('chat/bot_msg');
        $html = $skin->make();

        // api 인 경우 텍스트만 출력
        if($data['api']) $result = $text;
        else $result = $html;

        return $result;
    }

    // 이모티콘에 대한 랜덤 답변 추출 함수
    function getRanEmoticon($user_emo){
        $emo_array = array(
            "sunglass" => array("laugh"),
            "cry" => array("cry","shock","joke"),
            "sleep" => array("sunglass","smile"),
            "laugh" => array("sunglass","smile"),
            "love" => array("love","laugh","sunglass","smile"),
            "shock" => array("cry","smile"),
            "joke" => array("smile","cry"),
            "smile" => array("sunglass","laugh")
        );
        $emotions = $emo_array[$user_emo];
        $len = count($emotions)-1;
        $ran_emotion = $emotions[rand(0,$len)];
        return $ran_emotion;
    }

    // 이모티콘 리스트 추출 함수
    function getEmoticonList($bot_id){
        global $g;
        $m = $this->module;
        $emo_array=array("sunglass","smile","sleep","shock","love","laugh","joke","cry");
        $emo_folder = $g['url_root'].'/modules/'.$m.'/lib/emoticon';
        $emo_list ='<ul>';
        foreach ($emo_array as $emo) {
            $emo_list .='<li data-role="emoticon" data-emotion="'.$emo.'" data-id="'.$bot_id.'"><img src="'.$emo_folder.'/emo_'.$emo.'.png" /></li>';
        }
        $emo_list .='</ul>';
        return $emo_list;
    }

    // 업체 첨부파일 출력 함수
    function getVendorAttachFile($R){
        global $table;
        $d['upload'] = array();
        $d['upload']['tmp'] = $R['upload'];
        $d['_pload'] = getArrayString($R['upload']);
        $attach_file='<ul class="vendor-attach">';// 첨부파일 수량 체크  ---------------------------------> 20151.1 추가 by kiere.
        foreach($d['_pload']['data'] as $_val) {
            $U = getUidData($table['s_upload'],$_val);
            $attach_file .='<li class="file-attached">'.$U['name'].'</li>';
        }
        return $attach_file;
    }

    // 업체 첨부파일 출력 함수
    function getBotUpload($R,$data){
        global $table;
        $U = getUidData($table['s_upload'],$R['upload']);
        $result=array();
        $result['name'] = $U['name'];
        $result['src'] = $U['url'].$U['folder'].'/'.$U['tmpname'];
        return $result[$data];
    }

    // 카운트 등록함수
    function regisBotCount($bot_id){
        global $table,$g,$date,$my;

        $bot_id = $this->botid;

        $mod = $this->is_mobile()?'M':'D'; // 모바일 or desktop 체크

        $m = $this->module;
        $B = getDbData($table[$m.'bot'],"id='".$bot_id."'",'uid,vendor');
        $botuid = $B['uid'];
        $vendor = $B['vendor'];

        if ($_GET['m'] != 'admin' && $_GET['iframe'] !='Y' && !$_GET['system'])
        {
            $g['agent'] = $_SERVER['HTTP_USER_AGENT'];
            $g['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'] ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];

            if(strpos($g['agent'],'Google')||strpos($g['agent'],'Yahoo')||strpos($g['agent'],'naver')||strpos($g['agent'],'bot')) {
                $_SESSION['botlog'.$botuid] = $g['ip'].'-'.$date['totime'];
                $_SESSION['agent'.$botuid] = $g['agent'];
                return;
            }else{
                if($_SESSION['botlog'.$botuid]!='') {
                    // 주간,월간 페이지뷰 등록여부 체크
                    $_TODAYCNT = getDbData($table[$m.'counter'],"type=2 and d_regis='".$date['today']."' and botuid=".$botuid,'uid');
                    if ($_TODAYCNT['uid']){
                        // 주간/월간 카운팅 업데이트
                        $this->getUpdateBotCount($table[$m.'counter'],$_TODAYCNT['uid']);

                        // 일간 등록여부 체크
                        $_hour = substr($date['totime'],0,10);
                        $_HOURCNT = getDbData($table[$m.'dcounter'],"type=2 and d_regis='".$_hour."' and botuid=".$botuid,'uid');
                        if($_HOURCNT['uid']) $this->getUpdateBotCount($table[$m.'dcounter'],$_HOURCNT['uid']);
                        else $this->getInsertBotCount($table[$m.'dcounter'],$vendor,$botuid,2);

                    }
                } else {
                    // 주간,월간 등록여부 체크
                    $_TODAYCNT = getDbData($table[$m.'counter'],"type=1 and d_regis='".$date['today']."' and botuid=".$botuid,'uid');
                    if ($_TODAYCNT['uid']){
                        // 주간/월간 카운팅 업데이트
                        $this->getUpdateBotCount($table[$m.'counter'],$_TODAYCNT['uid']);

                        // 일간 등록여부 체크
                        $_hour = substr($date['totime'],0,10);
                        $_HOURCNT = getDbData($table[$m.'dcounter'],"type=1 and d_regis='".$_hour."' and botuid=".$botuid,'uid');
                            if($_HOURCNT['uid']) $this->getUpdateBotCount($table[$m.'dcounter'],$_HOURCNT['uid']);
                            else $this->getInsertBotCount($table[$m.'dcounter'],$vendor,$botuid,1);

                    }else{ // 최초 신규저장
                        // 일간,주간,월간 카운팅 저장 (d_regis 필드는 길이만큼 들어간다., counter-> 20170101, dcounter->2017010101)
                        for($i=1;$i<3;$i++){
                            $type=$i;
                            $this->getInsertBotCount($table[$m.'counter'],$vendor,$botuid,$type); // 주간/월간 저장 순방/페이지뷰 저장
                            $this->getInsertBotCount($table[$m.'dcounter'],$vendor,$botuid,$type); // 일간 순방/페이지뷰 저장
                        }
                    }

                    $_referer = $referer ? urldecode($referer) : $_SERVER['HTTP_REFERER'];
                    $_sengine = getSearchEngine($_referer);
                    $_outkeyw = getKeyword($_referer);
                    $_browser = getBrowzer($g['agent']);
                    $_req_uri = $_SERVER['REQUEST_URI'];
                    $_QKEY = 'vendor,botuid,mbruid,mbrsex,mbrage,ip,referer,agent,d_regis';
                    $_QVAL = "'$vendor','$botuid','".$my['uid']."','".$my['sex']."','".$my['age']."','".$g['ip']."','".$_referer."','".$g['agent']."','".$date['totime']."'";
                    getDbInsert($table[$m.'referer'],$_QKEY,$_QVAL);

                    $_REFCNT = getDbRows($table[$m.'referer'],'');

                    if ($_REFCNT > 1000000) {
                        $_REFOVER = getDbArray($table[$m.'referer'],'','*','uid','asc',($_REFCNT - 1000000),1);
                        while($_REFK=db_fetch_array($_REFOVER)) getDbDelete($table[$m.'referer'],$_REFK['uid']);
                    }

                    if ($_REFCNT == 1) {
                        db_query("OPTIMIZE TABLE ".$table[$m.'referer'],$DB_CONNECT);
                        db_query("OPTIMIZE TABLE ".$table[$m.'counter'],$DB_CONNECT);
                        db_query("OPTIMIZE TABLE ".$table[$m.'dcounter'],$DB_CONNECT);
                    }
                    $_SESSION['botlog'.$botuid] = $g['ip'].'-'.$date['totime'];
                    $_SESSION['agent'.$botuid] = $g['agent'];
                }
            }
        }
        return;
    }

    // 카운트 insert 함수 (type : 순방문, 페이지 뷰여부)
    function getInsertBotCount($table,$vendor,$botuid,$type){
        global $date,$my;

        $amod = $this->is_mobile()?'M':'D'; // 모바일 or desktop 체크
        if($my['sex']==1) $male=1;
        else if($my['sex']==1) $female=1;

        if($my['age']==10) $age_10=1;
        else if($my['age']==20) $age_20=1;
        else if($my['age']==30) $age_30=1;
        else if($my['age']==40) $age_40=1;
        else if($my['age']==50) $age_50=1;
        else if($my['age']==60) $age_60=1;

        $QKEY="vendor,botuid,type,amod,page,male,female,age_10,age_20,age_30,age_40,age_50,age_60,d_regis";
        $QVAL="'$vendor','$botuid','$type','$amod',1,'$male','$female','$age_10','$age_20','$age_30','$age_40','$age_50','$age_60','".$date['totime']."'";
        getDbInsert($table,$QKEY,$QVAL);
    }

    // 카운트 update 함수 (type : 순방문, 페이지 뷰여부)
    function getUpdateBotCount($table,$uid){
        global $date,$my;

        getDbUpdate($table,'page=page+1','uid='.$uid);
        if($my['uid']){
            if($my['sex']==1) getDbUpdate($table,'male=male+1','uid='.$uid);
            else if($my['sex']==2) getDbUpdate($table,'female=female+1','uid='.$uid);

            if($my['age']==10) getDbUpdate($table,'age_10=age_10+1','uid='.$uid);
            else if($my['age']==20) getDbUpdate($table,'age_20=age_20+1','uid='.$uid);
            else if($my['age']==30) getDbUpdate($table,'age_30=age_30+1','uid='.$uid);
            else if($my['age']==40) getDbUpdate($table,'age_40=age_40+1','uid='.$uid);
            else if($my['age']==50) getDbUpdate($table,'age_50=age_50+1','uid='.$uid);
            else if($my['age']==60) getDbUpdate($table,'age_60=age_60+1','uid='.$uid);
        }
    }

    // 챗봇 삭제 함수
    function getBotDelete($botuid){
        global $table;

        $m = $this->module;
        getDbDelete($table[$m.'bot'],'uid='.$botuid);
        getDbDelete($table[$m.'chatLog'],'botuid='.$botuid);
        getDbDelete($table[$m.'added'],'botuid='.$botuid);
        getDbDelete($table[$m.'referer'],'botuid='.$botuid);
        getDbDelete($table[$m.'counter'],'botuid='.$botuid);
        getDbDelete($table[$m.'dcounter'],'botuid='.$botuid);
    }

    // 사용자 이름 출력 함수
    function getUserName($mbruid){
        global $_HS,$table;
        if(!$mbruid) $result ='비회원';
        else{
            $M = getDbData($table['s_mbrdata'],'memberuid='.$mbruid,'name,nic');
            $result = $M['nic']?$M['nic']:'방문자';
        }
        return $result;
    }

    // 벤더 관련 데이타 추출 함수
    function getVendorData($mbruid){
        global $table,$my;

        $result=array();
        $m = $this->module;
        $is_vendor_register = getDbData($table[$m.'vendor'],'mbruid='.$my['uid'],'uid,type');

        // 로그인 계정이 벤더 등록자인 경우
        if($is_vendor_register['uid']){
            $result['my_vendor'] = true;
            $result['my_vendor_type'] = $is_vendor_register['type'];
        }

        // 로그인 계정이 메니져로 등록된 경우
        if($my['manager']){
            $MG = getDbData($table[$m.'manager'],'mbruid='.$my['uid'].' and auth=1','auth,vendor');
            $V = getDbData($table[$m.'vendor'],'uid='.$MG['vendor'],'type');
            $result['is_manager'] = true;
            // 메니져 승인여부
            if($MG['auth']==1) $result['is_manager_auth'] = true;
            else $result['is_manager_auth'] = false;

            // 벤더 타입 여부
            $result['our_vendor_type'] = $V['type'];

        }
        return $result;
    }

    // 사용자 챗팅 로그 추출
    function getUserChatLog($data){
        global $table;
        $m = $this->module;

        $bot = $data['bot'];
        $userUid = $data['userUid'];
        $roomToken = $data['roomToken'];

        $tbl = $table[$m.'chatLog'];
        $_wh = "vendor='".$data['vendor']."' and bot='".$bot."' and roomToken='".$roomToken."'";
        if($userUid) $_wh.=' and userUid='.$userUid;

        $query = "Select * From ".$tbl." Where ".$_wh." Order by uid ASC";
        $rows=$this->getAssoc($query);
        $chatLog ='';
        foreach ($rows as $row) {
            $chatLog .= $this->getUserChatRow($row);
            $chatLog .= $this->getBotChatRow($row);
        }
        return $chatLog;
    }

    // 사용자 챗팅 로그 추출
    function getUserChatRow($row){
        global $TMPL;

        if($row['printType'] != 'W') {
            $TMPL['img_layout'] = $g['img_layout'];
            //$TMPL['date'] = getDateFormat($row['d_regis'],'Y-m-d');
            $_time = strtotime($row['d_regis']);
            $TMPL['date'] = date('y.m.d', $_time).' '.(date('a', $_time) == 'am' ? '오전 ':'오후 ').date('g', $_time).':'.date('i', $_time);

            $TMPL['message'] = $row['content'];
            $skin=new skin('chat/user_msg');
            $chatRow=$skin->make();

            return $chatRow;
        }
    }

    // 챗봇 챗팅 로그 추출
    function getBotChatRow($R){
        global $TMPL,$table,$g;

        $m = $this->module;
        $tbl = $table[$m.'botChatLog'];

        $_wh = "vendor='".$R['vendor']."' and bot='".$R['bot']."' and roomToken='".$R['roomToken']."' and chat='".$R['uid']."'";
        $query = "Select * From ".$tbl." Where ".$_wh." Order by uid ASC";
        $rows=$this->getAssoc($query);
        $botChatRow ='';
        foreach ($rows as $row) {
            $TMPL['img_layout'] = $g['img_layout'];
            //$TMPL['date'] = getDateFormat($row['d_regis'],'Y-m-d');
            $_time = strtotime($row['d_regis']);
            $TMPL['date'] = date('y.m.d', $_time).' '.(date('a', $_time) == 'am' ? '오전 ':'오후 ').date('g', $_time).':'.date('i', $_time);

            if($row['content'] == strip_tags($row['content'])) {
                $_R=getUidData($table[$this->module.'bot'], $R['bot']);
                $TMPL['bot_name'] = $_R['name'];
                $TMPL['bot_avatar_src'] = $this->getBotAvatarSrc($_R);
                $TMPL['response'] = '<span>'.$row['content'].'</span>';
                $skin=new skin('chat/bot_msg');
                $chatRow=$skin->make();
            }else{
                $TMPL['response'] = $row['content'];
                $skin=new skin('chat/bot_content');
                $chatRow=$skin->make();
            }
            $botChatRow .=$chatRow;
        }
        return $botChatRow;
    }

    // 학습방식 추출
    function getLearnType($type){
        $learnType = array(
            "E"=>"Entity 추가",
            "I"=>"Intent 추가",
            "D"=>"딥런닝 진행"
        );
    }

    // 답변 못한 질문 리스트 추출
    function getUnKnownData($data){
        global $table;

        $vendor = $data['vendor']?$data['vendor']:$this->vendor;
        $mod = $data['mod']?$data['mod']:'wait';
        $is_learn = $mod=='wait'?0:1;

        $_wh = "vendor='".$vendor."' and sentence<>'' and is_learn='".$is_learn."'";
        if($data['bot']) $_wh.=' and bot='.$data['bot'];

        $tbl = $table[$this->module.'unknown'];
        $page = $data['page']?$data['page']:1;
        $recnum = $data['recnum']?$data['recnum']:10;
        $limit=(($page-1)*$recnum).','.$recnum;
        $total_query=sprintf("SELECT * FROM `%s` WHERE %s", $tbl,$_wh);
        $total = $this->getRows($total_query);
        $totalPage=ceil($total/$recnum);

        $limit_query = sprintf("SELECT * FROM `%s` WHERE %s GROUP BY `uid` ORDER BY `date` DESC LIMIT %s", $tbl,$_wh,$limit);
        $rows = $this->getAssoc($limit_query);
        if($total){
            $list ='';
            foreach ($rows as $row){
                $_date = $row['is_learn']?$row['d_learn']:$row['date'];
                $list.='
                <tr>
                   <td><input type="checkbox" name="unknownItem[]" value="'.$row['uid'].'" rel="'.$row['sentence'].'" data-role="checkbox" style="margin:0;" /></td>
                   <td class="txt-oflo" style="max-width:120px;">'.$row['sentence'].'</td>
                   <td class="txt-oflo">'.getDateFormat($_date,'Y.m.d').'</td>
                </tr>
                ';
            }

        }else{
            $list.='<tr><td colspan="3"> 데이타가 존재하지 않습니다. </td></tr>';
        }

        // 이전 버튼 세팅
        if($page==1) $prev_disabled = 'disabled';
        else $prev_page=$page-1;

        // 다음 버튼 세팅
        if($total>$recnum){
            if($page<$totalPage) $next_page=$page+1;
            else{
                $next_page ='';
                $next_disabled ='disabled';
            }
        }else{
           $next_page ='';
           $next_disabled ='disabled';
        }

        $pageBtn='
            <div class="btn-group" style="margin-right:10px;font-weight:300">
                   '.$page.'/'.$totalPage.'
            </div>
            <div class="btn-group">

                 <button type="button" class="btn btn-sm btn-default" data-role="unknown-paging" data-mod="'.$mod.'" data-page="'.$prev_page.'" '.$prev_disabled.'>
                    <i class="fa fa-angle-left"></i>
                 </button>
                 <button type="button" class="btn btn-sm btn-default" data-role="unknown-paging" data-mod="'.$mod.'" data-page="'.$next_page.'" '.$next_disabled.'><i class="fa fa-angle-right"></i>
                 </button>
            </div>';

        return array($limit_query,$list,$pageBtn);
    }

    // 많이한 질문, 단어 리스트
    function getFavorateQuestionData($data){
        global $table;

        $vendor = $data['vendor']?$data['vendor']:$this->vendor;
        $bot = $data['bot']?$data['bot']:$this->bot;
        $mod = $data['mod']?$data['mod']:'question';

        $aTable = array('node'=>'chatLog', 'question'=>'chatStsLog', 'word'=>'chatWordLog');

        $_wh = "vendor='".$vendor."' and bot='".$bot."' ";
        if($mod == 'node') $_wh .="and node <>'' ";

        if($data['d_start'] && $data['d_end']) {
            $d_start = str_replace('-','', $data['d_start']);
            $d_end = str_replace('-','', $data['d_end']);

            $d_field = $mod == 'node' ? 'd_regis' : 'date';

            $_wh .=" and (left(".$d_field.",8) between '".$d_start."' and '".$d_end."') ";
        }

        if($mod == 'node') {
            $_group = "vendor, bot, node";
        } else if($mod == 'question') {
            $_group = "vendor, bot, replace(sentence, ' ', '')";
        } else if($mod == 'word') {
            $_group = "vendor, bot, keyword";
        }

        $tbl = $table[$this->module.$aTable[$mod]];

        $page = $data['page']?$data['page']:1;
        $recnum = $data['recnum']?$data['recnum']:10;
        $limit=(($page-1)*$recnum).','.$recnum;
        $total_query = "Select count(*) as nCnt From (";
        $total_query .="  Select count(*) From ".$tbl." Where ".$_wh." Group by ".$_group;
        $total_query .=") as A ";
        $total = $this->getAssoc($total_query);
        $total = $total[0]['nCnt'];
        $totalPage=ceil($total/$recnum);

        if($mod == 'node') {
            $limit_query = sprintf("SELECT node as content, count(*) as hit FROM `%s` WHERE %s Group by ".$_group." ORDER BY `hit` DESC LIMIT %s", $tbl,$_wh,$limit);
        } else if($mod == 'question') {
            $limit_query = sprintf("SELECT sentence as content, sum(hit) as hit FROM `%s` WHERE %s Group by ".$_group." ORDER BY `hit` DESC LIMIT %s", $tbl,$_wh,$limit);
        } else if($mod == 'word') {
            $limit_query = sprintf("SELECT keyword as content, sum(hit) as hit FROM `%s` WHERE %s Group by ".$_group." ORDER BY `hit` DESC LIMIT %s", $tbl,$_wh,$limit);
        }
        $rows = $this->getAssoc($limit_query);
        $list ='';
        if($total){
            foreach ($rows as $row){
                $list.='<tr>';
                $list.='    <td class="txt-oflo">'.$row['content'].'</td>';
                $list.='    <td class="txt-oflo">'.$row['hit'].'</td>';
                $list.='</tr>';
            }
        }else{
            $list.='<tr><td colspan="2"> 데이타가 존재하지 않습니다.</td></tr>';
        }

        // 이전 버튼 세팅
        if($page==1) $prev_disabled = 'disabled';
        else $prev_page=$page-1;

        // 다음 버튼 세팅
        if($total>$recnum){
            if($page<$totalPage) $next_page=$page+1;
            else{
                $next_page ='';
                $next_disabled ='disabled';
            }
        }else{
           $next_page ='';
           $next_disabled ='disabled';
        }

        $pageBtn='';
        $pageBtn.='<div class="btn-group" style="margin-right:10px;font-weight:300">'.$page.'/'.$totalPage.'</div>';
        $pageBtn.='<div class="btn-group">';
        $pageBtn.=' <button type="button" class="btn btn-sm btn-default" data-role="btn-paging" data-mod="'.$mod.'" data-page="'.$prev_page.'" '.$prev_disabled.'><i class="fa fa-angle-left"></i></button>';
        $pageBtn.=' <button type="button" class="btn btn-sm btn-default" data-role="btn-paging" data-mod="'.$mod.'" data-page="'.$next_page.'" '.$next_disabled.'><i class="fa fa-angle-right"></i></button>';
        $pageBtn.='</div>';
        return array($limit_query,$list,$pageBtn);
    }

    // 질문 그룹(단어) 리스트
    function getWordGroupData($data){
        global $table;

        $vendor = $data['vendor']?$data['vendor']:$this->vendor;
        $bot = $data['bot']?$data['bot']:$this->bot;
        $mod = $data['mod']?$data['mod']:'word';
        $keyword = $data['keyword'];

        $_wh = "vendor='".$vendor."' and bot='".$bot."' and printType='T' and content like '%".$keyword."%'";
        if($data['d_start'] && $data['d_end']) {
            $d_start = str_replace('-','', $data['d_start']);
            $d_end = str_replace('-','', $data['d_end']);
            $_wh .=" and (left(d_regis,8) between '".$d_start."' and '".$d_end."') ";
        }

        $tbl = $table[$this->module.'chatLog'];
        $page = $data['page']?$data['page']:1;
        $recnum = $data['recnum']?$data['recnum']:10;
        $limit=(($page-1)*$recnum).','.$recnum;
        $total_query ="Select count(*) as nCnt From (Select count(*) From ".$tbl." Where ".$_wh." Group by content) as A";
        $total = $this->getAssoc($total_query);
        $total = $total[0]['nCnt'];
        $totalPage=ceil($total/$recnum);

        $limit_query = "Select uid, content FROM ".$tbl." WHERE ".$_wh." Group by content ORDER BY uid DESC LIMIT ".$limit;
        $rows = $this->getAssoc($limit_query);
        $list ='';
        if($total){
            foreach ($rows as $row){
                $list.='<tr>';
                $list.='    <td><input type="checkbox" name="unknownItem[]" value="'.$row['uid'].'" rel="'.$row['content'].'" data-role="checkbox" style="margin:0;" /></td>';
                $list.='    <td class="txt-oflo">'.$row['content'].'</td>';
                $list.='</tr>';
            }
        }else{
            $list.='<tr><td colspan="2"> 데이타가 존재하지 않습니다.</td></tr>';
        }

        // 이전 버튼 세팅
        if($page==1) $prev_disabled = 'disabled';
        else $prev_page=$page-1;

        // 다음 버튼 세팅
        if($total>$recnum){
            if($page<$totalPage) $next_page=$page+1;
            else{
                $next_page ='';
                $next_disabled ='disabled';
            }
        }else{
           $next_page ='';
           $next_disabled ='disabled';
        }

        $pageBtn='';
        $pageBtn.='<div class="btn-group" style="margin-right:10px;font-weight:300">'.$page.'/'.$totalPage.'</div>';
        $pageBtn.='<div class="btn-group">';
        $pageBtn.=' <button type="button" class="btn btn-sm btn-default" data-role="btn-paging" data-keyword="'.$keyword.'" data-page="'.$prev_page.'" '.$prev_disabled.'><i class="fa fa-angle-left"></i></button>';
        $pageBtn.=' <button type="button" class="btn btn-sm btn-default" data-role="btn-paging" data-keyword="'.$keyword.'" data-page="'.$next_page.'" '.$next_disabled.'><i class="fa fa-angle-right"></i></button>';
        $pageBtn.='</div>';
        return array($limit_query,$list,$pageBtn);
    }

    // 챗팅 박스 추출 함수
    function getChatLog($bot_id,$page,$recnum){
        global $table;
        $m = $this->module;
        $bot_id = $this->db->real_escape_string($bot_id);

        $tbl = $table[$m.'chatLog'];
        $_wh = "botid='".$bot_id."'";
        $page = $page?$page:1;
        $limit=(($page-1)*$recnum).','.$recnum;
        $query = sprintf("SELECT * FROM `%s` WHERE %s GROUP BY `d_regis` ORDER BY `uid` ASC LIMIT %s", $tbl,$_wh,$limit);
        $total_query=sprintf("SELECT * FROM `%s` WHERE %s", $tbl,$_wh);
        $rows=$this->getAssoc($query);
        $total=$this->getRows($total_query); // 전체 row 합계
        $totalPage=ceil($total/$recnum); // 전체 페이지
        $chatLog ='';
        foreach ($rows as $row) {
            $chatLog .= $this->getChatRow($row);
        }
        return $chatLog;

    }

    function getChatRow($row){
       global $TMPL;

       $by_who = $row['by_who']; // 보낸 주체
       $TMPL['img_layout'] = $g['img_layout'];
       $TMPL['date'] = getDateFormat($row['d_regis'],'Y-m-d');

       if($by_who=='bot'){
           if($row['msg_type']=='T'){
               $TMPL['response'] = '<span>'.$row['content'].'</span>';
               $skin=new skin('chat/bot_msg');
               $chatRow=$skin->make();
           }else{
               $TMPL['response'] = $row['content'];
               $skin=new skin('chat/bot_content');
               $chatRow=$skin->make();
           }

       }else{
           $TMPL['message'] = $row['content'];
           $skin=new skin('chat/user_msg');
           $chatRow=$skin->make();
       }
       return $chatRow;
    }

    // 챗봇 url 추출
    function getChatUrl($B){
       global $g;

       if($B['type']==1) $url = $g['url_root'].'/R2'.$B['id']; // 빌드 봇
       else if($B['type']==2) $url = $B['boturl']; // 등록 봇
       return $url;
    }


    // 카테고리 추출 : 슬래시(/)로 구분된 카테고리 정보
    function getCatName($slash_cat){
        global $table;
        $module =$this->module;
        $slash_cat_arr = explode('/',$slash_cat);
        $cat_name ='';
        foreach ($slash_cat_arr as $cat) {
            $C=getDbData($table[$module.'category'],'uid='.$cat,'name');
            $cat_name .= $C['name'].' > ';
        }
        return ltrim(rtrim($cat_name,' > '),' > ');
    }

    // 시간 체크
    function get_time() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

     // 형태소/패턴 추출 함수
    function getMopAndPattern($sentence){
        $_lang = $this->_lang;

        if($_lang=='en') $result = $this->getMopAndPattern_En($sentence); // 영어 형태소 분석
        else if($_lang=='zh') $result = $this->getMopAndPattern_Zh($sentence); // 중국어 형태소 분석
        else {
            if($_lang=='ja') $mecab_dic = '/usr/local/lib/mecab/dic/mecab-ipadic-neologd'; // 일본어 사전
            else if($_lang=='ko') $mecab_dic = $this->mecab_dic; // 한국어 사전

            // 특수문자 제거
            $sentence = getMorphStrReplace($sentence);
            $morpheme = getMecabMorph($sentence, '(*|*)');
            $result = array("mop"=>$morpheme,"pat"=>$pattern);
        }
        return $result;
    }

    // 영어 형태소/패턴 추출
    function getMopAndPattern_En($sentence){

        $data = array($sentence);
        $result = exec("python /home/users/sbot/www/get_posTag.py ".json_encode($data));

        $resultData = json_decode($result, true);

        foreach($resultData as $row){
            $word = $row[0];
            $PS = $row[1];
            if($word!='['&&$word!=']'){
               $mop .= $word.'(*|*)'.$PS.','; // 형태소/품사,
            }
        }
        $morpheme = rtrim($mop,',');
        $pattern = $this->getReplyRule_En($morpheme);

        $result = array("mop"=>$morpheme,"pat"=>$pattern);
        return $result;
    }

    // 중국어 형태소/패턴 추출
    function getMopAndPattern_Zh($sentence){
        $result = exec("python /home/users/sbot/www/jieba_zh.py ".$sentence);//.json_encode($data));
        $resultData = json_decode($result, true);

        foreach($resultData as $word=>$PS){
            $mop .= $word.'(*|*)'.$PS.','; // 형태소/품사,
        }
        $morpheme = rtrim($mop,',');
        $pattern = $this->getReplyRule_Zh($morpheme);

        $result = array("mop"=>$morpheme,"pat"=>$pattern);
        return $result;
    }

    // 형태소에서 답변 룰 추출하는 함수
    function getReplyRule($morpheme){
        $_lang = $this->_lang;

        if($_lang=='ko') $rule = $this->getReplyRule_Ko($morpheme); // 한국어 답변 룰
        else if($_lang=='ja') $rule = $this->getReplyRule_Ja($morpheme); // 일본어 답변 룰
        else if($_lang=='en') $rule = $this->getReplyRule_En($morpheme); // 영어 답변 룰
        else if($_lang=='zh') $rule = $this->getReplyRule_Zh($morpheme); // 중국어 답변 룰
        return $rule;
    }

    // 한국어 답변 룰
    function getReplyRule_Ko($morpheme){
        $use_mop = array('NNG','SL','VV','XPN','VA','MAG'); // 적용할 품사
        $rule='';
        $mop_arr = explode(' ',$morpheme);

        foreach ($mop_arr as $data) {
            $data_arr = explode('(*|*)',$data);
            $PS = $data_arr[1]; // 영문 품사
            $keyword =$data_arr[0]; // 추출된 단어
            if(in_array($PS,$use_mop)){
                if($PS=='XPN') $rule.=$keyword.'--'; // 체언 접두사
                else if($PS=='MAG'){
                    if($keyword =='못'|| $keyword=='안') $rule .= ' '.$keyword.' ';
                }else if($PS=='VA'){
                    if($keyword !='있') $rule .= ' '.$keyword.' ';
                }
                else $rule.=' '.$keyword.' ';
            }
        }
        $rule = preg_replace('/--/', '', $rule); // 체언 접두사 와 뒤에 오는 체언을 합체한다.
        $rule = str_replace('A S','AS',$rule);
        $rule = str_replace('a s','AS',$rule);
        $rule = str_replace('삼품 평,','상품평,',$rule);
        $rule = str_replace('무 통장,','무통장,',$rule);
        return $rule;
    }

    // 일본어 답변 룰
    function getReplyRule_Ja($morpheme){
        $use_mop = array('名詞','動詞'); // 적용할 품사
        $rule='';
        $mop_arr = explode(' ',$morpheme);

        foreach ($mop_arr as $data) {
            $data_arr = explode('(*|*)',$data);
            $PS = $data_arr[1]; // 영문 품사
            $keyword =$data_arr[0]; // 추출된 단어
            if(in_array($PS,$use_mop)){
                $rule.=' '.$keyword.' ';
            }
        }
        return $rule;
    }

    // 영어 답변 룰
    function getReplyRule_En($morpheme){
        $use_mop = array('NN','MD','NNS','VB','VBD','VBG','VBN','WDT','WP','WRB'); // 적용할 품사
        $rule='';
        $mop_arr = explode(',',$morpheme);

        foreach ($mop_arr as $data) {
            $data_arr = explode('(*|*)',$data);
            $PS = $data_arr[1]; // 영문 품사
            $keyword =$data_arr[0]; // 추출된 단어
            if(in_array($PS,$use_mop)){
                $rule.=' '.$keyword.' ';
            }
        }
        return $rule;
    }

    // 중국어 답변 룰
    function getReplyRule_Zh($morpheme){
        $use_mop = array('n','v','a'); // 적용할 품사
        $rule='';
        $mop_arr = explode(',',$morpheme);

        foreach ($mop_arr as $data) {
            $data_arr = explode('(*|*)',$data);
            $PS = $data_arr[1]; // 영문 품사
            $keyword =$data_arr[0]; // 추출된 단어
            if(in_array($PS,$use_mop)){
                $rule.=' '.$keyword.' ';
            }
        }
        return $rule;
    }

    // 언어설정 배열
    function getLangData(){
        $langArr=array("KOR"=>"한국어","ENG"=>"영어","JPN"=>"일본어","CHN"=>"중국어");
        return $langArr;
    }

    // 모든 업종 챗봇 리스트 추출 ($recnum : 챗봇 recnum)
    function getBotList($vendor,$where,$position,$recnum,$page){
        global $table,$TMPL,$r;
        $is_mobile = $this->is_mobile();
        $m = $this->module;
        $tbl = $table[$m.'bot'];
        $_wh = "auth=1 and hidden=0 and display=1";
        if($vendor) $_wh .=" and vendor='".$vendor."'";
        $query = sprintf("SELECT `induCat` FROM `%s` WHERE %s GROUP BY `induCat` ORDER BY `gid` ASC", $tbl,$_wh);
        $rows=$this->getAssoc($query);

        //if($is_mobile) $catLink = $g['s'].'/?mod=botlist&cat=';
        $catLink = $g['s'].'/?r='.$r.'&m='.$m.'&page=list&cat=';

        $html='';
        $botList = new skin('list/cat_bot');
        foreach ($rows as $row) {
            $cat = $row['induCat'];
            $TMPL['cat_name'] = $row['induCat'];
            $TMPL['more_link'] = $catLink.$cat;
            $TMPL['bot_rows'] = $this->getCatBotList($cat,$vendor,$where,$recnum,$page);
            $cat_bot = $botList->make();
            $html .= $cat_bot;

        }
        return $html;
    }

    // 업종 기준 챗봇 리스트 추출 (induCat : 업종)
    function getCatBotList($induCat,$vendor,$where,$recnum,$page){
        global $table;

        $m = $this->module;
        $tbl = $table[$m.'bot'];
        $_wh = "auth=1 and hidden=0 and display=1 and induCat='".$induCat."'";
        if($vendor) $_wh .=" and vendor='".$vendor."'";

        // 쿼리 & 페이징 세팅
        $limit=(($page-1)*$recnum).','.$recnum;
        $query = sprintf("SELECT * FROM `%s` WHERE %s GROUP BY `uid` ORDER BY `gid` ASC LIMIT %s", $tbl,$_wh,$limit);
        $total_query=sprintf("SELECT * FROM `%s` WHERE %s", $tbl,$_wh);
        $rows=$this->getAssoc($query);
        $total=$this->getRows($total_query); // 전체 row 합계
        $totalPage=ceil($total/$recnum); // 전체 페이지

        // row 출력
        $rows = $this->getAssoc($query);
        $botList='';
        foreach($rows as $row) {
           $botList .= $this->getCatBotRow($row,$page,$totalPage);
        }
        return $botList;
    }

    // 업종 기준 챗봇 row 마크업 추출
    function getCatBotRow($row,$page,$totalPage){
        global $g,$table,$TMPL,$r;

        $m = $this->module;
        $V = getDbData($table[$m.'vendor'],'uid='.$row['vendor'],'name,logo');
        $botView = $g['s'].'/?r='.$r.'&m='.$m.'&page=view&uid=';
        $bot_row = new skin('list/bot_row');

        // 변수값 세팅
        $TMPL['bot_view_link'] = $botView.$row['uid'];
        $TMPL['bot_avatar'] = $this->getBotAvatarSrc($row);
        $TMPL['bot_service'] = $row['service'];
        $TMPL['bot_uid'] = $row['uid'];
        $TMPL['bot_url'] = $g['s'].'/?r='.$r.'&m='.$m.'&page=view&uid='.$row['uid'];

        // 마크업 세팅
        $html= $bot_row->make();
        return $html;
    }

    // 사용자 챗봇 리스트 추출 (type : 출력형태/위치 (added,talked,all-inline...)
    function getUserBotList($mbruid,$type,$search,$recnum,$page){
        global $table,$g,$my;

        $m = $this->module;
        $is_mobile = $this->is_mobile();
        $tbl_added = $table[$m.'added'];
        $tbl_bot = $table[$m.'bot'];
        $tbl_chat = $table[$m.'chatLog'];
        $tbl_vendor = $table[$m.'vendor'];

        // 검색 엔터티 적용
        if($search){
            $search_query=' and ';
            foreach ($search as $key => $value) {
                $value = $this->db->real_escape_string($value);
                if($key=='keyword'){
                   $search_query .= "(concat_ws(' ',`induCat`,`name`,`service`,`intro`) LIKE '%".$value."%') or";
                }
                else $search_query .=" (".$key." = '".$value."') or";
            }
            $search_query=substr($search_query,0,-3);//마지막 and 제거
        }else{
            $search_query='';
        }

        // 쿼리 & 페이징 세팅
        $page = $page?$page:1;
        $limit=(($page-1)*$recnum).','.$recnum;
        $limit_query = " LIMIT ".$limit;

        if($type=='added'){ // 내가 추가한 봇
            $_query = "SELECT b.uid,b.service,b.avatar FROM %s as a left join %s as b on a.botuid=b.uid
                       WHERE a.mbruid<>0 and a.mbruid = '%s' and b.auth=1 and b.display=1 and b.hidden=0 ORDER BY a.uid ASC%s";
            $query = sprintf($_query,$tbl_added,$tbl_bot,$mbruid,$limit_query);
            $total_query = sprintf($_query,$tbl_added,$tbl_bot,$mbruid,'');

        }else if($type=='talked'){ // 내가 대화한 봇
            $_query = "SELECT b.uid,b.service,b.avatar FROM %s as c left join %s as b on c.botuid=b.uid
                       WHERE c.mbruid<>0 and c.mbruid = '%s' and c.hidden=0 GROUP BY b.uid ORDER BY c.uid ASC%s";
            $query = sprintf($_query,$tbl_chat,$tbl_bot,$mbruid,$limit_query);
            $total_query = sprintf($_query,$tbl_chat,$tbl_bot,$mbruid,'');

        }else if($type=='all-inline'){ // 내봇 전체 inline 출력
            if($my['manager']){
                $MG = getDbData($table[$m.'manager'],'mbruid='.$mbruid,'parentmbr');
                $mbruid = $MG['parentmbr'];
            }
            $_query = "SELECT name,service,avatar FROM %s WHERE mbruid = %s and auth=1 and display=1 and hidden=0 ORDER BY uid ASC%s";
            $query = sprintf($_query,$tbl_bot,$mbruid,$limit_query);
            $total_query = sprintf($_query,$tbl_bot,$mbruid,'');
        }else if($type=='just-one'){ // 내봇 전체 inline 출력
            if($my['manager']){
                $MG = getDbData($table[$m.'manager'],'mbruid='.$mbruid,'parentmbr');
                $mbruid = $MG['parentmbr'];
            }
            $_query = "SELECT name,service,avatar FROM %s WHERE mbruid = %s and auth=1 and display=1 and hidden=0 ORDER BY uid ASC%s";
            $query = sprintf($_query,$tbl_bot,$mbruid,$limit_query);
            $total_query = sprintf($_query,$tbl_bot,$mbruid,'');
        }else if($type=='search'){
            $_query = "SELECT uid,service,avatar FROM %s WHERE auth=1 and display=1 and hidden=0%s GROUP BY uid ORDER BY uid ASC%s";
            $query = sprintf($_query,$tbl_bot,$search_query,$limit_query);
            $total_query = sprintf($_query,$tbl_bot,$search_query,'');
        }

        $rows = $this->getAssoc($query);
        $total = $this->getRows($total_query);
        if($total){
            if($type=='added'||$type=='talked'||$type=='search'){
                if($is_mobile){
                    $botList ='
                    <div class="cb-list-slidelane-limiter" style="padding-top:26px;">
                        <div class="cb-list-slidelane-limitless">
                            <ul>';
                    foreach($rows as $row) {
                        $botList .= $this->getUserBotRow($row,$page,$totalPage);
                    }
                    $botList .='</ul></div></div>';

                }else{
                    $botList='<div class="cb-chatbot-circleitems"><div class="cb-row">';
                    $i=1;
                    foreach($rows as $row) {
                       $botList .= $this->getUserBotRow($row,$page,$totalPage);

                       if(!($i%6)) $botList .='<div class="cb-chatbot-circleitems"><div class="cb-row">';
                    }
                    $botList .='</div></div>';
                }

            }else if($type=='all-inline'){
                $FB = $rows[0];

                   $bot_avatar_src = $this->getBotAvatarSrc($FB);
                   $bot_avatar_bg = 'style="background: url('.$bot_avatar_src.') center top no-repeat;background-size:100% 100%;"';

                if($total>1){
                    $nameji_bot = $total-1;
                    $bot_more = '외 '.$nameji_bot.'개';
                }
                else $bot_more ='';
                if($is_mobile){
                    $botList='
                    <div class="cb-botwrapper" '.$bot_avatar_bg.'>
                        <div>
                            <span class="cb-icon'.(!$FB['avatar']?' cb-icon-mbot':'').'"></span>
                            <span class="cb-count">'.$total.'</span>
                        </div>
                    </div>
                    ';
                }else{
                    $botList ='
                    <span class="cb-botwrapper" '.$bot_avatar_bg.'>
                        <span class="cb-count">'.$total.'</span>
                        <span class="cb-icon'.(!$FB['avatar']?' cb-icon-mbot':'').'"></span>
                    </span>
                    <span>'.$FB['service'].$bot_more.'</span>';
                }

            }

        }else{
            if($type=='added'||$type=='talked'||$type=='search'){
                $botList='<div class="cb-chatbot-circleitems"><div class="cb-row" style="margin-left:0;margin-bottom:1.8rem;">';
                $botList .='데이타가 존재하지 않습니다.';
                $botList .='</div></div>';
            }else if($type=='all-inline'){
                $botList ='';
            }
        }
        return $botList; // 리스트

    }

    // 유저 관리자 모드에서 출력되는 특정 bot 데이타
    function getAdmBot($data){
        global $table;

        $m = $this->module;

        $R = getDbData($table[$m.'bot'],'uid='.$data['bot'],'*');
        $C = getDbData($table[$m.'category'],'uid='.$R['induCat'],'name');
        $BSCD = getDbSelect($table[$m.'botSettings'],'bot='.$data['bot'],'*');

        while($BS = db_fetch_array($BSCD)){
            if($BS['name']=='use_mediExam') $R['use_mediExam'] = $BS['value'];
            else if($BS['name'] =='use_compManual') $R['use_compManual'] = $BS['value'];
            else if($BS['name'] =='interface') $R['interface'] = $BS['value'];
            else if($BS['name'] =='use_chatting') $R['use_chatting'] = $BS['value'];
            else if($BS['name'] =='callBotName') $R['callBotName'] = $BS['value'];
            else if($BS['name'] =='default_context') $R['default_context'] = $BS['value'];
            else if($BS['name'] =='chatSkin') $R['chatSkin'] = $BS['value'];
            else if($BS['name'] =='chatBtn') $R['chatBtn'] = $BS['value'];
            else if($BS['name'] =='intro_use') $R['intro_use'] = $BS['value'];
            else if($BS['name'] =='pc_btn_bottom') $R['pc_btn_bottom'] = $BS['value'];
            else if($BS['name'] =='pc_btn_right') $R['pc_btn_right'] = $BS['value'];
            else if($BS['name'] =='m_btn_bottom') $R['m_btn_bottom'] = $BS['value'];
            else if($BS['name'] =='m_btn_right') $R['m_btn_right'] = $BS['value'];
            else if($BS['name'] =='intentMV') $R['intentMV'] = $BS['value'];
            else if($BS['name'] =='use_reserve') $R['use_reserve'] = $BS['value'];
            else if($BS['name'] =='reserve_category') $R['reserve_category'] = $BS['value'];
            else if($BS['name'] =='reserve_manage') $R['reserve_manage'] = $BS['value'];
            else if($BS['name'] =='reserve_api') $R['reserve_api'] = $BS['value'];
            else if($BS['name'] =='reserve_domainkey') $R['reserve_domainkey'] = $BS['value'];
            else if($BS['name'] =='reserve_onda_suburl') $R['reserve_onda_suburl'] = $BS['value'];
            else if($BS['name'] =='use_shopapi') $R['use_shopapi'] = $BS['value'];
            else if($BS['name'] =='shopapi_vendor') $R['shopapi_vendor'] = $BS['value'];
            else if($BS['name'] =='shopapi_domain') $R['shopapi_domain'] = $BS['value'];
            else if($BS['name'] =='shopapi_mall_id') $R['shopapi_mall_id'] = $BS['value'];
            else if($BS['name'] =='shopapi_access_token') $R['shopapi_access_token'] = $BS['value'];
            else if($BS['name'] =='shopapi_access_token_expire') $R['shopapi_access_token_expire'] = $BS['value'];
            else if($BS['name'] =='shopapi_refresh_token') $R['shopapi_refresh_token'] = $BS['value'];
            else if($BS['name'] =='shopapi_refresh_token_expire') $R['shopapi_refresh_token_expire'] = $BS['value'];
            else if($BS['name'] =='shopapi_mall_type') $R['shopapi_mall_type'] = $BS['value'];
            else if($BS['name'] =='shopapi_client_key') $R['shopapi_client_key'] = $BS['value'];
            else if($BS['name'] =='use_syscheckup') $R['use_syscheckup'] = $BS['value'];
            else if($BS['name'] =='syscheckup_start') $R['syscheckup_start'] = $BS['value'];
            else if($BS['name'] =='syscheckup_end') $R['syscheckup_end'] = $BS['value'];
            else if($BS['name'] =='syscheckup_msg') $R['syscheckup_msg'] = $BS['value'];
            else if($BS['name'] =='use_bargein') $R['use_bargein'] = $BS['value'];
            else if($BS['name'] =='faqMV') $R['faqMV'] = $BS['value'];
            else if($BS['name'] =='tts_vendor') $R['tts_vendor'] = $BS['value'];
            else if($BS['name'] =='tts_audio') $R['tts_audio'] = $BS['value'];
            else if($BS['name'] =='tts_pitch') $R['tts_pitch'] = $BS['value'];
            else if($BS['name'] =='tts_speed') $R['tts_speed'] = $BS['value'];
            else if($BS['name'] =='use_jusobot') $R['use_jusobot'] = $BS['value'];
            else if($BS['name'] =='chatTop') $R['chatTop'] = $BS['value'];
            else if($BS['name'] =='chatLogo') $R['chatLogo'] = $BS['value'];
            // 20230712 aramjo
            else if($BS['name'] =='use_chatgpt') $R['use_chatgpt'] = $BS['value'];

            // 20230829 aramjo
            else if($BS['name'] =='use_cschat') $R['use_cschat'] = $BS['value'];
            else if($BS['name'] =='cschat_api') $R['cschat_api'] = $BS['value'];
            else if($BS['name'] =='cschat_userinfo') $R['cschat_userinfo'] = $BS['value'];
        }

        $R['use_cschat'] = $R['use_cschat'] == 'on' && array_key_exists($R['cschat_api'], $this->csChatAPIs) ? 'on' : '';

        $R['chatBtn'] = $R['chatBtn'] ? $R['chatBtn'] : '/_core/skin/images/btn_chatbot.png';
        $R['chatTop'] = $R['chatTop'] ? $R['chatTop'] : 'title';

        if($data['mod']=='form'){
            $w ='45px';
            $h ='45px';
            $role = 'data-role="self-uploadImg"';
        }else if($data['mod']=='list'){
            $w ='35px';
            $h ='35px';
            $role = '';
        }

        $R['botId'] = $R['id'];
        $R['bot_url'] = $this->getChatUrl($R);
        $R['bot_avatar_src'] = $this->getBotAvatarSrc($R);
        $R['bot_avatar_bg'] = 'style="background-image: url('.$R['bot_avatar_src'].');background-color:#fff;width:'.$w.';height:'.$h.'"';

        $input = $data['mod'] == 'form' ? '<input type="hidden" data-role="img_url" name="avatar" value="'.$R['bot_avatar_src'].'">' : '';
        $R['bot_avatar_img'] = $input.'<span class="botAvatar-wrapper" '.$role.' '.$R['bot_avatar_bg'].'></span>';

        $R['bot_avatar'] = $R['bot_avatar_img'].' <span>'.$R['name'].'</span>';

        // 업종
        $R['upjong'] = $C['name'];

        $_data = array();
        $_data['botId'] = $R['id'];
        $_data['channel'] = $this->botks; // bottalks 채널명
        $_data['name_array'] = array("client_id","client_secret","access_token");
        $_data['act'] = 'getData'; // cf : saveData
        $API = $this->controlChannelData($_data);

        // API 설정값 추출 or 세팅
        if($API['console_id']) $R['console_id'] = $API['console_id'];
        else $R['console_id'] = $this->setBotApiSettings('console_id',$R);

        if($API['client_id']) $R['client_id'] = $API['client_id'];
        else $R['client_id'] = $this->setBotApiSettings('client_id',$R);

        if($API['client_secret']) $R['client_secret'] = $API['client_secret'];
        else $R['client_secret'] = $this->setBotApiSettings('client_secret',$R);

        if($API['access_token']) $R['access_token'] = $API['access_token'];
        else $R['access_token'] = $this->setBotApiSettings('access_token',$R);

        return $R;
    }

    // api 데이타 세팅
    function setBotApiSettings($type,$data){
        global $table;

        $m = $this->module;
        $tbl = $table[$m.'channelSettings'];

        if($type =='console_id'){
            $botid = $R['id'];
        }else if($type =='client_id'){
            $src = 'N';
            $len = 18;
        }else if($type =='client_secret'){
            $src = 'AN';
            $len = 54;
        }else if($type =='access_token'){
            $src = 'AN';
            $len = 270;
        }

        $_data = array();
        $_data['type'] = $type;
        $_data['botid'] = $botid;
        $_data['src'] = $src;
        $_data['len'] = $len;

        $ranString = $this->getRandomString($_data);

        // 봇톡스 api 역시 하나의 채널로 간주하고 저장한다.
        $data['act'] = 'saveData';
        $data['channel'] = $this->botks;
        $data['nameArray'][$type] = $ranString;
        $this->controlChannelData($data);

        return $ranString;
    }

    // 업종 기준 챗봇 row 마크업 추출
    function getUserBotRow($row,$page,$totalPage){
        global $g,$table,$TMPL,$r;

        $m = $this->module;
        $is_mobile =$this->is_mobile();
        $f_src = $this->getBotAvatarSrc($row);
        $botView = $g['s'].'/?r='.$r.'&m='.$m.'&page=view&uid=';
        if($is_mobile){
            $TMPL['bot_uid']=$row['uid'];
            $TMPL['bot_avatar']=$f_src;
            $TMPL['bot_service']=$row['service'];
            $bot_row = new skin('list/bot_row');
            $html = $bot_row->make();
        }else{
            $html='
            <div class="cb-row-6d bot-link">
                <a href="'.$botView.$row['uid'].'">
                    <div class="cb-chatbot-circleitem">
                        <div class="cb-chatbot-circleitem-picture">
                            <img src="'.$f_src.'" alt="Circle Image" />
                        </div>
                        <div class="cb-chatbot-circleitem-text">'.$row['service'].'</div>
                    </div>
                </a>
            </div>';
        }
        return $html;
    }

    // 벤더별, 타입별(build,regis) 첫번째 봇 데이타 추출
    function getFirstBotData($vendor,$type){
        global $table;
        $m = $this->module;
        $sort   = $sort ? $sort : 'gid';
        $orderby= $orderby ? $orderby : 'desc';
        $p = $p?$P:1;

        $RFB = array();
        $RCD = getDbArray($table[$m.'bot'],'vendor='.$vendor.' and type='.$type,'*',$sort,$orderby,1,$p); // build 된 bot 중 첫번째
        while ($R = db_fetch_array($RCD)) $RFB[] = $R;

        return $RFB[0];
    }

    // 챗봇 view 페이지 세팅
    function getBotDataFromId($bot_id){
        global $g,$table;
        $cmod = $this->cmod;

        $m = $this->module;

        $_table = $table[$m.'bot']." A ";
        $_table .="left join ".$table[$m.'dialog']." B on A.vendor = B.vendor and A.uid = B.bot and B.gid=0 and B.active=1 and B.type='D' ";
        $_table .="left join (Select A.uid, count(*) as nFaqCnt From ".$table[$m.'bot']." A, ".$table[$m.'faq']." B Where A.uid = B.bot Group by A.uid) as C on A.uid = C.uid ";
        $_table .="left join rb_s_mbrdata D on A.mbruid = D.memberuid ";
        $B = getDbData($_table, "A.id='".$bot_id."'", "A.*, B.uid as dialog_uid, C.nFaqCnt, D.cgroup");

        // 세팅내용
        $BSCD = getDbSelect($table[$m.'botSettings'],'bot='.$B['uid'],'*');

        while($BS = db_fetch_array($BSCD)){
            if($BS['name']=='use_mediExam') $B['use_mediExam'] = $BS['value'];
            else if($BS['name'] =='use_compManual') $B['use_compManual'] = $BS['value'];
            else if($BS['name'] =='interface') $B['interface'] = $BS['value'];
            else if($BS['name'] =='use_chatting') $B['use_chatting'] = $BS['value'];
            else if($BS['name'] =='callBotName') $B['callBotName'] = $BS['value'];
            else if($BS['name'] =='chatSkin') $B['chatSkin'] = $BS['value'];
            else if($BS['name'] =='intro_use') $B['intro_use'] = $BS['value'];
            else if($BS['name'] =='intentMV') $B['intentMV'] = $BS['value'];
            else if($BS['name'] =='use_reserve') $B['use_reserve'] = $BS['value'];
            else if($BS['name'] =='reserve_category') $B['reserve_category'] = $BS['value'];
            else if($BS['name'] =='reserve_manage') $B['reserve_manage'] = $BS['value'];
            else if($BS['name'] =='reserve_api') $B['reserve_api'] = $BS['value'];
            else if($BS['name'] =='reserve_domainkey') $B['reserve_domainkey'] = $BS['value'];
            else if($BS['name'] =='use_bargein') $B['use_bargein'] = $BS['value'];
            else if($BS['name'] =='faqMV') $B['faqMV'] = $BS['value'];
            else if($BS['name'] =='tts_vendor') $B['tts_vendor'] = $BS['value'];
            else if($BS['name'] =='tts_audio') $B['tts_audio'] = $BS['value'];
            else if($BS['name'] =='tts_pitch') $B['tts_pitch'] = $BS['value'];
            else if($BS['name'] =='tts_speed') $B['tts_speed'] = $BS['value'];
            else if($BS['name'] =='use_jusobot') $B['use_jusobot'] = $BS['value'];
            else if($BS['name'] =='chatTop') $B['chatTop'] = $BS['value'];
            else if($BS['name'] =='chatLogo') $B['chatLogo'] = $BS['value'];
            // 20230712 aramjo chatgpt
            else if($BS['name'] =='use_chatgpt') $B['use_chatgpt'] = $BS['value'];

            // 20230829 aramjo
            else if($BS['name'] =='use_cschat') $B['use_cschat'] = $BS['value'];
            else if($BS['name'] =='cschat_api') $B['cschat_api'] = $BS['value'];
            else if($BS['name'] =='cschat_userinfo') $B['cschat_userinfo'] = $BS['value'];
        }

        // service 명 출력
        if($this->showTimer) $service = '챗봇';
        else{
            if($cmod=='dialog') $service ='테스트 모드';
            else $service = $B['service']?$B['service']:$B['name'];
        }

        // 우상단 아이콘 & 링크
        if($cmod=='dialog'){
            $topRightIcon = 'close';
            $topRightLink = '##';
        }else{
            $topRightIcon = 'house';
            $topRightLink = $this->getBotWebUrl($B['website']);
        }

        // faq 여부
        $faq_usable = $B['nFaqCnt'] ? true : false;

        $B['chatTop'] = $B['chatTop'] ? $B['chatTop'] : "title";
        $B['chatLogo'] = $B['chatLogo'] ? $B['chatLogo'] : "";

        $result = array(
            'mbruid'=>$B['mbruid'],
            'bot_uid'=>$B['uid'],
            'bot_avatar_src'=> $this->getBotAvatarSrc($B), // 챗봇 아바타(src) 추출
            'bot_service'=> $service,
            'bot_name'=> $B['name'],
            'vendor'=> $B['vendor'],
            'bot'=>$B['uid'],
            'bot_home'=> $this->getBotWebUrl($B['website']),
            'topRightIcon'=>$topRightIcon,
            'topRightLink'=>$topRightLink,
            'dialog'=>$B['dialog_uid'],
            'use_chatting'=>$B['use_chatting'],
            'callBotName'=>$B['callBotName'],
            'botActive'=>$B['active'], // dev or live
            'monitering_fa'=>$B['monitering_fa'],
            'bot_interface'=>$B['interface'],
            'bot_skin'=>$B['chatSkin'],
            'intro_use'=>$B['intro_use'],
            'intentMV'=>$B['intentMV'],
            'use_reserve'=>$B['use_reserve'],
            'reserve_category'=>$B['reserve_category'],
            'reserve_manage'=>$B['reserve_manage'],
            'reserve_api'=>$B['reserve_api'],
            'reserve_domainkey'=>$B['reserve_domainkey'],
            'use_bargein'=>$B['use_bargein'],
            'faq_usable'=>$faq_usable,
            'faqMV'=>$B['faqMV'],
            'tts_vendor'=>$B['tts_vendor'],
            'tts_audio'=>$B['tts_audio'],
            'tts_pitch'=>$B['tts_pitch'],
            'tts_speed'=>$B['tts_speed'],
            'use_jusobot'=>$B['use_jusobot'],
            'chatTop'=>$B['chatTop'],
            'chatLogo'=>$B['chatLogo'],
            'cgroup'=>$B['cgroup'],
            //callbot--
            'bottype'=>$B['bottype'],
            // 20230712 aramjo chatgpt
            'use_chatgpt'=>$B['use_chatgpt'],
            // 20230829 aramjo cschat
            'use_cschat'=>($B['use_cschat'] == 'on' && array_key_exists($B['cschat_api'], $this->csChatAPIs) ? 'on' : ''),
            'cschat_api'=>$B['cschat_api'],
            'cschat_userinfo'=>$B['cschat_userinfo'],
        );
        return $result;
    }

    // 챗봇 홈페이지 url 추출
    function getBotWebUrl($url){
        if($url){
            if(!preg_match( '/(http|https):\\/\\//i' ,$url)){
                $_url = 'http://'.$url;
                return $_url;
            }else return $url;
        }
        else return;
    }

    // 상용자 입력 URL 출력 함수
    function getWebUrlText($name,$url){
        if($url){
            if(!preg_match( '/(http|https):\\/\\//i' ,$url)){
                $_url = 'http://'.$url;
                return '<a href="'.$_url.'" target="_blank" style="color:#b1b1b1">'.$_url.'</a>';

            }else{
                return '<a href="'.$url.'" target="_blank" style="color:#b1b1b1">'.$url.'</a>';
            }
        }

        else return $name.' URL이 등록되지 않았습니다.';
    }

    // 사용자 채팅 로그 추가 함수
    /*
       ## $data 인수 정리
       "type" : 메세지 타입(E: Emoticon, T: text, M: menu)
       "content" :  메세지 내용
       "find" :  성공/실패 여부 (S: success, F: fail)
       "emotion" 감성값
    */
    function addChatLog($data){
        global $g,$table,$date;

        $cmod = $this->cmod;
        $m = $this->module;
        $botid = $data['botid']?$data['botid']:$this->botid;
        $vendor = $data['vendor']?$data['vendor']:$this->vendor;
        $userId = $data['userId']?$data['userId']:$this->userId;
        $bot = $data['bot']?$data['bot']:$this->botuid;
        $botActive = $data['botActive']?$data['botActive']:$this->botActive;
        $userName = $data['userName']?$data['userName']:$this->fromPhone;

        // bot Setting 값 체크
        $data['bot'] = $bot;

        // 로그 저장 안함
        if($cmod=='dialog' || $cmod=='skin' || $cmod=='LC' || $cmod=='TS' || $botActive!=2) return; // active =1 > 개발중

        $roomToken = $data['roomToken']?$data['roomToken']:$this->roomToken;

        // data 값 세팅
        $chatType = $data['chatType']?$data['chatType']:'Q'; // 채팅타입 (질문 or 답변)
        $printType = $data['printType']?$data['printType']:'T'; // 출력 타입
        $content = getStrMasking($data['content']); // 사용자 입력값

        // 사용자 정보
        $userUid = $user = $this->mbruid?$this->mbruid:0;
        if($userUid){
            $M = getUidData($table['s_mbrid'],$userUid);
            $userId = $M['id'];
        }

        // 기타 정보
        $ip     = $_SERVER['HTTP_X_FORWARDED_FOR'] ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];;
        $agent  = $_SERVER['HTTP_USER_AGENT'];
        $d_regis = $date['totime'] ? $date['totime'] : date('YmdHis');

        if(!$data['chat_uid']) {
            if((int)$data['node']) {
                $_wh = "vendor='".$vendor."' and bot='".$bot."' and dialog='".$this->dialog."' and id='".$data['node']."'";
                $R = getDbData($table[$m.'dialogNode'],$_wh, 'name');
                $node = $R['name'];
            }

            // 챗팅 로그 등록
            $QKEY = "vendor,bot,roomToken,userName,userId,userUid,printType,chatType,content,ip,agent,node,d_regis";
            $QVAL = "'$vendor','$bot','$roomToken','$userName','$userId','$userUid','$printType','$chatType','$content','$ip','$agent','$node','$d_regis'";
            getDbInsert($table[$m.'chatLog'],$QKEY,$QVAL);

            // 저장된 chatLog uid
            $data['last_chat'] = getDbCnt($table[$m.'chatLog'],'max(uid)','');
            $this->last_chat = $data['last_chat'];

            //$this->console_log($data);

            // 텍스트 형태일 경우
            if($printType=='T'){
                $_today = $date['today'];
                $chat = $data['last_chat'];

                // 단어단위로 저장 ##################################################################
                $tbl = $table[$m.'chatWordLog'];
                $aPS = array('NNG', 'NNP', 'NP', 'SL'); //일반명사, 인명, 대명사, 외국어
                preg_match_all('/\w+\|(NNG|NNP|NP|SL)/u', str_replace('(*|*)', '|', $data['input_mop']), $aWord);
                foreach ($aWord[0] as $keyword) {
                    $keyword = substr($keyword, 0, strpos($keyword, '|'));
                    if($keyword) {
                        $is_word_q = "vendor='".$vendor."' and keyword='".$keyword."' and date='$_today'";
                        $is_word = getDbData($tbl,$is_word_q,'uid');
                        if($is_word['uid']) getDbUpdate($tbl,'hit=hit+1','uid='.$is_word['uid']);
                        else{
                            $QKEY = "vendor,bot,roomToken,keyword,hit,date";
                            $QVAL = "'$vendor','$bot','$roomToken','$keyword','1','$_today'";
                            getDbInsert($tbl,$QKEY,$QVAL);
                        }
                    }
                }

                // 문장단위로 저장 ############################################################
                $tbl = $table[$m.'chatStsLog'];
                $sentence = preg_replace('/\s+/', '', $content);
                $is_sentence_q = "vendor='".$vendor."' and bot='".$bot."' and replace(sentence, ' ', '')='".$sentence."' and date='$_today'";
                if($roomToken) $is_sentence_q.=" and roomToken='".$roomToken."'"; // roomToken 구분할 경우

                $is_sentence = getDbData($tbl,$is_sentence_q,'uid');
                if($is_sentence['uid']){
                    getDbUpdate($tbl,'hit=hit+1','uid='.$is_sentence['uid']);
                } else {
                    $_today = $date['today'];
                    $QKEY = "vendor,bot,roomToken,sentence,hit,date";
                    $QVAL = "'$vendor','$bot','$roomToken','$content','1','$_today'";
                    getDbInsert($tbl,$QKEY,$QVAL);
                }
            }
        } else {
            // 필터링된 인텐트, 엔터티, 노드
            $intent = $data['intent'] ? $data['intent'] : '';
            $score = $data['intentScore'] ? $data['intentScore'] : '';
            $node = $data['node'] ? $data['node'] : '';
            $entity = '';
            if($data['entity'] && is_array($data['entity'])) {
                foreach($data['entity'] as $aEntity) {
                    if($aEntity[2]) {
                        $entityVal = $aEntity[2].'|'.$aEntity[3].'|'.$aEntity[4].'|'.$aEntity[5];
                        $entity .= $entityVal.',';
                    }
                }
                $entity = rtrim($entity,',');
            }
            $is_unknown = $data['unknown'] ? 1 : 0;

            $_set = "intent='".$intent."', score='".$score."', entity='".$entity."', node='".$node."', is_unknown='".$is_unknown."'";
            getDbUpdate($table[$m.'chatLog'],$_set,"uid='".$data['chat_uid']."'");
        }
        return $data;
    }

    // 챗봇 Log
    function getBotLogContent($data){
        if(is_array($data['content'])){
            $resArr = $data['content'];
            $response='';
            foreach ($resArr as $res) {
                if(isset($res['res_type']) && isset($res['content'])) {
                    if($res['res_type'] == 'node' || $res['res_type'] == 'api') continue;
                    $type = $res['res_type'];
                    $response.= $res['content'];
                } else {
                    if(is_array($res[1])){
                        foreach ($res[1] as $item) {
                            if(is_array($item[0])) {
                                $type = $item[0]['type'];
                                $content = $item[0]['content'];
                                if(isset($item[0]['content']) && $item[0]['content']!='') $response.= $item[0]['content'];
                            } else {
                                $type = $item['res_type'];
                                $content = $item['content'];
                                if(isset($item['content']) && $item['content']!='') $response.= $item['content'];
                            }
                        }
                    }else{
                        if(is_array($res[0]) && array_key_exists('type', $res[0])) {
                            $type = $res[0]['type'];
                            $response.= $res[0]['content'];
                        } else {
                            $type = $res[0];
                            $response.= $res[1];
                        }
                    }
                }
            }
        } else {
            $type = 'text';
            $response = $data['content'];
        }

        $result['resType'] = $type;
        $result['response'] = $response;
        return $result;
    }

    // 챗봇 채팅 로그 추가 함수
    /*
       ## $data 인수 정리
       "type" : 메세지 타입(E: Emoticon, T: text, M: menu)
       "content" :  메세지 내용
       "find" :  성공/실패 여부 (S: success, F: fail)
       "emotion" 감성값
    */
    function addBotChatLog($data){
        global $g,$table,$date,$my;

        $cmod = $this->cmod;
        $roomToken = $data['roomToken']?$data['roomToken']:$this->roomToken;

        $R2P = array("text" =>'T',"mix" =>'M');
        $m = $this->module;
        $botid = $data['botid']?$data['botid']:$this->botid;
        $vendor = $data['vendor']?$data['vendor']:$this->vendor;
        $bot = $data['bot']?$data['bot']:$this->botuid;
        $botActive = $data['botActive']?$data['botActive']:$this->botActive;

        // bot Setting 값 체크
        $data['bot'] = $bot;

        // 로그 저장 안함
        if($cmod=='dialog' || $cmod=='skin' || $cmod=='LC' || $cmod=='TS' || $botActive!=2) return;

        $BLC = $this->getBotLogContent($data);

        $ctx_find = array("find"=>"last_chat");
        $ctx_last_chat = $this->getContextVal($ctx_find);

        // data 값 세팅
        $chatType = $data['chatType']?$data['chatType']:'R'; // 채팅타입 (질문 or 답변)
        //$printType = 'M';//$R2P[$BLC['resType']]; // 출력 타입
        $printType = $BLC['resType'] ? $BLC['resType'] : 'M'; // 출력 타입
        $findType = $data['findType']?$data['findType']:'';
        $is_unknown = $data['unknown'] ? 1 : 0;
        $score = $data['score'] ? $data['score'] : '';

        // 메세지 내용 세팅
        if($data['humanMod']) {
            $content = $this->getBotTextMsg($data); // 챗팅인 경우
            $chat = getDbCnt($table[$m.'chatLog'],'max(uid)', "vendor=$vendor and bot=$bot and roomToken='$roomToken'");
            $chatType = 'B';
            $printType = 'text';

        } else {
            $content = $BLC['response']; // 챗봇 답변
            $chat = $data['last_chat']?$data['last_chat']:($this->last_chat?$this->last_chat:$ctx_last_chat); // 사용자 챗 uid (chatLog 테이블 uid )
        }

        // 사용자 정보
        $user = $my['uid']?$my['uid']:0;

        // 기타 정보
        $d_regis = $date['totime'] ? ($date['totime']+1) : (date('YmdHis')+1); // 사용자 입력문장과 시간이 같아지는 현상 대응 방지

        if($content){
            // 챗봇 챗팅 로그 등록
            $content = addslashes($content);
            $QKEY = "vendor,bot,roomToken,user,chat,printType,chatType,findType,content,intent,score,entity,node,is_unknown,d_regis";
            $QVAL = "'$vendor','$bot','$roomToken','$user','$chat','$printType','$chatType','$findType','$content','$intent','$score','$entity','$node','$is_unknown','$d_regis'";
            getDbInsert($table[$m.'botChatLog'],$QKEY,$QVAL);

            // 응답 카운트
            $same_chat = $data['same_chat'] ? $data['same_chat'] : 0;
            $_count = array('same_chat'=>$same_chat, 'last_chat'=>$chat, 'unknown'=>$is_unknown, 'd_regis'=>$d_regis);
            $this->setBotResCounter($_count);
        }

        // 텍스트 형태일 경우
        if($data['unknown']){
            $tbl_chatLog = $table[$m.'chatLog'];
            $Q = getDbData($tbl_chatLog,'uid='.$chat,'content');
            $sentence = $Q['content'];

            $tbl = $table[$m.'unknown'];
            $is_sentence_q = "vendor='".$vendor."' and bot='".$bot."' and sentence='".$sentence."'";
            if($roomToken) $is_sentence_q.=" and roomToken='".$roomToken."'"; // roomToken 구분할 경우

            $is_sentence = getDbData($tbl,$is_sentence_q,'uid');
            if($is_sentence['uid']) getDbUpdate($tbl,'hit=hit+1','uid='.$is_sentence['uid']);
            else{
                $_today = $date['today'];
                $QKEY = "vendor,bot,roomToken,sentence,hit,date";
                $QVAL = "'$vendor','$bot','$roomToken','$sentence','1','$_today'";
                getDbInsert($tbl,$QKEY,$QVAL);
            }
        }
        return $data;
    }

    // 답변 못한것 저장
    function addUnknownLog($botid,$user_input){
        global $my,$table,$date;

        $m = $this->module;
        $B = getDbData($table[$m.'bot'],"id='".$botid."'",'uid,vendor');
        $vendor = $B['vendor'];
        $botuid = $B['uid'];

        $myuid = $my['uid']?$my['uid']:0;
        $d_regis = $date['totime'];

        $QKEY = "mbruid,botuid,vendor,message,d_regis";
        $QVAL = "'$mbruid','$botuid','$vendor','$user_input','$d_regis'";
        getDbInsert($table[$m.'unknown'],$QKEY,$QVAL);
    }

    // 챗봇 아바타 src 추출
    function getBotAvatarSrc($R){
        global $g,$table;
        $cmod = $this->cmod;
        $m = $this->module;

        if($R['avatar'] && file_exists($_SERVER['DOCUMENT_ROOT'].$R['avatar'])) {
            $avatar_src = $R['avatar'];
        } else {
            $_skin = $this->bot_skin ? $this->bot_skin : ($R['chatSkin'] ? $R['chatSkin'] : 'skin.default');
            $aTemp = explode(".", $_skin);
            $avatar_src = '/_core/skin/images/sender_ico_'.$aTemp[1].'.png';
        }
        return $avatar_src;
    }

     // User 아바타 src 추출
    function getUserAvatar($mbruid,$type){
        global $g,$table;
        if($mbruid) $M = getDbData($table['s_mbrdata'],'memberuid='.$mbruid,'photo,manager');
        if($M['photo'] && file_exists($_SERVER['DOCUMENT_ROOT'].'/_var/avatar/'.$M['photo'])) $user_avatar_src = $g['url_root'].'/_var/avatar/'.$M['photo'];
        else $user_avatar_src = $g['url_root'].$g['img_layout'].'/user_blank2.png';
        $user_avatar_bg = 'style="background: url('.$user_avatar_src.') center top no-repeat;background-size:100% 100%;"';

        $result = array();
        $result['src'] = $user_avatar_src;
        $result['bg'] = $user_avatar_bg;
        return $result[$type];
    }

    // 테마 패스 추출함수
    public function getThemePath($type){
        global $g;

        if($type=='relative') $result = $g['path_module'].$this->module.'/theme/'.$this->theme_name;
        else if($type=='absolute') $result = $g['url_root'].'/modules/'.$this->module.'/theme/'.$this->theme_name;

        return $result;
    }

    // get html only (no replace-parse)
    public function getHtmlOnly($fileName) {
        global $g,$CONF;

        $file = sprintf($CONF['theme_path'].'/'.$CONF['theme_name'].'/html/%s.html', $fileName);
        $fh_skin = fopen($file, 'r');
        $skin = @fread($fh_skin, filesize($file));
        fclose($fh_skin);
        return $skin;
    }

        // get html & replace-parse
    public function getHtml($fileName) {
        global $g,$TMPL;
        $theme_path = $this->getThemePath('relative');
        $file = sprintf($theme_path.'/html/%s.html', $fileName);
        $fh_skin = fopen($file, 'r');
        $skin = @fread($fh_skin, filesize($file));
        fclose($fh_skin);
        //return $skin;
        return $this->getParseHtml($skin);
    }

    public function getParseHtml($skin) {
        global $TMPL;
        // $skin = preg_replace_callback('/{\$lng->(.+?)}/i', create_function('$matches', 'global $LNG; return $LNG[$matches[1]];'), $skin);
        $skin = preg_replace_callback('/{\$([a-zA-Z0-9_]+)}/', create_function('$matches', 'global $TMPL; return (isset($TMPL[$matches[1]])?$TMPL[$matches[1]]:"");'), $skin);
        return $skin;
    }

    public function getCountryData($type,$input){

        $C2N = array('KOR'=>'한국','JPN'=>'일본','TWN'=>'대만','CHN'=>'중국','HKG'=>'홍콩','IND'=>'인도','USA'=>'미국','FRA'=>'프랑스');
        $N2C = array('한국'=>'KOR','일본'=>'JPN','대만'=>'TWN','중국'=>'CHN','홍콩'=>'HKG','인도'=>'IND','미국'=>'USA','프랑스'=>'FRA');

        if($type=='all') return $C2N;
        else if($type=='name') return $C2N[$input]?$C2N[$input]:'기타';
        else if($type=='code') return $N2C[$input]?$N2C[$input]:'ETC';
    }

    // 메타 정보 추출 함수
    function getMetaData($row,$data){
        global $g;

        $result['title'] = $row['subject'];
        $result['url'] = strip_tags($g['url_root'].'/ad/'.$row['uid']);
        $result['description'] = getStrCut($row['content'],50,'');
        $result['image'] = $this->getAdFeaturedImgSrc($row);
        return $result[$data];
    }

    // 파일 삭제 함수
    function deleteFile($row){
        if($row['type']==2){
            unlink('.'.$row['folder'].'/'.$row['tmpname']);
            unlink('.'.$row['folder'].'/'.$row['thumbname']);
        }else if($row['type']==5){
            unlink('.'.$row['folder'].'/'.$row['name']);
        }
        // DB 삭제
        getDbDelete($this->table('photo'),'uid='.$row['uid']);
    }

    // 숫자 변경 함수
    function formatWithSuffix($input){
        $suffixes = array('', 'K', 'M', 'G', 'T');
        $suffixIndex = 0;

        while(abs($input) >= 1000 && $suffixIndex < sizeof($suffixes)){
            $suffixIndex++;
            $input /= 1000;
        }

        return (
            $input > 0
                // precision of 3 decimal places
                ? floor($input * 1000) / 1000
                : ceil($input * 1000) / 1000
            )
            . $suffixes[$suffixIndex];
    }

    // dialog_match > response 값으로 답변 추출하기
    function getResDataFromDMR($data){
        $result= '';
        $DMR = $data['DMR']; // dialog_match > response
        foreach ($DMR as $resGroup) {
            if(is_array($resGroup[0])) {
                foreach($resGroup as $resItem){
                    $resType = $resItem[0];
                    $content = $resItem[1];

                    if($resType =='text') $result.= $content;
                    else if($resType =='if'){
                        foreach($content as $ifGroup){
                            $resType = $ifGroup['res_type'];
                            $content = $ifGroup['content'];
                            if(!is_array($content)) {
                                $result.= $content;
                            } else {
                                foreach ($ifGroup as $ifItem) {
                                     $resType = $ifItem['type'];
                                     $content = $ifItem['content'];
                                    if($resType=='text') $result.= $content;
                                }
                            }
                        }
                    }else if($resType=='hMenu'){
                        foreach ($content as $btnItem) {
                            $result.= $btnItem;
                        }
                    }
                }
            } else {
                if($resGroup[0] == 'text') {
                    $result .= $resGroup[1];
                }
            }
        }
        return $result;
    }

    function deleteBotFile($data) {
        global $table;

        if(trim($data['file_url'])) {
            $chFile = substr($data['file_url'],strpos($data['file_url'],'/files'));
            $chFile = $_SERVER['DOCUMENT_ROOT'].$chFile;
            if(file_exists($chFile)) @unlink($chFile);

            if($data['vendor'] && $data['bot']) {
                $filename = substr(strrchr($data['file_url'],'/'), 1);
                getDbDelete($table[$this->module.'upload'],"vendor=".$data['vendor']." and bot=".$data['bot']." and tmpname='".$filename."'");
            }
        }
    }

    function getNodeTreeJson($data, $json=true) {
        global $table;
        $nods = $pointers = array();

        // 기존 그래프에서 필요없는 노드 삭제
        $R = getDbData($table[$this->module.'dialog'],"vendor='".$data['vendor']."' and bot='".$data['bot']."' and dialog='".$data['dialog']."'",'graph');
        if($R['graph']) {
            $aGraphNode = array();
            $xml = simplexml_load_string($R['graph']);
            foreach($xml->root->mxCell as $cell){
                $attr = $cell->attributes();
                if($attr['value'] && $attr['uid']) {
                    $aGraphNode[] = $attr['uid'];
                }
            }
            if(count($aGraphNode) > 0) {
                sort($aGraphNode);
                $aGraphNode = implode(",", $aGraphNode);
                $_wh = "vendor='".$data['vendor']."' and bot='".$data['bot']."' and dialog='".$data['dialog']."' and is_unknown=0 and id not in (".$aGraphNode.")";
                getDbDelete($table[$this->module.'dialogNode'],$_wh);
            }
        }
        //-------------------------------------------------------

        $tbl = $table[$this->module.'dialogNode'];
        $tbl_dialog = $table[$this->module.'dialog'];
        $_wh = "A.vendor='".$data['vendor']."' and A.bot='".$data['bot']."' and A.dialog='".$data['dialog']."' and A.hidden=0";
        $query = "Select ";
        $query .="  A.uid as dialogNodeUid, A.id as nodeid, A.parent, A.depth, A.name, A.dialog, A.recCondition, A.node_action, A.jumpTo_node, ";
        $query .="  A.is_unknown, A.use_topic, A.track_flag, B.active as topicActive ";
        $query .="From ".$tbl." A ";
        $query .="left join ".$tbl_dialog." B on A.use_topic = B.uid ";
        $query .="Where ".$_wh." Order by A.parent ASC, A.gid ASC";
        $rows=$this->getAssoc($query);
        foreach($rows as $row) {
            $row['name'] = getRemoveBackslash($row['name']);
            if(!isset($pointers[$row['nodeid']])) {
                $pointers[$row['nodeid']] = $row;
            }
            if(!empty($row['parent'])) {
                if(!isset($pointers[$row['parent']])) {
                    $pointers[$row['parent']] = $row;
                }
                $pointers[$row['parent']]['children'][] =  &$pointers[$row['nodeid']];
            } else {
                $nods[$row['nodeid']] = &$pointers[$row['nodeid']]; // This is our top level
                $nods[$row['is_unknown']] = &$pointers[$row['is_unknown']];
            }
        }
        $nods = array_values($nods);
        if($json) {
            $nods = count($nods[0])>0 ? json_encode($nods[0], JSON_UNESCAPED_UNICODE) : '';
        }
        return $nods;
    }

    function getBotIntroData($data) {
        global $table;
        $query = "SELECT uid, name, value FROM ".$table[$this->module.'botSettings']." WHERE vendor='".$data['vendor']."' and bot='".$data['bot']."' ORDER BY uid ASC";
        $rows=$this->getAssoc($query);

        $aBotIntro = array();
        $aBotIntro['aIntroProfile'] = array();
        $aBotIntro['aIntroMenu'] = array();

        foreach($rows as $row) {
            if($row['name'] == 'intro_profile_img') {
                $aBotIntro['aIntroProfile'][] = $row;
            } else if($row['name'] == 'intro_menu_name') {
                $aTemp = explode('|', $row['value']);
                $row['name'] = $aTemp[0];
                $row['url'] = $aTemp[1];
                $aBotIntro['aIntroMenu'][] = $row;
            } else if($row['name'] == 'intro_logo_url') {
                $aBotIntro['intro_logo_url_uid'] = $row['uid'];
                $aBotIntro[$row['name']] = $row['value'];
            } else {
                $aBotIntro[$row['name']] = $row['value'];
            }
        }
        return $aBotIntro;
    }

    // 챗로그 워드 군집분석용
    function getChatLogWord($data) {
        global $table;

        $aWord = array();

        $_wh = "vendor='".$data['vendor']."' and bot='".$data['bot']."' and printType='T' and char_length(content) >= 2";
        if($data['d_start'] && $data['d_end']) {
            $d_start = str_replace('-','', $data['d_start']);
            $d_end = str_replace('-','', $data['d_end']);
            $_wh .=" and (left(d_regis,8) between '".$d_start."' and '".$d_end."') ";
        }
        $_query = "Select uid, content From ".$table[$this->module.'chatLog']." Where ".$_wh." Group by content Order by uid ASC ";
        $rows = $this->getAssoc($_query);

        $word_str = "";
        foreach ($rows as $row){
            if($row['content']) $word_str .= $row['content'].' ';
        }
        $word_str = trim($word_str);
        if($word_str) {
            if(extension_loaded('mecab')) {
                $mecab = new \MeCab\Tagger(array('-d', $this->mecab_dic));
                $node = $mecab->parseToNode($word_str);
                foreach($node as $m) {
                    $feature = $m->getFeature();
                    $feature_arr = explode(',',$feature);
                    $PS = $feature_arr[0]; // 품사
                    if(strpos($PS,"BOS") === false && strpos($PS,"EOS") === false) {
                        if($PS=='NNG' || $PS=='NNP' || $PS=='NP' || $PS=='SL'){
                            $word = $m->getSurface();
                            if(strlen($word)>3){ // 한글 1글자 3byte
                                if(array_key_exists($word,$aWord)) $aWord[$word]++;
                                else $aWord[$word] = 1;
                            }
                        }
                    }
                }
            } else {
                $mecab_exe = "/usr/local/bin/mecab";
                $cmd = "echo '".$word_str."' | ".$mecab_exe." -d ".$this->mecab_dic;
                exec($cmd, $aResult, $return);
                if($retrun == 0 && count($aResult) > 0) {
                    foreach($aResult as $node) {
                        $m = explode("\t", $node);
                        if(strpos($m[0],'BOS') !== false || strpos($m[0],'EOS') !== false) continue;
                        $word = $m[0];
                        $aInfo = explode(",", $m[1]);
                        $PS = $aInfo[0];
                        if($PS=='NNG' || $PS=='NNP' || $PS=='NP' || $PS=='SL'){
                            if(strlen($word)>3){ // 한글 1글자 3byte
                                if(array_key_exists($word,$aWord)) $aWord[$word]++;
                                else $aWord[$word] = 1;
                            }
                        }
                    }
                }
            }
            arsort($aWord, SORT_NUMERIC);
        }
        return $aWord;
    }

    function getJsonEncodeClean($data) {
        array_walk_recursive($data, function(&$value, $key) {
            $value = addslashes($value);
        });
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    function getJsonDecodeClean($data) {
        $data = preg_replace('/\r\n|\r|\n/',' ', $data);
        $data = preg_replace('/([{,]+)(\s*)([^"]+?)\s*:/','$1"$3":',$data);
        $data = preg_replace('/(,)\s*}$/','}',$data);
        return json_decode($data, true);
    }

    function getBotContext($data) {
        global $table, $date;
        $vendor = $data['vendor'] ? $data['vendor'] : $this->vendor;
        $bot = $data['bot'] ? $data['bot'] : $this->botuid;
        $roomToken = $data['roomToken'] ? $data['roomToken'] : $this->roomToken;

        $_time = date('YmdHis', strtotime("-1 hours"));
        getDbDelete($table[$this->module.'context'], "d_regis < '".$_time."'");

        $_wh = "vendor='".$vendor."' and bot='".$bot."' and roomToken='".$roomToken."'";
        $R = getDbData($table[$this->module.'context'], $_wh, 'context');
        if($R['context']) {
            $this->context = getUnesc(json_decode(preg_replace('/\r\n|\r|\n/',' ', $R['context']), true));
        } else {
            $this->context = array();
        }
    }

    function setBotContext($data) {
        global $table, $date;
        $vendor = $data['vendor'] ? $data['vendor'] : $this->vendor;
        $bot = $data['bot'] ? $data['bot'] : $this->botuid;
        $roomToken = $data['roomToken'] ? $data['roomToken'] : $this->roomToken;

        $_time = date('YmdHis', strtotime("-1 hours"));
        getDbDelete($table[$this->module.'context'], "d_regis < '".$_time."'");

        $context = json_encode(getEsc($this->context), JSON_UNESCAPED_UNICODE);

        $_wh = "vendor='".$vendor."' and bot='".$bot."' and roomToken='".$roomToken."'";
        $is_context = getDbRows($table[$this->module.'context'], $_wh);
        $date['totime'] = $date['totime'] ? $date['totime'] : date('YmdHis');
        if($is_context) {
            getDbUpdate($table[$this->module.'context'], "context='".$context."', d_regis='".$date['totime']."'", $_wh);
        } else {
            $QKEY = "vendor, bot, roomToken, context, d_regis";
            $QVAL = "'$vendor','$bot','$roomToken', '$context', '".$date['totime']."'";
            getDbInsert($table[$this->module.'context'], $QKEY, $QVAL);
        }
    }

    // pesonlp 학습 ------------------------------------------------------------------
    function getTrainIntentPesoNLP($data) {
        global $g, $table;

        // corpus 파일과 model 파일 경로 설정
        $R = getDbData($table[$this->module.'bot'], "uid='".$data['bot']."'", 'mbruid');
        $mbruid = $R['mbruid'];

        $baseDir = $_SERVER['DOCUMENT_ROOT'].'/files/trainData/';
        $saveDir = $mbruid.'/'.$data['bot']; // bottalks에서는 mbruid로 디렉토리 설정
        $aSaveDir = explode('/', $saveDir);
        $tempDir = '';
        foreach($aSaveDir as $dir) {
            if($dir == '') continue;
            $tempDir .=$dir.'/';
            if (!is_dir($baseDir.$tempDir)){
                $oldmask = umask(0);
                mkdir($baseDir.$tempDir,0707);
                umask($oldmask);
            }
        }

        $aDir = dir($baseDir.$saveDir);
        while ($chFile = $aDir->read() ) {
            if ($chFile != "." && $chFile != "..") @unlink($baseDir.$saveDir."/".$chFile);
        }

        $corpus_file = $data['vendor']."_".$data['bot']."_corpus.txt";
        $model_file = $data['vendor']."_".$data['bot']."_model";

        // pesonlp 학습용 corpus 파일 생성
        $aTrainData = $this->getIntentTrainData($data);
        if(count($aTrainData) > 0) {
            shuffle($aTrainData);

            $corpus_data = "";

            foreach($aTrainData as $aData) {
                // 특수문자 제거
                $chContent = getMorphStrReplace(strtolower($aData[1]));
                if(!trim($chContent)) continue;

                $chSample = "";
                if($this->intentTokenType == 'morph') {
                    //형태소 분석 (불용어 제거)
                    $chSample = getRemoveStopWords(getMecabMorph($chContent, '|'));
                } else {
                    $chSample = getKoreanSplit($chContent);
                }

                $corpus_data .= "__label__".$aData[0]." ".$chSample."\n";
            }

            // corpus 파일 저장
            file_put_contents($baseDir.$saveDir."/".$corpus_file, $corpus_data);

            // pesonlp 학습
            $lr = $lr ? $lr : "1.0";
            $epoch = $epoch ? $epoch : "25";
            $wordNgrams = $wordNgrams ? $wordNgrams : 1;

            $cmd = $this->pesonlp." supervised -input ".$baseDir.$saveDir."/".$corpus_file." -output ".$baseDir.$saveDir."/".$model_file." -lr ".$lr." -epoch ".$epoch." -wordNgrams ".$wordNgrams; //-loss ns
            exec($cmd, $aResult, $return);
            if($return != 0) {
                $aDir = dir($baseDir.$saveDir);
                while ($chFile = $aDir->read() ) {
                    if ($chFile != "." && $chFile != "..") @unlink($baseDir.$saveDir."/".$chFile);
        		}
                return false;
            } else {
                return true;
            }

        } else {
            return true;
        }
    }

    function getHtmlFormList($row,$mod=''){
        global $table, $my;

        $bot = $row['bot'] ? $row['bot'] : $this->botuid;

        $aHtml = array();
        $aHtml['reserve']['reserve_request'] = "예약 신청";
        $aHtml['reserve']['reserve_search'] = "예약 조회";
        $aHtml['reserve']['reserve_modify'] = "예약 변경";
        $aHtml['reserve']['reserve_cancel'] = "예약 취소";

        $aHtml['jusobot']['jusobot_request'] = "주소봇";

        if($my['cgroup'] == 'gsitm') {
            // gsitm
            $aHtml['gsitm']['gsitm_request'] = "ITSM 요청폼";
            $aHtml['gsitm']['gsitm_services'] = "ITSM 요청현황조회";
            $aHtml['gsitm']['gsitm_approvals'] = "ITSM 결재문서함";
            $aHtml['gsitm']['gsitm_circulars'] = "ITSM 회람문서함";
            $aHtml['gsitm']['gsitm_notices'] = "ITSM 공지사항";
        }

        if($my['cgroup'] == 'kblife') {
            $aHtml['kblife']['kblife_join'] = "보험가입신청";
            $aHtml['kblife']['kblife_request'] = "보험가입상담신청";
        }

        $option = '';
        $result = $htmlData = array();
        foreach($aHtml as $key=>$data) {
            // 폼 종류가 on일 경우에만 적용
            $R = getDbData($table[$this->module.'botSettings'], "bot=".$bot." and name='use_".$key."'", "value");
            if($R['value'] == 'on' || ($my['cgroup'] && $my['cgroup'] == $key)) {
                foreach($data as $doc=>$doc_text) {
                    $htmlData[] = array("id"=>$doc,"name"=>$doc_text);
                    if(isset($row['varchar_val'])) $check_selected = $row['varchar_val']==$doc ? ' selected':'';
                    else $check_selected = '';
                    $option .='<option value="'.$doc.'"'.$check_selected.'>'.$doc_text.'</option>';
                }
            }
        }
        if($mod=='option') $result = $option;
        else $result['data'] = $htmlData;
        return $result;
    }

    // 예약 유형
    function getReserveCategory($upjong=''){
        $reserve_array = array();
        if($upjong == '병원') $reserve_array['hospital'] = "병원 예약";
        else if($upjong == '숙박') $reserve_array['hotel'] = "숙박 예약";
        else if($upjong == '학원') $reserve_array['academy'] = "상담 예약";
        else $reserve_array['normal'] = "일반 예약";

        return $reserve_array;
    }

    function getReserveAPI($data) {
        global $table;

        $result = array();
        if(isset($data['api_url']) && $data['api_url']) {
            $apiURL = $data['api_url'];
        } else {
            $_wh = "vendor=".$data['vendor']." and bot=".$data['bot']." and name like 'reserve_%'";
            $RCD = getDbArray($table[$this->module.'botSettings'], $_wh, 'name, value', 'uid', 'asc', '', 1);
            $aAPI = array();
            while ($R = db_fetch_array($RCD)) {
                $aAPI[$R['name']] = $R['value'];
            }

            if($aAPI['reserve_category'] == 'hospital' && $aAPI['reserve_manage'] == 'erpbottalks') {
                $apiURL = $aAPI['reserve_domainkey'] ? 'http://'.$aAPI['reserve_domainkey'].'.'.$this->getAPIUrl($aAPI['reserve_manage']) : '';

            } else if($aAPI['reserve_category'] == 'hotel' && $aAPI['reserve_manage'] == 'onda') {
                // 온다 예약일 경우 숙소 id, vendor id, 토큰값 확인
                if($data['api_path']) {
                    $apiURL = $this->getAPIUrl($aAPI['reserve_manage']).$data['api_path'];
                } else {
                    if($aAPI['reserve_onda_suburl']) {
                        $apiURL = $this->getAPIUrl($aAPI['reserve_manage']).'/be/properties/'.$aAPI['reserve_onda_suburl'].'/'.$data['end_point'];
                    }
                }
                if(isset($data['postParam']) && count($data['postParam']) > 0) {
                    $aHeader = array();
                    $aHeader[] = "Content-Type: application/json";
                }
            } else {
                $apiURL = $aAPI['reserve_api'];
            }
        }

        if(!$apiURL) {
            $result['result'] = 0;
        } else {
            $ch = curl_init();

            if(isset($data['getParam']) && count($data['getParam']) > 0) {
                $apiURL .="?".http_build_query($data['getParam']);
            }
            curl_setopt($ch, CURLOPT_URL, $apiURL);

            if(isset($data['headers']) && is_array($data['headers']) && count($data['headers']) > 0) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $data['headers']);
            } else if(isset($aHeader) && count($aHeader) > 0) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
            }

            if(($data['method'] == 'post' || $data['method'] == 'put') || (isset($data['postParam']) && count($data['postParam']) > 0)) {
                if($data['method'] == 'put') {
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                } else {
                    curl_setopt($ch, CURLOPT_POST, 1);
                }
                if(isset($data['contentType']) && $data['contentType'] == "formData") {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data['postParam']);
                } else {
                    if(isset($data['postParam']['_json']) && $data['postParam']['_json']) {
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $data['postParam']['_json']);
                    } else {
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data['postParam']));
                    }
                }
            }
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.75 Safari/537.36");
            $response = curl_exec($ch);
            $aResInfo = curl_getinfo($ch);
            curl_close($ch);

            if($aResInfo['http_code'] == 200 && $response) {
                $result = json_decode($response, true);
                if(json_last_error() != JSON_ERROR_NONE) {
                    $result = $response;
                }
            } else {
                $result['result'] = 0;
            }
        }
        return $result;
    }

    function getReserveRespond($data) {
        global $TMPL;

        $r = $data['row'];

        if($data['botData']['use_reserve'] != 'on') {
            $bot_msg .='죄송합니다. 예약이 불가능합니다.<br>전화 예약 부탁드립니다.';
            $result = $_data = array();
            $_data['text'] = trim($bot_msg);
            $_data['parse'] = false;
            $_data['api'] = $data['api'];
            $parseText = $this->getBotTextMsg($_data);
            $result['content'] = $parseText;
            $result['res_type'] = 'text';
            $result['next_status'] = array('action'=>'hangup');
            return $result;

        } else {
            // hform이 예약, form 타입이 reserve_request일 경우 날짜, 의사명 정보 획득 후 해당 API로 먼저 조회.
            if($r['varchar_val'] == 'reserve_request') {
                $bot_msg = $r_date = $r_time = '';

                foreach($data['entityData'] as $entity) {
                    if($entity[4] == 'S') {
                        if($entity[5] == '시스템-날짜' && $entity[6]) {
                            $r_date = $entity[6]; continue;
                        }
                        if($entity[5] == '시스템-시각' && ($entity[6] && $entity[6] != '00:00:00')) {
                            $r_time = substr($entity[6], 0, strrpos($entity[6], ':')); continue;
                        }
                        if($entity[5] == '시스템-요일' && $entity[6]) {
                            $r_week = $entity[6]; continue;
                        }
                    }
                }

                if(!$r_date) {
                    $bot_msg .='예약을 진행하시겠습니까?';
                } else {
                    $bot_msg .='예약희망일 : '.$r_date.($r_week ? '('.$r_week.')' : '').'<br>';
                    $bot_msg .=($r_time ? '예약희망시간 : '.$r_time.'<br><br>' : '');
                    $bot_msg .='예약을 진행하시겠습니까?';
                }

                $b_reserve = true;
                $TMPL['category'] = $data['category'];
                $TMPL['last_chat'] = $this->last_chat;
                $TMPL['action'] = $data['docType'];
                $TMPL['sys_date'] = $r_date ? $r_date : '';
                if($data['category'] == 'hotel' && $r_date) {
                    $TMPL['sys_nights'] = 1;
                }
                $TMPL['sys_week'] = $r_week ? $r_week : '';
                $TMPL['sys_time'] = $r_time ? $r_time : '';
                $TMPL['sys_use'] = $b_reserve;
                $TMPL['sys_disp'] = $b_reserve ? '' : 'none';
                $TMPL['response'] = '<span>'.nl2br($bot_msg).'</span>';

                //callbot--
                if($data['botData']['bottype'] == 'call' && $data['api']) {
                    $sys_fast = preg_match("/빠른|빨리|아무|언제/ui", $data['clean_input']) ? true : false;

                    $msg = '예약 진행을 도와드리겠습니다. 음성으로 하시겠어요? 보이는 ARS로 하시겠어요?';
                    $next_status = array('action'=>'recognize');

                    $r_data = array();
                    $r_data['form'] = $data['formType'];
                    $r_data['category'] = $data['category'];
                    $r_data['last_chat'] = $this->last_chat;
                    $r_data['action'] = $data['docType'];
                    $r_data['step'] = 'start';
                    $r_data['sys_date'] = $TMPL['sys_date'];
                    $r_data['sys_week'] = $TMPL['sys_week'];
                    $r_data['sys_time'] = $TMPL['sys_time'];
                    $r_data['sys_fast'] = $sys_fast;
                    $r_data['content'] = $msg;
                    $r_data['next_status'] = $next_status;
                    $r_data['uphone'] = $this->fromPhone ? $this->fromPhone : '';

                    $content = array('res_type'=>'text', 'content'=>$msg, 'next_status'=>$next_status, 'r_data'=>$r_data);
                } else {
                    $skinFile = $data['category'] == 'hotel' ? $data['formType'].'_'.$data['category'].'_'.$data['docType'] : $data['formType'].'_'.$data['docType'];
                    $skin = new skin($skinFile);
                    $content = $skin->make('lib');
                }
                return $content;
            }

            if($r['varchar_val'] == 'reserve_search' || $r['varchar_val'] == 'reserve_modify' || $r['varchar_val'] == 'reserve_cancel') {
                $TMPL['last_chat'] = $this->last_chat;
                $TMPL['action'] = str_replace('reserve_', '', $r['varchar_val']);

                //callbot--
                if($data['botData']['bottype'] == 'call' && $data['api']) {
                    $msg = '예약 진행을 도와드리겠습니다. 음성으로 하시겠어요? 보이는 ARS로 하시겠어요?';
                    $next_status = array('action'=>'recognize');

                    $r_data = array();
                    $r_data['form'] = $data['formType'];
                    $r_data['category'] = $data['category'];
                    $r_data['last_chat'] = $this->last_chat;
                    $r_data['action'] = $TMPL['action'];
                    $r_data['step'] = 'start';
                    $r_data['content'] = $msg;
                    $r_data['next_status'] = $next_status;
                    $r_data['uphone'] = $this->fromPhone ? $this->fromPhone : '';

                    $content = array('res_type'=>'text', 'content'=>$msg, 'next_status'=>$next_status, 'r_data'=>$r_data);
                } else {
                    $skinFile = $data['category'] == 'hotel' ? 'reserve_hotel_search' : 'reserve_search';
                    $skin = new skin($skinFile);
                    $content = $skin->make('lib');
                }
                return $content;
            }
        }
    }

    function getJusobotRespond($data) {
        global $g, $TMPL;

        $r = $data['row'];

        $bot_msg .='주소 검색 및 등록을 진행하시겠습니까?';
        $TMPL['category'] = $data['category'];
        $TMPL['last_chat'] = $this->last_chat;
        $TMPL['action'] = $data['docType'];
        $TMPL['response'] = '<span>'.nl2br($bot_msg).'</span>';

        //callbot--
        if($data['botData']['bottype'] == 'call' && $data['api']) {
            $data['accessToken'] = $this->accessToken;
            $data['user_utt'] = 'init';

            require_once $g['dir_module'].'lib/addrBot/client.php'; // addrBot 패키지
            $addrBot = new Peso\addrbot\Client();
            $addr_result = $addrBot->processAddrBot($data);

            $msg = $addr_result['response'];
            $next_status = array('action'=>'recognize');

            $r_data = array();
            $r_data['form'] = $data['formType'];
            $r_data['category'] = $data['category'];
            $r_data['last_chat'] = $this->last_chat;
            $r_data['action'] = $data['docType'];
            $r_data['step'] = 'start';
            $r_data['content'] = $msg;
            $r_data['next_status'] = $next_status;
            $content = array('res_type'=>'text', 'content'=>$msg, 'next_status'=>$next_status, 'r_data'=>$r_data);
        } else {
            $skinFile = $data['formType'].'_'.$data['docType'];
            $skin = new skin($skinFile);
            $content = $skin->make('lib');
        }
        return $content;
    }

    // gsitm
    function getHTMLFormRespond($data) {
        global $g, $table, $TMPL;

        $r = $data['row'];

        $TMPL['category'] = $data['category'];
        $TMPL['last_chat'] = $this->last_chat;
        $TMPL['action'] = $data['docType'];

        // gsitm
        if($data['formType'] == "gsitm") {
            if(isset($data['objGSITM']) && $data['objGSITM']) {
                $objGSITM = $data['objGSITM'];
            } else {
                include_once $g['dir_module'] . "includes/gsitm.class.php";
                $objGSITM = new GSITM($this);
            }

            // 요청 유형 선택
            //unset($_SESSION['aGSITMCodes']);
            if(!isset($_SESSION['aGSITMCodes'])) {
                $objGSITM->getGSITMTypeCode();
            }

            // intent명에 따라 응답 분기
            if($data['intentName'] == '서비스요청현황조회') {
                $data['gsitm_form_start'] = true;
                $data['gsitm_listtype'] = 'service';
                $bot_msg = $objGSITM->getGSITMServiceList($data);
            } else if($data['intentName'] == '결재문서함') {
                $data['gsitm_form_start'] = true;
                $data['gsitm_listtype'] = 'approval';
                $bot_msg = $objGSITM->getGSITMServiceList($data);
            } else if($data['intentName'] == '회람문서함') {
                $data['gsitm_form_start'] = true;
                $data['gsitm_listtype'] = 'circular';
                $bot_msg = $objGSITM->getGSITMServiceList($data);
            } else if($data['intentName'] == '공지사항') {
                $data['gsitm_form_start'] = true;
                $data['gsitm_listtype'] = 'notice';
                $bot_msg = $objGSITM->getGSITMServiceList($data);
            } else {
                $bot_msg = $objGSITM->getGSITMFormRespond($data);
            }
        }

        // kblife
        if($data['formType'] == "kblife") {
            //$bot_msg .='안녕하세요. KB 라이프입니다. 지금부터 보험 가입을 위한 녹음을 시작하겠습니다. 통화내용은 재청취 가능합니다. 녹음에 동의하십니까?';
            //$TMPL['response'] = '<span>'.nl2br($bot_msg).'</span>';
        }

        //callbot--
        if($data['botData']['bottype'] == 'call' && $data['api']) {
            $next_status = array('action'=>'recognize');

            $r_data = array();
            $r_data['form'] = $data['formType'];
            $r_data['category'] = $data['category'];
            $r_data['last_chat'] = $this->last_chat;
            $r_data['action'] = $data['docType'];
            $r_data['step'] = 'start';
            $r_data['content'] = $bot_msg;
            $r_data['next_status'] = $next_status;
            $r_data['uphone'] = $this->fromPhone ? $this->fromPhone : '';

            $content = array('res_type'=>'text', 'content'=>$bot_msg, 'next_status'=>$next_status, 'r_data'=>$r_data);
        } else {
            $skinFile = $data['category'] ? $data['formType'].'_'.$data['category'].'_'.$data['docType'] : $data['formType'].'_'.$data['docType'];
            $skin = new skin($skinFile);
            $content = $skin->make('lib');
        }
        return $content;
    }

    // shop api -------------------------------------------------------------------------------------------
    function getShopAPIRefreshToken($data) {
        $cbotapi = new cbotShopOauthApi($data['params']);
        $tokenTime =  strtotime($data['params']['access_token_expire']);
        $nowTime = time();
        if($tokenTime < $nowTime) {
            $resultToken = $cbotapi->getRefreshToken();
            if($resultToken->access_token && $resultToken->refresh_token) {
                $_data = array();
                $_data['nameArray'] = array();
                $_data['nameArray']['shopapi_access_token'] = $resultToken->access_token;
                $_data['nameArray']['shopapi_access_token_expire'] = $resultToken->expires_at;
                $_data['nameArray']['shopapi_refresh_token'] = $resultToken->refresh_token;
                $_data['nameArray']['shopapi_refresh_token_expire'] = $resultToken->refresh_token_expires_at;

                $_data['data'] = array();
                $_data['data']['vendor'] = $data['vendor'];
                $_data['data']['bot'] = $data['bot'];
                $this->updateBotSettings($_data);
                return $resultToken->access_token;
            } else {
                return false;
            }
        } else {
            return $data['params']['access_token'];
        }
    }

    function getShopAPIRespond($data) {
        global $g, $table, $TMPL;

        $apiUsable = true;
        $result = array();
        $aAPINode = array('상품문의'=>'goods', '주문내역확인'=>'order');
        if(!array_key_exists($data['nodeName'], $aAPINode)) return false;
        $apiMode = $aAPINode[$data['nodeName']];
        $data['shopapi_reqType'] = $apiMode;

        $_wh = "vendor=".$data['vendor']." and bot=".$data['bot']." and (name = 'use_shopapi' or name like 'shopapi_%')";
        $RCD = getDbArray($table[$this->module.'botSettings'], $_wh, 'name, value', 'uid', 'asc', '', 1);
        $aAPI = array();
        while ($R = db_fetch_array($RCD)) {
            $aAPI[$R['name']] = $R['value'];
        }

        if($aAPI['use_shopapi'] != 'on') $apiUsable = false;

        // 카페24
        if($aAPI['shopapi_vendor'] == 'cafe24') {
            if(!$aAPI['shopapi_mall_id'] || !$aAPI['shopapi_access_token'] || !$aAPI['shopapi_access_token_expire'] || !$aAPI['shopapi_refresh_token']) $apiUsable = false;

            if($apiUsable) {
                // access_token 체크
                include_once $g['path_root'].'/shopAPI/class/cbotShopApi.oauth.php';

                $params = array();
                $params['client_id'] = $this->shopApiVendor['cafe24']['client_id'];
                $params['client_secret'] = $this->shopApiVendor['cafe24']['client_secret'];
                $params['mall_id'] = $aAPI['shopapi_mall_id'];
                $params['mall_domain'] = $aAPI['shopapi_domain'];
                $params['access_token'] = $aAPI['shopapi_access_token'];
                $params['access_token_expire'] = $aAPI['shopapi_access_token_expire'];
                $params['refresh_token'] = $aAPI['shopapi_refresh_token'];
                $data['params'] = $params;
                $accessToken = $this->getShopAPIRefreshToken($data);
                // 변경된 accessToken 적용
                if(!$accessToken) {
                    $apiUsable = false;
                } else {
                    $params['access_token'] = $aAPI['shopapi_access_token'] = $accessToken;

                    // shop class 호출
                    include_once $g['path_root'].'/shopAPI/class/cbotShopApi.cafe24.php';
                    $oShopAPI = new cbotShopApiCafe24($params);
                }
            }

        // 고도몰
        } else if($aAPI['shopapi_vendor'] == 'godo') {
            if(!$aAPI['shopapi_mall_type'] || !$aAPI['shopapi_client_key']) $apiUsable = false;

            if($apiUsable) {
                $params = array();
                $params['client_id'] = $this->shopApiVendor['godo']['client_id'];
                $params['client_key'] = $aAPI['shopapi_client_key'];
                $params['mall_type'] = $aAPI['shopapi_mall_type'];
                $params['mall_domain'] = $aAPI['shopapi_domain'];

                // shop class 호출
                include_once $g['path_root'].'/shopAPI/class/cbotShopApi.godo.php';
                $oShopAPI = new cbotShopApiGodo($params);
            }
        }

        // 상품조회 ------------------------------------------------
        $data['shopapi_vendor'] = $aAPI['shopapi_vendor'];

        if($apiMode == 'goods') {
            $apiResponse = array();

            $aKeyword = array('new'=>'신상품', 'recommend'=>'추천상품', 'favorate'=>'인기상품');
            $searchMode = '';
            foreach ($data['entityData'] as $entitySet) {
                if(in_array($entitySet[3], $aKeyword)) {
                    $searchMode = array_search($entitySet[3], $aKeyword); break;
                }
            }

            if(!$apiUsable) {
                // shop api 설정 없을 경우
                $apiResponse['resType'] = 'text';
                $apiResponse['content'] = '죄송합니다. 상품 검색이 원활하지 않습니다.';
            } else {
                $params = array();
                $params['gname'] = '';
                $apiResult = $oShopAPI->getGoodsList($params);

                if($apiResult['result'] == 'succ') {
                    $data['shopapi_searchMode'] = $searchMode;
                    $data['shopapi_data'] = $apiResult['data'];
                    $apiResponse = $this->getShopAPIList($data);
                } else {
                    $apiResponse['resType'] = 'text';
                    $apiResponse['content'] = $apiResult['message'] ? $apiResult['message'] : '죄송합니다. 상품 검색이 원활하지 않습니다.';
                }
            }
        }

        // 주문조회 ------------------------------------------------
        if($apiMode == 'order') {
            $apiResponse = array();

            if($apiUsable) {
                // 주문자 휴대폰 번호로 확인
                if(!$data['last_botQ'] || !isset($this->context['shopapi_order_mobile'])) return false;
                $this->context = array();

                $cellPhone = '';
                foreach ($data['entityData'] as $entitySet) {
                    if($entitySet[4] == 'S' && $entitySet[3] == '전화번호') {
                        $cellPhone = $entitySet[6]; break;
                    }
                }
                if(!$cellPhone) $apiUsable = false;
            }

            if(!$apiUsable) {
                // shop api 설정 없을 경우
                $apiResponse['resType'] = 'text';
                $apiResponse['content'] = '주문내역은 마이페이지 > 주문내역에서 확인이 가능합니다.';
            } else {
                $params = array();
                $params['buyer_cellphone'] = $cellPhone;
                $apiResult = $oShopAPI->getOrderList($params);

                if($apiResult['result'] == 'succ') {
                    if(count($apiResult['data']) > 0) {
                        $data['shopapi_data'] = $apiResult['data'];
                        $apiResponse = $this->getShopAPIList($data);
                    } else {
                        $apiResponse['resType'] = 'text';
                        $apiResponse['content'] = '최근 한달간 주문하신 상품이 없습니다.';
                    }
                } else {
                    $apiResponse['resType'] = 'text';
                    $apiResponse['content'] = $apiResult['message'] ? $apiResult['message'] : '죄송합니다. 주문 조회가 원활하지 않습니다.';
                }
            }
        }

        if(count($apiResponse) > 0) {
            if($apiResponse['resType']=='text'){
                $_data = array();
                $_data['text'] = $apiResponse['content'];
                $_data['parse'] = false; // 텍스트내 데이타 파싱함
                $_data['api'] = $data['api'];
                $response = $this->getBotTextMsg($_data);
                $result[]= array('text',$response);
            } else {
                $resultMsg = $apiMode == 'goods' ? '상품 조회 결과입니다' : '최근 한달간 주문내역입니다.';
                $_data = array();
                $_data['text'] = $resultMsg;
                $_data['parse'] = false; // 텍스트내 데이타 파싱함
                $_data['api'] = $data['api'];
                $response = $this->getBotTextMsg($_data);
                $result[]= array('text',$response);

                $is_mobile = $this->is_mobile();
                $TMPL['card_freemode'] = $is_mobile ? 'true' : 'false';
                $TMPL['card_loop'] = $is_mobile ? 'true' : 'false';
                $TMPL['card_button'] = $is_mobile ? 'false' : 'true';
                $TMPL['card_center'] = 'false';
                $TMPL['slidePerView'] = '1.5';
                $TMPL[$apiResponse['resType'].'_rows'] = $apiResponse['rows'];
                $skin = new skin('chat/'.$apiResponse['resType'].'_list');
                $list = $skin->make();
                if($data['api']) $result[]= array($apiResponse['resType'], $apiResponse['resArray']);
                else $result[]= array($apiResponse['resType'], $list);
            }

            // 챗봇 답변 Log 저장
            $botChat = array();
            $botChat['content'] = $result;
            $botChat['unknown'] = $data['msg_type'] == 'unknown' ? true : false;
            $botChat['last_chat'] = $data['last_chat']?$data['last_chat']:$this->last_chat; // 사용자 chat uid
            $this->addBotChatLog($botChat);
        }
        return $result;
    }

    function getShopAPIList($data) {
        global $g, $my, $TMPL;
        $tempDataDir = $g['path_tmp'].'cache';

        // 1일 지난 파일 삭제
        $files = glob($tempDataDir.'/*');
        foreach($files as $file) {
            if((time()-filemtime($file)) >= (60*60*24*1)) @unlink($file);
        }

        $rows='';
        $result = $resArray = array();

        $shopData = $data['shopapi_data'];

        if($data['shopapi_reqType'] == 'goods') {
            // cafe24일 경우 인기상품은 추천상품 랜덤으로 대체
            $_searchMode = $data['shopapi_vendor'] == 'cafe24' && $data['shopapi_searchMode'] == 'favorate' ? 'recommend' : $data['shopapi_searchMode'];
        }

        if($shopData[0]['img']) {
            $aParseUrl = parse_url($shopData[0]['img']);
            $img_scheme = $aParseUrl['scheme'];
        }

        $_tempList = array();
        foreach($shopData as $idx=>$list) {
            // https가 아닌 이미지 주소의 경우 챗봇 서버에 다운
            if($list['img'] && $g['https_on'] && $img_scheme != 'https') {
                $aImg = explode('/', $list['img']);
                $_file = $tempDataDir.'/'.($_SESSION['mbr_uid'] ? $_SESSION['mbr_uid'] : $my['uid']).'_'.$this->botuid.'_'.$aImg[(count($aImg)-1)];
                if(file_exists($_file)) {
                    $_tempImg = $_file;
                } else {
                    $_tempImg = $this->getRemoteImage($list['img'], $_file);
                }
                $shopData[$idx]['img'] = $list['img'] = $_tempImg;
            }
            if($list[$_searchMode]) $_tempList[] = $list;
        }
        if(count($_tempList) == 0) {
            if($_searchMode) shuffle($shopData);
            $_tempList = $shopData;
        }

        // 10개까지 출력
        $TMPL['addClass'] = 'goods';
        foreach($_tempList as $idx=>$R) {
            if($idx >= 10) break;
            if($data['shopapi_reqType'] == 'goods') {
                $summary = "<span class='dt_head'>판매가</span><span>".number_format($R['price'])."원</span>";
            } else {
                $summary = "<span class='dt_head'>".$R['order_status']."</span>".($R['ship_url'] ? "<span class='btn_ship' onclick=\"window.open('".$R['ship_url']."')\">배송 조회</span>" : "");
            }

            $R['resType'] = 'card';
            $R['link1'] = $R['link'];
            $R['title'] = $R['gname'];
            $R['summary'] = $summary;
            $R['img_url'] = ltrim($R['img'], '.');
            if($data['api']) {
                $R['api'] = $data['api'];
                $resArray[] = $this->getDialogMultiItemRow($R);
            } else {
                $rows.= $this->getDialogMultiItemRow($R);
            }
        }
        $result['resType'] = 'card';
        $result['rows'] = $rows;
        $result['resArray'] = $resArray;
        return $result;
    }

    function getRemoteImage($chURL, $chToFile) {
		global $g;

		$fp = fopen($chToFile, 'w');
		$ch = curl_init($chURL);
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$data = curl_exec($ch);
		curl_close($ch);
		fclose($fp);
		return $chToFile;
	}

	function getSysCheckup($data) {
        global $table;

        $dNowTime = date('Y-m-d H:i:s');
        $sysCheckMsg = '';

        $_wh = "vendor=".$data['vendor']." and bot=".$data['bot']." and (name = 'use_syscheckup' or name like 'syscheckup_%')";
        $RCD = getDbArray($table[$this->module.'botSettings'], $_wh, 'name, value', 'uid', 'asc', '', 1);
        $aSysCheck = array();
        while ($R = db_fetch_array($RCD)) $aSysCheck[$R['name']] = $R['value'];

        if($aSysCheck['use_syscheckup'] == 'on' && $aSysCheck['syscheckup_start'] && $aSysCheck['syscheckup_end'] && $aSysCheck['syscheckup_msg']) {
            if($dNowTime >= $aSysCheck['syscheckup_start'].':00' && $dNowTime <= $aSysCheck['syscheckup_end'].':00') {
                $sysCheckMsg = $aSysCheck['syscheckup_msg'];
            }
        }
        return $sysCheckMsg;
    }

    function getSysBargein($data) {
        global $table;

        $R = getDbData($table[$this->module.'bot'], "uid='".$data['bot']."' and vendor='".$data['vendor']."'", "*");
        $bottype = $R['bottype'];

        $_wh = "vendor=".$data['vendor']." and bot=".$data['bot']." and (name = 'interface' or name = 'use_bargein')";
        $RCD = getDbArray($table[$this->module.'botSettings'], $_wh, 'name, value', 'uid', 'asc', '', 1);
        $aSetting = array();
        while ($R = db_fetch_array($RCD)) $aSetting[$R['name']] = $R['value'];
        $bargein = $bottype == 'call' && $aSetting['interface'] == 'voice' && $aSetting['use_bargein'] == 'on' ? true : false;
        $ctiaction = $bottype == 'call' ? true : false;

        $result = array('bargein'=>$bargein, 'ctiaction'=>$ctiaction);
        return $result;
    }

    function setBotResCounter($data) {
        return true;
        global $table, $g, $_db_traffic, $DB_CONNECT;

        $channel = $data['channel'] ? $data['channel'] : $this->channel;
        $roomToken = $data['roomToken'] ? $data['roomToken'] : $this->roomToken;
        $last_chat = $data['last_chat'] ? $data['last_chat'] : $this->last_chat;
        $device = $this->is_mobile() ? 'M' : 'D';
        $d_date = substr($data['d_regis'],0,8);
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];

        $B = getDbData($table[$this->module.'bot'], "uid='".$this->botuid."' and vendor='".$this->vendor."'", "*");

        // 프론트 사이트에서 등록된 user_uid가 있고 paid가 1일 경우만
        if($B['user_uid'] && $B['paid']) {
            if((isset($data['same_chat']) && $data['same_chat']) || $data['ctime'] || $data['sms'] || $data['lms']) {
                $is_res = 1;
            } else {
                $_wh = "vendor='".$this->vendor."' and bot='".$this->botuid."' and roomToken='".$roomToken."' and chat='".$last_chat."'";
                $is_res = getDbRows($table[$this->module.'botChatLog'], $_wh);
            }

            if($is_res == 1) {
                // 집계용 별도 DB일 경우
                if(isset($g['sys_rcount']) && $g['sys_rcount']) {
                    $_DB_CONNECT_ = $DB_CONNECT;
                    $DB_CONNECT = isConnectedToDB($_db_traffic);
                }

                $_wh = "mbruid='".$B['mbruid']."' and botid='".$B['id']."' and roomToken='".$roomToken."' ";
                // 콜봇일 경우 roomToken 기준으로
                if($channel != "call") {
                    $_wh .="and d_date='".$d_date."'";
                }

                $aCounter = getDbData($table[$this->module.'rescounter'], $_wh, "uid, bottype, unknown");
                if($aCounter['uid']) {
                    $query = "Update ".$table[$this->module.'rescounter']." Set ";
                    if(isset($data['ctime']) && $data['ctime']) {
                        // 콜봇일 경우 계산된 rcount에서 unknown 수 제외
                        $data['rcount'] = ($data['rcount'] - $aCounter['unknown']);

                        $query .="rcount='".$data['rcount']."', ctime='".$data['ctime']."', cstarttime='".$data['cstarttime']."', cendtime='".$data['cendtime']."', d_date='".$d_date."' ";
                    } else if(isset($data['sms']) && $data['sms']) {
                        $query .="sms=sms+1 ";
                    } else if(isset($data['lms']) && $data['lms']) {
                        $query .="lms=lms+1 ";
                    } else if(isset($data['unknown']) && $data['unknown']) {
                        $query .="unknown=unknown+1 ";
                    } else {
                        $query .="rcount=rcount+1 ";
                    }
                    $query .="Where uid='".$aCounter['uid']."' ";
                } else {
                    $query = "Insert into ".$table[$this->module.'rescounter']." ( ";
                    $query .="  mbruid, user_uid, botid, roomToken, bottype, channel, rcount, unknown, ctime, cstarttime, cendtime, sms, lms, ip, device, d_date ";
                    $query .=") values ( ";
                    $query .="  '".$B['mbruid']."', '".$B['user_uid']."', '".$B['id']."', '".$roomToken."', ";
                    $query .="  '".$B['bottype']."', '".$channel."', '1', '0', '0', '', '', '0', '0', '".$ip."', '".$device."', '".$d_date."' ";
                    $query .=") ";
                }
                db_query($query, $DB_CONNECT);

                if(isset($g['sys_rcount']) && $g['sys_rcount']) {
                    $DB_CONNECT = $_DB_CONNECT_;
                }
            }
        }
    }

    function getHTMLLoginFormRespond($data) {
        global $TMPL,$g,$table;

        $result = array();
        $botData = $this->getBotDataFromId($this->botid);
        $TMPL['bot_avatar_src'] = $botData['bot_avatar_src'];
        $TMPL['bot_name'] = $botData['bot_name'];
        $TMPL['date'] = (date('a') == 'am' ? '오전 ':'오후 ').date('g').':'.date('i');
        $TMPL['category_type'] = "";
        $TMPL['category'] = "login";
        $TMPL['hform_type'] = "login";
        $TMPL['last_chat'] = $this->last_chat;
        $TMPL['action'] = "login";

        $skinFile = "user_login";
        $skin = new skin($skinFile);
        $content = $skin->make('lib');

        $_result = array();
        $_result[] = "hform";
        $_result[] = $content;
        $result[] = $_result;
        return $result;
    }

    // 20230712 aramjo chatgpt
    function getChatgpt($data){
        $data['no_setting'] = true;
        $gpt = new Chatgpt('azure');
        $result = $gpt->chat($data);
        return $result['content']." 더 궁금하신 사항이 있으신가요?";
    }
}
?>