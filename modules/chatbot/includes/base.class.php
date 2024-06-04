<?php

require_once dirname(__file__).'/botTemp.class.php';
require_once dirname(__file__).'/topicTemp.class.php';

// Rb DB 객체화
Class DB{

    public $changeQue;

    public function query($sql){
   	   global $DB_CONNECT;
  	   $change_result=$this->changeQue=db_query($sql,$DB_CONNECT);
       return $change_result;
    }

    public function fetch_assoc($result){
  	   $change_result=$this->changeQue=db_fetch_assoc($result);
       return $change_result;
    }

    public function fetch_array($result){
  	   $change_result=$this->changeQue=db_fetch_array($result);
       return $change_result;
    }

    public function num_rows($result){
  	   $change_result=$this->changeQue=db_num_rows($result);
       return $change_result;
    }

    // 문자열  escape
    public function real_escape_string($string){
    	global $DB_CONNECT;
    	return mysqli_real_escape_string($DB_CONNECT, $string);
    }

}


// 모듈 기본환경 설정
class Module_base {
	public $module;
    public $tbl_question;
    public $tbl_reply;
    public $tbl_rule;
    public $lang;  // 구글 번역용
    public $_lang; // 첼린지용
    public $botMsgType='text'; // text, slot...
    public $now_query;
    public $dev_mod=1;
    public $hcn_my;
    public $showTimer;
    public $userid;
    public $mbruid;
    public $copyRemoteServers;

    public function __construct() {
    	global $g,$m,$module,$table;
        $this->module = 'chatbot';
		$this->db=new DB();

        $this->_lang = $_POST['_lang']?$_POST['_lang']:'ko';
        $_lang = $this->_lang;
        $suffix = ($_lang=='ko'||!$_lang)?'':'_'.$_lang;

        $this->tbl_question = $table[$this->module.'question'].$suffix;
        $this->tbl_reply = $table[$this->module.'reply'].$suffix;
        $this->tbl_rule = $table[$this->module.'rule'].$suffix;

        // 구글 번역시 필요
        if($_lang=='zh') $this->lang = 'zh-CN';
        else $this->lang = $_lang;

        // hcn 회원정보 세팅
        $huid = urldecode($_GET['huid']);
        if($huid) {
            $is_user = getDbData($table['s_mbrid'],"id='".$huid."' and site=2",'*');
            $my = array();
            $my['uid'] = $is_user['uid'];
            $my['id'] = $is_user['id'];
            $this->hcn_my = $my;
        }

        $this->copyRemoteServers = array();
        $this->copyRemoteServers['sys-chatbot'] = array('need_db'=>false);
        $this->copyRemoteServers['cloud-chatbot'] = array('need_db'=>true);
        $this->copyRemoteServers['cv1-chatbot'] = array('need_db'=>false);
	}

    // 테이블명 추출
	public function table($lastname){
		global $table;
	    return $table[$this->module.$lastname];
	}

    // mobile 여부 값 추출
	public function is_mobile(){
		global $g;

		if($g['mobile']&&$_SESSION['pcmode']!='Y') return true;
        else return false;
	}

    // device 정보 추출
	public function getUserAgent(){
		$device = '';
		$result = array();

		if( stristr($_SERVER['HTTP_USER_AGENT'],'ipad') ) {
			$device = "ipad";
		} else if( stristr($_SERVER['HTTP_USER_AGENT'],'iphone') || strstr($_SERVER['HTTP_USER_AGENT'],'iphone') ) {
			$device = "iphone";
		} else if( stristr($_SERVER['HTTP_USER_AGENT'],'blackberry') ) {
			$device = "blackberry";
		} else if( stristr($_SERVER['HTTP_USER_AGENT'],'android') ) {
			$device = "android";
		}

		if( $device ) {
			return $device;
		} return false; {
			return false;
		}
	}

	public function getAssoc($query){
	    $rows=array();
	    $result=$this->db->query($query);
	  	while ($row=$this->db->fetch_assoc($result)) $rows[]=$row;

		return $rows;
	}

	public function getArray($query){
	    $rows=array();
	    $result=$this->db->query($query);
	  	while ($row=$this->db->fetch_array($result)) $rows[]=$row;

		return $rows;
	}

	public function getRows($query){
	    $result=$this->db->query($query);
	  	$rows=$this->db->num_rows($result);
		return $rows;
	}

	// uid 기준 row 데이타 추출
	public function getUidData($table,$uid){
        $query= sprintf("SELECT * FROM `%s` WHERE `uid` = %s",$table,$this->db->real_escape_string($uid));
        $rows = $this->getArray($query);

        return $rows[0];
    }

	function showError($error) {
		global $LNG;
		$message = '<div class="message-container"><div class="message-content"><div class="message-header">'.$LNG['error_ttl'.$error].'</div><div class="message-inner">'.$LNG["$error"].'</div></div></div>';

		return array($message, 1);

	}

}




// 템플릿 파싱 및 데이타 치환
class skin {
	var $filename;

	public function __construct($filename) {
		$this->filename = $filename;
	}

	public function mk($filename) {
		$this->filename = $filename;
		return $this->make();
	}

	public function make($bLib='') {
		global $g, $CONF;
		if($bLib == '') {
		    $file = sprintf($CONF['theme_path'].'/'.$CONF['theme_name'].'/html/%s.html', $this->filename);
		} else {
		    $file = sprintf($g['dir_module'].'lib/htmlForm/%s.html', $this->filename);
		}
		$fh_skin = fopen($file, 'r');
		$skin = @fread($fh_skin, filesize($file));
		fclose($fh_skin);
		return $this->parse($skin);
	}

    public function make2() {
        global $CONF;
        $file = sprintf($CONF['theme_path'].'/'.$CONF['theme_name'].'/html/%s.html', $this->filename);
        $fh_skin = fopen($file, 'r');
        $skin = @fread($fh_skin, filesize($file));
        fclose($fh_skin);
        return $skin;
        //return $this->parseNoReplace($skin);
    }

	private function parse($skin) {
		global $TMPL, $LNG;

        $skin = preg_replace_callback('/{\$lng->(.+?)}/i', create_function('$matches', 'global $LNG; return $LNG[$matches[1]];'), $skin);
        $skin = preg_replace_callback('/{\$([a-zA-Z0-9_\@\&\%]+)}/', create_function('$matches', 'global $TMPL; return (isset($TMPL[$matches[1]])?$TMPL[$matches[1]]:"");'), $skin);

		return $skin;
	}
}

// 문자 숫자 계산
class calString{

    function getUnitData($data){
        // 천만, 백만 이 만, 천 보다 우선순위를 높게 한단.  2천만원, 5백만원 형태
        $array = array(
            "조"=>1000000000000,
            "억"=>100000000,
            "만"=>10000,
            "천"=>1000,
            "백"=>100,
            "십|열"=>10,
        );

        $data['array'] = $array;

        if(isset($data['numString'])){
            $han = $data['numString'];
            $result = $this->getHanToNum($data);
        }else if($data['regexPt']){
            $result = $this->getRegexFromArray($data);
        }

        return $result;

    }

    function getNumData($data){

        $array = array(
            "일[^곱]|한|하나|원"=>1,
            "이|둘|두|투"=>2,
            "삼|셋|세|쓰리"=>3,
            "사|넷|네|포"=>4,
            "오|다섯"=>5,
            "육|여섯"=>6,
            "칠|일곱"=>7,
            "팔|여덟|여덜"=>8,
            "구|아홉"=>9,
            "십|열"=>10,
            "스물|스무"=>20,
            "서른"=>30,
            "마흔"=>40,
            "쉰"=>50,
            "예순|얘순"=>60,
            "일흔"=>70,
            "여든"=>80,
            "아흔"=>90,
        );

        $data['array'] = $array;

        if(isset($data['numString'])){
            $han = $data['numString'];
            $result = $this->getHanToNum($data);
        }else if($data['regexPt']){
            $result = $this->getRegexFromArray($data);
        }

        return $result;
    }

    function getHanToNum($data){
        $numString = $data['numString'];
        $array = $data['array'];
        foreach ($array as $key => $value) {
            if(preg_match('/('.$key.')/',$numString)){
                $result= $value;
                break;
            }else{
                $result= $numString; // 숫자가 올 경우 그냥 리턴
            }
        }
        return $result;
    }

    function getRegexFromArray($data){
        if($data['array']){
            $array = $data['array'];
            $regexPt ='(';
            foreach ($array as $key => $value) {
                if(strstr($key,'|')) $regexPt.='('.$key.')|';
                else $regexPt.= $key.'|';
            }
            $result= rtrim($regexPt,'|').')';

        }else if($data['priceNum']) $result = "([0-9]+|[0-9]{1,3}(,[0-9]{3}))";

        return $result;

    }

