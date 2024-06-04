<?
include_once(dirname(__FILE__).'/cbotShopApi.php');

class cbotShopApiGodo extends cbotShopApi {
	public $clientId;
	public $clientSecret;
	public $mallDomain;
	public $godomallType = 'godomall5';
	public $resultLimit;
	public $getResponseUrls = array();

	public function __construct($params) {
	    global $g;
	    
		parent::__construct();
		$this->clientId = $params['client_id'];
		$this->godomallType = $params['mall_type'];
		$this->clientSecret = $params['client_key'];
		$this->mallDomain = $params['mall_domain'];
		$this->resultLimit = 20;

		$this->getResponseUrls['goodsList'] = 'https://openhub.godo.co.kr/'.$this->godomallType.'/goods/Goods_Search.php';
		$this->getResponseUrls['category'] = 'https://openhub.godo.co.kr/'.$this->godomallType.'/goods/Category_Search.php';
		$this->getResponseUrls['brand'] = 'https://openhub.godo.co.kr/'.$this->godomallType.'/goods/Brand_Search.php';
		$this->getResponseUrls['order'] = 'https://openhub.godo.co.kr/'.$this->godomallType.'/order/Order_Search.php';
		$this->getResponseUrls['orderUpdate'] = 'https://openhub.godo.co.kr/'.$this->godomallType.'/order/Order_Status.php';
		$this->getResponseUrls['common'] = 'https://openhub.godo.co.kr/'.$this->godomallType.'/common/Code_Search.php';
		
		$this->orderStatus = array(		    
            'o1'=>'입금대기', 'p1'=>'결제완료', 'g1'=>'상품준비중', 'g2'=>'구매발주', 'g3'=>'상품입고', 'g4'=>'상품출고', 'd1'=>'배송중', 'd2'=>'배송완료', 
            's1'=>'구매확정', 'c1'=>'자동취소', 'c2'=>'품절취소', 'c3'=>'관리자취소', 'c4'=>'고객취소요청', 'f1'=>'결제시도', 'f2'=>'고객결제중단', 'f3'=>'결제실패', 
            'b1'=>'반품접수', 'b2'=>'반송중', 'b3'=>'반품보류', 'b4'=>'반품회수완료', 'e1'=>'교환접수', 'e2'=>'반송중', 'e3'=>'재배송중', 'e4'=>'교환보류', 'e5'=>'교환완료', 
            'r1'=>'환불접수', 'r2'=>'환불보류', 'r3'=>'환불완료', 'z1'=>'추가입금대기', 'z2'=>'추가결제완료', 'z3'=>'추가배송중', 'z4'=>'추가배송완료', 'z5'=>'교환추가완료'
		);
	}	
	
	/**
	* @ api서버응답결과 가공
	**/
	public function _responseResult($response) {
		$xmlResult = simplexml_load_string($response);
		if($xmlResult->header->code == '000') {
			$result = array('result'=>'succ','data'=>$xmlResult);
		} else {
			$result = array('result'=>'error','code'=>(int)$xmlResult->header->code,'message'=>(string)$xmlResult->header->msg); 
		}
		return $result;
	}

	public function _getCategoryList($setparams="") {		
		$params['url'] = $this->getResponseUrls['category'];
		$params['datas'] = array('partner_key'=>$this->clientId,'key'=>$this->clientSecret);
		if(!is_array($setparams))$setparams=array();
				
		if($setparams['code'])$params['datas']['cateCd'] = $setparams['code'];
		return $params;
	}
	
	public function _getCategoryResponse($datas) {		
		$resultItem = array();
		foreach($datas->return->category_data as $data) {
			$resultItem[] = array('code'=>(string)$data->cateCd,'cname'=>(string)$data->cateNm);
		}
		return $resultItem;		
	}

	
	public function _getBrandList($setparams="") {		
		$params['url'] = $this->getResponseUrls['brand'];
		$params['datas'] = array('partner_key'=>$this->clientId,'key'=>$this->clientSecret);
		if(!is_array($setparams))$setparams=array();
		
		if($setparams['code'])$params['datas']['cateCd'] = $setparams['code'];
		return $params;
	}

