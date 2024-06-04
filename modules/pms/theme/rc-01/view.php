<?php 
include_once $g['dir_module_skin'].'main.func.php';
$U=getUidData($table['s_upload'],$R['featured_img']); // 대표 이미지 uid 로 찾기  
$FI=$U['url'].$U['folder'].'/'.$U['tmpname']; // 오리지널 이미지 path
$CI=getDynamicResizeImg($FI,'500_200'); // 커버 이미지 동적 리사이징 (sys.function.php 참조) 
$content_arr=Shop_theme_getContentArray($R['content']);
$content=array();
$content['label']=Shop_theme_getGoodsLabel($R);
$content['content01']=$content_arr[0];
$content['content02']=$content_arr[1];
$content['content03']=Shop_theme_getGoodsGallery($R);
?>
<link href="<?php echo $g['url_module_skin']?>/style.css" rel="stylesheet"> 
 <div class="rb-catalog-view">
	 <div class="page center" id="page-catalog-view">
	    <header class="bar bar-nav">
	        <a href="<?php echo $g['s']?>/" class="icon icon-home pull-left"></a>
	        <h1 class="title"><?php echo $R['name']?></h1>
	    </header>

	    <div class="bar bar-standard bar-footer rb-buttons">
	        <div class="row">
	            <div class="col-xs-6">
	                <a data-toggle="page" data-start="#page-catalog-view" data-target="#page-catalog-contact2" data-title="<?php echo $R['name']?>" data-url="" data-id="<?php echo $R['uid']?>"  class="btn btn-primary btn-block">가입신청</a>
	            </div>
	            <div class="col-xs-6">
	                <a href="tel:1544-1507" class="btn btn-primary btn-block">전화상담</a>
	            </div>
	        </div>
	    </div>
	    <div class="content">
	        <div class="row">
	            <div class="col-sm-6">
	                <section class="rb-cover">
	                    <div class="card card-full card-inverse">
	                        <img class="card-img img-fluid" src="<?php echo $CI?>" alt="Card image">
	                        <div class="card-img-overlay text-xs-center">
	                            <p class="card-text"><?php echo $R['review']?></p>
	                            <h3 class="card-title"><?php echo $R['name']?></h3>
	                        </div>
	                    </div>
	                </section>

	                <section class="rb-price content-padded">
	                    <div class="input-group">
	                      <div class="input-row">
	                        <label>총 납입금액</label>
	                        <span class="badge badge-primary badge-inverted rb-total"><?php echo number_format($R['price']).' 원'?></span>
	                      </div>
	                      <div class="input-row">
	                       <?php echo $R['addinfo']?>
	                      </div>
	                    </div>
	                </section>

	                <section class="rb-buttons content-padded">

	                </section>

	                <section class="rb-related content-padded">
	                    <h3>사용가능 상품</h3>
	                    <div class="card-group">
	                       <?php echo $content['label']?>
	                    </div>
	                </section>
	            </div>

	            <div class="col-sm-6">

	                <section class="rb-detail">
	                    <div class="segmented-control content-padded">
	                      <a class="control-item active" href="#tab-detail-01">
	                        구성품목
	                      </a>
	                      <a class="control-item" href="#tab-detail-02">
	                        유의사항
	                      </a>
	                      <a class="control-item" href="#tab-detail-03">
	                        사진보기
	                      </a>
	                    </div>
	                    <div class="card">
	                        <span id="tab-detail-01" class="control-content active content-padded rb-article"><?php echo $content['content01']?></span>
	                        <span id="tab-detail-02" class="control-content content-padded rb-article"><?php echo $content['content02']?></span>

	                        <span id="tab-detail-03" class="control-content content-padded rb-article"><?php echo $content['content03']?></span>
	                    </div>
	                </section>

	            </div>

	        </div>
	        <div style="height: 50px"></div>

	    </div>
	</div>

	<div class="page right" id="page-catalog-contact2">
	    <div class="rb-catalog-form">
	        <header class="bar bar-nav rb-nav">
	            <a href="#" data-history="back" class="btn btn-link btn-nav pull-left">취소</a>
	            <h1 class="title"><?php echo $R['name']?></h1>
	        </header>

	        <div class="bar bar-standard bar-footer rb-buttons">
	            <button class="btn btn-primary btn-block" data-role="event-handler" data-toggle="submit" data-module="catalog" data-actionfile="get_Sendquestion" data-target="#question-form" data-id="<?php echo $R['uid']?>">신청하기</button>
	        </div>
	        <div class="content">
	            <div class="row">
	                <div class="col-sm-6">

	                    <section class="rb-form content-padded">

	                        <h3>가입문의</h3>
	                        <p>가입신청서를 작성해 주시면 담당자가 연락드리겠습니다.</p>

	                        <form id="question-form">
	                                <input type="text" placeholder="이름" name="name">
	                                <input type="text" placeholder="휴대폰" name="hp">
	                                <input type="email" placeholder="이메일" name="email">
	                                <textarea rows="5" name="content"></textarea>
	                        </form>

	                    </section>

	                </div>
	                <div class="col-sm-6">
	                </div>
	            </div>
	            <div style="height: 50px"></div>
	        </div>
	    </div>
	</div>
</div>	
 <script type="text/javascript">
  $(document).on('tap click','[data-toggle="submit"]',function(e){
      e.preventDefault();
      var form=$(this).data('target');
      var module=$(this).data('module');
      var actionfile=$(this).data('actionfile');
      var uid=$(this).attr('data-id');
      var formData=$(form).serializeArray();
      var chkEmail=chkEmailAddr(formData[2].value);
      if(formData[2].value==''){
            alert('이메일 주소를 입력해주세요 ');
            $(form).find('input[name="email"]').focus();
            return false;
      }else if(!chkEmail){
            alert('올바른 이메일 주소가 아닙니다.');
            $(form).find('input[name="email"]').focus();
            return false;
      } 
      $.post('/?m='+module+'&a='+actionfile,{
            from_name : formData[0].value,
            from_hp : formData[1].value,
            from_email : formData[2].value,
            from_content : formData[3].value,
            module : module,
            uid : uid   
      },function(response){
            var result=$.parseJSON(response);
            console.log(result);
            alert(result.msg);
      });        
  })

// 포토 캡션 
var doPhotoCaption=function(){
    var img=$('.rb-article').find('img');
    $(img).each(function(){
          var alt=$(this).attr('alt');
          var figure='<figure class="rb-media">';
          var figcaption='<figcaption>'+alt+'</figcaption>';
          $(this).wrap(figure);
          $(this).parent().append(figcaption); 
          
    });
}

$(document).ready(function(){
      doPhotoCaption();
});

</script>