    function getRegexPt($data){
        $type = $data['type'];
        if($type =='hanNum'|| $type =='hanNumUnit'){ // 일/이....or 십/백/천...
            $data['regexPt'] = true;
            if($type=='hanNum') $result = $this->getNumData($data);
            else if($type=='hanNumUnit') $result = $this->getUnitData($data);

        }else if($type =='priceNum'){ // 3000 or 3,000
            $result = "([0-9]+|[0-9]{1,3}(,[0-9]{3}))";
        }

        return $result;

    }

}

// 안드로이드  push 전송 클래스
class GCMPushMessage {

    var $url = 'https://android.googleapis.com/gcm/send';
    var $serverApiKey = "AIzaSyB0UDlyX5RzZUWpv1kliSA5frpzoEz41EQ";
    var $devices = array();

    function __construct($deviceIds){
        //$this->serverApiKey = $apiKeyIn;
        $this->setDevices($deviceIds);
    }

    function setDevices($deviceIds){

        if(is_array($deviceIds)){
            $this->devices = $deviceIds;
        } else {
            $this->devices = array($deviceIds);
        }

    }

    function send($title, $message, $link) {

        if(!is_array($this->devices) || count($this->devices) == 0){
            $this->error("No devices set");
        }

        if(strlen($this->serverApiKey) < 8){
            $this->error("Server API Key not set");
        }
        $fields = array(
              'registration_ids'  => $this->devices,
                'data'              => array( 'title' => $title, 'message' => $message, 'link' => $link ),
        );

        $headers = array(
            'Authorization: key=' . $this->serverApiKey,
            'Content-Type: application/json'
        );

        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt( $ch, CURLOPT_URL, $this->url );

        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );

        // Execute post
        $result = curl_exec($ch);

        // Close connection
        curl_close($ch);

        return $result;
    }

    function error($msg){
        echo "Android send notification failed with error:";
        echo "\t" . $msg;
        exit(1);
    }
}

//namespace Statickidz;
/**
 * GoogleTranslate.class.php
 *
 * Class to talk with Google Translator for free.
 *
 * @package PHP Google Translate Free;
 * @category Translation
 * @author Adrián Barrio Andrés
 * @author Paris N. Baltazar Salguero <sieg.sb@gmail.com>
 * @copyright 2016 Adrián Barrio Andrés
 * @license https://opensource.org/licenses/GPL-3.0 GNU General Public License 3.0
 * @version 2.0
 * @link https://statickidz.com/
 */
/**
 * Main class GoogleTranslate
 *
 * @package GoogleTranslate
 *
 */
class GoogleTranslate
{
    /**
     * Retrieves the translation of a text
     *
     * @param string $source
     *            Original language of the text on notation xx. For example: es, en, it, fr...
     * @param string $target
     *            Language to which you want to translate the text in format xx. For example: es, en, it, fr...
     * @param string $text
     *            Text that you want to translate
     *
     * @return string a simple string with the translation of the text in the target language
     */
    public static function translate($source, $target, $text)
    {
        // Request translation
        $response = self::requestTranslation($source, $target, $text);
        // Get translation text
        // $response = self::getStringBetween("onmouseout=\"this.style.backgroundColor='#fff'\">", "</span></div>", strval($response));
        // Clean translation
        $translation = self::getSentencesFromJSON($response);
        return $translation;
    }
    /**
     * Internal function to make the request to the translator service
     *
     * @internal
     *
     * @param string $source
     *            Original language taken from the 'translate' function
     * @param string $target
     *            Target language taken from the ' translate' function
     * @param string $text
     *            Text to translate taken from the 'translate' function
     *
     * @return object[] The response of the translation service in JSON format
     */
    protected static function requestTranslation($source, $target, $text)
    {
        // Google translate URL
        $url = "https://translate.google.com/translate_a/single?client=at&dt=t&dt=ld&dt=qca&dt=rm&dt=bd&dj=1&hl=es-ES&ie=UTF-8&oe=UTF-8&inputm=2&otf=2&iid=1dd3b944-fa62-4b55-b330-74909a99969e";
        $fields = array(
            'sl' => urlencode($source),
            'tl' => urlencode($target),
            'q' => urlencode($text)
        );
        // URL-ify the data for the POST
        $fields_string = "";
        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');
        // Open connection
        $ch = curl_init();
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'AndroidTranslate/5.3.0.RC02.130475354-53000263 5.1 phone TRANSLATE_OPM5_TEST_1');
        // Execute post
        $result = curl_exec($ch);
        // Close connection
        curl_close($ch);
        return $result;
    }
    /**
     * Dump of the JSON's response in an array
     *
     * @param string $json
     *            The JSON object returned by the request function
     *
     * @return string A single string with the translation
     */
    protected static function getSentencesFromJSON($json)
    {
        $sentencesArray = json_decode($json, true);
        $sentences = "";
        foreach ($sentencesArray["sentences"] as $s) {
            $sentences .= isset($s["trans"]) ? $s["trans"] : '';
        }
        return $sentences;
    }
}

// 문자열로부터 DATE, TIME 값 배열로 파싱
class DateParse {
    var $nRegIdx, $set, $setStr, $matchVal, $aUnit_Index, $aWeek_Day, $aParsing_Tokens, $aExceptShiftUnit, $aLoc, $aResult, $matchWord, $matchResult;