	public function _getBrandResponse($datas) {		
		$resultItem = array();
		foreach($datas->return->brand_data as $data) {
			$resultItem[] = array('code'=>(string)$data->cateCd,'cname'=>(string)$data->cateNm);
		}
		return $resultItem;		
	}

	
	public function _getGoodsList($setparams="") {		
		$params['url'] = $this->getResponseUrls['goodsList'];
		$params['datas'] = array('partner_key'=>$this->clientId,'key'=>$this->clientSecret, 'size'=>$this->resultLimit);
		if(!is_array($setparams))$setparams=array();
		
		if($setparams['gname'])$params['datas']['goodsNm'] = $setparams['gname'];
		if($setparams['keyword'])$params['datas']['goodsSearchWord'] = $setparams['keyword'];
		if($setparams['startDate'])$params['datas']['startDate'] = $setparams['startDate'];
		if($setparams['endDate'])$params['datas']['endDate'] = $setparams['endDate'];
		if($setparams['searchDateType'])$params['datas']['searchDateType'] = $setparams['searchDateType'];
		
		if($setparams['gno'])$params['datas']['goodsNo'] = $setparams['gno'];
		if($setparams['gname'])$params['datas']['goodsNm'] = $setparams['gname'];

		$this->setParams['category'] = $setparams['category'];
		$this->setParams['brand'] = $setparams['brand'];
		return $params;
	}
	
	public function _getGoodsResponse($datas) {
		$resultItem = array();
		foreach($datas->return->goods_data as $data) {			
			$searchDataFlag = false;
			$categoryFlag = false;	
			$brandFlag = false;
			
			if($data->goodsSellFl != 'y' || $data->goodsSellMobileFl != 'y') continue;
			
			if($this->setParams['category'] || $this->setParams['brand']) {			
				if($this->setParams['category'] && $data->allCateCd) {
					$categoryList = explode('|',$data->allCateCd);
					foreach($categoryList as $catecode) {
						if($this->setParams['category'] == $catecode) {
							$categoryFlag = true;
							break;
						}
					}
				}
				if($this->setParams['brand'] && $data->brandCd && ($this->setParams['brand'] == $data->brandCd)) {
					$brandFlag = true;					
				}
				if($this->setParams['category'] && $this->setParams['brand']) {
					if($categoryFlag && $brandFlag) $searchDataFlag = true;
				} else if($this->setParams['category'] && $categoryFlag) {
					$searchDataFlag = true;
				} else if($this->setParams['brand'] && $brandFlag) {
					$searchDataFlag = true;
				}
			} else {
				$searchDataFlag = true;
			}
			if($searchDataFlag) {
				$resultItem[] = array(
				    'no'=>(int)$data->goodsNo,
				    'gname'=>(string)$data->goodsNm,
				    //'img'=>(string)$data->listImageData,
				    'img'=>(string)$data->magnifyImageData,
					'category'=>(string)$data->cateCd,
					'brand'=>(string)$data->brandCd,	
					'price'=>(int)$data->goodsPrice,
					'consumer_price'=>(int)$data->goodsPrice,
					'orders'=>(int)$data->orderCnt,
					'new'=>((string)$data->goodsIconCd == 'icon0003' ? 1 : ''), 
					'favorate'=>((string)$data->goodsIconCd == 'icon0004' ? 1 : ''), 
					'recommend'=>((string)$data->goodsIconCd == 'icon0005' ? 1 : ''),					
					'date'=>(string)$data->regDt,
					'link'=>$this->mallDomain.'/goods/goods_view.php?goodsNo='.$data->goodsNo
				);
			}
		}
		return $resultItem;		
	}	
	
	public function _getOrderList($setparams="") {		
		$params['url'] = $this->getResponseUrls['order'];
		$params['datas'] = array('partner_key'=>$this->clientId, 'key'=>$this->clientSecret, 'searchType'=>'orderCellPhone');
		
		if(!$setparams['buyer_cellphone']) {
			$datas['result'] = 'error';
			$datas['code'] = 'CBOT503';
			$datas['message'] = '필수파라미터 미입력';
			$this->responseResultPrint($datas);
			exit;	
		}
		
		// 디폴트 30일간 검색
		$end_date = date('Y-m-d', time());
		$_tempDate = explode('-', $end_date);
		$start_date = date("Y-m-d", mktime(0,0,0, $_tempDate[1], ($_tempDate[2]-30), $_tempDate[0]));
		
		$params['datas']['startDate'] = $start_date;
		$params['datas']['endDate'] = $end_date;
		$params['datas']['searchKeyword'] = $setparams['buyer_cellphone'];		
		
		if($setparams['ordno'])$params['datas']['orderNo'] = $setparams['ordno'];
		
		$this->setParams['pcs'] = $setparams['buyer_cellphone'];
		return $params;
	}
	
