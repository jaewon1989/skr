<?php
namespace Peso\addrbot;

/*
   다이얼로그 그래프 역할
*/

class Dialog
{
    private $nlp;
    private $user_utt;
    private $context;
    private $juso_data;
    private $addr_default = []; // 기본주소 = 상세주소 전단계
    private $addr_default_jiben; // 확인된 기본주소 > 지번주소
    private $addr_default_road;  // 확인된 기본주소 > 도로주소
    private $addr_detail; // 확인된 상세주소
    private $bot_utt_mod; // 봇 utt mod 
    private $intent_yes = ["예","예스","네","응","그래","맞아","맞아요","그래요"];
    private $intent_no = ["아니","노","아니요","아니오","틀린데요","틀려요","다른데요","달라"];

    /**
     * Dialog constructor.
     *
     * @param string $user_utt 사용자 발화
     * @param array $juso_data 주소검색 결과 리스트
     * @param array $context 대화 컨텍스트(로그) : 클라우드쪽에도 DB 저장해서 공유함

     */
   
    public function __construct($data=''){
        global $g;
        
        require_once $g['dir_module'].'lib/addrBot/nlp.php'; // nlp 관련 처리
        require_once $g['dir_module'].'lib/addrBot/juso.php'; // juso instance 클래스

        $this->nlp = new \Peso\addrbot\Nlp();
    
    }
     // context 값 업데이트
    private function updateContext($up_key,$up_val){
        $context = $this->context;
        $context[$up_key] = $up_val;

        $this->context = $context;
    }

    // context 값 얻기
    private function getContextVal($key){
        $context = $this->context;

        return $context[$key];
    }
    
  
    // 주소 데이터 tracking 
    private function trackJuso(){
        $result =[]; // 최종 리턴값 (utt, rn_fail, jiben_fail)
       
        $jiben_fail = []; // 지번주소 불합격
        $rn_fail = []; // 도로명주소 불합격
        $addr_default = []; // 기본주소 : 상세주소(동,호수,층 등) 이전단계  
        $check_jiben = []; // 지번주소 체크 데이터  
        $check_rn = []; // 도로주소 체크 데이터
        $check_si = []; // 시/도 체크 데이터 
        $check_sgg = []; // 시/군/구 체크 데이터 
        
        // 필수 체크 주소 unit 
        $cnt_si = 0; // 시/도 찾기 성공
        $cnt_sgg = 0; // 시/군/구 찾기 성공
        $cnt_emd_jiben = 0; // 읍면동 & 지번 찾기 성공
        $cnt_rn_buld = 0; // 도로명 & 건물번호 찾기 성공

       
        foreach ($this->juso_data as $arr) {
            $juso = new \Peso\addrbot\Juso($arr,$this->user_utt);
            
            // ************ 사용자 발화에 해당 값이 있는지 체크 **************
         
            // 공통주소 관련 데이터 체크 
            $has_si = $juso->check_si();// 시/도 체크
            $has_sgg = $juso->check_sgg();// 시/군/구 체크

            // 도로주소 관련 데이터 체크
            $has_emd = $juso->check_emd(); // 지번 주소 > 읍면동 체크
            $has_jiben = $juso->check_jiben(); // 지번 주소 > 지번 체크
            
            // 지번주소 관련 데이터 체크
            $has_rn = $juso->check_rn(); // 도로명 주소 > 도로명 체크 
            $has_buld = $juso->check_buld(); // 도로명 주소 > 건물번호 체크 
            
            // 시/도, 시/군/구 값 체크 
            if($has_si['find']){
                $cnt_si++;
                $this->updateContext("last_siNm",$has_si['word']); // 컨텍스트 없데이트    
            } 
            if($has_sgg['find']){
                $cnt_sgg++;
                $this->updateContext("last_sggNm",$has_sgg['word']); // 컨텍스트 없데이트
                  
            } 
            
            // 둘다 찾은 경우 
            if($has_emd['find'] && $has_jiben['valid']){
                $cnt_emd_jiben++;

                // emd & jiben 컨텍스트에 저장
                if($has_jiben['SN_find']){
                    $last_jiben = $has_jiben['MN_word'].'-'.$has_jiben['SN_word'];
                }else{
                    $last_jiben = $has_jiben['SN_word']; 
                } 
                $this->updateContext("last_emdNm",$has_emd['word']); 
                $this->updateContext("last_jiben",$last_jiben);
            }   

            if($has_rn['find'] && $has_buld['valid']){
                $cnt_rn_buld++;

                if($has_buld['SN_find']){
                    $last_jiben = $has_buld['MN_word'].'-'.$has_buld['SN_word'];
                }else{
                    $last_jiben = $has_buld['SN_word']; 
                }

                // rn & buld 컨텍스트에 저장 
                $this->updateContext("last_rn",$has_rn['word']);
                $this->updateContext("last_buld",$last_buld);
            } 
            
            // 둘 중에 하나 못 찾은 경우 
            if(($has_rn['find'] && !$has_buld['valid']) || (!$has_rn['find'] && $has_buld['valid'])) array_push($rn_fail,$arr);            
            if(($has_emd['find'] && !$has_jiben['valid']) || (!$has_emd['find'] && $has_jiben['valid'])) array_push($jiben_fail,$arr);
          
            // 체크 데이터
            $check_jiben[] = [
                 "check_si"=>$has_si,
                 "check_sgg"=>$has_sgg,
                 "check_emd"=>$has_emd,
                 "check_jiben"=>$has_jiben,
                 "juso"=>$arr
            ];
            $check_rn[] = [
                "check_si"=>$has_si,
                "check_sgg"=>$has_sgg,
                "check_rn"=>$has_rn,
                "check_buld"=>$has_buld,
                "juso"=>$arr
            ];


            //si,sgg && emd/jiben or rn/buld 모두 있는 경우 > 기본주소 충족
            if($has_si['find'] && $has_sgg['find'] && 
                (($has_emd['find'] && $has_jiben['valid']) || ($has_rn['find'] && $has_buld['valid']))

            ){
                $result['check_jiben'] = $has_jiben;
                $result['check_buld'] = $has_buld;

                array_push($addr_default,$arr);
                break;
            }

  
        }
        
        $result['user_utt'] = $this->user_utt; 
        $result['addr_default'] = $addr_default; // 기본주소 체크 성공
        $result['check_jiben'] = $check_jiben;// 읍면동 체크 데이터
        $result['check_rn'] = $check_rn;//      
        $result['rn_fail'] = $rn_fail;// 도로주소 실패
        $result['jiben_fail'] = $jiben_fail;// 지번주소 실패
        $result['cnt_si'] = $cnt_si;// 시/도 체크 수
        $result['cnt_sgg'] = $cnt_sgg;// 시/군/구 체크 수
        $result['cnt_emd_jiben'] = $cnt_emd_jiben;// 읍면동 & 지번 체크 수
        $result['cnt_rn_buld'] = $cnt_rn_buld;// 도로명 & 건물번호 체크 수
     
        return $result;

    }
    