    public function __construct() {
        $this->nRegIdx = null;
        $this->matchResult = array();
        $this->aResult = array();
        $this->set = array();
        $this->setStr = array();
        $this->matchVal = array();
        $this->matchWord = "";
        $this->aUnit_Index = array(1=>'second', 2=>'minute', 3=>'hour', 4=>'day', 5=>'week', 6=>'month', 7=>'year');
        $this->aWeek_Day = array(0=>'sunday', 1=>'monday', 2=>'tuesday', 3=>'wednesday', 4=>'thursday', 5=>'friday', 6=>'saturday');
        $this->aWeek_DayKr = array(0=>'일요일', 1=>'월요일', 2=>'화요일', 3=>'수요일', 4=>'목요일', 5=>'금요일', 6=>'토요일');
        $this->aParsing_Tokens = array(
            'yyyy'=>array('param'=>'year', 'src'=>'\\d{4}'),
            'MM'=>array('param'=>'month', 'src'=>'[01]?\\d'),
            'dd'=>array('param'=>'date', 'src'=>'[0123]?\\d'),
            'hh'=>array('param'=>'hour', 'src'=>'[0-2]?\\d'),
            'mm'=>array('param'=>'minute', 'src'=>'[0-5]\\d'),
            'ss'=>array('param'=>'second', 'src'=>'[0-5]\\d(?:[,.]\\d+)?'),
            'yy'=>array('param'=>'year', 'src'=>'\\d{2}'),
            'y'=>array('param'=>'year', 'src'=>'\\d'),
            'yearSign'=>array('src'=>'[+-]', 'sign'=>true),
            'timestamp'=>array('src'=>'\\d+')
        );
        $this->aExceptShiftUnit = array(
            '주'=>"재작, 작, 당, 올, 후내, 내후",
            '달'=>"재작, 작, 금, 당, 올, 차, 후내, 내후",
            '월'=>"재작, 작, 지난, 저번, 이번, 올, 다음, 담, 내, 차, 후내, 내후, 다다음, 다담",
            '년'=>"지지난, 지난, 저번, 이전, 이번, 당, 올, 다음, 담, 차",
            '해'=>"재작, 작, 이전, 전, 이번, 금, 담, 내, 차, 후내, 내후"
        );
        $chPT_Shift = "지지난|전전|재작|작|지난|저번|이전|전|이번|금|당|올|다음|담|내|차|후내|내후|다다음|다담";
        $chPT_ShiftUnit = "주|달|월|년|해";
        $chPT_Unit = "밀리초|초|분|시간|일|주|달|개월|년|해";
        $chPT_Weekday = "[일월화수목금토]요일|주말";
        $chPT_Day = "그끄저께|그그저께|아레|그저께|그제|전전날|전날|어제|오늘|금일|내일모레|내일 모레|내일|명일|익일|모레|글피";
        $chPT_Numeral = "영|일|이|삼|사|오|육|유|칠|팔|구|십|시|백|천";
        $chPT_Ordinal = "한|첫|두|둘|세|셋|네|넷|다섯|여섯|일곱|여덟|아홉|열";
        $chPT_DayOrdinal = "하루|이틀|사흘|나흘|닷새|엿새|이레|여드레|아흐레|열흘";
        $chPT_HourNumeral = "영|".str_replace(array('첫|','둘|','셋|','넷|'), "", $chPT_Ordinal);
        $chPT_Midday = "정오|자정";
        $chPT_Ampm = "오전|오후|아침|저녁";
        $chPT_Sign = "전|앞|후|뒤|이따가|있다가";
        $this->aLoc = array(
            "compiledFormats"=>array(
                array(
                    "reg"=>"~ *(".$chPT_Shift.")? ?(".$chPT_ShiftUnit.")? (?:(\d{4}|\d{2}|(?:".$chPT_Numeral."|".$chPT_Ordinal.")+)년)? ?(?:([01]?\d|(?:".$chPT_Numeral.")+)월)? ?(?:([1-6]|(?:".$chPT_Ordinal.")+)째주) ?(".$chPT_Weekday.")? *~iu",
                    "to"=>array("shift", "unit", "year", "month", "week", "weekday")
                ),
                array(
                    "reg"=>"~ *(".$chPT_Shift.") ?(".$chPT_ShiftUnit.") ?(?:([01]?\d|(?:".$chPT_Numeral.")+)월)? ?(?:([0123]?\d|(?:".$chPT_Numeral.")+)일)? ?(".$chPT_Weekday.")? ?(".$chPT_Midday.")? ?(".$chPT_Ampm.")? ?(?:([0-2]?\d|(?:".$chPT_HourNumeral.")+)시)? ?(반)? ?(?:([0-5]?\d|(?:".$chPT_Numeral.")+)분)? ?(?:([0-5]?\d|(?:".$chPT_Numeral.")+)초)? *~iu",
                    "to"=>array("shift", "unit", "month", "date", "weekday", "midday", "ampm", "hour", "half", "minute", "second")
                ),
                array(
                    "reg"=>"~ *(".$chPT_Day.") ?(".$chPT_Midday.")? ?(".$chPT_Ampm.")? ?(?:([0-2]?\d|(?:".$chPT_HourNumeral.")+)시)? ?(반)? ?(?:([0-5]?\d|(?:".$chPT_Numeral.")+)분)? ?(?:([0-5]?\d|(?:".$chPT_Numeral.")+)초)? *~iu",
                    "to"=>array("day", "midday", "ampm", "hour", "half", "minute", "second")
                ),
                array(
                    "reg"=>"~ *(\d+|(?:".$chPT_Numeral."|".$chPT_Ordinal.")+)(".$chPT_Unit.") ?(".$chPT_Sign.") ?(?:([01]?\d|(?:".$chPT_Numeral.")+)월)? ?(?:([0123]?\d|(?:".$chPT_Numeral.")+)일)? ?(".$chPT_Weekday.")? ?(".$chPT_Midday.")? ?(".$chPT_Ampm.")? ?(?:([0-2]?\d|(?:".$chPT_HourNumeral.")+)시)? ?(반)? ?(?:([0-5]?\d|(?:".$chPT_Numeral.")+)분)? ?(?:([0-5]?\d|(?:".$chPT_Numeral.")+)초)? *~iu",
                    "to"=>array("num", "unit", "sign", "month", "date", "weekday", "midday", "ampm", "hour", "half", "minute", "second")
                ),
                array(
                    "reg"=>"~ *(?:(\d{4}|\d{2}|(?:".$chPT_Numeral."|".$chPT_Ordinal.")+)년)? ?(?:([01]?\d|(?:".$chPT_Numeral.")+)월)? ?(?:([0123]?\d|(?:".$chPT_Numeral.")+)일) ?(".$chPT_Weekday.")? ?(".$chPT_Midday.")? ?(".$chPT_Ampm.")? ?(?:([0-2]?\d|(?:".$chPT_HourNumeral.")+)시)? ?(반)? ?(?:([0-5]?\d|(?:".$chPT_Numeral.")+)분)? ?(?:([0-5]?\d|(?:".$chPT_Numeral.")+)초)? *~iu",
                    "to"=>array("year", "month", "date", "weekday", "midday", "ampm", "hour", "half", "minute", "second")
                ),
                array(
                    "reg"=>"~ *(?:(".$chPT_Shift.") ?(주))? ?(".$chPT_Weekday.") ?(".$chPT_Midday.")? ?(".$chPT_Ampm.")? ?(?:([0-2]?\d|(?:".$chPT_HourNumeral.")+)시)? ?(반)? ?(?:([0-5]?\d|(?:".$chPT_Numeral.")+)분)? ?(?:([0-5]?\d|(?:".$chPT_Numeral.")+)초)? *~iu",
                    "to"=>array("shift", "unit", "weekday", "midday", "ampm", "hour", "half", "minute", "second")
                ),
                array(
                    "reg"=>"~ *(".$chPT_DayOrdinal.") ?(".$chPT_Sign.") ?(?:([01]?\d|(?:".$chPT_Numeral.")+)월)? ?(?:([0123]?\d|(?:".$chPT_Numeral.")+)일)? ?(".$chPT_Weekday.")? ?(".$chPT_Midday.")? ?(".$chPT_Ampm.")? ?(?:([0-2]?\d|(?:".$chPT_HourNumeral.")+)시)? ?(반)? ?(?:([0-5]?\d|(?:".$chPT_Numeral.")+)분)? ?(?:([0-5]?\d|(?:".$chPT_Numeral.")+)초)? *~iu",
                    "to"=>array("day", "sign", "month", "date", "weekday", "midday", "ampm", "hour", "half", "minute", "second")
                ),
                array(
                    "reg"=>"~ *(\d+|(?:".$chPT_Numeral."|".$chPT_Ordinal.")+)(".$chPT_Unit.") ?(".$chPT_Sign.") *~iu",
                    "to"=>array("num", "unit", "sign")
                ),
                array(
                    "reg"=>"~ *(?:(\d{4}|\d{2}|(?:".$chPT_Numeral."|".$chPT_Ordinal.")+)년)? ?(?:([01]?\d|(?:".$chPT_Numeral.")+)월)? ?(?:([1-6]|(?:".$chPT_Ordinal.")+)째주)? ?(?:([0123]?\d|(?:".$chPT_Numeral.")+)일) ?(".$chPT_Weekday.")? ?(".$chPT_Midday.")? ?(".$chPT_Ampm.")? ?(?:([0-2]?\d|(?:".$chPT_HourNumeral.")+)시)? ?(반)? ?(?:([0-5]?\d|(?:".$chPT_Numeral.")+)분)? ?(?:([0-5]?\d|(?:".$chPT_Numeral.")+)초)? *~iu",
                    "to"=>array("year", "month", "week", "date", "weekday", "midday", "ampm", "hour", "half", "minute", "second")
                ),
                array(
                    "reg"=>"~ *(?:(\d{4}|\d{2}|(?:".$chPT_Numeral."|".$chPT_Ordinal.")+)년) ?(?:([01]?\d|(?:".$chPT_Numeral.")+)월) ?(?:([1-6]|(?:".$chPT_Ordinal.")+)째주)? *~iu",
                    "to"=>array("year", "month", "week")
                ),
                array(
                    "reg"=>"~ *(?:(\d{4}|\d{2}|(?:".$chPT_Numeral."|".$chPT_Ordinal.")+)년)? ?(?:([01]?\d|(?:".$chPT_Numeral.")+)월) *~iu",
                    "to"=>array("year", "month")
                ),
                array(
                    "reg"=>"~ *(?:(\d{4}|\d{2}|(?:".$chPT_Numeral."|".$chPT_Ordinal.")+)년) *~iu",
                    "to"=>array("year")
                ),
                array(
                    "reg"=>"~ *(\d{4})[-.\/]([01]?\d)(?:[-.\/]([0123]?\d)) ?(".$chPT_Ampm.")? ?(?:([0-2]?\d|(?:".$chPT_HourNumeral.")+)시)? ?(반)? ?(?:([0-5]?\d|(?:".$chPT_Numeral.")+)분)? ?(?:([0-5]?\d|(?:".$chPT_Numeral.")+)초)? *~iu",
                    "to"=>array("yyyy", "MM", "dd", "ampm", "hour", "half", "minute", "second")
                ),
                array(
                    "reg"=>"~ *([0-9]{1,2}):([0-9]{1,2}+)(?::([0-9]{1,2}))? *~iu",
                    "to"=>array("hour", "minute", "second")
                ),
                array(
                    "reg"=>"~ *(".$chPT_Ampm.")? ?([0-2]?\d|(?:".$chPT_HourNumeral.")+)(?::|시) ?(반)? ?(?:([0-5]?\d|(?:".$chPT_Numeral.")+)(?::|분))? ?(?:([0-5]?\d|(?:".$chPT_Numeral.")+)(초)?)? *~iu",
                    "to"=>array("ampm", "hour", "half", "minute", "second")
                )
            ),
            "ampmMap"=>array("오전"=>0, "아침"=>0, "오후"=>1, "저녁"=>1),
            "dayMap"=>array(
                "그끄저께"=>-3, "그그저께"=>-3, "아레"=>-2, "그저께"=>-2, "그제"=>-2, "전전날"=>-2, "전날"=>-1, "어제"=>-1, "오늘"=>0, "금일"=>0,
                "내일"=>1, "명일"=>1, "익일"=>1, "모레"=>2, "내일모레"=>2, "내일 모레"=>2, "글피"=>3,
                "하루"=>1, "이틀"=>2, "사흘"=>3, "나흘"=>4, "닷새"=>5, "엿새"=>6, "이레"=>7, "여드레"=>8, "아흐레"=>9, "열흘"=>10
            ),
            "halfMap"=>array("반"=>0.5),
            "middayMap"=>array("정오"=>12, "자정"=>24),
            "numeralMap"=>array(
                "영"=>0, "일"=>1, "한"=>1, "이"=>2, "두"=>2, "둘"=>2, "삼"=>3, "세"=>3, "셋"=>3, "사"=>4, "네"=>4, "넷"=>4, "오"=>5, "다섯"=>5,
                "육"=>6, "유"=>6, "여섯"=>6, "칠"=>7, "일곱"=>7, "팔"=>8, "여덟"=>8, "구"=>9, "아홉"=>9, "십"=>10, "시"=>10, "열"=>10, "백"=>100, "천"=>1000
            ),
            "shiftMap"=>array(
                "지지난"=>-2, "전전"=>-2, "재작"=>-2, "작"=>-1, "지난"=>-1, "저번"=>-1, "이전"=>-1, "전"=>-1, "이번"=>0, "금"=>0, "당"=>0, "올"=>0,
                "다음"=>1, "담"=>1, "내"=>1, "차"=>1, "후내"=>2, "내후"=>2, "다다음"=>2, "다담"=>2
            ),
            "signMap"=>array("전"=>-1, "앞"=>-1, "후"=>1, "뒤"=>1, "이따가"=>1, "있다가"=>1),
            "unitMap"=>array("밀리초"=>0, "초"=>1, "분"=>2, "시간"=>3, "일"=>4, "주"=>5, "달"=>6, "개월"=>6, "월"=>6, "년"=>7, "해"=>7),
            "weekdayMap"=>array("일요일"=>0, "월요일"=>1, "화요일"=>2, "수요일"=>3, "목요일"=>4, "금요일"=>5, "토요일"=>6, "주말"=>6),
            "weekMap"=>array("첫"=>1, "둘"=>2, "두"=>2, "세"=>3, "셋"=>3, "네"=>4, "넷"=>4, "다섯"=>5, "여섯"=>6),
        );
    }
    public function getDateParse($data) {
        $chStr = $data['clean_input'];
        if (!$chStr || !is_string($chStr)) return "";
        $chStr = strtolower($chStr);
        $dDateTime = date("Y-m-d H:i:s");
        $aDate = explode(" ", $dDateTime);
        $dDate = $aDate[0];
        $dTime = $aDate[1];
        $nDateTimeStamp = (strtotime('sunday', strtotime($dDate)) > strtotime($dDate)) ? strtotime('last sunday', strtotime($dDate)) : strtotime('sunday', strtotime($dDate));
        $dEndDateTime = "";
        $bMatch = false;
        $bExcept = false;
        $bReserve = isset($data['bReserve']) && $data['bReserve'] == true ? true : false;
        $this->set = array();
		$this->setStr = array();
		$this->matchVal = array();
        $this->matchResult = array();

        foreach($this->aLoc['compiledFormats'] as $idx=>$dif) {
            preg_match($dif['reg'], $chStr, $aMatch);
            if ($aMatch) {
                $bMatch = true;
				$_weekSet = $_weekdaySet = false;
				$this->nRegIdx = $idx;
				$this->matchResult = $aMatch;

                $this->setCacheFormat($dif, $idx);
                $set = $this->getFormatParams($aMatch, $dif, $dDate);
                /*
                if (isset($set['set']['shift']) && count($set['set']) < 3) {
                    $bMatch = false;
                    break;
                }
                */
                $this->set = $set['set'];
                $this->setStr = $set['str'];

                // 복수 날짜일 경우 첫번째 검출 파라미터 병합
                if(isset($this->aResult['data']) && count($this->aResult['data']) > 0) {
                    $this->set = array_merge($this->aResult['data'][0]['set'], $this->set);
                    $this->setStr = array_merge($this->aResult['data'][0]['setStr'], $this->setStr);
                }

                if (isset($this->set['midday']) && $this->set['midday']) {
					$this->getHandleMidday($this->set['midday']);
				}
				if (isset($this->set['ampm']) && $this->set['ampm']) {
					$this->getHandleAmpm($this->set['ampm']);
				}
                if (isset($this->set['shift']) && (isset($this->set['unit']) && $this->set['unit'])) {
                    if ($this->set['shift'] == 0) {
					    if ($this->set['unit'] == 5) {
					        $_todayWeek = date('w');
					        $dDate = date('Y-m-d', strtotime('-'.($_todayWeek-1).'days'));
					    }
                        if ($this->set['unit'] == 6) $dDate = date('Y-m')."-01";
                        if ($this->set['unit'] == 7) $dDate = date('Y')."-01-01";
                        $dTime = '00:00:00';
                    } else {
                        $chUnit = $this->aUnit_Index[$this->set['unit']];
                        if ($this->set['shift'] == -1 && $chUnit == 'month') {
                            // strtotime -1 month 문제 회피
                            $dDate = date('Y-m-d', strtotime('first day of '.$this->set['shift'].' '.$chUnit));
                        } else {
                            $dDate = date('Y-m-d', strtotime($this->set['shift'].' '.$chUnit));
                            if ($this->set['unit'] == 5) {
    					        $_todayWeek = date('w', strtotime($dDate));
    					        $dDate = date('Y-m-d', strtotime($dDate.' -'.($_todayWeek-1).'days'));
    					    }
                            if ($this->set['unit'] == 6) $dDate = substr($dDate,0,7)."-01";
                            if ($this->set['unit'] == 7) $dDate = substr($dDate,0,4)."-01-01";
                        }
                        $dTime = '00:00:00';
                    }
                    if (isset($this->set['week'])) {
                        $dDate = $this->getWeekNumDateInMonth($dDate, $this->set['week']);
                        $this->matchVal['day'] = $this->setStr['week'].'째주';
                        $_weekSet = true;
                    }
                    if (isset($this->set['weekday'])) {
                        $nDateTimeStamp = (strtotime('sunday', strtotime($dDate)) > strtotime($dDate)) ? strtotime('last sunday', strtotime($dDate)) : strtotime('sunday', strtotime($dDate));
                        $dDate = date('Y-m-d', strtotime($this->aWeek_Day[$this->set['weekday']], $nDateTimeStamp));
                        $_weekdaySet = true;
                    }
                    //-------------------------------------------------------------------
                    $this->matchVal['year'] = $this->setStr['shift'].$this->setStr['unit'];
                    if ($this->set['unit'] == 5 || isset($this->set['weekday'])) {
                        $this->matchVal['month'] = $this->setStr['shift'].$this->setStr['unit'];
                        $this->matchVal['day'] = $this->setStr['weekday'];
                        $this->matchVal['weekday'] = $this->setStr['weekday'];
                    }
                    if ($this->set['unit'] == 6) $this->matchVal['month'] = $this->matchVal['day'] = $this->setStr['shift'].$this->setStr['unit'];
                    //-------------------------------------------------------------------
                } else if ((isset($this->set['sign']) && $this->set['sign']) && (isset($this->set['unit']) && $this->set['unit']) && isset($this->set['num'])) {
                    $plus = $this->set['sign'] > 0 ? '+' : '-';
                    $chUnit = $this->aUnit_Index[$this->set['unit']];
                    if ($this->set['unit'] > 3) {
                        $dDate = date('Y-m-d', strtotime($plus.$this->set['num'].' '.$chUnit));
                        $dTime = '00:00:00';
                    } else {
                        $dDateTime = date('Y-m-d H:i:s', strtotime($plus.$this->set['num'].' '.$chUnit));
                        $aDate = explode(" ", $dDateTime);
                        $dDate = $aDate[0];
                        $dTime = $aDate[1];
                    }
                    //-------------------------------------------------------------------
                    $this->matchVal['year'] = $this->setStr['num'].$this->setStr['unit'].$this->setStr['sign'];
                    if ($this->set['unit'] == 4) {
                        $this->matchVal['month'] = $this->setStr['num'].$this->setStr['unit'].$this->setStr['sign'];
                        $this->matchVal['day'] = $this->setStr['num'].$this->setStr['unit'].$this->setStr['sign'];
                    }
                    if ($this->set['unit'] == 5) {
                        $this->matchVal['month'] = $this->setStr['num'].$this->setStr['unit'].$this->setStr['sign'];
                        $this->matchVal['day'] = $this->setStr['num'].$this->setStr['unit'].$this->setStr['sign'];
                    }
                    if ($this->set['unit'] == 6) $this->matchVal['month'] = $this->matchVal['day'] = $this->setStr['num'].$this->setStr['unit'].$this->setStr['sign'];
                    //-------------------------------------------------------------------
                } else {
                    if (isset($this->set['weekday'])) {
                        $nDateTimeStamp = (strtotime('sunday', strtotime($dDate)) > strtotime($dDate)) ? strtotime('last sunday', strtotime($dDate)) : strtotime('sunday', strtotime($dDate));
                        $dDate = date('Y-m-d', strtotime($this->aWeek_Day[$this->set['weekday']], $nDateTimeStamp));
                        $_weekdaySet = true;
                        // 예약 시 주어진 요일이 오늘보다 클 경우 다음주 같은 요일의 날짜
                        if($bReserve == true) {
                            if(isset($data['able_weeks']) && count($data['able_weeks']) > 0) {
                                if(array_key_exists($this->aWeek_DayKr[$this->set['weekday']], $data['able_weeks'])) {
                                    $dDate = $data['able_weeks'][$this->aWeek_DayKr[$this->set['weekday']]];
                                }
                            } else {
                                // 220401 예약 시 주어진 요일의 날짜가 오늘 이전이라면 다음주 같은 요일 날짜
                                if(time() > strtotime($dDate)) {
                                    $dDate = date('Y-m-d', strtotime('next '.$this->aWeek_Day[$this->set['weekday']], strtotime($dDate)));
                                }
                            }
                        }
                        $dTime = '00:00:00';
                    } else {
                        if (isset($this->set['day'])) {
                            $dDate = date('Y-m-d', strtotime($this->set['day']. 'day'));
                            $dTime = '00:00:00';
                        }
                    }
                    //-------------------------------------------------------------------
                    if (isset($this->set['day'])) {
                        $this->matchVal['year'] = $this->matchVal['month'] = $this->matchVal['day'] = $this->setStr['day'];
                    }
                    if (isset($this->set['weekday'])) {
                        $this->matchVal['weekday'] = $this->setStr['weekday'];
                    }
                    //-------------------------------------------------------------------
                }
                if (isset($this->set['year']) && $this->set['year']) {
				    if ($this->set['year'] < 100) {
				        $year = $this->set['year'] < 10 ? '0'.$this->set['year'] : $this->set['year'];
				        $this->set['year'] = $this->set['year'] > date('y') ? '19'.$year : '20'.$year;
				    }
                    $dDate = $this->set['year'].'-01-01';
                    $dTime = '00:00:00';
                    $this->matchVal['year'] = $this->setStr['year'].'년';
                }
                if (isset($this->set['month']) && $this->set['month']) {
                    $dDate = substr($dDate,0,4).'-'.sprintf('%02d', $this->set['month']).'-01';
                    $dTime = '00:00:00';
                    $this->matchVal['month'] = $this->setStr['month'].'월';
                }
                if (isset($this->set['date']) && $this->set['date']) {
                    $_dDate = $dDate;
                    $dDate = substr($dDate,0,7).'-'.sprintf('%02d', $this->set['date']);
                    // 예약 시 주어진 일이 10이하이고 오늘보다 작을 경우 다음 달의 일로 변경
                    if($bReserve && !isset($this->set['year']) && !isset($this->set['month'])) {
                        if((int)substr($_dDate,8) > $this->set['date'] && $this->set['date'] <= 10) {
                            $aDate = explode('-', $dDate);
                            $dDate = date('Y-m-d', mktime(0,0,0,($aDate[1]+1),$aDate[2],$aDate[0]));
                        }
                    }
                    $dTime = '00:00:00';
                    $this->matchVal['day'] = $this->setStr['date'].'일';
                }
                if (!$_weekSet && isset($this->set['week'])) {
                    $dDate = $this->getWeekNumDateInMonth($dDate, $this->set['week']);
                    $this->matchVal['day'] = $this->setStr['week'].'째주';
                    if (isset($this->set['weekday'])) {
                        $nDateTimeStamp = (strtotime('sunday', strtotime($dDate)) > strtotime($dDate)) ? strtotime('last sunday', strtotime($dDate)) : strtotime('sunday', strtotime($dDate));
                        $dDate = date('Y-m-d', strtotime($this->aWeek_Day[$this->set['weekday']], $nDateTimeStamp));
                        $this->matchVal['day'] = $this->setStr['week'].'째주 '.$this->setStr['weekday'];
                        $this->matchVal['weekday'] = $this->setStr['weekday'];
                    }
                }
                if (isset($this->set['hour'])) {
                    // 예약 시 주어진 시간을 예약 시작 시간 기준으로 오전 오후 변경
                    if($bReserve && !isset($this->set['ampm'])) {
                        // 예약 시간을 08시로 설정
                        if($this->set['hour'] < 8) {
                            $this->set['hour'] = ($this->set['hour']+12);
                            $this->set['ampm'] = 1;
                        }

                        // 예약 시 날짜 정보 없을 경우
                        if(!isset($this->set['date']) && !isset($this->set['day']) && !isset($this->set['weekday'])) {
                            $dDate = "";
                        }
                    }
                    $dTime = sprintf('%02d', $this->set['hour']).':00:00';
                    $this->matchVal['hour'] = isset($this->set['ampm']) ? $this->setStr['ampm'].' '.$this->setStr['hour'].'시' : $this->setStr['hour'].'시';
                }
                if (isset($this->set['half'])) {
                    $dTime = sprintf('%02d', $this->set['hour']).':30:00';
                    $this->matchVal['minute'] = $this->setStr['half'];
                } else {
                    if ($this->set['minute']) {
                        $dTime = sprintf('%02d', $this->set['hour']).':'.sprintf('%02d', $this->set['minute']).':00';
                        $this->matchVal['minute'] = $this->setStr['minute'].'분';
                    }
                }
                if (isset($this->set['second']) && $this->set['second']) {
                    $dTime = sprintf('%02d', $this->set['hour']).':'.sprintf('%02d', $this->set['minute']).':'.sprintf('%02d', $this->set['second']);
                    $this->matchVal['second'] = $this->setStr['second'].'초';
                }
                // 최종 출력
                $dDateTime = $dDate.' '.$dTime;
                break;
            }
        }

        if($bMatch) {
            $_result = $this->getDateSplit($dDateTime);

            // shift, unit 정보에서 쓰지 않는 단어일 경우 제외
            if(isset($this->setStr['shift']) && isset($this->setStr['unit']) && array_key_exists($this->setStr['unit'], $this->aExceptShiftUnit)) {
                $_shift = explode(",", $this->aExceptShiftUnit[$this->setStr['unit']]);
                $_shift = array_map("trim", $_shift);
                if(in_array(trim($this->setStr['shift']), $_shift)) $bExcept = true;
            }

            $_matchStr = $this->matchResult[0];

            if(!$bExcept) {
                $_result['_matchStr'] = trim($_matchStr);
                $this->aResult['data'][] = $_result;

                $this->matchWord .=trim($_matchStr).",";
            }

            $data['clean_input'] = preg_replace("/".$_matchStr."/iu", "", $data['clean_input']);
            $this->getDateParse($data);
        }
        $this->aResult['matchWord'] = rtrim($this->matchWord, ",");
        return $this->aResult;
    }

