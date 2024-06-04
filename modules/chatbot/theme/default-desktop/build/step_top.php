<?php
// 모듈 설정파일 
include $g['dir_module'].'var/var.php';

if($vendor){
    $R=getUidData($table[$m.'vendor'],$vendor);
}

// 스펨방지 코드 
if (!$_SESSION['upsescode']) $_SESSION['upsescode'] = str_replace('.','',$g['time_start']);
$sescode = $_SESSION['upsescode'];

$stepArray = array("1" => 0,"2" => 25,"3" => 50,"4" => 75,"5" => 100);
$step = str_replace('build/','',$page);

function get__currentStep_class($_step){
   global $step;

   if(str_replace('step','',$step)==$_step) $result =' cb-currentstep';
   else $result='';

   return $result;
}
?>
<section id="cb-chatbot-factory">
    <div class="cb-chatbot-factory-wrapper">
        <div class="cb-chatbot-factoryline">
            <div class="cb-chatbot-factorystep">
                <div class="cb-chatbot-factorystep-drawline"></div>
                <?php foreach ($stepArray as $_step => $left):?>
                <div class="cb-chatbot-factorystep-item<?php echo get__currentStep_class($_step)?>" style="left: <?php echo $left?>%;">
                    <div class="cb-radio-circle">
                        <span class="cb-radio-innercircle"></span>
                    </div>
                    <div class="cb-radio-label"><?php echo $_step!='5'?$_step.'단계':'완료'?></div>
                </div>
                <?php endforeach?>
          
            </div>
            <?php if($step!='step5'):?>
            <div class="cb-chatbot-talking">
                <div class="cb-layout">
                    <div class="cb-left">
                        <img src="<?php echo $g['img_layout']?>/bobo.jpg" alt="chatbot image" />
                    </div>
                    <div class="cb-right">
                        <div class="cb-chatting-balloon">
                            <p>
                                <?php if($page=='build/step1'):?>
                                안녕하세요 <b><?php echo $my[$_HS['nametype']]?></b>님을 위한 인공지능 챗봇입니다^^
                                업종을 입력해주세요.
                                <?php elseif($page=='build/step4'):?>
                                <?php echo $my[$_HS['nametype']]?>님! 추천상품과 URL을 입력해보세요. 고객들에게 추천해드려요.
                                <?php else:?>
                                <?php echo $my[$_HS['nametype']]?>님! 회사 정책을 입력해주세요 ^^
                                 미입력시에는 기본답변이 노출됩니다.
                                <?php endif?>

                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif?>
        </div>
    </div>
</section>