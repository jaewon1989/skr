<?php 
include $g['dir_module_skin'].'build/step_top.php'; // 단계 출력 부분
$quesTap = array("주문"=>"order","결제"=>"pay","배송"=>"delivery","취소"=>"cancel","환불"=>"refund"); // 질문 탭 배열

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
                <input type="hidden" name="next_page" value="build/step3" />                
                <input type="hidden" name="induCat" value="<?php echo $R['induCat']?>" />
                <input type="hidden" name="upload" value="<?php echo $R['upload']?>" />
                <input type="hidden" name="step" value="2" />
                <div class="cb-chatbot-policytab">
                    <ul>
                        <?php foreach ($quesTap as $label => $name):?>
                        <li class="nav-item<?php if($name=='order'):?> active<?php endif?>">
                            <a class="nav-link" data-toggle="tab" href="#qset-<?php echo $name?>">
                                <?php echo $label?>
                            </a>
                        </li>
                        <?php endforeach?>
                    </ul>
                </div>
                <div class="tab-content">
                    <?php foreach ($quesTap as $label => $name):?>
                    <div class="tab-pane<?php if($name=='order'):?> active<?php endif?>" id="qset-<?php echo $name?>"> 
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