    private function setCacheFormat($dif, $i) {
        array_splice($this->aLoc['compiledFormats'], $i, 1);
        array_unshift($this->aLoc['compiledFormats'], $dif);
    }
    private function getFormatParams($aMatch, $dif, $dDate="") {
        $set = array();
        $set['set'] = array();
        $set['str'] = array();
        for ($i=0, $nCnt=count($dif['to']); $i<$nCnt; $i++) {
            $field = $dif['to'][$i];
            $str = $aMatch[$i+1];
            if ($str) {
                if ($field == 'yy' || $field == 'y') {
                    $field = 'year';
                    $val = $this->getYearFromAbbreviation($str, $dDate);
                } else if (isset($this->aParsing_Tokens[$field]) && $this->aParsing_Tokens[$field]) {
                    $token = $this->aParsing_Tokens[$field];
                    $field = $token['param'];
                    $val = $this->getParsingTokenValue($token, $str);
                } else {
                    $val = $this->getTokenValue($field, $str);
                }
                $set['set'][$field] = $val;
                $set['str'][$field] = $str;
            }
        }
        return $set;
    }
    private function getTokenValue($field, $str) {
        if (isset($this->aLoc[$field.'Map'])) {
		    $val = $this->aLoc[$field.'Map'][$str];
		}
        if (!is_numeric($val)) {
            $val = $this->getNumber($str);
        }
        return $val;
    }
    private function getNumber($str) {
        $num = $this->aLoc['numeralMap'][$str];
        if ($num) return $num;
        $num = preg_replace("/,/i", ".", $str);
        if (is_numeric($num)) return $num;
        $num = $this->getNumeralValue($str);
        if ($num) {
            $this->aLoc['numeralMap'][$str] = $num;
            return $num;
        }
        return $num;
    }
    private function mb_str_split($str) {
        return preg_split('/(?<!^)(?!$)/u', $str);
    }
    private function getNumeralValue($str) {
        $place = 1; $num = 0; $lastWasPlace; $isPlace; $numeral; $digit; $arr;
        $arr = $this->mb_str_split($str);
        for ($i=(count($arr)-1); $i>=0; $i--) {
            $numeral = $arr[$i];
            $digit = $this->aLoc['numeralMap'][$numeral];
            if (!$digit) $digit = 0;
            $isPlace = $digit > 0 && $digit % 10 == 0;
            if ($isPlace) {
                if ($lastWasPlace) $num += $place;
                if ($i) {
                    $place = $digit;
                } else {
                    $num += $digit;
                }
            } else {
                $num += $digit * $place;
                $place *= 10;
            }
            $lastWasPlace = $isPlace;
        }
        return $num;
    }
    private function getParsingTokenValue($token, $str) {
        $val = "";
        if ($token['val']) {
            $val = $token['val'];
        } else if ($token['sign']) {
            $val = $str == '+' ? 1 : -1;
        } else if ($token['bool']) {
            $val = !!$val;
        } else {
            $val = preg_replace("/,/i", ".", $str);
        }
        return $val;
    }
    private function getYearFromAbbreviation($str, $d, $prefer=0) {
        $val = +($str);
        $val += $val < 50 ? 2000 : 1900;
        if ($prefer) {
            $delta = $val - substr($d,0,4);
            if ($delta / abs($delta) !== $prefer) $val += $prefer * 100;
        }
        return $val;
    }
    private function getHandleAmpm($ampm) {
        if ($ampm == 1 && $this->set['hour'] < 12) {
            $this->set['hour'] += 12;
        } else if ($ampm == 0 && $this->set['hour'] == 12) {
            $this->set['hour'] = 0;
        }
    }
    private function getHandleMidday($midday) {
        if ($midday == 12) {
            $this->set['hour'] = 12;
            $this->set['minute'] = $this->set['second'] = 0;
        } else {
            $this->set['hour'] = $this->set['minute'] = $this->set['second'] = 0;
        }
    }
    private function getWeekNumDateInMonth($dDate, $nNumber=0) {
        $nTime = strtotime($dDate);
        $dFirstMondayDate = date("Y-m-d", strtotime(date("Y-m-01", $nTime)));
        $nFirstWeekday = date('N', strtotime($dFirstMondayDate));
        $chThisNext = ($nFirstWeekday > 4) ? "next" : "this";
        $dFirstWeekDate = date("Y-m-d", strtotime($chThisNext." week", strtotime($dFirstMondayDate)));
        if ($nNumber > 1) {
            $dFirstWeekDate = date("Y-m-d", strtotime("+".($nNumber-1)." week", strtotime($dFirstWeekDate)));
        }
        return $dFirstWeekDate;
    }
    private function getDateSplit($dDateTime, $dEndDateTime="") {
        $nTime = strtotime($dDateTime);
        $aTemp = explode(" ", $dDateTime);
        $aDate = explode("-", $aTemp[0]);
        $aTime = explode(":", $aTemp[1]);
        $aWeek = array("일", "월", "화", "수", "목", "금", "토");

        $aVal = array();
        $aVal['match'] = $this->matchVal;
        $aVal['set'] = $this->set;
        $aVal['setStr'] = $this->setStr;
        $aVal['year'] = $aDate[0];
        $aVal['month'] = $aDate[1];
        $aVal['day'] = $aDate[2];
        $aVal['ampm'] = (int)$aTime[0] >= 12 ? "pm" : "am";
        $aVal['hour'] = $aTime[0];
        $aVal['minute'] = $aTime[1];
        $aVal['second'] = $aTime[2];
        $aVal['weekday'] = $aWeek[date("w", $nTime)];
        return $aVal;
    }
}

