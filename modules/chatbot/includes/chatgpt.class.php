<?php
include_once dirname(__file__)."/base.class.php";
include_once dirname(__file__)."/ThumbImage.php";

class Chatgpt {
    public $vendor;
    public $aModel;
    public $targetVer;
	public $targetModel;
    public $gptApiUrl;
    public $gptKey;
    public $dataDir;
    public $nThumbWidth;
    public $googleTranslate;
    public $botid;
    public $cgroup;

    public function __construct($vendor="openai") {
        $this->aModel = array();
        $this->aModel['openai']['key'] = "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";
        $this->aModel['openai']['gpt'] = array('url'=>"https://api.openai.com/v1/completions", 'model'=>'text-davinci-003');
        $this->aModel['openai']['chat'] = array('url'=>"https://api.openai.com/v1/chat/completions", 'model'=>'gpt-3.5-turbo');
        $this->aModel['openai']['image'] = array('url'=>"https://api.openai.com/v1/images/generations");

        $this->aModel['azure']['key'] = "f135fa9ad6bf49a6b2cf0ccb1c74febe"; // or 34e103af6e9e4b369c0820542a59bbaa - peso_internal
        $this->aModel['azure']['chat'] = array('url'=>"https://persona-openai.openai.azure.com//openai/deployments/gpt-35-turbo/chat/completions?api-version=2023-03-15-preview", 'model'=>'gpt-35-turbo');

        $this->vendor = $vendor;
        $this->gptKey = $this->aModel[$this->vendor]['key'];

        $this->dataDir = $_SERVER['DOCUMENT_ROOT']."/_tmp/upload";
        $this->nThumbWidth = 800;
        $this->googleTranslate = false; // 구글 번역 사용 여부

        if(!isset($_SESSION['S_Messages'])) $_SESSION['S_Messages'] = [];
    }

    public function setGptSettings($data){
        $this->aModel['option']['max_tokens'] = ($_SESSION['chatgpt']['gpt_max_tokens'])? $_SESSION['chatgpt']['gpt_max_tokens'] : '800';
        $this->aModel['option']['temperature'] = ($_SESSION['chatgpt']['gpt_temperature'])? $_SESSION['chatgpt']['gpt_temperature'] : '0.7';
        $this->aModel['option']['top_p'] = ($_SESSION['chatgpt']['gpt_top_p'])? $_SESSION['chatgpt']['gpt_top_p'] : '0.95';
        $this->aModel['option']['frequency_penalty'] = ($_SESSION['chatgpt']['gpt_frequency_penalty'])? $_SESSION['chatgpt']['gpt_frequency_penalty'] : '0';
        $this->aModel['option']['presence_penalty'] = ($_SESSION['chatgpt']['gpt_presence_penalty'])? $_SESSION['chatgpt']['gpt_presence_penalty'] : '0';

        $this->aModel['prompt']['name'] = ($data['chatgpt']['name'])? $data['chatgpt']['name'] : '';
        $this->aModel['prompt']['motion'] = ($data['chatgpt']['motion'])? $data['chatgpt']['motion'] : '';
        $this->aModel['prompt']['tone'] = ($data['chatgpt']['tone'])? $data['chatgpt']['tone'] : '';
        $this->aModel['prompt']['style'] = ($data['chatgpt']['style'])? $data['chatgpt']['style'] : '';
        $this->aModel['prompt']['reader_level'] = ($data['chatgpt']['reader_level'])? $data['chatgpt']['reader_level'] : '';
        $this->aModel['prompt']['perspective'] = ($data['chatgpt']['perspective'])? $data['chatgpt']['perspective'] : '';
        $this->aModel['prompt']['format'] = ($data['chatgpt']['format'])? $data['chatgpt']['format'] : '';
        $this->aModel['prompt']['prompt'] = ($data['chatgpt']['prompt'])? $data['chatgpt']['prompt'] : '';
        $this->aModel['prompt']['use_text'] = ($data['chatgpt']['use_text'])? $data['chatgpt']['use_text'] : '';
    }

