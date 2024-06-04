<?php

// content 내용 분리 함수 
function Shop_theme_getContentArray($content){
    $content_array=explode('<p>{{{{{Tab-Content}}}}}</p>',$content);
    return $content_array;
}

// 라벨 네임출력 함수
function Shop_theme_getLabelName($label)
{
     $label_name=array('01'=>'여행/크루즈','02'=>'웨딩','03'=>'결혼정보','04'=>'어학캠프','05'=>'약관대출','06'=>'장례');
     $label_arr=explode('.',$label);
     $label_arr2=explode('-',$label_arr[0]);
     $label_no=$label_arr2[2];
     
      return $label_name[$label_no];
}

//상품강조사진
function Shop_theme_getGoodsLabel($R)
{
	global $g,$m;
	$retag = '';
	$labels = explode(',',$R['icons']);
	foreach($labels as $pic)
	{
		if (!$pic) continue;
		$picName=Shop_theme_getLabelName($pic);
		$retag .= '
            <div class="card text-xs-center">
                        <img class="card-img-top" src="'.$g['url_root'].'/modules/'.$m.'/var/icons/'.$pic.'" alt="'.$picName.'" style="height: 60px">
           </div>';

	}
	return $retag;
}

// 상품 gallery 추출 함수 
function Shop_theme_getGoodsGallery($R){
      global $g,$table;

      $upload=getArrayString($R['upload']);
      $_WHERE='(';
      foreach($upload['data'] as $val){
         if($val!=$R['featured_img'])  $_WHERE.='uid='.$val.' or ';
      }
      $_WHERE= substr($_WHERE,0,strlen($_WHERE)-4).')';	
      $_WHERE.=' and hidden=0';
      $sort	= 'gid';
      $orderby ='asc';
      $recnum =20;
      $p=1;
    
      $RCD = getDbArray($table['s_upload'],$_WHERE,'*',$sort,$orderby,$recnum,$p);
      $gallery='
      <section class="rb-photos content-padded">
           <div class="card-columns">';
      while($U=db_fetch_array($RCD)){
      	      $origin=$U['url'].$U['folder'].'/'.$U['tmpname'];
      	      $thumb=getDynamicResizeImg($origin,'250_180');
                 $fileName=explode('.',$U['name']);
                 $file_name=$fileName[0]; // 파일명만 분리 
                 $caption=$U['caption']?$U['caption']:$file_name;
                 $gallery.='
	           <div class="card">
	                 <img class="card-img img-fluid" src="'.$thumb.'" alt="'.$caption.'">
	           </div>';
      }
      $gallery.='
           </div>
      </section> 
      ';    

      return $gallery;

}

// 가입신청 버튼 추출함수 
function Shop_theme_getBtnJoin($R){
	$btn='<a href="#" data-toggle="page" data-start="#page-catalog-list" data-target="#page-catalog-contact" data-title="'.$R['name'].'" data-url="/catalog/product/'.$R['uid'].'/contact" class="btn btn-primary btn-block">가입신청</a>';
}

