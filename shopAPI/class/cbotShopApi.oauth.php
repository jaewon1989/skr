<?
/**
*@ oAuth 를 이용한 api 접근시
*@ 반드시 session을 사용할수 있어야 함
**/
class cbotShopOauthApi {
	/********* 카페24 관리자 권한으로 api 실행시 설정할 변수 ****************/
	private $cfScope = 'mall.read_product, mall.read_category, mall.read_personal, mall.read_order, mall.read_shipping'; // 카페24권한
	private $stateCode = 'cafe24cbotkey';
	private $mallId;
	private $clientId;
	private	$redirectUri;
	private $clientSecret;
	private $access_token;
	private $access_token_expire;
	private $refresh_token;
	
	public function __construct($params) {
	    global $g;
	    
		$this->clientId = $params['client_id'];
		$this->clientSecret = $params['client_secret'];		
		$this->mallId = $params['mall_id'];		
		$this->access_token = $params['access_token'];
		$this->access_token_expire = $params['access_token_expire'];
		$this->refresh_token = $params['refresh_token'];
		$this->redirectUri = $g['url_host'].'/shopAPI/oauth_callback.php';
	}

	/**
	* @ 공통함수 
	* @ callback 함수 실행
	* @ 토큰값 존재 및 만료여부 체크
	**/ 
	public function checkRefreshToken() {		
		if($this->access_token) {
			$tokenTime =  strtotime($this->access_token_expire);
			//$nowTime = mktime(date('H'),date('i')+2,date('s'),date('m'),date('d'),date('Y'));
			$nowTime = time();
			if($tokenTime < $nowTime) { // 토큰값 만료일경우
				return $this->getRefreshToken();
			} else {
			    return true;
			}
		} else {
			if($_GET['state'] == $this->stateCode && $_GET['code']) {
				return $this->getAccessToken($_GET['code']);
			} else if($_GET['error']) {
				echo $_GET['error_description'];
				exit;
			} else {
				$this->getAuthCode();
			}
		}
		
	}
	/**
	* @ cafe24 
	* @ 인증키 획득
	**/
	public function getAuthCode() {		
		echo "<script>";
		echo "location.href='https://".$this->mallId.".cafe24api.com/api/v2/oauth/authorize?response_type=code&client_id=".$this->clientId."&state=".$this->stateCode."&redirect_uri=".$this->redirectUri."&scope=".$this->cfScope."';";
		echo "</script>";
		exit;
	}


	/**
	* @ cafe24 
	* @ 토큰 획득
	**/
	public function getAccessToken($code) {		
		$params['url'] = 'https://'.$this->mallId.'.cafe24api.com/api/v2/oauth/token';		
		$params['headers'] = array('Authorization: Basic '.base64_encode($this->clientId.':'.$this->clientSecret),
									'Content-Type: application/x-www-form-urlencoded');

		$params['datas'] = 'grant_type=authorization_code&code='.$code.'&redirect_uri='.$this->redirectUri;
		$result = $this->sendCurl($params);
		$ret = json_decode($result);	
		return $ret;
	}

	/**
	* @ cafe24 
	* @ 토큰 리프레쉬
	**/
	public function getRefreshToken() {
		$params['url'] = 'https://'.$this->mallId.'.cafe24api.com/api/v2/oauth/token';
		$params['headers'] = array('Authorization: Basic '.base64_encode($this->clientId.':'.$this->clientSecret),
									'Content-Type: application/x-www-form-urlencoded');
		$params['datas'] = 'grant_type=refresh_token&refresh_token='.$this->refresh_token;
		$result = $this->sendCurl($params);
		$ret = json_decode($result);
		return $ret;
  	}

	private function sendCurl($params) {
		$ch = curl_init();	
		curl_setopt($ch, CURLOPT_URL,$params['url']);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $params['headers']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$params['datas']);			
		$result = curl_exec($ch);           
		curl_close($ch);
		if($result === FALSE) {
			die('Curl failed: ' . curl_error($ch));
		} else {
			return $result;
		}
	}
}