    public function chat($_data) {
        $this->setGptSettings($_data);

        $this->botid = $_data['botid'];
        $this->cgroup = $_data['cgroup'];
        $this->targetType = "chat";
        $this->gptApiUrl = $this->aModel[$this->vendor][$this->targetType]['url'];
        $this->targetModel = $this->aModel[$this->vendor][$this->targetType]['model'];

        $result = array();
        $result['error'] = false;
        $result['result'] = false;
        $result['restype'] = "text";
        $result['user'] = $_data['msg'];

        $data = array();

        $_msg = '';

        if(!isset($_data['no_setting']) || !$_data['no_setting']) {
            if($this->aModel['prompt']['name'] == 'FAQ'){ // FAQ 예외처리
                $_msg = ($this->aModel['prompt']['prompt'] != '')? $this->aModel['prompt']['prompt'] : '';
                $_msg .= '\n\n#response_format\n';
                $_msg .= '- Tone : '.$this->aModel['prompt']['tone'].'\n';
                $_msg .= '- Style : '.$this->aModel['prompt']['style'].'\n';
                $_msg .= '- Reader level : '.$this->aModel['prompt']['reader_level'].'\n';
                $_msg .= '- Length : '.$this->aModel['option']['max_tokens'].'자 이내\n';
                $_msg .= '- Perspective : '.$this->aModel['prompt']['perspective'].'\n';
                $_msg .= '- Format : '.$this->aModel['prompt']['format'].'\n\n';
                $_msg .= '\n\n#question\n';
                $_msg .= $_data['msg'].'\n';
                //$_msg .= '\n\n\n#question 내용을 #data의 Q)에서 하나만 찾은 뒤 바로 다음 A)의 내용만 #response_format 형식으로 답변해줘\n';
                $_msg .= '\n\n\n'.$this->aModel['prompt']['motion'].'\n';
            }else{
                $_msg .= '\n\n#response_format\n';
                $_msg .= '- Tone : '.$this->aModel['prompt']['tone'].'\n';
                $_msg .= '- Style : '.$this->aModel['prompt']['style'].'\n';
                $_msg .= '- Reader level : '.$this->aModel['prompt']['reader_level'].'\n';
                $_msg .= '- Length : '.$this->aModel['option']['max_tokens'].'자 이내\n';
                $_msg .= '- Perspective : '.$this->aModel['prompt']['perspective'].'\n';
                $_msg .= '- Format : '.$this->aModel['prompt']['format'].'\n\n';
                $_msg .= '\n\n#question\n';
                if($this->aModel['prompt']['use_text'] == "1"){ // text 창 사용
                    $_msg .= ($this->aModel['prompt']['prompt'] != '')? $this->aModel['prompt']['prompt'] : '';
                }else{
                    $_msg .= ($_data['msg'] != '')? $_data['msg'] : '';
                }
                $_msg .= '\n\n\n#response_format 형태를 지켜서 #question 내용에 대한 답변을 '.$this->aModel['prompt']['motion'].'\n';
            }
        } else {
            $_msg = $_data['msg'];
        }

        // chatgpt api 파라미터
        $data['postData'] = array();
        if($this->vendor == "openai") {
            $data['postData']['model'] = $this->targetModel; // 모델 설정
        }

        $data['postData']['max_tokens'] = (int)$this->aModel['option']['max_tokens']; // 응답 길이
        $data['postData']['temperature'] = (int)$this->aModel['option']['temperature']; // 0 ~ 1 사이의 실수. 창의성과 다양성 조절 (낮을수록 일관성, 높을수록 창의적)
        $data['postData']['top_p'] = (int)$this->aModel['option']['top_p'];; // 0 ~ 1 사이의 실수. 생성된 텍스트에서 다음 단어를 선택하는 임계값 (낮을스록 창의적, 높을수록 일관성)
        $data['postData']['frequency_penalty'] = (int)$this->aModel['option']['frequency_penalty']; // 0 ~ 1 사이의 실수. 중복 단어와 구절의 빈도 제어 (낮을수록 중복 단어 또는 구절이 더 자주 나타남)
        $data['postData']['presence_penalty'] = (int)$this->aModel['option']['presence_penalty']; // 0 ~ 1 사이의 실수. 이전에 생성된 토큰들의 출현 빈도 제어 (0이면 이전 생성된 토큰 고려하지 않고 새로운 텍스트만 생성)
        //$data['postData']['stop'] = ""; // 작업 중지 설정. 생성된 텍스트가 일정 기준에 도달하면 작업 중지. 예) "stop": ["\n", ".", "?"], "stop": {"time_limit": 30}

        $messages = array();
        if($this->targetType == "gpt") {
            $data['postData']['prompt'] = $_msg;
        } else {
            if(count($_SESSION['S_Messages']) == 0) {
                // 20230713 aramjo w shopping
                $_SESSION['S_Messages'][] = array("role"=>"assistant", "content"=>"You are a customer service representative.");
                /*
                if($this->cgroup == "skt") {
                    $_SESSION['S_Messages'][] = array("role"=>"assistant", "content"=>"You are a customer service representative.");
                } else {
                    $_SESSION['S_Messages'][] = array("role"=>"assistant", "content"=>"You are a helpful assistant in korean.");
                }
                */
            }

            // 대화 context는 마지막 5개까지만 설정
            $_SESSION['S_Messages'] = array_slice($_SESSION['S_Messages'], -5);

            $_SESSION['S_Messages'][] = array("role"=>"user", "content"=>$_msg);
            $data['postData']['messages'] = $_SESSION['S_Messages'];
        }

        $data['postData'] = json_encode($data['postData']);

        //----- 발화문 응답 요청 ------//
        $response = $this->getAPIRequest($data);
        if(isset($response['error']) && $response['error']) {
            $result['error'] = true;
            $result['content'] = $response['error']['type'].": ".$response['error']['message'];
        } else {
            $result['result'] = true;
            $result = array_merge($result, $this->getResponse($response));
            $_SESSION['S_Messages'][] = array("role"=>$result['role'], "content"=>$result['content']);
        }
        return $result;
    }