// 문자열로부터 숫자 관련 단위 값 파싱
class NumberParse {
    var $chPT_Numeral, $chPT_Ordinal, $aNumeralMap, $aOrdinalMap, $aUnitTypePt;

    public function __construct() {
        $this->chPT_Numeral = '영|일|이|삼|사|오|육|칠|팔|구|십|백|천|만|억|조';
        $this->chPT_Ordinal = '공|제로|한|하나|첫|두|둘|세|셋|네|넷|다섯|여섯|일곱|여덟|아홉|열|스물|서른|마흔|쉰|예순|일흔|여든|아흔';
        $this->aNumeralMap = array(
            '영'=>0,'일'=>1,'이'=>2,'삼'=>3,'세'=>3,'사'=>4,'오'=>5,'육'=>6,'칠'=>7,'팔'=>8,'구'=>9,'십'=>10,'백'=>100,'천'=>1000,'만'=>10000,'억'=>pow(10,8),'조'=>pow(10,12)
        );
        $this->aOrdinalMap = array(
            '공'=>'영','제로'=>'영','한'=>'일','하나'=>'일','첫'=>'일','원'=>'일','두'=>'이','둘'=>'이','투'=>'이','세'=>'삼','셋'=>'삼','네'=>'사','넷'=>'사','다섯'=>'오',
            '여섯'=>'육','일곱'=>'칠','여덟'=>'팔','아홉'=>'구','열'=>'십','스물'=>'이십','서른'=>'삼십','마흔'=>'사십','쉰'=>'오십','예순'=>'육십','일흔'=>'칠십','여든'=>'팔십','아흔'=>'구십'
        );
        $this->aNumberMap = array(
            '0'=>'공','1'=>'일','2'=>'이','3'=>'삼','4'=>'사','5'=>'오','6'=>'육','7'=>'칠','8'=>'팔','9'=>'구'
        );
        $this->aUnitTypePt = array(
            '수량'=>array('개','대','척','방울','그룻','벌','켤레','채','장','정','자루','접','권','단','마리','판','줌','웅큼','필','포기','샷','shot','근'),
            '금액'=>array('원','달러','엔','위안','파운드','프랑','유로'),
            '인원'=>array('명','사람'),
            '순서,횟수'=>array('번'),
            '퍼센트'=>array('%','퍼센트'),
            '연령'=>array('살','세')
        );
    }

