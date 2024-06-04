<?
class cbotShopApi {	
	public $ENVMODE = 'debug'; // debug: 디버그 , active : 실행	
	public $setHeader = array();
	public $sendMethod = 'POST';
	
	public function __construct() {
		
	}
	
	/**
	*
	* @ curl
	**/
	private function sendCurl($params) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$params['url']);
		if($this->sendMethod == 'POST')curl_setopt($ch, CURLOPT_POST, true);
		
		if($this->setHeader)curl_setopt($ch, CURLOPT_HTTPHEADER, $this->setHeader);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if($params['datas'])curl_setopt($ch, CURLOPT_POSTFIELDS,$params['datas']);
		
		$result = curl_exec($ch);           
		if($result === FALSE) {
		    if($this->ENVMODE == 'debug') {
				die('Curl failed: ' . curl_error($ch));
			} else {
				$result = array('result'=>'fail');
				echo json_encode($result);
				exit;
			}
		}
		curl_close($ch);
		return $result;
	}

	/**
	*
	* @ api서버응답결과 가공
	**/
	private function responseResult($response,$callback) {
		$datas = $this->_responseResult($response);
		return $this->responseResultPrint($datas,$callback);
	}

	public function responseResultPrint($datas,$callback="") {
		if($datas['result'] == 'error') {
			if($this->ENVMODE == 'debug') {
				//echo 'code : '.$datas['code'].', message : '.$datas['message'];
				//exit;
				$result = array('result'=>'fail','code'=>$datas['code'],'message'=>$datas['message']);
			} else {
				$result = array('result'=>'fail','code'=>$datas['code'],'message'=>$datas['message']);
			}
			return $result;
			
		} else if($callback) {
		    if($datas['result'] == 'error') {
		        $result = array('result'=>'fail','code'=>$datas['code'],'message'=>$datas['message']);
		    } else {
    			$responseResult = $this->$callback($datas['data']);
    			$result = array('result'=>'succ','data'=>$responseResult);
    		}
			if($this->ENVMODE == 'debug') {
				//print_r($result);
				return $result;
			} else {
				//echo json_encode($result);
				return $result;
			}		
		}
	}

	/**
	*@ 카테고리리스트
	*@ request array code: 카테고리 코드
	*@ return array code: 카테코드, cname: 카테고리명
	**/
	
	public function getCategoryList($params="") {
		$sendPrams = $this->_getCategoryList($params);
		$response = $this->sendCurl($sendPrams);
		return $this->responseResult($response,'_getCategoryResponse');
	}
	
    /**
	*@ 브랜드리스트
	*@ request array code: 브랜드 코드
	*@ return array code: 브랜드코드, cname: 브랜드명
	**/
	public function getBrandList($params="") {
		$sendPrams = $this->_getBrandList($params);
		$response = $this->sendCurl($sendPrams);
		return $this->responseResult($response,'_getBrandResponse');
	}
	

	/**
	*
	*@ 상품리스트
	*@ request array gname: 상품명, keyword : 검색엔터티 (상품등록시 엔터티로 카테고리 or 브랜드명 을 입력하면 카테고리와 브랜드를 검색할수 있음 
	*				startDate : 등록일 검색 (시작일) Y-m-d type , endDate: 등록일 검색 (종료일) Y-m-d type, limit : 출력상품수
	*
	*@ return array no: 상품고유키, gname: 상품명, img: 이미지 , price 가격 , consumer_price: 소비자가격 , brand: 브랜드코드 ,category : 카테고리 코드(카페24일경우 빈값이 옴)
	**/
	public function getGoodsList($params="") {
		$sendPrams = $this->_getGoodsList($params);
		$response = $this->sendCurl($sendPrams);
		$result = $this->responseResult($response,'_getGoodsResponse');
		return $result;
	}
	
	/**
	*@ 주문정보리스트
	*@ request array pcs: 주문자휴대폰번호(필수), name: 주문자명(필수)
	*@ return array ordno: 주문번호, gname: 상품명, price: 결제금액 ,transcp:배송업체번호, transno:송장번호,ordstatus:주문상태코드(각 api별로 리턴코드 참조) ,
	*				ordchannel: 주문채널(shop	쇼핑몰 주문,payco	페이코 주문,naverpay	네이버페이 주문)
	*				(상품이미지는 api에서 지원하지 않음)
	*				goodsData: 주문상품 정보  Array ( [sno] => 주문상품번호 [ordstatus] 주문상태 [gno] => 상품고유번호 [gname] 상품명 [cnt] 구매수량 [price] = 상품가격 
	**/
	public function getOrderList($params="") {
		$sendPrams = $this->_getOrderList($params);
		$response = $this->sendCurl($sendPrams);
		return $this->responseResult($response,'_getOrderResponse');
	}
	
	
	/**
	*@ 주문정보 변경(취소/환불/교환)
	*@ request array pcs: 주문자휴대폰번호(필수), name: 주문자명(필수)
	*@ return array ordno: 주문번호, gname: 상품명, price: 결제금액 ,transcp:배송업체번호, transno:송장번호,ordstatus:주문상태코드(각 api별로 리턴코드 참조) (상품이미지는 api에서 지원하지 않음)
	**/
	public function setOrderUpdate($params) {
		$sendPrams = $this->_setOrderUpdate($params);
		$response = $this->sendCurl($sendPrams);
		$this->responseResult($response,'_getOrderUpdateResponse');
	}
	public function getCommonCode($code) {
		$sendPrams = $this->_getCommonCode($code);
		$response = $this->sendCurl($sendPrams);
		$this->responseResult($response,'_getCommonCodeResponse');
	}
}