    // 기본주소 찾기 실패 한 경우 
    private function get_fail_addr_default_botUtt($trackJuso){
        $addr_default = $trackJuso['addr_default']; // 기본주소 체크 성공
        $rn_fail = $trackJuso['rn_fail']; // 도로주소 실패 
        $jiben_fail = $trackJuso['jiben_fail']; // 지번주소 실패
        $cnt_si = $trackJuso['cnt_si']; // 시/도 성공 횟수
        $cnt_sgg = $trackJuso['cnt_sgg']; // 시/군/구 성공 횟수
        $cnt_emd_jiben = $trackJuso['cnt_emd_jiben']; // 읍면동 & 지번 성공 횟수
        $cnt_rn_buld = $trackJuso['cnt_rn_buld']; // 도로명 & 건물번호 성공 횟수

        $cnt_rn_fail = count($rn_fail); // 도로명 실패 횟수
        $cnt_jiben_fail = count($jiben_fail); // 지번 실패 횟수 
        
        $result =[];

        $result['cnt_jiben_fail'] = $cnt_jiben_fail;
        $result['cnt_rn_fail'] = $cnt_rn_fail;
        
        $bot_utt='';

        // 발화문에 > 시/도, 시/군/구 존재 여부 체크
        if(!$cnt_si || !$cnt_sgg ){
            if(!$cnt_si) $bot_utt.= $this->getUtt('addr_default_fail_si');
        
            if(!$cnt_sgg){
               $fail_sgg = $this->getUtt('addr_default_fail_sgg');  
               if($cnt_si) $bot_utt.= $fail_sgg;
               else $bot_utt.='과 '.$fail_sgg;   
            }
            
            $bot_utt.=' '.$this->getUtt('not_found');    
        } 
         
        
        if(!$cnt_emd_jiben && !$cnt_rn_buld){ // 발화문에 > 읍면동&지번 혹은 도로명&건물번호 체크 결과 실패(유효성) 한 경우
            if(!$cnt_rn_fail && !$cnt_jiben_fail){ 
                $fail_utt = $this->getUtt('addr_default_fail_jiben_rn'); // 발화문에 두 가지 항목이 아예 언급 없는 경우   

            }else{ // 둘 중 하나는 언급되었으나 유효성 실패한 경우
                if($cnt_rn_fail>=$cnt_jiben_fail) $fail_utt=$this->getUtt('addr_default_fail_rn');
                else $fail_utt =$this->getUtt('addr_default_fail_jiben');
            }
            
            if(!$cnt_si || !$cnt_sgg) $bot_utt.=' '.$fail_utt; // 앞에 한칸 띄우기
            else $bot_utt.=$fail_utt;
        } 

        $result['bot_utt'] = $bot_utt;                    

        return $result;
    }