    public function getNumberParse($data) {
        $aResult = array();
        $aResult['is_matched'] = false;
        $aResult['data'] = array();
        $aResult['matched'] = $aResult['unitStr'] = $sum = '';

        if($data['user_input']) {
            $chStr = preg_replace('/[\s\.\-]/', '', $data['user_input']);

            // 인풋 문장이 숫자만인지 체크
            preg_match_all('/\d|'.$this->chPT_Numeral.'|'.preg_replace('/한\||첫\||두\||세\||네\|/iu', '', $this->chPT_Ordinal).'/iu', $chStr, $aMatch);
            if($chStr && ($chStr == implode('', $aMatch[0]))) {
                $sum = $this->getNumber($chStr);
                $aResult['is_matched'] = true;
                $aNumberResult = getCheckNumberFormat($sum);
                if($aNumberResult) {
                    $unitStr = $aNumberResult['type'];
                } else {
                    $unitStr = '숫자';
                }
                $aResult['data'][] = array('sum'=>$sum, 'matched'=>$chStr, 'unitStr'=>$unitStr);
            } else {
                // 날짜 시간 문자열 제외
                $chStr = preg_replace('/((\d|'.$this->chPT_Numeral.'|'.$this->chPT_Ordinal.')+[년월일시분초])'.'/iu', '', $chStr);

                $chStrMop = '';
                // 형태소 데이터 있을 경우 수사, 숫자, 가산명사만 처리
                if($data['user_input_mop']) {
                    $aUnit = call_user_func_array('array_merge', $this->aUnitTypePt);
                    $aMop = explode(' ', $data['user_input_mop']);
                    foreach($aMop as $idx=>$mop) {
                        $aWord = explode('|', $mop);
                        if($aWord[1] == 'NR' || $aWord[1] == 'SN' || $aWord[1] == 'MM') {
                            $aWordNext = explode('|', $aMop[($idx+1)]);
                            if($aWord[1] == 'MM') {
                                $_pos = array('MM', 'NNG', 'NNBC');
                                if(!in_array($aWordNext[1], $_pos)) continue;
                            }
                            $chStrMop .=$aWord[0];
                            if(in_array($aWordNext[0], $aUnit)) $chStrMop .=$aWordNext[0];
                        }
                    }
                }

                $chStr = $data['user_input_mop'] ? $chStrMop : $chStr;
                if($chStr) {
                    $unitType = implode(call_user_func_array('array_merge', $this->aUnitTypePt), '|');
                    $unitTypePt = $unitType ? ' ?('.$unitType.')?' : '';
                    $chCon = preg_match('/사람/u', $chStr) ? $this->chPT_Ordinal : $this->chPT_Numeral.'|'.$this->chPT_Ordinal;
                    $chPattern = '~ *((?:\d|,|'.$chCon.')+)'.$unitTypePt.' *~iu';
                    preg_match_all($chPattern, $chStr, $aMatch);
                    if (is_array($aMatch) && count($aMatch[0]) > 0) {
                        $aResult['is_matched'] = true;

                        for($i=0, $nCnt=count($aMatch[0]); $i<$nCnt; $i++) {
                            $_result = array();

                            // 나이 ~세를 위해 마지막 문자가 "세"일 경우 강제 분리
                            $_lastChar = mb_substr($aMatch[0][$i], -1, null, 'utf-8');
                            if($_lastChar == "세") {
                                $aMatch[1][$i] = mb_substr($aMatch[0][$i], 0, -1, 'utf-8');
                                $aMatch[2][$i] = $_lastChar;
                            }

                            $_result['sum'] = $this->getNumber($aMatch[1][$i]);
                            $_matched = $aMatch[0][$i];

                            if($aMatch[2][$i]) {
                                foreach($this->aUnitTypePt as $key=>$aUnit) {
                                    if(in_array($aMatch[2][$i], $aUnit)) {
                                        $_result['unitStr'] = $key; break;
                                    }
                                }
                            }

                            if($_result['sum'] && $_matched) {
                                // 수량, 금액이 아닌 일반 숫자, 전화번호
                                if($_result['unitStr'] == '') {
                                    $aNumberResult = getCheckNumberFormat($_result['sum']);
                                    if($aNumberResult) {
                                        $_result['sum'] = $aNumberResult['value'];
                                        $_result['unitStr'] = $aNumberResult['type'];
                                    } else {
                                        $_result['unitStr'] = '숫자';
                                    }
                                }
                            }
                            $_result['matched'] = trim($_matched);
                            $aResult['data'][] = $_result;
                        }
                    }
                }
            }
        }
        return $aResult;
    }

