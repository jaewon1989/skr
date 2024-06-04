
<section id="intro-custom">
	<div class="intro-img-wrappper" id="custom-1" style="background-image:url(<?php echo $g['img_layout'].'/custum_bg1.jpg'?>);">
        <div id="custom01-inner">
            <div id="btn-wrapper">
               <div class="intro-btn"><img src="<?php echo $g['img_layout']?>/peso.png" alt="페르소나 홈페이지" /></div>
               <div class="intro-btn"><img src="<?php echo $g['img_layout']?>/down.png" alt="소개서다운로드" /></div>
            </div>
        </div>
	</div>
    <div class="intro-img-wrappper" id="custom-2" style="background-image:url(<?php echo $g['img_layout'].'/custum_bg2.jpg'?>);">
        <div class="banner-wrapper">
            <div class="banner-inner">
                <span class="cb-icon cb-icon-prev swiper-button-prev"></span>
                <span class="cb-icon cb-icon-next swiper-button-next"></span>       
                <div class="swiper-container">
                    <ul class="swiper-wrapper">
                        <?php for($i=1;$i<4;$i++):?>
                        <li class="cb-slidebanner-item swiper-slide">
                            <img src="<?php echo $g['img_layout']?>/slide<?php echo $i?>.png" alt="Slide-0<?php echo $i?>" />
                        </li>
                        <?php endfor?>
                    </ul>
                </div>
            </div>
        </div>         
    </div>
    <?php for($i=3;$i<8;$i++):?> 
    <div class="intro-img-wrappper" id="custom-<?php echo $i?>" style="background-image:url(<?php echo $g['img_layout'].'/custum_bg'.$i,'.jpg'?>);">

    </div>
    <?php endfor?>
</section>

<script>
$(document).ready(function() {
    var swiper = new Swiper('.swiper-container', {
        nextButton: '.swiper-button-next',
        prevButton: '.swiper-button-prev',
        slidesPerView: 1,
        paginationClickable: true,
        spaceBetween: 0,
        grabCursor: true,
        loop: true
    });
});
</script>