    private function getIntent($data){

        $intent_yes = ["예","예스","네","응","그래","맞아","맞아요","그래요"];
        $intent_no = ["아니","노","아니요","아니오","틀린데요","틀려요","다른데요","달라"];
        $user_utt = $data['user_utt'];
        
        // 형태소 체크 
        $dd = ["input"=>$user_utt];    
        $PTD = $this->nlp->getPosTagData($dd);

        $result = [];

        if(in_array($user_utt,$intent_yes)) $result[] = 'yes'; // yes 인텐트 체크 
        if(in_array($user_utt,$intent_yes)) $result[] = 'no'; // no 인텐트 체크 

        foreach ($PTD as $arr) {
            $pos = $arr['pos']; // 품사
            $mop = $arr['mop']; // 형태소
            
            if($pos =='VA' && $mop =='없') $result[] = 'mu'; // 없음 인텐트 발견
        }

        return $result; 
    }

    /**
       client.php 에서 호출 
       @user_utt : 사용자 발화
       @context  : 컨텍스트(DB 에 저장된 것 > 클라우드와 계속 공유하는 방식)
       @juso_data : 주소서버에서 검색해온 결과 
    */
    public function getBotResponse($data){
        $this->user_utt = $data['user_utt'];
        $this->context = $data['context'];
        
        // bot 최근(이전) 발화 모드
        $last_botUttMod = $data['context']['last_botUttMod'];

        // 컨텍스트 값 
        $this->addr_default_jiben = $data['context']['addr_default']['jibunAddr']; // 기본주소 (지번 주소) 
        $this->addr_default_road = $data['context']['addr_default']['roadAddr']; // 기본주소 (도로 주소) 
        $this->addr_detail = $data['context']['addr_detail']; // 이전단계 발화 > 확인 요청한 상세주소


        
        $result = [];
        if($this->user_utt == 'init' || $this->user_utt == 'retry'){
            // 초기, 네트워크 장애, 목적(주소찾기) 완료시 
            $result['bot_utt'] = $this->getUtt($this->user_utt);

            $this->updateContext("goal_success", false);
                
        }else{

            // 인텐트 체크
            $intent = $this->getIntent($data); // array();
            $result['intent'] = $intent;
             
            if($last_botUttMod=='addr_default_success'){ // 이전 단계 bot 발화 > 기본주소값 찾은 경우 (last 발화 : 상세주소 있으면 말씀해주세요)
                
                // 상세주소 존재 여부 체크 > intent : mu 체크시 > 사용자 발화를 전체주소에 적용하지 않음
                if(in_array('mu',$intent)) $result['bot_utt'] = $this->getUtt('addr_default_confirm');// 전체 주소 확인 요청 => 사용자 발화를 상세주소 적용 안함 
                else $result['bot_utt'] = $this->getUtt('addr_all_confirm');// 전체 주소 확인 요청 = 사옹자 발화를 상세주소로 적용 

            }else if($last_botUttMod=='addr_all_confirm' || $last_botUttMod=='addr_default_confirm'){ // 이전단계 발화 > 전체 주소 or 기본주소 확인 요청한 경우                
               
                if(in_array('yes', $intent)) $result['bot_utt'] = $this->getUtt('addr_confirm_yes'); // 사용자가 컨펌한 경우(yes) > 완료
                else $result['bot_utt'] = $this->getUtt('addr_confirm_no');   
            
            }else{
                $totalCount = $data['totalCount'];// 주소 데이터 전체 갯수 
                $this->juso_data = $data['juso_data'];

                if(!$totalCount){ // 주소검색 결과 없는 경우 
                    $result['bot_utt'] = $bot_utt.' '.$this->getUtt('not_found'); 
               
                }else{
                    
                    // 주소 트래킹
                    $trackJuso = $this->trackJuso();
                    $addr_default = $trackJuso['addr_default']; // 기본주소 체크 성공
                    $rn_fail = $trackJuso['rn_fail']; // 도로주소 실패 
                    $jiben_fail = $trackJuso['jiben_fail']; // 지번주소 실패
                    $cnt_si = $trackJuso['cnt_si']; // 시/도 성공 횟수
                    $cnt_sgg = $trackJuso['cnt_sgg']; // 시/군/구 성공 횟수
                    $cnt_emd_jiben = $trackJuso['cnt_emd_jiben']; // 읍면동 & 지번 성공 횟수
                    $cnt_rn_buld = $trackJuso['cnt_rn_buld']; // 도로명 & 건물번호 성공 횟수
                    

                    if(count($addr_default)>0){
                        $this->addr_default = $addr_default[0];
                        $result['bot_utt'] = $this->getUtt('addr_default_success'); // 기본주소 성공
                        
                        // 기본주소 데이터 context 에 추가 
                        $this->updateContext("addr_default",$this->addr_default);

                        // 기본주소 발환 context 에 추가 
                        $this->updateContext("addr_default_utt",$this->user_utt);

            
                    }else{ // 기본주소 찾기 실패
                        $get_fail_utt = $this->get_fail_addr_default_botUtt($trackJuso); 

                        $result['bot_utt'] = $get_fail_utt['bot_utt'];
                        $result['fail_bot_utt'] = $get_fail_utt; 
                    } 
                    
                    $result['trackJuso'] = $trackJuso;
                }
               
            }  
        }

        // debug 용도
        //$result['init_data'] = $data;

        // 컨텍스트값 추가 리턴
        $result['context'] = $this->context;
        $result['user_utt'] = $this->user_utt;
        $result['data'] = $data;

        return $result;
  
    } 

