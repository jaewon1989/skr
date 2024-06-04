<?php 
$vendor = $V['uid'];
$quesTap = array("요금"=>"fee","해지"=>"cancel"); // 질문 탭 배열
$RFB = $chatbot->getFirstBotData($vendor,1); // 첫번째 bot 추출 
$botuid = $botuid?$botuid:$RFB['uid'];
?>
<style>
.cb-chatbot-factory-wrapper h1{
    font-size: 22pt;
    font-weight: 200;
    margin: 0;
    margin-top: 10px;
}    
</style>
<section id="cb-chatbot-factory" style="padding: 35px 35px 0px 35px">
    <div class="cb-chatbot-factory-wrapper">
        <h1>Q&A 관리</h1>
        <div class="cb-chatbot-factoryline" style="padding-top:0">
             <form name="procForm" action="<?php echo $g['s']?>/" method="post" enctype="multipart/form-data">
                <input type="hidden" name="r" value="<?php echo $r?>" />
                <input type="hidden" name="c" value="<?php echo $c?>" />
                <input type="hidden" name="m" value="<?php echo $m?>" />
                <input type="hidden" name="a" value="update_reply" />
                <input type="hidden" name="mbruid" value="<?php echo $my['uid']?>" />                                
                <input type="hidden" name="uid" value="<?php echo $botuid?>" />
                <input type="hidden" name="return_link" value="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;c=mybot/qna" /> 
                <div class="cb-chatbot-policytab" style="height:0">
                    
                </div>
                <div class="tab-content">
                    <?php foreach ($quesTap as $label => $name):?>
                    <div class="tab-pane fade<?php if($name=='fee'):?> active in<?php endif?>" id="qset-<?php echo $name?>"> 
                         <?php echo $chatbot->getVendorReply($V['uid'],$label)?>
                    </div>
                    <?php endforeach?>
                </div> 
                 <div class="cb-chatbot-recommendation">
                    <div class="cb-chatbot-recommendation-top">
                        <span class="cb-title" style="color:#fff;">추가질문/답변</span>
                        <span class="cb-add-recommendation" data-role="add-vqa">추가하기</span>
                    </div>
                    <div class="cb-chatbot-recommendation-list" id="vqa-rows">
                         <?php echo $chatbot->getVendorQA($V['uid'],$botuid);?> <!-- vendor/bot 기준 개별 질문/답변 -->  
                    </div>
                </div>       

                 <div class="cb-chatbot-casedone cb-row">
                    <div class="cb-row-3d" style="width: 33.3% !important">
                    </div>
                    <div class="cb-row-3d" style="width: 33.3% !important">
                        <input class="cb-submitbutton" type="submit" value="저장하기" />
                    </div>
                     <div class="cb-row-3d" style="width: 33.3% !important">
                    </div>
                </div>
                       
            </form>
        </div>    
    </div>
</section>
<script>
 // 추가질문/답변 추가 이벤트 
 $('[data-role="add-vqa"]').on('click',function(){
     var clone_obj = $('[data-role="vqa-item"]:last');
     $(clone_obj).clone().appendTo('#vqa-rows');
     setTimeout(function(){
         $(document).find('[data-role="vqa-item"]:last').find('input[name="vqa_reply[]"]').val('');
         $(document).find('[data-role="vqa-item"]:last').find('input[name="vqa_question[]"]').val('');
     },10);
    

 });
</script>
