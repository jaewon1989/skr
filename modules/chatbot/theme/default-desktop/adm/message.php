<?php
$_WHERE2='vendor='.$V['uid'];
if($botuid) $_WHERE .=' and botuid='.$botuid.' and type=2';
$BCD = getDbArray($table[$m.'bot'],$_WHERE2,'*','gid','asc','',1);

if($botuid){
    $NB = getDbData($table[$m.'bot'],'uid='.$botuid,'id');
    $now_bid = $NB['id'];  
}
$RFB = $chatbot->getFirstBotData($V['uid'],1);
$B_id = $now_bid?$now_bid:$RFB['id']; // chatbox id 부여
$themeName ='default-desktop'; 
$emoticon_path = $g['dir_module'].'lib/emoticon/';
$chatbox_mod ='message';// chatbox 좌상단 화살표 표시 안나오게 하기 위한 변수
$cmod = $page;
?>
<style>
#search-form {position: absolute;width:457px;left: 0;top: 0px;z-index: 20}
.cb-mychatbot-adinput-submit span {cursor: pointer;}
.cb-mychatbot-iphone .cb-chatting-info {margin-top: 0;}
.cb-mychatbot-adinput-box {position: relative;}
.cb-mychatbot-adinput-box textarea {padding: 12px;}
#cb-chatting-input {bottom: -40px;}
#cb-chatting-input .cb-chatting-icon li {height: 30px;padding-top:2px;}
</style>
<div class="cb-mychatbot-main" style="padding-top: 20px; padding-right: 0;">
    <div class="cb-mychatbot-adinput-wrapper" style="position:relative;">
        <h1>메세지입력</h1>
        <div id="search-form">
    		<input type="hidden" name="r" value="<?php echo $r?>" />
            <input type="hidden" name="c" value="<?php echo $c?>" />
            <input type="hidden" name="m" value="<?php echo $m?>" />
            <div class="cb-viewchat-search-timebox" style="width:50%;float:right;margin-right:0">
                <select name="botuid" style="font-size:inherit;">
                   <?php $i=1;while($B=db_fetch_array($BCD)):?>
                    <option value="<?php echo $B['uid']?>" <?php if($botuid==$B['uid']):?>selected<?php endif?>>
                        <?php echo str_replace('입학상담 폴리봇','통합봇',$B['service'])?>
                    </option>
                    <?php $i++;endwhile?> 
                </select>
            </div>

        </div>
        <div class="cb-mychatbot-adinput cb-layout">
            <div class="cb-left">
            	<form name="procForm" action="<?php echo $g['s']?>/" method="post" enctype="multipart/form-data">
		            <input type="hidden" name="r" value="<?php echo $r?>" />
		            <input type="hidden" name="c" value="<?php echo $c?>" />
		            <input type="hidden" name="m" value="<?php echo $m?>" />
		            <input type="hidden" name="a" value="regis_ADmessage" />
		            <input type="hidden" name="vendor" value="<?php echo $V['uid']?>" />
	                <div class="cb-mychatbot-adinput-box">
	                    <textarea name="message" data-role="ta-message" placeholder="메세지 내용을 입력해주세요."></textarea>
	                </div>
	                <div class="cb-mychatbot-adinput-submit">
	                    <span class="cb-mychatbot-adinput-preview" data-role="btn-previewMessage">미리보기</span>
	                    <span class="cb-mychatbot-adinput-send" data-role="btn-sendMessage">보내기</span>
	                </div>
               </form>
            </div>
            <div class="cb-right">
                <div class="cb-mychatbot-iphone">
                    <div class="cb-mychatbot-chatarea" id="msgChat-wrapper">
                    	<!-- 챗팅 박스 삽입 -->		
					</div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo $g['url_root']?>/_core/js/rc.swiper.js"></script>
<script src="<?php echo $g['url_root']?>/_core/js/jquery.timer.js"></script>
<script src="<?php echo $g['url_root']?>/_core/js/jquery.bottalks.2.0.js"></script>
<script>
$(function() {
    $('#msgChat-wrapper').PS_chatbot({
        moduleName : '<?php echo $m?>',
        themeName : '<?php echo $themeName?>',
        emoticon_path : '<?php echo $emoticon_path?>',
        botId : '<?php echo $bot_id?>',
        cmod : '<?php echo $cmod?>',
    });
});

</script>
<script>
$(function(){
 
    // 광고 메세지 전송 이벤트 
    $(document).on('click','[data-role="btn-previewMessage"]',function(){
            var module = '<?php echo $m?>';
    var message_ta = $('textarea[name="message"]');
    var message = $(message_ta).val(); 
    var vendor = $('input[name="vendor"]').val();
    var botuid = $('select[name="botuid"]').val();
        var msgBox = $('.cb-chatting-scroll');
        if(botuid==''){
            alert('챗봇 서비스를 선택해주세요.');
            return false;
        }
        if(message==''){
            alert('메세지를 입력해주세요.   ');
            $(message_ta).focus();
            return false;
        }
        $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=do_UserAction',{
            act : 'show-previewMessage',
            message : message,
            botuid : botuid
        },function(response){
            var result = $.parseJSON(response);
            var bot_msg = result.content;
            $(msgBox).append(bot_msg);
        });  
    });

    $('[data-role="btn-sendMessage"]').on('click',function(){
        var module = '<?php echo $m?>';
        var message_ta = $('textarea[name="message"]');
        var message = $(message_ta).val(); 
        var vendor = $('input[name="vendor"]').val();
        var botuid = $('select[name="botuid"]').val();
        var container = $('.cb-mychatbot-adinput-box');
        if(botuid==''){
            alert('챗봇 서비스를 선택해주세요.');
            return false;
        }
        if(message==''){
            alert('메세지를 입력해주세요.   ');
            $(message_ta).focus();
            return false;
        }
        $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=regis_ADmessage',{
            message : message,
            vendor : vendor,
            botuid : botuid,
            send_mod : 'mobile'
        },function(response){
           var result = $.parseJSON(response);
           var message=result.message;
           show__Notify(container,message);  
        });  
         
    });

});


</script>
