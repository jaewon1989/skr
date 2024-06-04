<?php
namespace Peso\addrbot;

class Nlp {

	private $origin_input;

	public function __construct(){
		return;   
	}
    
    // 형태소분석기 instance 생성
	private function get_posTagger_instance($input){
        $dic = '/usr/local/lib/mecab/dic/mecab-ko-dic'; // 한국어 사전
        if(extension_loaded('mecab')) {
            $t = new \MeCab\Tagger(array('-d', $dic));
            return $t->parseToNode($input);
        } else {
            $cmd = "echo '".$chStr."' | /usr/local/bin/mecab -d ".$dic;
            exec($cmd, $aResult, $return);
            return $retrun == 0 && count($aResult) > 0 ? $aResult : array();
        }
	}
    
    // 전처리
	private function getPreprocess($input){
		$origin_input = $input;
		$node = $this->get_posTagger_instance($input);
        
        $pos_removed = array(); // 주소검색에 필요 없는 형태소
        $pos_all = array(); // 전체 형태소 
        foreach($node as $m) {
            if(is_object($m)) {
                $feature = $m->getFeature();
                $feature_arr = explode(',',$feature);
                $PS = $feature_arr[0]; // 품사
                if(strpos($PS,'BOS') !== false || strpos($PS,'EOS') !== false) continue;
                // pos_removed(용언+어미 형태) 추가 
                $use_PS = ["NNG","NNP","SN","SY"]; // 여기서는 기호도 포함(- 때문)
                if(!in_array($PS,$use_PS)){
                    $trash = $m->getSurface();
                    array_push($pos_removed,$trash);                    
                }

                $mop = $m->getSurface(); // 형태소
                $pos_all[] = array("pos"=>$PS,"mop"=>$mop,"prev"=>$prev,"next"=>$next);
            } else {
                $feature = explode("\t", $m);
                if(strpos($feature[0],'BOS') !== false || strpos($feature[0],'EOS') !== false) continue;
                $aInfo = explode(",", $feature[1]);
                $PS = $aInfo[0];
                $use_PS = ["NNG","NNP","SN","SY"]; // 여기서는 기호도 포함(- 때문)
                if(!in_array($PS,$use_PS)){
                    $trash = $feature[0];
                    array_push($pos_removed,$trash);
                }
                $mop = $feature[0]; // 형태소
                $pos_all[] = array("pos"=>$PS,"mop"=>$mop,"prev"=>$prev,"next"=>$next);
            }
        }
        
        // 원문장에서 pos_removed 제거 
        $result = array();
        $result['origin'] = $origin_input;
        foreach ($pos_removed as $str) {
            $origin_input = str_replace($str,'',$origin_input);
        }

        $result['pp_result'] = $origin_input;
        $result['pos_all'] = $pos_all;
        $result['pos_removed'] = $pos_removed;

        return $result;  
	}

	public function getClean($input){
        global $g;

        //strip any html tags .. naughty naughty these shouldnt be here but just in case
        $replace_arr = [
           "서울특별시"=>["서울","서울시"],
           "인천광역시"=>["인천","인천시"],
           "대전광역시"=>["대전","대전시"],
           "대구광역시"=>["대구","대구시"],
           "부산광역시"=>["부산","부산시"],
           "광주광역시"=>["광주","광주시"],           
           "울산광역시"=>["울산","울산시"],           
           "충청남도"=>["충남"],
           "충청북도"=>["충북"],
           "전라남도"=>["전남"],
           "전라북도"=>["전북"],
           "경상남도"=>["경남"],
           "경상북도"=>["경북"],
        ];

   
        $input = strip_tags($input);

        //remove puncutation except full stops
        $input = preg_replace('/\.+/', '.', $input);
        $input = preg_replace('/\,+/', '', $input);
        //$input = preg_replace('/\!+/', '', $input);
        //$input = preg_replace('/\?+/', '', $input);
        $input = str_replace("'", " ", $input);
        $input = str_replace("\"", " ", $input);
        $input = preg_replace('/\s\s+/', ' ', $input);
        //replace more than 2 in a row occurances of the same char with two occurances of that char
        $input = preg_replace('/ㄱㄱ+/', 'ㄱㄱ', $input);
   
        // 행정구역명 축약어 처리
        $utt_arr = preg_split('/(\s)/',$input);
        foreach ($utt_arr as $word){
            foreach ($replace_arr as $replace => $arr) {
                if(in_array($word,$arr)){
                    $input = str_replace($word,$replace,$input);  
                } 
            }        
        } 

        // 전처리 진행
        $GPR = $this->getPreprocess($input);

        //return the string
        $result = array();
        $result['clean_input'] = $input;
        $result['pp_input'] = $input;//$GPR['pp_result'];
        $result['pos_all'] = $GPR['pos_all'];
        $result['pos_removed'] = $GPR['pos_removed'];    
    
        return $result;
    }

    public function getPosTagData($data){
        $input = $data['input'];
        $node = $this->get_posTagger_instance($input);

        $result =[];
        foreach($node as $m) {
            if(is_object($m)) {
                $feature = $m->getFeature();
                $feature_arr = explode(',',$feature);
                $PS = $feature_arr[0]; // 품사
                if(strpos($PS,'BOS') !== false || strpos($PS,'EOS') !== false) continue;
                $pos = $PS; // 품사명
                $mop = $m->getSurface(); // 형태소
                $result[] = array("pos"=>$pos,"mop"=>$mop);
            } else {
                $feature = explode("\t", $m);
                if(strpos($feature[0],'BOS') !== false || strpos($feature[0],'EOS') !== false) continue;
                $aInfo = explode(",", $feature[1]);
                $PS = $aInfo[0];
                $pos = $PS; // 품사명
                $mop = $feature[0]; // 형태소
                $result[] = array("pos"=>$pos,"mop"=>$mop);
            }
        }

        return $result;        
    }


}