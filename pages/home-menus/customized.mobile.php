
<section id="intro-custom">
	<div class="intro-img-wrappper" id="custom-1">
        <div id="btn-wrapper">
            <div class="intro-btn"><img src="<?php echo $g['img_layout']?>/peso.png" alt="페르소나 홈페이지" /></div>
            <div class="intro-btn"><img src="<?php echo $g['img_layout']?>/m_down.png" alt="소개서다운로드" /></div>
        </div>
        <img src="<?php echo $g['img_layout'].'/m_custom1.png'?>" />
	</div>
    <?php for($i=2;$i<8;$i++):?> 
    <div class="intro-img-wrappper" id="custom-<?php echo $i?>">
        <img src="<?php echo $g['img_layout'].'/m_custom'.$i.'.png'?>" />
    </div>
    <?php endfor?>
</section>
