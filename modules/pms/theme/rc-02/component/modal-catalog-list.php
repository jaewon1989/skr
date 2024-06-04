<style>
.rb-catalog-list .swiper-button-prev, 
.rb-catalog-list .swiper-button-next {
    position: absolute;
    top: 25%;
    width: 27px;
    height: 44px;
    margin-top: 0;
    background-image: none;
    color: #fff;
}
.rb-catalog-list .swiper-button-prev {
    left: 15px
}
.rb-catalog-list .swiper-button-next {
    right: 23px
}
.rb-catalog-list .swiper-button-prev .icon, 
.rb-catalog-list .swiper-button-next .icon {
    font-size: 40px;
    text-shadow: 1px 1px 1px rgba(0,0,0,0.6);
    z-index: 10
}
.rb-catalog-list .swiper-container {
    padding-bottom: 30px
}



</style>

<?php echo $cat?>
<link href="/plugins/swiper/3.2.7/css/swiper.css" rel="stylesheet">
<link href="/modules/catalog/theme/rc-02/style.css" rel="stylesheet"> 
    <div class="page center" id="page-catalog-list">
        <div class="rb-catalog-list">
            <header class="bar bar-nav">
                <a class="icon icon-left-nav pull-left" data-history="back"></a>
                <button class="btn btn-link btn-nav pull-right"><span class="icon icon-more-vertical"></span></button>
                <h1 class="title" data-role="catName"><!-- 카테고리명 --></h1>
            </header>
            <div class="content" data-role="content" id="swipper-parent" data-contentbg-role="contentbg"> 
                  <!-- 상품 리스트 : swiper theme/main.func.php 참조 -->
            </div>
        </div>
    </div>
    <div class="page right" id="page-catalog-view" >
        <div class="rb-catalog-view">
            <header class="bar bar-nav rb-nav">
                <a class="btn btn-link btn-nav pull-left" href="#" data-history="back"><span class="icon icon-left-nav"></span></a>
                <button class="btn btn-link btn-nav pull-right"><span class="icon icon-more-vertical"></span></button>
                <h1 class="title" data-role="productName"><!-- 상품명 --></h1>
            </header>

            <div class="bar bar-standard bar-footer rb-buttons">
                <div class="row">
                    <div class="col-xs-6">
                        <a href="#" data-toggle="page" data-start="#page-catalog-view" data-target="#page-catalog-contact" data-title="'" data-url="" data-id="" data-role="event-handler" class="btn btn-primary btn-block">가입신청</a>
                    </div>
                    <div class="col-xs-6">
                        <button class="btn btn-primary btn-block">전화상담</button>
                    </div>
                </div>
            </div>
            <div class="content" data-contentbg-role="contentbg">
                <div class="row">

                    <div class="col-sm-6">
                        <section class="rb-cover">
                            <div class="card card-full">
                                <img class="card-img img-fluid rb-cover-image" src="" alt="" data-role="cover-img">
                                <div class="card-img-backdrop rb-cover-bg"></div>
                                <div class="card-img-overlay text-xs-center" id="cover-name">
                                    <p class="card-text" data-role="review"><!-- 리뷰 영역 --></p>
                                    <h3 class="card-title" data-role="productName"><!-- title 영역 --></h3>
                                </div>
                            </div>
                        </section>

                    </div>

                    <div class="col-sm-6">

                        <section class="rb-detail">
                            <div class="segmented-control content-padded">
                              <a class="control-item active" href="#tab-detail-01">
                                상세정보
                              </a>
                              <a class="control-item" href="#tab-detail-02">
                                유의사항
                              </a>
                              <a class="control-item" href="#tab-detail-03">
                                사진보기
                              </a>
                            </div>
                            <div class="card">
                                <span id="tab-detail-01" class="control-content active content-padded rb-article" data-role="content01">

                                </span>
                                <span id="tab-detail-02" class="control-content content-padded rb-article" data-role="content02"></span>

                                <span id="tab-detail-03" class="control-content content-padded rb-article" data-role="content03">

                                </span>

                            </div>
                        </section>

                    </div>

                </div>
                <div style="height: 50px"></div>
            </div>
        </div>
    </div>

    <div class="page right" id="page-catalog-contact" >
        <div class="rb-catalog-form">
            <header class="bar bar-nav rb-nav">
                <a class="btn btn-link btn-nav pull-left" href="#" data-history="back">취소</a>
                <h1 class="title" data-role="title"><!-- 상품명 --> </h1>
            </header>

            <div class="bar bar-standard bar-footer rb-buttons">
                <a href="#" class="btn btn-primary btn-block" data-role="event-handler" data-toggle="submit" data-module="catalog" data-actionfile="get_Sendquestion" data-target="#question-form" data-id="" >전송하기</a>
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
 <script type="text/javascript">
// 애니메이션 바
(function($){
    var fadeStart=50
        ,fadeUntil=90
        ,fading = $('.rb-cover-image')
        ,fading2 = $('.rb-cover-bg')
        ,fading3 = $('.rb-nav')
        ,fading4 = $('.rb-cover-name')
    ;
    $('.content').bind('scroll', function(){
        var offset = $(this).scrollTop()
            ,opacity=0
            ,opacity2=1
        ;
        if( offset<=fadeStart ){
            opacity=1;
            opacity2=0;
            opacity4=1;
            fading3.removeClass('rb-inverse fadeIn animated');
        }else if( offset<=fadeUntil ){
            opacity=1-offset/fadeUntil;
            opacity2=offset/fadeUntil;
            opacity4=0.7-offset/fadeUntil;
            fading3.removeClass('rb-inverse fadeIn animated');
        }else if( offset>fadeUntil ){
            fading3.addClass('rb-inverse fadeIn animated');
        }
        fading.css('opacity',opacity);
        fading2.css('opacity',opacity2);
        fading4.css('opacity',opacity4);
        
    });
})(jQuery)
  
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
            alert(result.msg);
      });        
  })
</script>