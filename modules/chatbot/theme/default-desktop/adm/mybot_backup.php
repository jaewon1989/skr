<div class="cb-mychatbot-insertion">
    <div class="cb-layout">
        <div class="cb-left">
            <div class="cb-mychatbot-list">
                <div class="cb-mychatbot-list-plusline" id="addBot-wrapper">
                    <a href="<?php echo RW('c=build')?>">
                       <span class="cb-icon cb-icon-plus"></span>
                       <span class="cb-name">추가하기</span>
                    </a>
                </div>
                <?php if(!$NUM):?>
                    <div class="cb-chatbot-item">
                        <div class="cb-chatbot-centering">
                            <a href="<?php echo RW('c=build')?>"> 
                            <span class="cb-icon cb-icon-plus2"></span>
                            </a>
                        </div>                                        
                    </div>
                <?php else:?>
                    <?php $i=1;while($B=db_fetch_array($BCD)):?>
                    <?php $chatUrl = $chatbot->getChatUrl($B);?>
                    <div id="bot-box-<?php echo $B['uid']?>">
                        <div class="cb-chatbot-item" style="position:relative;" id="botUrl-wrapper-<?php echo $i?>">
                            <div class="cb-rightward">
                                <span class="cb-icon cb-icon-close" data-act="delete-bot" data-uid="<?php echo $B['uid']?>" ></span>
                            </div>
                            <div class="cb-centerward" >
                                <span class="cb-chatbotname"><?php echo $B['service']?></span>
                                <div class="cb-circle-image">
                                    <a href="<?php echo $chatUrl?>" >
                                       <img src="<?php echo $chatbot->getBotAvatarSrc($B)?>" alt="Circle Image">
                                    </a>
                                </div>
                                <div class="cb-button cb-button-copyurl" data-role="clipboard-copy" data-clipboard-text="<?php echo $chatUrl?>" data-container="#botUrl-wrapper-<?php echo $i?>" data-feedback="챗봇 URL 이 복사되었습니다.">URL 복사</div>
                                <div class="cb-button cb-button-geturl" data-role="get-boturl" data-uid="<?php echo $B['uid']?>">퍼가기</div>
                            </div>
                        </div>
                        <div class="cb-chatbot-item-enableoption"  style="position:relatvie;margin-bottom:20px">
                            <div class="cb-cell-layout">
                                <div class="cb-cell cb-cell-left">
                                    <span class="cb-label" data-role="botActive-label-<?php echo $B['uid']?>">챗봇 활성화</span>
                                </div>
                                <div class="cb-cell cb-cell-right<?php echo !$B['display']?' botSwitch-off':''?>">
                                    <div class="cb-switch" data-role="bot-active" data-uid="<?php echo $B['uid']?>" data-container="#botUrl-wrapper-<?php echo $i?>">
                                        <span class="cb-switch-button"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="cb-chatbot-item-enableoption"  style="position:relatvie;margin-bottom:20px">
                            <div class="cb-cell-layout">
                                <div class="cb-cell cb-cell-left">
                                    <span class="cb-label">설정 변경</span>
                                </div>
                                <div class="cb-cell cb-cell-right">
                                    <a href="<?php echo RW('c=build')?>&amp;uid=<?php echo $B['uid']?>">
                                        <div class="cb-switch icon-go-wrap">
                                           <span class="cb-icon cb-icon-go"></span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php $i++;endwhile?>
                <?php endif?>
            </div>
        </div>
        <div class="cb-right">
            <div id="chatbox-wrapper" >
             <style>
             #cb-chatting-input {bottom: 0 !important;}
             </style>   
            <?php 
                $RFB = $chatbot->getFirstBotData($V['uid'],1);
                $B_id = $RFB['id']; // chatbox id 부여
                $chatbox_mod ='mybot';// chatbox 좌상단 화살표 표시 안나오게 하기 위한 변수
                include $g['dir_module_skin'].'chat.php';
            ?>
            </div>
            <div id="geturl-wrapper" style="display:none;">
                <div id="show-chatbox" data-role="show-chatbox">채팅창 보기</div>
                <?php $wdg_array = array("원형"=>1,"가로형"=>2,"정사각형"=>3);?>
                <?php $i=1;foreach($wdg_array as $label=>$index):?>
                <div class="getbox-wrapper">
                    <h1><?php echo $label?></h1>
                    <div class="getbox-inner" id="sourceImg-type0<?php echo $i?>">
                        <img src="<?php echo $g['img_layout']?>/geturl_0<?php echo $i?>.png" alt="avatar-type0<?php echo $i?>"/>
                    </div>
                    <div class="copyurl-box" id="getSource-type0<?php echo $i?>" >
                        <span class="iframe-source"><input type="text" data-role="show-boturl"/></span>
                        <span class="copy-text copy-boturl" data-role="clipboard-copy" data-clipboard-text="" data-container="#sourceImg-type0<?php echo $i?>" data-feedback="챗봇 퍼가기 소스가 복사되었습니다.">복사</span>
                    </div>
                </div> 
                <?php $i++;endforeach?>            
            </div>
        </div>   
    </div>
</div>
<script>
$(document).on('click','[data-role="get-boturl"]',function(){
    var uid = $(this).data('uid');
    var src_type01 = '<iframe src="http://www.bottalks.co.kr/getWidget/'+uid+'/type01" width="150" height="150" frameborder="0"></iframe>';
    var src_type02 = '<iframe src="http://www.bottalks.co.kr/getWidget/'+uid+'/type02" width="341" height="173" frameborder="0"></iframe>';
    var src_type03 = '<iframe src="http://www.bottalks.co.kr/getWidget/'+uid+'/type03" width="200" height="200" frameborder="0"></iframe>';
    $('#getSource-type01').find('[data-role="show-boturl"]').val(src_type01);
    $('#getSource-type01').find('.copy-boturl').attr('data-clipboard-text',src_type01);
    $('#getSource-type02').find('[data-role="show-boturl"]').val(src_type02);
    $('#getSource-type02').find('.copy-boturl').attr('data-clipboard-text',src_type02); 
    $('#getSource-type03').find('[data-role="show-boturl"]').val(src_type03);
    $('#getSource-type03').find('.copy-boturl').attr('data-clipboard-text',src_type03);
    
    // 최종적으로 각 박스 숨김/보임 
    $('#chatbox-wrapper').hide();
    $('#geturl-wrapper').show();
});
$(document).on('click','[data-role="show-chatbox"]',function(){
     $('#chatbox-wrapper').show();
     $('#geturl-wrapper').hide(); 
});
</script>
