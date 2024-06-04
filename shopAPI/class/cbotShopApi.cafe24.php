<?
include_once(dirname(__FILE__).'/cbotShopApi.php');

class cbotShopApiCafe24 extends cbotShopApi {
	public $mallId;	
	public $accessToken;
	public $mallDomain;
	public $resultLimit;
	public $getResponseUrls = array();
		
	public function __construct($params) {
		parent::__construct();
		
		$this->mallId = $params['mall_id'];
		$this->accessToken = $params['access_token'];
		$this->mallDomain = $params['mall_domain'];
		$this->resultLimit = 20;

		$this->getResponseUrls['category'] = 'https://'.$params['mall_id'].'.cafe24api.com/api/v2/admin/categories';
		$this->getResponseUrls['goodsList'] = 'https://'.$params['mall_id'].'.cafe24api.com/api/v2/admin/products';
		$this->getResponseUrls['orderList'] = 'https://'.$params['mall_id'].'.cafe24api.com/api/v2/admin/orders';
		$this->getResponseUrls['shipping'] = 'https://'.$params['mall_id'].'.cafe24api.com/api/v2/admin/carriers';
		$this->setHeader = array(
		    'Content-Type: application/json', 'Authorization: Bearer '.$params['access_token']
		);
		$this->orderStatus = array(
            'N00'=>'입금전', 'N10'=>'상품 준비중', 'N20'=>'배송준비중', 'N21'=>'배송대기', 'N22'=>'배송보류', 'N30'=>'배송중', 'N40'=>'배송완료', 
            'C00'=>'취소신청', 'C10'=>'취소접수', 'C34'=>'취소처리중', 'C36'=>'취소처리중', 'C40'=>'취소완료', 
            'C47'=>'입금전취소', 'C48'=>'입금전취소', 'C49'=>'입금전취소', 
            'R00'=>'반품신청', 'R10'=>'반품접수', 'R12'=>'반품보류', 'R30'=>'반품처리중', 'R34'=>'반품처리중', 'R36'=>'반품처리중', 'R40'=>'반품완료(환불완료)', 
            'E00'=>'교환신청', 'E10'=>'교환접수', 'E12'=>'교환보류', 'E20'=>'교환준비', 'E30'=>'교환처리중', 'E32'=>'교환처리중', 'E34'=>'교환처리중', 
            'E36'=>'교환처리중', 'E40'=>'교환완료'
		);
	}	
	
	/**
	* @ api서버응답결과 가공
	**/
	public function _responseResult($response) {
		$jsonResult = json_decode($response);
		if($jsonResult->error) {
			if($jsonResult->error->code == '403') {
				$this->getAuthCode();
			} else $result = array('result'=>'error','code'=>$jsonResult->error->code,'message'=>$jsonResult->error->message); 
		} else $result = array('result'=>'succ','data'=>$jsonResult);
		return $result;		
	}
	
	public function _getCategoryList($setparams="") {
		if(!is_array($setparams))$setparams=array();
		
		$this->sendMethod = 'GET';	
	
		$params['url'] = $this->getResponseUrls['category'];
		$sendparams = array();

		if($setparams['cname'])$sendparams[] = 'category_name='.$setparams['cname'];
		if($setparams['code'])$sendparams[] = 'category_no='.$setparams['code'];
		if($setparams['depth'])$sendparams[] = 'category_depth='.$setparams['depth'];
		if($setparams['parent_no'])$sendparams[] = 'parent_category_no='.$setparams['parent_no'];

		if(count($sendparams)>0) {
			$params['url'] = $params['url'].'?'.implode('&',$sendparams);
		}
		return $params;
	}

	public function _getCategoryResponse($datas) {
		$resultItem = array();
		foreach($datas->categories as $data) {			
			$resultItem[] = array('code'=>$data->category_no,'name'=>$data->category_name,'depth'=>$data->category_depth,'parent_code'=>$data->parent_category_no);
		}
		return $resultItem;		
	}

	public function _getGoodsList($setparams="") {		
		if(!is_array($setparams))$setparams=array();
		
		$params['url'] = $this->getResponseUrls['goodsList'];
		
		$this->sendMethod = 'GET';	
		$sendparams[] = 'display=T';
		$sendparams[] = 'selling=T';
		$sendparams[] = 'limit='.$this->resultLimit;

		if($setparams['gname'])$sendparams[] = 'product_name='.urlencode($setparams['gname']);
		if($setparams['keyword'])$sendparams[] = 'product_tag='.urlencode($setparams['keyword']);		
		if($setparams['brand'])$sendparams[] = 'brand_code='.urlencode($setparams['brand']);
		if($setparams['category'])$sendparams[] = 'category='.urlencode($setparams['category']);
		if($setparams['gno'])$sendparams[] = 'product_no='.$setparams['gno'];
		if($setparams['startDate']) $sendparams[] = 'created_start_date='.$setparams['startDate'];
		if($setparams['endDate']) $sendparams[] = 'created_end_date='.$setparams['endDate'];
		
		$params['url'] = $params['url'].'?'.implode('&',$sendparams);
		
		return $params;
	}
	
