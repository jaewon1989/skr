<?php
namespace Peso\addrbot;

class Juso
{
    // 사용자 발화 
    private $user_utt;

    // 주소 데이터     
    private $roadAddr; // 전체 도로명주소
    private $roadAddrPart1; // 도로명주소
    private $roadAddrPart2; // 도로명주소 상세
    private $jibunAddr; // 지번주소 전체 
    private $engAddr; // 영문주소 전체
    private $zipNo; // 우편번호
    private $admCd; // 행정구역코드
    private $rnMgtSn; // 도로명코드
    private $bdMgtSn; // 건물관리번호
    private $detBdNmList; // 상세건물명
    private $bdNm; // 건물명
    private $siNm; // 시도명    
    private $sggNm; // 시군구명 
    private $emdNm; // 읍면동
    private $liNm; // 법정리
    private $hemdNm; // 행정구역명
    private $rn; // 도로명
    private $buldMnnm; // 도로명 주소 > 건물본번
    private $buldSlno; // 도로명 주소> 건물부번
    private $lnbrMnnm; // 지번주소 > 지번본번
    private $lnbrSlno; // 지번주소 > 지번부본

    /**
     * Juso constructor.
     * @juso  array (주소 검색결과 데이터)
     * @user_utt  String (사용자 발화)
     */
    public function __construct($juso,$user_utt){
        $this->roadAddr = $juso['roadAddr'];
        $this->roadAddrPart1 = $juso['roadAddrPart1'];
        $this->roadAddrPart2 = $juso['roadAddrPart2'];
        $this->jibunAddr = $juso['jibunAddr'];
        $this->engAddr = $juso['engAddr'];
        $this->zipNo = $juso['zipNo'];
        $this->admCd = $juso['admCd'];
        $this->rnMgtSn = $juso['rnMgtSn'];
        $this->bdMgtSn = $juso['bdMgtSn'];
        $this->detBdNmList = $juso['detBdNmList'];
        $this->bdNm = $juso['bdNm'];
        $this->siNm = $juso['siNm'];
        $this->sggNm = $juso['sggNm'];
        $this->emdNm = $juso['emdNm'];
        $this->liNm = $juso['liNm'];
        $this->hemdNm = $juso['hemdNm'];
        $this->rn = $juso['rn'];
        $this->buldMnnm = $juso['buldMnnm'];
        $this->buldSlno = $juso['buldSlno'];
        $this->lnbrMnnm = $juso['lnbrMnnm'];
        $this->lnbrSlno = $juso['lnbrSlno'];

        // 사용자 발화 규정
        $this->user_utt = $user_utt;

    }
    
    // 사용자 발화문에 주소 찾기
    private function find_juso_in_utt($unit_type,$needle){
        //$haystack = $this->user_utt; // 사용자 발화문 
        //if(strpos($haystack,$needle) !==false) return true;
        
        $result = [];
        $result['find'] = false;        
        $user_utt = $this->user_utt; // 사용자 발화
        $utt_arr = preg_split('/(\-|\s)/',$user_utt);
        foreach ($utt_arr as $index=>$word) {
            if($word == $needle){
                $result['find'] = true;
                $result['index'] = $index;
                $result['word'] = $word;

            }
        }
        
        // '고양시 일산서구' 형태 체크  
        if($unit_type =='sggNm' && !$result['find']){
            $pt ='/(\s)(.+)시(\s)(.+)구/';
            preg_match($pt,$user_utt,$mat);
            if($mat[0]){
                $find_all = ltrim($mat[0]);
                $find_si = ltrim($mat[2]);
                $find_sgg = ltrim($mat[4]);
                if($find_all == $needle){ // 매칭값 == 주소 리스트 sggNm 값이 같은 경우
                    if($find_si!='' && $find_sgg!=''){ // 시 구 같은 상황 체크 
                       $result['find'] = true;
                       $result['word'] = $find_all; 
                    }
                       
                }
                
            }
            $result['sggNm_mat'] = $mat; 
        }

        return $result;
    }

    // 사용자 발화에 > 시/도 체크 
    public function check_si(){
        return $this->find_juso_in_utt('siNm',$this->siNm); 
    }

    // 사용자 발화에 > 시/군/구 체크 
    public function check_sgg(){
        return $this->find_juso_in_utt('sggNm',$this->sggNm); 
    }
    
    // 사용자 발화에 도로명 주소 > 읍면동 체크 
    public function check_emd(){
       return $this->find_juso_in_utt('emdNm',$this->emdNm);
    }

    // 사용자 발황에 지번 주소 > 지번 체크 
    public function check_jiben(){
        $_MN = $this->find_juso_in_utt('lnbrMnnm',$this->lnbrMnnm); // 본번 체크
        $_SN = $this->find_juso_in_utt('lnbrSlno',$this->lnbrSlno); // 부본 체크 

        $result = $this->verify_MSN($_MN,$_SN,'jiben');

        return $result;
    }

    // 사용자 발화에 도로명 주소 > 도로명 체크 
    public function check_rn(){
        return $this->find_juso_in_utt('rn',$this->rn);;
    }

    // 사용자 발황에 도로명 주소 > 건물번호 체크 
    public function check_buld(){
        $_MN = $this->find_juso_in_utt('buldMnnm',$this->buldMnnm); // 본번 체크
        $_SN = $this->find_juso_in_utt('buldSlno',$this->buldSlno); // 부본 체크 
        
        $result = $this->verify_MSN($_MN,$_SN,'rn');

        return $result;
    }

    /*
       발화문에 본번 & 부본 있는지 검색 후 유효성 체크 
       
       $_MN : 본번 검색 결과 (find, index, word)
       $_SN : 부본 검색 결과 (find, index, word)
       $addr_type : 주소 타입 (도로명 > rn, 지번 > jiben)
    
    **/
    private function verify_MSN($_MN,$_SN,$addr_type){
        $result = [];
        

        $result['MN_find'] = $_MN['find'];
        $result['MN_index'] = $_MN['index'];
        $result['MN_word'] = $_MN['word'];
        $result['SN_find'] = $_SN['find'];
        $result['SN_index'] = $_SN['index'];
        $result['SN_word'] = $_SN['word'];

        $result['valid'] = false;
        
        // 해당 주소데이터 배열에 부본값이 있는지 체크 
        if($addr_type =='rn') $juso_has_SN = $this->buldSlno;
        else $juso_has_SN = $this->lnbrSlno;
        
        $result['juso_has_SN'] = $juso_has_SN;

        if($juso_has_SN && $juso_has_SN!='0'){ // 주소 리스트에 부본이 존재하는 경우(단, 0 이 아닌 경우)
           
            // 특정 주소에 부본이 있는 경우 발화문에도 본번과 부본이 반드시 있어야 한다.
            // ex : 역삼동 34-5
            if($_MN['find'] && $_SN['find'] &&  $_MN['index'] < $_SN['index']){

               $result['SN_find'] = $_SN['find'];
               $result['SN_index'] = $_SN['index'];
               $result['SN_word'] = $_SN['word'];
               $result['valid'] = true;

            }
        
        }else{
            // 특정 주소에 부본이 없는 경우 발화문에 본번만 있으면 된다.
            // ex : 역삼동 34
            if($_MN['find']) $result['valid'] = true;
        }

        return $result;
    }

    

}