    public function getResponse($response) {
        $result = array();

        // Chat Response
        if($this->targetType == "gpt" || $this->targetType == "chat") {
            if(isset($response["usage"])) {
                $result['usage'] = $response["usage"];
            }

            // gpt 3 response
            if(isset($response["choices"][0]['text'])) {
                $result['content'] = $response["choices"][0]['text'];
            }
            // gpt 3.5 response
            if(isset($response["choices"][0]['message'])) {
                $result['role'] = $response["choices"][0]['message']['role'];
                $result['content'] = $response["choices"][0]['message']['content'];
            }
        }

        // Image Response
        if($this->targetType == "image") {
            $result['data'] = $response['data'];
        }
        return $result;
    }

    public function getAPIRequest($data) {
        if(isset($data['postData']) && $data['postData'] != ''){
            $data['headers'][] = "Content-Type: application/json";
            if($this->vendor == "openai") {
                $data['headers'][] = "Authorization: Bearer ".$this->gptKey;
            } else {
                $data['headers'][] = "api-key: ".$this->gptKey;
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->gptApiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data['postData']);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
            if(isset($data['headers']) && count($data['headers']) > 0) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $data['headers']);
            }
            $response = curl_exec($ch);
            $aResInfo = curl_getinfo($ch);
            curl_close($ch);