    // 차량번호 검출
    public function getCarNumbers($data)
    {
        $aResult = array();
        $aResult['is_matched'] = false;
        $aResult['matched'] = $aResult['unitStr'] = $sum = '';

        if ($data['user_input']) {
            $chStr = preg_replace('/[\s\.\-에애의]/ui', '', $data['user_input']);
            $chPT_Area = '서울|경기|부산|대구|인천|대전|제주|경북|경남|전북|전남|충북|충남|세종';
            $chPT_Numeral = '공' . preg_replace('/백|천|만|억|조/', '', str_replace('|', '', $this->chPT_Numeral));
            $chMiddleChars1 = array('가', '나', '다', '라', '마', '바', '사', '아', '자', '하');
            $chMiddleChars2 = array('거', '너', '더', '러', '머', '버', '서', '어', '저', '허');
            $chMiddleChars3 = array('고', '노', '도', '로', '모', '보', '소', '오', '조', '호');
            $chMiddleChars4 = array('구', '누', '두', '루', '무', '부', '수', '우', '주');
            $chMiddleChars5 = array('육', '공', '해', '국', '합', '배');
            $aMiddleChars = array_merge($chMiddleChars1, $chMiddleChars2, $chMiddleChars3, $chMiddleChars4, $chMiddleChars5);

            $chPattern = '/((' . $chPT_Area . ')([\d' . $chPT_Numeral . ']{1,2})([가-힣])([\d' . $chPT_Numeral . ']{4}))|(([\d' . $chPT_Numeral . ']{2,3})([가-힣])([\d' . $chPT_Numeral . ']{4}))/iu';
            preg_match($chPattern, $chStr, $aMatch);

            if ((isset($aMatch[2]) && isset($aMatch[3]) && isset($aMatch[4]) && isset($aMatch[5])) || (isset($aMatch[7]) && isset($aMatch[8]) && isset($aMatch[9]))) {
                // 매칭 결과에서 중간 한글이 지정 글자인지 체크
                if ((isset($aMatch[4]) && !in_array($aMatch[4], $aMiddleChars)) && (isset($aMatch[8]) && !in_array($aMatch[8], $aMiddleChars))) return $aResult;

                $_carnumber = '';

                if (isset($aMatch[2]) && $aMatch[2]) {
                    $_carnumber .= $aMatch[2];
                    $_carnumber .= $this->getNumber($aMatch[3]);
                    $_carnumber .= $aMatch[4];
                    $_carnumber .= $this->getNumber($aMatch[5]);
                } else {
                    $_carnumber .= $this->getNumber($aMatch[7]);
                    $_carnumber .= $aMatch[8];
                    $_carnumber .= $this->getNumber($aMatch[9]);
                }

                $aResult['is_matched'] = true;
                $aResult['unitStr'] = '차량번호';
                $aResult['matched'] = $aMatch[0];
                $aResult['sum'] = $_carnumber;
            }
            return $aResult;
        }
    }

    // TTS용 텍스트로 변환
    public function getNumberToTTSText($data) {
        $_result = "";
        if($data['unitStr'] == "차량번호") {
            $_len = mb_strlen($data['text'], 'utf-8');
            $arr = preg_split('/(?<!^)(?!$)/u', $data['text']);
            for($i=0, $nCnt=count($arr); $i<$nCnt; $i++) {
                if(is_numeric($arr[$i])) {
                    $_result .=$this->aNumberMap[$arr[$i]];
                } else {
                    if(($_len == 9 && $i == 4) || ($_len == 8 && $i == 3) || ($_len == 7 && $i == 2)) {
                        $_result .=$arr[$i]."에 ";
                    } else {
                        $_result .=$arr[$i];
                    }
                }
            }
        }
        if($data['unitStr'] == "숫자") {
            $arr = preg_split('/(?<!^)(?!$)/u', $data['text']);
            foreach($arr as $_char) {
                $_result .=$this->aNumberMap[$_char];
            }
        }
        return $_result;
    }

    private function getNumber($chStr) {
        $num = preg_replace("/,/i", ".", $chStr);
		if (is_numeric($num)) return $num;

        $num = $this->aNumeralMap[$chStr];
		if ($num) return $num;

		//서수 -> 기수
		preg_match_all('/'.$this->chPT_Ordinal.'/u', $chStr, $aMatch);
		if(count($aMatch[0]) > 0) {
		    foreach($aMatch[0] as $val) {
		        $chStr = str_replace($val, $this->aOrdinalMap[$val], $chStr);
		    }
		}

		$num = $this->getNumeralValue($chStr);
		if ($num) {
			$this->aNumeralMap[$chStr] = $num;
			return $num;
		}
		return $num;
    }

    private function getNumeralValue($chStr) {
		$m = $n = 0;
		$unit = $unit_sub = 1;
		$a = '';

		$arr = preg_split('/(?<!^)(?!$)/u', $chStr);

		for ($i=(count($arr)-1); $i>=0; $i--) {
			$strNum = $arr[$i];
			if(is_numeric($strNum)) {
			    $a = $strNum.$a;
			} else {
			    $num = $this->aNumeralMap[$strNum];
			    if($num < 10) {
			        $a = $num.$a;
			    }else if($num >= 10 && $num <= 1000) {
			        if($a != '') $m = $m+$a*$unit;
			        else if($unit != 1) $m = $m+$unit;
			        $a = '';
			        $unit = $num;
			    }else if($num > 1000) {
			        if ($a != '') $m = $m+$a*$unit;
                    else if ($unit != 1) $m = $m+$unit;
                    $n = $m*$unit_sub+$n;
                    $m = 0;
                    $a = '';
                    $unit = 1;
                    $unit_sub = $num;
			    }
			}
		}

		$ss = '';
        if (preg_match("/^(0+)/", $a, $match) != false) $ss = $match[1];
        if ($a != '') $m = $m+$a*$unit;
        else if ($unit != 1) $m = $m+$unit;
        $n = $m*$unit_sub+$n;

        if ($ss == '') return $n;
        else if ($n == 0) return $ss;
        else return $ss.$n;
	}
}

// AES Encryption, Decryption
class AesEncryption {
    private $modes = array("CBC" => "AES-%d-CBC", "CFB" => "AES-%d-CFB8");
    private $sizes = array(128, 192, 256);
    private $saltLen = 16;
    private $ivLen = 16;
    private $macLen = 32;
    private $macKeyLen = 32;
    private $mode;
    private $keyLen;
    private $masterKey = null;
    public $keyIterations = 20000;
    public $base64 = true;
    /**
     * Creates a new AesEncryption object.
     * @param string $mode Optional, the AES mode (CBC, CFB).
     * @param int $size Optional, the key size (128, 192, 256).
     */
    public function __construct($size=128, $mode="CBC") {
        $this->mode = strtoupper($mode);
        $this->keyLen = $size / 8;
        if (!array_key_exists($this->mode, $this->modes)) {
            throw new UnexpectedValueException("$mode is not supported!");
        }
        if (!in_array($size, $this->sizes)) {
            throw new UnexpectedValueException("Invalid key size!");
        }
    }

