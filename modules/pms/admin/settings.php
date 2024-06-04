<?php
include_once $g['path_module'].$module.'/var/var.php';
$configset = array
(
	'theme' => '기본환경 설정',
	// 'config' => '사업자정보',
	// 'bank' => '은행계좌등록',
	// 'card' => '신용카드PG설정',
	// 'pay' => '결제관련설정',
	// 'tack' => '배송관련설정',
	// 'cancel' => '주문/취소/반품설정',
	// 'qna' => '상품문의/평가설정',
	// 'tax' => '계산서발행설정',
	// 'addinfo' => '부가정보 세트',
	// 'options' => '상품옵션 세트',
	// 'server' => '사진서버',
	//'makerbrand' => '제조사/브랜드',
	//'productmsg' => '상품공통메세지',
);
$type = $type ? $type : 'theme';
?>
<div class="row">
     <div class="col-md-4 col-lg-3">
   	     <div class="panel panel-default">  <!-- 메뉴 리스트 패털 시작 -->
   			<div class="panel-heading rb-icon">
				<div class="icon">
					<i class="fa fa-file-text-o fa-2x"></i>
				</div>
				<h4 class="panel-title">운영환경 설정</h4>
			</div>
			
			<div class="panel-body" style="border-top:1px solid #DEDEDE;">
				  <div class="list-group rb-list-group">
				  	    <?php foreach($configset as $key => $val):?>
				  	         <a href="<?php echo $g['adm_href']?>&amp;type=<?php echo $key?>" class="list-group-item<?php echo $type==$key?' active':''?>"><?php echo $val?></a>
				  	    <?php endforeach?>
				  </div>
                </div>   
		</div> <!-- 좌측 패널 끝 -->	
      </div><!-- 좌측  내용 끝 -->	
      <!-- 우측 내용 시작 -->
      <div id="tab-content-view" class="col-md-8 col-lg-9">
	      <?php include_once $g['dir_module_admin'].'/'.$type.'.php';?>
      </div> <!-- 우측내용 끝 --> 
</div> <!-- .row 전체 box --> 


