<?php 
include $g['dir_module_skin'].'build/step_top.php'; // 단계 출력 부분
?>

<section id="cb-chatbot-factory">
    <div class="cb-chatbot-factory-wrapper">
        <div class="cb-chatbot-factoryline">
           <form name="procForm" action="<?php echo $g['s']?>/" method="post" enctype="multipart/form-data" onsubmit="return saveCheck(this);">
                <input type="hidden" name="r" value="<?php echo $r?>" />
                <input type="hidden" name="c" value="<?php echo $c?>" />
                <input type="hidden" name="m" value="<?php echo $m?>" />
                <input type="hidden" name="a" value="update_bot" />
                <input type="hidden" name="mbruid" value="<?php echo $my['uid']?>" />                                
                <input type="hidden" name="uid" value="<?php echo $uid?>" />
                <input type="hidden" name="next_page" value="build/step5" />
                <input type="hidden" name="upload" value="<?php echo $R['upload']?>" />
                <input type="hidden" name="step" value="4" />
                <div class="cb-chatbot-recommendation">
                    <div class="cb-chatbot-recommendation-top">
                        <span class="cb-title">추천상품</span>
                        <span class="cb-add-recommendation" data-role="add-goods">추가하기</span>
                    </div>
                    <div class="cb-chatbot-recommendation-list" id="recommend-rows">
                        <?php echo $chatbot->getVendorGoods($V['uid'],$uid,'regis-page')?>
                        <!-- 추가되는 Chunk -->
                        <div class="cb-chatbot-recommendation-item cb-row" data-role="recommend-item">
                            <div class="cb-row-2d">
                                <div class="cb-chatbot-recommendation-item-wrapper"> 
                                    <input name="goods_name[]" placeholder="상품명" type="text" />
                                </div>                                    
                            </div>
                            <div class="cb-row-2d">
                                <div class="cb-chatbot-recommendation-item-wrapper">
                                    <input name="goods_link[]" placeholder="URL" type="text" />
                                </div>
                            </div>
                        </div>
               
                    </div>
                </div>
                <div class="cb-chatbot-recommendation">
                    <div class="cb-chatbot-recommendation-top">
                        <span class="cb-title">추가질문/답변</span>
                        <span class="cb-add-recommendation" data-role="add-vqa">추가하기</span>
                    </div>
                    <div class="cb-chatbot-recommendation-list" id="vqa-rows">
                         <?php echo $chatbot->getVendorQA($V['uid'],$uid);?> <!-- vendor/bot 기준 개별 질문/답변 -->  
                    </div>
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
<script>
// 추천상품 추가 이벤트 
 $('[data-role="add-goods"]').on('click',function(){
     var clone_obj = $('[data-role="recommend-item"]:last');
     $(clone_obj).clone().appendTo('#recommend-rows');
     setTimeout(function(){
         $(document).find('[data-role="recommend-item"]:last').find('input[name="goods_name[]"]').val('');
         $(document).find('[data-role="recommend-item"]:last').find('input[name="goods_link[]"]').val('');
     },10);
    

 });

 // 추가질문/답변 추가 이벤트 
 $('[data-role="add-vqa"]').on('click',function(){
     var clone_obj = $('[data-role="vqa-item"]:last');
     $(clone_obj).clone().appendTo('#vqa-rows');
     setTimeout(function(){
         $(document).find('[data-role="vqa-item"]:last').find('input[name="vqa_reply[]"]').val('');
         $(document).find('[data-role="vqa-item"]:last').find('input[name="vqa_question[]"]').val('');
     },10);
    

 });

function saveCheck(f)
{
    getIframeForAction(f);
    f.submit();
}
</script>