	public function _getOrderResponse($datas) {		
		$resultItem = array();
		$aShippers = $this->getShippers();
		
		foreach($datas->return->order_data as $data) {
		    $ship_company = $ship_no = $ship_url = '';
			$orderPcs = (string)$data->orderInfoData->orderCellPhone;
			if(str_replace('-','',$orderPcs) == str_replace('-','',$this->setParams['pcs'])) {
			    $is_shipping = (string)$data->orderStatus == 'd1' ? true : false;
			    $img = $data->orderGoodsData[0]->listImageData;
				$orderGoodsData = $data->orderGoodsData[0];
				if($is_shipping) {
				    $ship_company = (string)$orderGoodsData->invoiceCompany;
				    $ship_no = (string)$orderGoodsData->invoiceNo;
				    $ship_url = array_key_exists($ship_company, $aShippers) ? $aShippers[$ship_company].$ship_no : '';
				}
				$resultItem[] = array(
				    'order_id'=>(int)$data->orderNo, // 주문번호
				    'order_status'=>$this->orderStatus[(string)$data->orderStatus], // 주문상태
				    'is_shipping'=>$is_shipping,
				    'gname'=>(string)$data->orderGoodsNm.((int)$data->orderGoodsCnt > 1 ? ' 외 '.(int)$data->orderGoodsCnt : ''), // 주문상품명
				    'img'=>$img,
				    'ship_company'=>$ship_company,
			        'ship_no'=>$ship_no,
			        'ship_url'=>$ship_url,
			        'link'=>$this->mallDomain.'/mypage/order_view.php?orderNo='.(int)$data->orderNo
				    /*
				    'price'=>(int)$data->settlePrice,				    
				    'ordchannel'=>(string)$data->orderChannelFl,
					'transcp'=>(int)$data->orderGoodsData[0]->invoiceCompanySno,
					'transno'=>(string)$data->orderGoodsData[0]->invoiceNo,
					'goodsData'=>$goodsDatas
					*/
				);
			}
		}	
		return $resultItem;		
	}
	
	public function _setOrderUpdate($setparams) {		
		$params['url'] = $this->getResponseUrls['orderUpdate'];
		$params['datas'] = array('partner_key'=>$this->clientId,'key'=>$this->clientSecret);		
	
		$params['datas']['orderNo'] = $setparams['ordno'];
		$params['datas']['sno'] = $setparams['sno'];
		$params['datas']['orderStatus'] = $setparams['ordstatus'];
		$params['datas']['cancelCnt'] = $setparams['cancelcnt'];

		$params['datas']['cancelCnt'] = $setparams['cancelcnt'];
		$params['datas']['handleDetailReason'] = $setparams['handleDetailReason'];
		$params['datas']['handleReason'] = $setparams['handleReason'];
		$params['datas']['refundMethod'] = $setparams['refundMethod'];
		$params['datas']['refundBankName'] = $setparams['refundBankName'];
		$params['datas']['refundAccountNumber'] = $setparams['refundAccountNumber'];
		$params['datas']['refundDepositor'] = $setparams['refundDepositor'];
		return $params;
	}
	
	public function _getOrderUpdateResponse($datas) {		
		return $datas;		
	}

	public function _getCommonCode($code) {
		$params['url'] = $this->getResponseUrls['common'];
		$params['datas'] = array('partner_key'=>$this->clientId,'key'=>$this->clientSecret);		
	
		$params['datas']['code_type'] = $code;
		return $params;
	}

	public function _getCommonCodeResponse($datas) {
		$resultItem = array();
		foreach($datas->return->code_data as $data) {
			$resultItem[] = array('code'=>(string)$data->itemCd,'name'=>(string)$data->itemNm);
		}
		return $resultItem;
	}

