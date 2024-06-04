<?php
namespace Peso\addrbot;

class Client{

    const API_URL = 'http://jusoupdate.bottalks.ai:8080/app/search/addrSearchApi.do';
    const method = 'post';

    private $client;
    private $apiKey;
    private $nlp;
    private $dialog;
    private $user_utt;
    private $pp_input; // user_utt > 전처리된 것
    private $context;

    public function __construct()
    {
        global $g;

        require_once $g['dir_module'].'lib/guzzel/autoloader.php'; // restfull api library
        require_once $g['dir_module'].'lib/addrBot/nlp.php'; // nlp 관련 처리
        require_once $g['dir_module'].'lib/addrBot/dialog.php'; // dialog 관련 처리

       

        $this->apiKey = $apiKey; 
        $this->client = new \GuzzleHttp\Client();
        $this->nlp = new \Peso\addrbot\Nlp();
        $this->dialog = new \Peso\addrbot\Dialog();
        
    }

     /*
     ** ##########  주소솔루션 서버로부터 API 로 결과값 가져오는 함수 ############# 
     *
     * @param     $keyword // 검색어
     * @param int $page // 현재 페이지
     * @param int $pageSize // 페이지당 row 갯수 
     * @param     $resultType // json / xml
     *
     * @return Response // json 타입 string
     * @throws UnexpectedResponseException 
       
    */
    public function getSearchAddrResult($keyword){
        
        $params = [
            //'confmKey' => $this->apiKey,
            'countPerPage' => 100,
            'currentPage' => 1,
            'keyword' => $keyword,
            'resultType' => 'json',
        ];

        $query = http_build_query($params);

        $response = $this->client->request(self::method,self::API_URL, ["query"=>$query]);
        //$result = json_decode($RQ->getBody(),true);
        $result = [
            "header" => $response->getHeaders(),
            "statusCode" => $response->getStatusCode(),
            "body" => $response->getBody()
        ];

        $search_body = json_decode($result['body'],true);
        $search_results = $search_body['results'];
        $result['juso_data'] = $search_results['juso'];
        $result['totalCount'] = $search_results['common']['totalCount'];
        $result['currentPage'] = $search_results['common']['currentPage'];
        $result['errorCode'] = $search_results['common']['errorCode'];
        $result['errorMessage'] = $search_results['common']['errorMessage'];


        return $result;
    }

    // context 값 얻기
    private function get_contextVal($key){
        $context = $this->context;

        return $context[$key];
    }

    // 사용자 발화에 컨텍스트 주소값 추가 : 기존 턴에 체크된 주소값 적용하기 위함
    private function addContextToUserUtt(){
        $juso_context = [
            'last_siNm', // 시/도 
            'last_sggNm', // 시/군/구
            'last_emdNm', // 읍변동
            'last_jiben', // 지번
            'last_rn', // 도로명 
            'last_buld', // 건물번호
        ]; 

        $user_utt = $this->pp_input; // 전처리된 발화
        foreach ($juso_context as $val) {
            $ctx_val = $this->get_contextVal($val);

            if($ctx_val) $user_utt.= ' '.$ctx_val;
        }

        $result = [];
           
        $utt_arr = preg_split('/\s/',$user_utt); // 컨텍스트 추가된 발화를 공백으로 분리
        $unique_utt = array_unique($utt_arr); // 중복값 제거 
        
        $addContextUtt =''; 
        foreach ($unique_utt as $utt) {
            $addContextUtt.=' '.$utt;
        } 

        
        $result['user_utt'] = $user_utt;
        $result['utt_arr'] = $utt_arr;
        $result['addContextUtt'] = $addContextUtt;  
        
        return $result;
    }