            $result = array();
            if($response) {
                $result = json_decode($response, true);
                if(json_last_error() != JSON_ERROR_NONE) {
                    $result = array("error"=>true, "content"=>"네트워크 장애로 응답할 수 없습니다.");
                }
            } else {
                $result = array("error"=>true, "content"=>"네트워크 장애로 응답할 수 없습니다.");
            }
            $result['response_api'] = $response;
            return $result;
        }else{
            $result = array("error"=>true, "content"=>"네트워크 장애로 응답할 수 없습니다.");
        }
    }

    public function sttApiRequest($data) {
        $secret = '88f01f0c20b544fbb35b30605cfff41b';
        $apiURL = "https://clovaspeech-gw.ncloud.com/external/v1/2424/789a27ce1ac854fc83cff0c3241bc720b0b39811dcbb853f9e9fd1ac35516ee9";

        $audioFile = $_SERVER['DOCUMENT_ROOT']."/_tmp/upload/".$data['file'];

        $params = array();
        $params['language'] = "ko-KR";
        $params['completion'] = "sync";
        $params['callback'] = null;
        $params['userdata'] = null;
        $params['forbiddens'] = null;
        $params['boostings'] = null;
        $params['wordAlignment'] = null;
        $params['fullText'] = null;
        $params['diarization'] = null;

        $headers = array();
        $headers[] = "X-CLOVASPEECH-API-KEY: ".$secret;

        $aPostVal = array();
        $aPostVal['params'] = json_encode($params);
        $aPostVal['media'] = new CURLFile($audioFile);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiURL."/recognizer/upload");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $aPostVal);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSLVERSION,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        $aResInfo = curl_getinfo($ch);
        curl_close ($ch);

        $result_stt = "";
        $result = array();
        if ($err) {
            $result = array("error"=>true, "content"=>"네트워크 장애로 응답할 수 없습니다.3".print_r($err, 1));
        }else{
            if(!$response) {
                $result = array("error"=>true, "content"=>"네트워크 장애로 응답할 수 없습니다.1");
            } else {
                $result = json_decode($response, true);
                if(json_last_error() != JSON_ERROR_NONE) {
                    $result = array("error"=>true, "content"=>"네트워크 장애로 응답할 수 없습니다.2");
                }

                $result_stt = $result['text'];
                $result = array("result"=>true, "content"=>trim($result_stt));
            }
        }
        return $result;
    }

    public function image($_data) {
        $this->vendor = "openai";
        $this->targetType = "image";
        $this->gptKey = $this->aModel[$this->vendor]['key'];
        $this->gptApiUrl = $this->aModel[$this->vendor][$this->targetType]['url'];

        $result = array();
        $result['error'] = false;
        $result['result'] = false;
        $result['user'] = $_data['msg'];
        $result['content'] = "";

        $this->googleTranslate = true;
        $msg_tran = $this->getTranslate("ko", "en", $_data['msg']);
        if($msg_tran) {
            $result['user_tran'] = $msg_tran;
            $msg = $msg_tran;
        }

        $data = array();

        // chatgpt api 파라미터
        $data['postData'] = array();
        $data['postData']['prompt'] = $msg;
        $data['postData']['n'] = 3;
        $data['postData']['size'] = "1024x1024";
        $data['postData'] = json_encode($data['postData']);

        $response = $this->getAPIRequest($data);
        if(isset($response['error']) && $response['error']) {
            $result['error'] = true;
        } else {
            $result['result'] = true;
            $result = array_merge($result, $this->getResponse($response));
        }

        // 이미지 로컬 저장
        if(isset($result['data']) && count($result['data']) > 0) {
            $aResImage = array();
            $oThumb = new ThumbImage();

            foreach($result['data'] as $_image) {
                if(isset($_image['url']) && $_image['url']) {
                    $_fileName = $_thumbName = date("ymdHis")."_".rand(1000,9999).".jpg";
                    $_saveFile = $this->getRemoteImage($_image['url'], $this->dataDir."/".$_fileName);
                    if(file_exists($_saveFile)) {
                        $aSize = getimagesize($_saveFile);
                        if($aSize[0] > $this->nThumbWidth) {
                            $_thumbName = "thumb_".$_fileName;
                            $_thumbFile = $oThumb->getCreateThumb($_saveFile, $this->dataDir."/".$_thumbName, $this->nThumbWidth, 0);
                        }
                        $aResImage[] = array("image"=>"/_tmp/upload/".$_fileName, "thumb"=>"/_tmp/upload/".$_thumbName, "name"=>$_thumbName);
                    }
                }
            }

            $result['restype'] = "image";
            $result['content'] = count($aResImage) > 0 ? $aResImage : "이미지 생성 실패";
        }
        return $result;
    }

    public function getTranslate($in_lang,$out_lang,$text){
        $trans = new GoogleTranslate();
        $result = $trans->translate($in_lang, $out_lang, $text);
        return $result;
    }

    public function setMKDir($chDataDir, $chTargetDir) {
        if(!is_dir($chDataDir."/".$chTargetDir)) {
            $oldmask = umask(0);
            mkdir($chDataDir."/".$chTargetDir, 0707);
            umask($oldmask);
            return $chDataDir."/".$chTargetDir;
        } else {
            return $chDataDir."/".$chTargetDir;
        }
    }

    public function getRemoteImage($chURL, $chToFile) {
        $fp = fopen($chToFile, 'w');

        $ch = curl_init($chURL);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        $data = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        return $chToFile;
    }
}

?>