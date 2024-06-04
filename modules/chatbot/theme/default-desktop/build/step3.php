<?php 
include $g['dir_module_skin'].'build/step_top.php'; // 단계 출력 부분
$quesTap = array("반품"=>"return","교환"=>"exchange","AS"=>"AS","웹사이트"=>"website","기타"=>"etc"); // 질문 탭 배열
?>

<section id="cb-chatbot-factory">
    <div class="cb-chatbot-factory-wrapper">
        <div class="cb-chatbot-factoryline">
             <form name="procForm" action="<?php echo $g['s']?>/" method="post" enctype="multipart/form-data">
                <input type="hidden" name="r" value="<?php echo $r?>" />
                <input type="hidden" name="c" value="<?php echo $c?>" />
                <input type="hidden" name="m" value="<?php echo $m?>" />
                <input type="hidden" name="a" value="update_bot" />
                <input type="hidden" name="mbruid" value="<?php echo $my['uid']?>" />                                
                <input type="hidden" name="uid" value="<?php echo $uid?>" />
                <input type="hidden" name="next_page" value="build/step4" /> 
                <input type="hidden" name="upload" value="<?php echo $R['upload']?>" />
                <input type="hidden" name="step" value="3" />
                <div class="cb-chatbot-policytab">
                    <ul class="nav nav-tabs" role="tablist">
                        <?php foreach ($quesTap as $label => $name):?>
                        <li<?php if($name=='return'):?> class="active"<?php endif?>>
                            <a href="#qset-<?php echo $name?>" role="tab" data-toggle="tab"><?php echo $label?></a>
                        </li>
                        <?php endforeach?>
                    </ul>
                </div>
                <div class="tab-content">
                    <?php foreach ($quesTap as $label => $name):?>
                    <div class="tab-pane fade<?php if($name=='return'):?> active in<?php endif?>" id="qset-<?php echo $name?>"> 
                         <?php echo $chatbot->getVendorReply($V['uid'],$label)?>
                    </div>
                    <?php endforeach?>
                </div>        

                <div class="cb-chatbot-casedone cb-row">
                    <div class="cb-row-2d">
                        <div class="cb-submitbutton cb-go-previous" onclick="javascript:history.back();">이전으로</div>
                    </div>
                    <div class="cb-row-2d">
                        <input class="cb-submitbutton" type="submit" value="다음으로" />
                    </div>
                </div>
                       
            </form>
        </div>    
    </div>
</section>