    /**
     * Encrypts data using a key or the supplied password.
     */
    public function encrypt($data, $password = null) {
        $salt = $this->randomBytes($this->saltLen);
        $iv = $this->randomBytes($this->ivLen);
        try {
            list($aesKey, $macKey) = $this->keys($salt, $password);
            $cipher = $this->cipher($aesKey, $iv, Cipher::Encrypt);
            $ciphertext = $cipher->update($data, true);
            $mac = $this->sign($iv.$ciphertext, $macKey);
            $encrypted = $salt . $iv . $ciphertext . $mac;

            if ($this->base64) {
                $encrypted = base64_encode($encrypted);
            }
            return $encrypted;
        } catch (RuntimeException $e) {
            $this->errorHandler($e);
        }
    }

    /**
     * Decrypts data using a key or the supplied password.
     */
    public function decrypt($data, $password = null) {
        $data = $this->base64 ? base64_decode($data, true) : $data;
        try {
            if ($data === false) {
                throw new UnexpectedValueException("Invalid data format!");
            }
            $salt = mb_substr($data, 0, $this->saltLen, "8bit");
            $iv = mb_substr($data, $this->saltLen, $this->ivLen, "8bit");
            $ciphertext = mb_substr(
                $data, $this->saltLen + $this->ivLen, -$this->macLen, "8bit"
            );
            $mac = mb_substr($data, -$this->macLen, $this->macLen, "8bit");
            list($aesKey, $macKey) = $this->keys($salt, $password);
            $this->verify($iv.$ciphertext, $mac, $macKey);

            $cipher = $this->cipher($aesKey, $iv, Cipher::Decrypt);
            $plaintext = $cipher->update($ciphertext, true);
            return $plaintext;
        } catch (RuntimeException $e) {
            $this->errorHandler($e);
        } catch (UnexpectedValueException $e) {
            $this->errorHandler($e);
        }
    }

    /**
     * Sets a new master key.
     * This key will be used to create the encryption and authentication keys.
     */
    public function setMasterKey($key, $raw = false) {
        $key = ($raw) ? $key : base64_decode($key, true);
        if ($key === false) {
            $this->errorHandler(new RuntimeException('Failed to decode the key!'));
        } else {
            $this->masterKey = $key;
        }
    }
    /**
     * Returns the master key (or null if the key is not set).
     */
    public function getMasterKey($raw = false) {
        if ($this->masterKey === null) {
            $this->errorHandler(new RuntimeException("The key is not set!"));
        } elseif (!$raw) {
            return base64_encode($this->masterKey);
        } else {
            return $this->masterKey;
        }
    }
    /**
     * Generates a new random key.
     * This key will be used to create the encryption and authentication keys.
     */
    public function randomKeyGen($keyLen = 32, $raw = false) {
        $this->masterKey = $this->randomBytes($keyLen);
        if (!$raw) {
            return base64_encode($this->masterKey);
        }
        return $this->masterKey;
    }

    /**
     * Handles exceptions (prints the error message by default).
     */
    protected function errorHandler($exception) {
        echo $exception->getMessage();
    }
    /**
     * Derives encryption and authentication keys from a key or password.
     * If the password is not null, it will be used to create the keys.
     */
    private function keys($salt, $password = null) {
        if ($password !== null) {
            $dkey = openssl_pbkdf2(
                $password, $salt, $this->keyLen + $this->macKeyLen,
                $this->keyIterations, "SHA512"
            );
        } elseif ($this->masterKey !== null) {
            $dkey = $this->hkdfSha256(
                $this->masterKey, $salt, $this->keyLen + $this->macKeyLen
            );
        } else {
            throw new RuntimeException('No password or key specified!');
        }
        return array(
            mb_substr($dkey, 0, $this->keyLen, "8bit"),
            mb_substr($dkey, $this->keyLen, $this->macKeyLen, "8bit")
        );
    }

    /**
     * Returns a new Cipher object; used for encryption / decryption.
     */
    private function cipher($key, $iv, $method) {
        $algorithm = sprintf($this->modes[$this->mode], $this->keyLen * 8);
        return new Cipher($algorithm, $method, $key, $iv);
    }
    /**
     * Creates random bytes, used for IV, salt and key generation.
     */
    private function randomBytes($size) {
        if (is_callable("random_bytes")) {
            return random_bytes($size);
        }
        return openssl_random_pseudo_bytes($size);
    }
    /**
     * Computes the MAC of ciphertext, used for ciphertext authentication.
     */
    private function sign($data, $key) {
        return hash_hmac("SHA256", $data, $key, true);
    }

    /**
     * Verifies the authenticity of ciphertext.
     * @throws UnexpectedValueException if MAC is invalid.
     */
    private function verify($data, $mac, $key) {
        $dataMac = $this->sign($data, $key);
        $this->compareMacs($mac, $dataMac);
    }

    /**
     * Safely compares two byte arrays, used for ciphertext uthentication.
     */
    private function constantTimeComparison($macA, $macB) {
        $result = mb_strlen($macA, "8bit") ^ mb_strlen($macB, "8bit");
        $minLen = min(mb_strlen($macA, "8bit"), mb_strlen($macB, "8bit"));
        for ($i = 0; $i < $minLen; $i++) {
            $result |= ord($macA[$i]) ^ ord($macB[$i]);
        }
        return $result === 0;
    }
    /**
     * Compares the received MAC with the computed MAC, used for uthentication.
     * @throws UnexpectedValueException if the MACs don't match.
     */
    private function compareMacs($macA, $macB) {
        if (is_callable("hash_equals") && !hash_equals($macA, $macB)) {
            throw new UnexpectedValueException("MAC check failed!");
        }
        elseif (!$this->constantTimeComparison($macA, $macB)) {
            throw new UnexpectedValueException("MAC check failed!");
        }
    }
    /**
     * A HKDF implementation, with HMAC SHA256.
     * Expands the master key to create the AES and HMAC keys.
     */
    private function hkdfSha256($key, $salt, $keyLen, $info = "") {
        $dkey = "";
        $hashLen = 32;
        $prk = hash_hmac("SHA256", $key, $salt, true);
        for ($i = 0; $i < $keyLen; $i += $hashLen) {
            $data = mb_substr($dkey, -$hashLen, $hashLen, "8bit");
            $data .= $info . pack("C", ($i / $hashLen) + 1);
            $dkey .= hash_hmac("SHA256", $data, $prk, true);
        }
        return mb_substr($dkey, 0, $keyLen, "8bit");
    }
}
/**
 * Encrypts data using AES. Supported modes: CBC, CFB.
 *
 * This class is a wrapper for openssl_ encrypt/decrypt functions, that can be used to encrypt multiple chunks of data.
 * The data size must be a multiple of the block size (16 bytes).
 * Note that this class is a helper of AesEncryption and should NOT be used on its own.
 */
class Cipher {
    private $key;
    private $iv;
    private $mode;
    private $method;
    const Encrypt = "encrypt";
    const Decrypt = "decrypt";
    /**
     * Creates a new Cipher object.
     */
    function __construct($cipher, $method, $key, $iv) {
        $this->cipher = $cipher;
        $this->method = $method;
        $this->key = $key;
        $this->iv = $iv;
        $this->mode = strtoupper(explode("-", $cipher)[2]);
    }
    /**
     * Encrypts or decrypts a chunk of data.
     */
    public function update($data, $final = false) {
        if ($final && $this->method == Cipher::Encrypt && $this->mode == "CBC") {
            $data = $this->pad($data);
        }
        $options = OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING;
        $method =  "openssl_$this->method";
        $newData = $method($data, $this->cipher, $this->key, $options, $this->iv);
        if ($newData === false) {
            throw new RuntimeException(openssl_error_string());
        }
        $ciphertext = ($this->method == Cipher::Encrypt) ? $newData : $data;
        $this->iv = $this->lastBlock($ciphertext);
        if ($final && $this->method == Cipher::Decrypt && $this->mode == "CBC") {
            $newData = $this->unpad($newData);
        }
        return $newData;
    }
    /**
     * Adds PKCS7 padding to plaintext, used with CBC mode.
     */
    private function pad($data) {
        $pad = 16 - (mb_strlen($data, "8bit") % 16);
        return $data . str_repeat(chr($pad), $pad);
    }
    /**
     * Removes PKCS7 padding from plaintext, used with CBC mode.
     * @throws RuntimeException If padding is invalid.
     */
    private function unpad($data) {
        $pad = ord(mb_substr($data, -1, 1, "8bit"));
        $count = substr_count(mb_substr($data, -$pad, $pad, "8bit"), chr($pad));
        if ($pad < 1 || $pad > 16 || $count != $pad) {
            throw new RuntimeException("Padding is invalid!");
        }
        return mb_substr($data, 0, -$pad, "8bit");
    }

    /**
     * Returns the last block of ciphertext,
     * which is used as an IV for openssl, to chain multiple chunks.
     */
    private function lastBlock($data) {
        return mb_substr($data, -16, 16, '8bit');
    }
}
?>