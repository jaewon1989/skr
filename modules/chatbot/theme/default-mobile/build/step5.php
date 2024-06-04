<?php 
include $g['dir_module_skin'].'build/step_top.php'; // 단계 출력 부분
?>
<section id="cb-chatbot-factory">
    <div class="cb-chatbot-factory-wrapper">
        <div class="cb-chatbot-factoryline">
          
            <div class="cb-chatbot-finish">
                <img src="<?php echo $g['img_layout']?>/bobo.jpg" alt="chatbot image" />

                <h1><?php echo $my[$_HS['nametype']]?>님의 챗봇이 <?php echo $uid?'수정되었습니다.':'만들어졌습니다.'?></h1>

                <a href="<?php echo RW('c=mybot')?>">확인</a>
            </div>
            
        </div>
    </div>
</section>