// 상품 리스트 출력 함수 
function Shop_theme_getProductList($category,$sort,$orderby,$recnum,$_WHERE){
      global $g,$table,$m;
      include_once $g['path_module'].$m.'/var/var.php';
       $sort	= $sort?$sort:'gid';
	 $orderby =$orderby?$orderby:'desc';
	 $recnum =$recnum?$recnum:20;
	 $p=1;
	 $_WHERE='category='.$category;   
	 $RCD = getDbArray($table[$m.'product'],$_WHERE,'*',$sort,$orderby,$recnum,$p);
      $NUM=getDbRows($table[$m.'product'],$_WHERE);
      if($NUM){
           $list='
             <div class="swiper-container">
                     <div class="swiper-wrapper">';
             while($R=db_fetch_array($RCD)){
               $U=getUidData($table['s_upload'],$R['featured_img']); // 대표 이미지 uid 로 찾기  
                      $FI=$U['url'].$U['folder'].'/'.$U['tmpname']; // 오리지널 이미지 path
                      $WI=getDynamicResizeImg($FI,'500_239'); // 위젯 이미지 동적 리사이징 (sys.function.php 참조)
                      $CI=getDynamicResizeImg($FI,'500_200'); // 커버 이미지 동적 리사이징 (sys.function.php 참조)  
                      $data_etc_array=array();
                      $data_etc_array['review']=$R['review']; // 리뷰 
                      $data_etc_array['cover-img']=$CI; // 커버 이미지
                      $data_etc_array['productName']=$R['name']; // 상품명 
                      $data_etc_array['price']=number_format($R['price']).' 원'; 
                      $data_etc_array['addinfo']=$R['addinfo']; // 부가정보                
                      $data_etc_json=json_encode($data_etc_array,true);
                      $data_etc=str_replace("\"","'", $data_etc_json);
                       $CAT=getUidData($table[$m.'category'],$R['category']);
                      $theme=$CAT['skin_mobile']?$CAT['skin_mobile']:$d['shop']['skin_mobile']; // 테마
                     $list.='
                           <div class="swiper-slide">
                                <section class="rb-buttons content-padded">
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <a href="#" data-toggle="page" data-start="#page-catalog-list" data-target="#page-catalog-contact2" data-title="'.$R['name'].'" data-etc="'.$data_etc.'" data-url="/catalog/product/'.$R['uid'].'/contact" data-id="'.$R['uid'].'" class="btn btn-primary btn-block">가입신청</a>
                                        </div>
                                        <div class="col-xs-6">
                                            <a href="tel:1544-1507" class="btn btn-primary btn-block">전화상담</a>
                                        </div>
                                    </div>
                                </section>

                                <section class="rb-cover">
                                    <div class="card card-inverse">
                                        <img class="card-img img-fluid" src="'.$CI.'" alt="'.$U['caption'].'">
                                        <div class="card-img-overlay text-xs-center">
                                            <p class="card-text">'.$R['review'].'</p>
                                            <h3 class="card-title">'.$R['name'].'</h3>
                                        </div>
                                    </div>
                                </section>

                                <section class="rb-price content-padded">
                                    <div class="input-group">
                                      <div class="input-row">
                                        <label>총 납입금액</label>
                                        <span class="badge badge-primary badge-inverted rb-total">'.number_format($R['price']).' 원</span>
                                      </div>
                                      <div class="input-row">
                                         '.$R['addinfo'].'
                                      </div>
                                    </div>
                                </section>

                                <section class="rb-related">
                                    <h3 class="content-padded">사용가능 상품</h3>
                                    <div class="card-group">
                                         '.Shop_theme_getGoodsLabel($R).'
                                    </div>
                                </section>

                                <section class="content-padded">
                                    <a href="#" data-toggle="page" data-start="#page-catalog-list" data-target="#page-catalog-view" data-title="'.$R['name'].'" data-etc="'.$data_etc.'" data-url="/catalog/product/'.$R['uid'].'" 
                                         data-theme="'.$g['path_module'].$m.'/theme/'.$theme.'/" 
                                        data-themeurl="/modules/catalog/theme/'.$theme.'" 
                                        data-module="'.$m.'" 
                                        data-content="catalog-view"
                                        data-actionfile="get_ProductContent"
                                        data-id="'.$R['uid'].'" class="btn btn-positive btn-block">구성품목 및 유의사항</a>
                                </section>
                            </div>';
             }
                 $list.=' 
                     </div>

                      <div class="swiper-pagination"></div>
                      <div class="swiper-button-prev"><span class="icon icon-left-nav"></span></div>
                      <div class="swiper-button-next"><span class="icon icon-right-nav"></span></div>
                      
                </div>';
      }else{
           $list=getNoListMarkup();
      }
	 

	 return $list;

}

function Shop_theme_getMembershipList($category,$sort,$orderby,$recnum,$_WHERE){
       global $g,$table,$m;
       $sort = $sort?$sort:'gid';
       $orderby =$orderby?$orderby:'desc';
       $recnum =$recnum?$recnum:20;
       $p=1;
       $_WHERE='category='.$category;   
       $RCD = getDbArray($table[$m.'product'],$_WHERE,'*',$sort,$orderby,$recnum,$p);
       $NUM=getDbRows($table[$m.'product'],$_WHERE);

      if($NUM){
           $list=' 
            <div class="swiper-container">
                <div class="swiper-wrapper">';
                 while($R=db_fetch_array($RCD)){
                  $U=getUidData($table['s_upload'],$R['featured_img']); // 대표 이미지 uid 로 찾기  
                  $FI=$U['url'].$U['folder'].'/'.$U['tmpname']; // 오리지널 이미지 path
                  $WI=getDynamicResizeImg($FI,'500_239'); // 위젯 이미지 동적 리사이징 (sys.function.php 참조)
                  $CI=getDynamicResizeImg($FI,'500_200'); // 커버 이미지 동적 리사이징 (sys.function.php 참조)  
                  $data_etc_array=array();
                  $data_etc_array['review']=$R['review']; // 리뷰 
                  $data_etc_array['cover-img']=$CI; // 커버 이미지
                  $data_etc_array['productName']=$R['name']; // 상품명 
                  $data_etc_array['price']=number_format($R['price']).' 원'; 
                  $data_etc_array['addinfo']=$R['addinfo']; // 부가정보                
                  $data_etc_json=json_encode($data_etc_array,true);
                  $data_etc=str_replace("\"","'", $data_etc_json);       
                 $list.='
                    <div class="swiper-slide">
                        <div class="card">
                            <img class="card-img-top img-fluid" src="'.$FI.'" alt="Card image cap">
                            <div class="card-block">
                                '.$R['addinfo'].'
                            </div>
                        </div>
                    </div>';
                  } 
                 $list.='
                </div>
                 <div class="swiper-pagination"></div>
            </div>';
      }else{
            $list=getNoListMarkup();
      }
      
       return $list;     
}

function getNoListMarkup(){
      $noList='
           <div class="rb-nopost content-padded">
                <i class="fa fa-exclamation-circle"></i>
                <p>리스트가 존재하지 않습니다. </p>
            </div>';

       return $noList;     
      
}

?>