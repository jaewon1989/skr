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
        //$t = mecab_new(['-d', $dic]);
        mecab_new(array('-d', $dic));
        return mecab_sparse_tonode($t, $input);
	}
    
    // 전처리
	private function getPreprocess($input){
		$origin_input = $input;
		$m = $this->get_posTagger_instance($input);
        
        $pos_removed = array(); // 주소검색에 필요 없는 형태소
        $pos_all = array(); // 전체 형태소 
        while($m) {
            //writeln(mecab_node_surface($m)); // 형태소 추출
            //writeln(mecab_node_length($m)); // 형태소 길이
            $feature = mecab_node_feature($m);
            $feature_arr = explode(',',$feature);
            $PS = $feature_arr[0]; // 품사
            if($PS!='BOS/EOS'){
                // 이전 node
                if ($_prev = mecab_node_prev($m)) {
                    $p_feature = mecab_node_feature($_prev);
                    $p_feature_arr = explode(',',$p_feature);
                    $p_pos = $p_feature_arr[0]; // 품사
                    $prev = mecab_node_surface($_prev); // 다음 노드
                }

                // 다음 node
                if ($_next = mecab_node_next($m)) {
                    $n_feature = mecab_node_feature($_next);
                    $n_feature_arr = explode(',',$n_feature);
                    $n_pos = $n_feature_arr[0]; // 품사
                    $next = mecab_node_surface($_next); // 다음 노드

                }
                
                // pos_removed(용언+어미 형태) 추가 
                $use_PS = ["NNG","NNP","SN","SY"]; // 여기서는 기호도 포함(- 때문)
                if(!in_array($PS,$use_PS)){
                    $trash = mecab_node_surface($m);
                    array_push($pos_removed,$trash);
                    
                }

                $mop = mecab_node_surface($mecab); // 형태소
                $pos_all[] = array("pos"=>$PS,"mop"=>$mop,"prev"=>$prev,"next"=>$next);
            }
            $m = mecab_node_next($m);

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
        $m = $this->get_posTagger_instance($input);

        $result =[];
        while($m) {
            //writeln(mecab_node_surface($m)); // 형태소 추출
            //writeln(mecab_node_length($m)); // 형태소 길이
            $feature = mecab_node_feature($m);
            $feature_arr = explode(',',$feature);
            $PS = $feature_arr[0]; // 품사 
            if($PS!='BOS/EOS'){
                $pos = $PS; // 품사명  
                $mop = mecab_node_surface($m); // 형태소 

                $result[] = array("pos"=>$pos,"mop"=>$mop);
            }
            $m = mecab_node_next($m);
        }


        return $result;        
    }


}