	public function _getGoodsResponse($datas) {
		$resultItem = array();
		foreach($datas->products as $data) {
			$resultItem[] = array(
			    'no'=>$data->product_no, 
			    'gname'=>$data->product_name, 
			    'img'=>$data->list_image,
			    'category'=>$data->category, 
			    'brand'=>$data->brand_code, 
			    'price'=>$data->price, 
			    'new'=>$data->list_icon->new_icon, 
			    'recommend'=>$data->list_icon->recommend_icon,
			    'date'=>$data->created_date, 
			    'link'=>$this->mallDomain.'/product/detail.html?product_no='.$data->product_no
			);
		}
		return $resultItem;		
	}

	public function _getBrandList($setparams="") {
		$datas['result'] = 'error';
		$datas['code'] = 'CBOT404';
		$datas['message'] = '지원하지 않는 API 코드입니다';
		$this->responseResultPrint($datas);
		exit;	
	}
	
	public function _getOrderList($setparams="") {
		if(!is_array($setparams))$setparams=array();
		
		$params['url'] = $this->getResponseUrls['orderList'];
		
		// 디폴트 1달간 검색
		$end_date = date('Y-m-d', time());
		$_tempDate = explode('-', $end_date);
		$start_date = date("Y-m-d", mktime(0,0,0, $_tempDate[1]-1, $_tempDate[2], $_tempDate[0]));
		
		$this->sendMethod = 'GET';	
		$sendparams[] = 'start_date='.$start_date;
		$sendparams[] = 'end_date='.$end_date;
		$sendparams[] = 'buyer_cellphone='.urlencode($setparams['buyer_cellphone']);
		$sendparams[] = 'limit='.$this->resultLimit;

		if($setparams['buyer_phone'])$sendparams[] = 'buyer_phone='.urlencode($setparams['buyer_phone']);
		if($setparams['buyer_email'])$sendparams[] = 'buyer_email='.urlencode($setparams['buyer_email']);
		
		$params['url'] = $params['url'].'?'.implode('&',$sendparams);
		return $params;
	}
	
	public function _getOrderResponse($datas) {		    
		$resultItem = array();
		$aShippers = $this->getShippers();
		
		foreach($datas->orders as $data) {
		    $order_status = $ship_company = $ship_no = $ship_url = '';
		    
		    // 주문별 주문상품 정보
		    $params = array();
		    $params['url'] = $this->getResponseUrls['orderList'].'/'.$data->order_id.'/items';
		    $response = json_decode($this->sendCurl2($params));
		    $item = $response->items[0];
		    
		    $product_no = $item->product_no; // 상품번호
		    $gname = $item->product_name.(count($response->items) > 1 ? ' 외 '.(count($response->items)-1) : '');
		    
		    $order_status = $this->orderStatus[$item->order_status]; // 주문상태
		    $is_shipping = $item->order_status == 'N30' ? true : false;
		    
		    // 배송 정보
		    if($is_shipping) {
    		    $ship_company = $item->shipping_company_name;
    		    $ship_no = $item->tracking_no;
    		    $ship_url = array_key_exists($ship_company, $aShippers) ? $aShippers[$ship_company].$ship_no : '';
    		}
		    
		    // 주문상품 이미지
		    $params = array();
		    $params['url'] = $this->getResponseUrls['goodsList'].'/'.$product_no;
		    $response = json_decode($this->sendCurl2($params));		    
		    $goods = $response->product;
		    
		    $img = $goods->list_image; // 상품 이미지
		    
			$resultItem[] = array(
			    'order_id'=>$data->order_id,
			    'order_status'=>$order_status,
			    'is_shipping'=>$is_shipping,
			    'gname'=>$gname,
			    'img'=>$img,
			    'ship_company'=>$ship_company,
			    'ship_no'=>$ship_no,
			    'ship_url'=>$ship_url,
			    'link'=>$this->mallDomain.'/myshop/order/detail.html?order_id='.$data->order_id
			);
		}
		return $resultItem;		
	}
	

	public function _setOrderUpdate($setparams) {
		$datas['result'] = 'error';
		$datas['code'] = 'CBOT404';
		$datas['message'] = '지원하지 않는 API 코드입니다';
		$this->responseResultPrint($datas);
		exit;	
	}

	public function _getCommonCode($code) {
		$datas['result'] = 'error';
		$datas['code'] = 'CBOT404';
		$datas['message'] = '지원하지 않는 API 코드입니다';
		$this->responseResultPrint($datas);
		exit;	
	}
	
	public function sendCurl2($params) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$params['url']);		
		if($this->setHeader)curl_setopt($ch, CURLOPT_HTTPHEADER, $this->setHeader);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);		
		$result = curl_exec($ch); 
		curl_close($ch);
		return $result;
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