    // 최종 발화 추출
    private function getUtt($mod){

        $fail_rn = '도로명과 건물번호';
        $fail_jiben = '읍명동과 번지수';
        $tell_together = '함께 말씀해주세요';        

        if($mod =='init') $result = '도로명주소 혹은 지번주소를 말씀해주세요.';
        else if($mod =='retry') $result ='다시 한번 말씀해주시겠어요?';
        else if($mod =='addr_default_fail_si') $result = '시도명';
        else if($mod =='addr_default_fail_sgg') $result = '시군구명';
        else if($mod =='addr_default_fail_rn') $result = $fail_rn.'를 '.$tell_together;
        else if($mod =='addr_default_fail_jiben') $result = $fail_jiben.'를 '.$tell_together;
        else if($mod =='addr_default_fail_jiben_rn') $result = $fail_rn.' 혹은 '.$fail_jiben.'를 '.$tell_together;
        else if($mod =='addr_default_success') $result = '동이나 층 혹은 호수 등 상세주소가 있으면 말씀해주세요';
        else if($mod =='addr_all_confirm') $result = '이 주소가 맞으세요? '.$this->addr_default_jiben.' '.$this->user_utt;  
        else if($mod =='addr_default_confirm') $result = '이 주소가 맞으세요? '.$this->addr_default_jiben; // 기본주소로만 체크 (상세주소 없음)
        else if($mod =='addr_confirm_yes') $result ='말씀하신 주소 '.$this->addr_default_jiben.' '.$this->addr_detail.'가 정상적으로 저장되었습니다';
        else if($mod =='addr_confirm_no') $result ='도로명주소 혹은 지번주소를 다시 한번 말씀해주세요';
        else if($mod =='not_found') $result ='정보를 찾을 수 없습니다';


        // 이 값을 가지고 주소검색할지 여부 결정
        $this->bot_utt_mod = $mod;

        // bot_utt_mod 값 컨텍스트에 업데이트
        $this->updateContext("last_botUttMod",$this->bot_utt_mod);

        // 상세주소 컨텍스트에 업데이트 
        if($mod =='addr_all_confirm'){
            $this->updateContext("addr_detail",$this->user_utt);
        }

        // confirm > 예/노 인 경우 주소값 초기화
        if($mod =='addr_confirm_yes' || $mod =='addr_confirm_no'){
            $this->updateContext("last_botUttMod","init");
            $this->updateContext("addr_default",""); // 기본주소 초기화
            $this->updateContext("addr_default_utt",""); // 기본주소 초기화
            $this->updateContext("addr_detail",""); // 상세주소 초기화
            $this->updateContext("last_siNm",""); 
            $this->updateContext("last_sggNm","");
            $this->updateContext("last_emdNm","");
            $this->updateContext("last_jiben","");
            $this->updateContext("last_rn",""); 
            $this->updateContext("last_buld","");

            // 목적 성공 상태 저장
            $this->updateContext("goal_success",true);
        }


        return $result; 
    } 
    
}	