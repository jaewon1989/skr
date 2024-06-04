<!-- 모달시작 -->
 
    <div class="rb-catalog-list">
        <header class="bar bar-nav">
            <button class="btn btn-link btn-nav pull-left" data-history="back">
                <span class="icon icon-left-nav"></span>
            </button>
            <button class="btn btn-link btn-nav pull-right"><span class="icon icon-more-vertical"></span></button>
            <h1 class="title" data-role="title"> <!-- 카테고리 타이틀 --> </h1>
        </header>
        <div class="content" data-role="content">
                <!-- 상품 리스트 : swiper-->        
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

  var doSwiper=function(){
      // Initialize Swiper
      var swiper = new Swiper('.rb-catalog-list .swiper-container', {
          pagination: '.rb-catalog-list .swiper-pagination',
          paginationClickable: true,
          spaceBetween: 30,
          nextButton: '.swiper-button-next',
          prevButton: '.swiper-button-prev',
      });  
  }
  window.addEventListener('push', doSwiper);
</script>