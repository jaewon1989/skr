<?php
$RFB = $chatbot->getFirstBotData($V['uid'],1);
$B_uid = $RFB['uid']; 
?>
<style>
.cb-row-3d {
    width: auto !important;
}

</style>
<section id="cb-chatbot-factory" style="padding: 0 35px">
    <div class="cb-chatbot-factory-wrapper">
        <div class="cb-chatbot-factoryline">
           <form name="procForm" action="<?php echo $g['s']?>/" method="post" enctype="multipart/form-data" onsubmit="return saveCheck(this);">
                <input type="hidden" name="r" value="<?php echo $r?>" />
                <input type="hidden" name="c" value="<?php echo $c?>" />
                <input type="hidden" name="m" value="<?php echo $m?>" />
                <input type="hidden" name="a" value="regis_eventGoods" />
                <input type="hidden" name="mbruid" value="<?php echo $my['uid']?>" />
                <input type="hidden" name="uid" value="<?php echo $B_uid?>" />
                <div class="cb-chatbot-recommendation">
                    <div class="cb-chatbot-recommendation-top">
                        <span class="cb-title">이벤트 상품</span>
                        <span class="cb-add-recommendation" data-role="add-goods">추가하기</span>
                    </div>
                    <div class="cb-chatbot-recommendation-list" id="recommend-rows">
                        <?php echo $chatbot->getVendorGoods($V['uid'],$B_uid,'regis-page')?>
                        <!-- 추가되는 Chunk -->
                        <div class="cb-chatbot-recommendation-item cb-row" data-role="recommend-item">
                            <div class="cb-row-3d">
                                <div class="cb-chatbot-recommendation-item-wrapper"> 
                                    <input name="goods_code[]" placeholder="영화코드" type="text" />
                                </div>                                    
                            </div>
                            <div class="cb-row-3d">
                                <div class="cb-chatbot-recommendation-item-wrapper"> 
                                    <input name="goods_name[]" placeholder="영화명" type="text" />
                                </div>                                    
                            </div>
                            <div class="cb-row-3d last-3d">
                                <div class="cb-chatbot-recommendation-item-wrapper">
                                    <input name="goods_link[]" placeholder="URL" type="text" />
                                </div>
                            </div>
                        </div>
               
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
// 이벤트 상품 추가 이벤트 
$('[data-role="add-goods"]').on('click',function(){
     var clone_obj = $('[data-role="recommend-item"]:last');
     $(clone_obj).clone().appendTo('#recommend-rows');
     setTimeout(function(){
        $(document).find('[data-role="recommend-item"]:last').find('input[name="goods_code[]"]').val('');
        $(document).find('[data-role="recommend-item"]:last').find('input[name="goods_name[]"]').val('');
        $(document).find('[data-role="recommend-item"]:last').find('input[name="goods_link[]"]').val('');
     },10);   

});

$(document).on('click','[data-role="del-eventGoods"]',function(e){
    var uid = $(this).data('uid');
    var item = $(this).parent().parent();
    $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=do_UserAction',{
        act : 'del-eventGoods',
        uid : uid
    },function(response){
        var result=$.parseJSON(response);//$.parseJSON(response);
        var content = result.content;
        if(content =='OK') $(item).remove();  
    });  
});

function saveCheck(f)
{
    getIframeForAction(f);
    f.submit();
}
</script>