	public function getGodoNaverRefundMsg($code) {
		$refundMsg['ber'][] = array('code'=>'의사 취소 반품 접수시 사용 가능','name'=>'의사 취소 반품 접수시 사용 가능');
		$refundMsg['ber'][] = array('code'=>'배송 지연','name'=>'배송 지연');
		$refundMsg['ber'][] = array('code'=>'상품 품절','name'=>'상품 품절');
		
		$refundMsg['be'][] = array('code'=>'색상 및 사이즈 변경','name'=>'색상 및 사이즈 변경');
		$refundMsg['be'][] = array('code'=>'다른 상품 잘못 주문','name'=>'다른 상품 잘못 주문');
		$refundMsg['be'][] = array('code'=>'서비스 및 상품 불만족','name'=>'서비스 및 상품 불만족');
		$refundMsg['be'][] = array('code'=>'배송 누락','name'=>'배송 누락');
		$refundMsg['be'][] = array('code'=>'상품 파손','name'=>'상품 파손');
		$refundMsg['be'][] = array('code'=>'상품 정보 상이','name'=>'상품 정보 상이');
		$refundMsg['be'][] = array('code'=>'오배송','name'=>'오배송');
		$refundMsg['be'][] = array('code'=>'색상 등이 다른 상품을 잘못 배송','name'=>'색상 등이 다른 상품을 잘못 배송');
		
		if($code=='r1')return $refundMsg['ber'];
		else return $refundMsg['be'];
	}
	
	public function getShippers() {
	    $aShippers = array();
		$aShippers['건영택배'] = 'http://www.kunyoung.com/goods/goods_02.php?mulno=';
		$aShippers['경동택배'] = 'https://kdexp.com/basicNewDelivery.kd?barcode=';
		$aShippers['대신택배'] = 'http://home.daesinlogistics.co.kr/daesin/jsp/d_freight_chase/d_general_process2.jsp?billno1=';
		$aShippers['로젠택배'] = 'https://www.ilogen.com/web/personal/trace/';
		$aShippers['롯데택배'] = 'https://www.lotteglogis.com/home/reservation/tracking/linkView?InvNo=';
		$aShippers['우체국택배'] = 'http://service.epost.go.kr/trace.RetrieveRegiPrclDeliv.postal?sid1=';
		$aShippers['일양로지스'] = 'http://www.ilyanglogis.com/functionality/tracking_result.asp?hawb_no=';
		$aShippers['일양택배'] = 'http://www.ilyanglogis.com/functionality/tracking_result.asp?hawb_no=';
		$aShippers['천일택배'] = 'http://www.cyber1001.co.kr/HTrace/HTrace.jsp?transNo=';
		$aShippers['한진택배'] = 'https://www.hanjin.co.kr/Delivery_html/inquiry/result_waybill.jsp?wbl_num=';
		$aShippers['합동택배'] = 'https://hdexp.co.kr/basic_delivery.hd?barcode=';
		$aShippers['호남택배'] = 'http://honamlogis.co.kr/page/?pid=tracking_number&SLIP_BARCD=';
		$aShippers['CJ대한통운'] = 'https://www.doortodoor.co.kr/parcel/doortodoor.do?fsp_action=PARC_ACT_002&fsp_cmd=retrieveInvNoACT&invc_no=';
		$aShippers['CU편의점택배'] = 'https://www.cupost.co.kr/postbox/delivery/localResult.cupost?invoice_no=';
		$aShippers['CVS편의점택배'] = 'http://www.doortodoor.co.kr/jsp/cmn/TrackingCVS.jsp?pTdNo=';
		$aShippers['CVSnet(편의점택배)'] = 'http://www.doortodoor.co.kr/jsp/cmn/TrackingCVS.jsp?pTdNo=';		
		$aShippers['DHL'] = 'http://www.dhl.co.kr/content/kr/ko/express/tracking.shtml?brand=DHL&AWB=';
		$aShippers['FEDEX'] = 'https://www.fedex.com/apps/fedextrack/?action=track&ascend_header=1&clienttype=dotcomreg&cntry_code=kr&language=korean&tracknumbers=';
		$aShippers['FedEx [Federal Corporation]'] = 'https://www.fedex.com/apps/fedextrack/?action=track&ascend_header=1&clienttype=dotcomreg&cntry_code=kr&language=korean&tracknumbers=';
		$aShippers['KGB택배'] = 'http://www.kgbps.com/delivery/delivery_result.jsp?item_no=';
		return $aShippers;
	}
}

