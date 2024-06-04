
<section id="intro-custom">
	<div class="intro-img-wrappper" id="custom-1" style="background-image:url(<?php echo $g['img_layout'].'/m_custum_bg1.png'?>);">
        <div id="custom01-inner">
            <div id="btn-wrapper">
               <div class="intro-btn"><img src="<?php echo $g['img_layout']?>/peso.png" alt="페르소나 홈페이지" /></div>
               <div class="intro-btn"><img src="<?php echo $g['img_layout']?>/down.png" alt="소개서다운로드" /></div>
            </div>
        </div>
	</div>
    <?php for($i=2;$i<8;$i++):?> 
    <div class="intro-img-wrappper" id="custom-<?php echo $i?>" style="background-image:url(<?php echo $g['img_layout'].'/m_custum_bg'.$i,'.png'?>);">

    </div>
    <?php endfor?>
</section>