    // 주소검색 결과 기준 talk
    private function getSearchTalk(){
        // 1. 발화 전처리
        $CIR = $this->nlp->getClean($this->user_utt);
        $pp_input = $CIR['pp_input'];// 전처리된 발화 
        $this->pp_input = $pp_input; 

        // 2. 컨텍스트 > 주소 데이터 > 발화에 추가 
        $addr_default = $this->get_contextVal('addr_default');
        if(count($addr_default)<1){ // 기본주소 못 찾은 경우
           $ACU = $this->addContextToUserUtt();
           $user_utt = $ACU['addContextUtt'];    
        }else{
            $user_utt = $pp_input;
        }
          

        // 3. 전처리 + 컨텍스트 추가 발화 --> 주소서버에 검색 요청
        $addrServer_response = $this->getSearchAddrResult($user_utt);// 주소서버 response
        $statusCode = $addrServer_response['statusCode']; // 주소서버 response > statusCode
        
        
        $result = [];
        if($statusCode =='200'){ // 주소서버와 정상 통신

            // 주소검색 결과기반 bot 발화 규정
            $addrServer_response['user_utt'] = $user_utt; // 전처리+컨텍스트 추가된 발화 적용
            $addrServer_response['context'] = $this->context;
            $result = $this->dialog->getBotResponse($addrServer_response);

            // debug 용
            //$result['addrServer_response'] = $addrServer_response;
        
        }else{ // 주소서버와 통신 오류
            $data =[]; 
            $data['user_utt'] = 'retry';
            $result = $this->dialog->getBotResponse($data);
        } 

        $result['statusCode'] = $statusCode;
        $result['user_utt_origin'] = $this->user_utt; // 최초 발화
        $result['user_utt_pp'] = $pp_input; // 전처리된 발화
        $result['CIR'] = $CIR;
        $result['ACU'] = $ACU; // add context to utt (사용자 발화에 컨텍스트값 추가 결과)

        return $result;
    }

    /*
       외부에서 입력된 input --> 리턴 발화 리턴
       * @data : 파라미터 배열
        > user_utt :사용자 발화
        > access_token : 대화 토큰 
        > room_token : 콜 토큰 
        > context : 컨텍스트 값 array(DB 저장값) 
    **/  

    public function getAddrTalk($data){
        $this->user_utt = $data['user_utt']; // 사용자 발화
        $this->context = $data['context']; // 컨텍스트 
        $addr_default = $this->get_contextVal('addr_default');
        $goal_success = $this->get_contextVal('goal_success');// 주소찾기 완료 여부 
      
        if( $this->user_utt =='init' || 
            (count($addr_default)>0 && !$goal_success)
        ){ 
           // 최초 시작 or 기본주소 완성된 경우 
           
            $result = $this->dialog->getBotResponse($data);

        }else{ // 주소검색 필요한 경우            
           
            $result = $this->getSearchTalk(); 
        }

        return $result; 
    }
    
    // 주소봇 발화 처리
    public function processAddrBot($data){
        global $g;
        
        // 저장된 컨텍스트 추출   
        $data['room_token'] = $data['roomToken'];
        $data['access_token'] = $data['accessToken'];//'access_token';
        $data['act'] = 'get';    
        $getAddrData = $this->controlAddrBotData($data);

        $params = [
           "context" =>$getAddrData['context'], // 컨텍스트 값 array (DB 저장값)
           "user_utt" => $data['user_utt'] // 사용자 발화
        ];
         
        $addr_result = $this->getAddrTalk($params);

        // 컨텍스트 추가 or 업데이트 
        $data['act'] = 'update';
        $data['context'] = $addr_result['context'];
        $this->controlAddrBotData($data);

        $result =[];
        $result['addr_result'] = $addr_result;
        $result['res_type'] ='text';
        $result['response'] = $addr_result['bot_utt']; 
        $result['goal_success'] = $data['context']['goal_success'];       

        return $result;        
    }
    
    // 주소봇 데이터 컨트롤
    public function controlAddrBotData($data){
        $tbl ='rb_chatbot_addrBot';
        
        $bot = $data['bot'];
        $access_token = $data['access_token'];
        $room_token = $data['room_token'];
        $act = $data['act'];
        
        $_wh = "bot='".$bot."' and access_token='".$access_token."' and room_token='".$room_token."'";
        $R = getDbData($tbl,$_wh,'uid');

        if($act =='update'){ // 추가
            // context 값 json 인코딩
            $context = json_encode($data['context'],JSON_UNESCAPED_UNICODE);
      
            if($R['uid']){
                $up_qry = "context='$context'";
                getDbUpdate($tbl,$up_qry,'uid='.$R['uid']);
            }else{
                $QKEY = "bot,access_token,room_token,context";
                $QVAL ="'$bot','$access_token','$room_token','$context'";
                getDbInsert($tbl,$QKEY,$QVAL);
            }
        }else if($act =='get'){
            $R = getDbData($tbl,$_wh,'*');
            
            $result = $data;
            $result['context'] = json_decode($R['context'],true);
        }
        return $result;        
    }
}



