<?php
$bot_id = $_GET['botid']?$_GET['botid']:($B['id']?$B['id']:$B_id);
$themeName ='default-desktop';
$emoticon_path = $g['dir_module'].'lib/emoticon/';
$cmod = $_GET['cmod'];
$B = $chatbot->getBotDataFromId($bot_id);
$bot_service = $cmod=='monitering'?'상담원':$B['bot_service'];
$bot_avatar_src = $B['bot_avatar_src'];
$B['bot_skin'] = $_GET['skin'] ? $_GET['skin'] : $B['bot_skin'];
$bot_skin = ($B['bot_skin'] && file_exists($g['path_core']."skin/css/".$B['bot_skin'])) ? $B['bot_skin'] : "skin.default.css";
$socketiourl = $g['web_socket_host'].":".$g['web_socket_port'];
$use_unity = $B['bottype'] == 'chat' && ($B['bot_interface'] == 'character' || $B['bot_interface'] == '3dmanual') ? 'on' : '';

if ('59.10.147.17' === $_SERVER['HTTP_X_FORWARDED_FOR']) {
    //print_r($B);
}

?>

<link href="<?php echo $g['s']?>/_core/skin/css/<?=$bot_skin?>?<?=date("YmdH")?>" rel="stylesheet">
<link href="<?php echo $g['s']?>/_core/skin/css/skin.theme.css" rel="stylesheet">
<link href="<?php echo $g['s']?>/_core/skin/css/intro.css" rel="stylesheet">
<link href="<?php echo $g['s']?>/_core/css/form.css?<?=date("YmdHi")?>" rel="stylesheet">
<link href="<?php echo $g['s']?>/_core/css/datepicker.css" rel="stylesheet">
<link href="<?php echo $g['s']?>/_core/css/picker.date.css" rel="stylesheet">
<link href="<?php echo $g['s']?>/_core/css/auto-complete.css" rel="stylesheet">
<link href="<?php echo $g['s']?>/_core/css/jquery.modal.css" rel="stylesheet">

<?php if($_GET['call']=='external' || $cmod =='dialog' || $cmod =='monitering'):?>
<style>
    html, body {height: 100%;}
    #cb-chatzone .cb-chatzone-wrapper {margin-top: -55px; width: 100% !important;}
    .kwd-item {font-size: 1rem;}
    <?php if($cmod =='monitering'):?>
    .chatSwitch-label {position: absolute;top: 25%; right:68px;}
    .chatSwitch-type {-webkit-appearance:auto !important;}
    .chatBox-switch {margin-left: 45px; margin-right:0px !important;}
    .cb-switch {width:60px !important;}
    .switch-label {position: absolute; top: 45%; left: 10px; color: #fff; font-size: 0.9em;}
    .botSwitch-off {margin-left:15px;}
    .botSwitch-off .switch-label {right: -10px;}
    .kwd-item {margin-right: 20px;}
    .cb-chatting-balloon span {color: #fff;}
    <?php endif?>
</style>
<?php endif?>

<div id="chatBox-container"></div>

<? if(($B['use_chatting'] == 'on' || $B['use_cschat'] == 'on') && $cmod != 'dialog' && $cmod != 'skin') {?>
<script src="<?php echo $g['s']?>/plugins/socket.io-client/1.7.2/socket.io.js"></script>
<?}?>
<script src="<?php echo $g['s']?>/_core/js/encryption.js?<?=date("Ymd")?>"></script>
<script src="<?php echo $g['s']?>/_core/js/jquery.timer.js"></script>
<script src="<?php echo $g['s']?>/_core/js/jquery.mask.min.js"></script>
<script src="<?php echo $g['s']?>/_core/js/jquery.datepicker.js?2.2.3"></script>
<script src="<?php echo $g['s']?>/_core/js/picker.date.js"></script>
<script src="<?php echo $g['s']?>/_core/js/jquery.auto-complete.js"></script>
<script src="<?php echo $g['s']?>/_core/js/jquery.fileform.js"></script>
<script src="<?php echo $g['s']?>/_core/js/jquery.modal.js"></script>
<script src="<?php echo $g['s']?>/_core/js/jquery.bottalks.2.0.js?<?=date("YmdH")?>"></script>
<script src="<?php echo $g['s']?>/_core/js/jquery.bottalks.cschat.js?<?=date("YmdH")?>"></script>
<? if($B['cgroup'] && file_exists($g['path_core'].'js/jquery.bottalks.'.$B['cgroup'].'.js')) {?>
<script src="<?php echo $g['s']?>/_core/js/jquery.bottalks.<?=$B['cgroup']?>.js?<?=date("YmdH")?>"></script>
<?} else {?>
<script src="<?php echo $g['s']?>/_core/js/jquery.bottalks.reserve.js?<?=date("YmdH")?>"></script>
<?}?>
<script>
    var chatbot;
    var isAdm = <?=($cmod =='monitering' ? 1 : 0)?>;

    $(function() {
    	$('#chatBox-container').PS_chatbot({
        	moduleName : '<?php echo $m?>',
        	themeName : '<?php echo $themeName?>',
        	emoticon_path : '<?php echo $emoticon_path?>',
            bot_service: '<?php echo $bot_service?>',
            bot_avatar_src: '<?php echo $bot_avatar_src?>',
            botId : '<?php echo $bot_id?>',
            cmod : '<?php echo $cmod?>',
            socketioUrl : '<?php echo $socketiourl?>',
            use_chatting : '<?php echo $B['use_chatting']?>',
            bot_type: '<?php echo $B['bottype']?>',
            bot_skin: '<?php echo str_replace('.css', '', $bot_skin)?>',
            bot_intro: '<?php echo $B['intro_use']?>',
            showNotice: true,
            isAdm: isAdm,
            userNameText: '<?php echo $my['nic']?$my['nic']:'손님'?>',
            showTimer: '<?php echo $showTimer?>',
            reserve: '<?php echo $B['use_reserve']?>',
            faq_usable: '<?php echo $B['faq_usable']?>',
            bot_interface : '<?php echo $B['bot_interface']?>',
            use_unity : '<?=$use_unity?>',
            use_cschat : '<?=$B['use_cschat']?>',
            cschat_userinfo : '<?=$B['cschat_userinfo']?>',
            error_msg : '<?=$B['error_msg']?>',
            timeout : '<?=$B['timeout']?>',
            timeout_msg : '<?=$B['timeout_msg']?>',
            msg_chatgpt : '<?=$B['msg_chatgpt']?>'
        });
        chatbot = $('#chatBox-container')[0].getBottalksObj();

        window.onunload = function () {
            chatbot.mode = 'chat_force_end';
            csChatting.getCSChatForceEnd(chatbot);
        }

        // 외부에서 호출시 해당 프레임 높이 체크
        <?php if($_GET['call']=='external' || $cmod == 'dialog' || $cmod =='monitering'):?>
        $('#chatBox-container').on('shown.ps.chatbot',function(e){
            var wh = $(window).height();
            var dh = $(document).height();
            var sh = screen.height;
            var topOffset = 30;
            var height = ((wh > 0) ? wh : sh) - 1;
            height = height - topOffset;
            var default_height = height - 240;
            var mot_height = height;
            var last_height;
            var chatBody = $('#cb-chatting-body');
            <?php if($cmod =='monitering'):?>
                last_height = mot_height;
            <?php else:?>
                last_height = default_height;
            <?php endif?>

            chatBody.css({'height': last_height+'px'});
        });
        <?php endif?>

        console.log("1");
    });

    function setLayer(){

        const floating = '<div class="panel layer active" id="floatingLayerPanel">' +
            '<div class="panel-header"></div>' +
            '<div class="panel-contents">' +
            '<div class="layer-url"><button class="btn-sclose" onclick="closeLayer()"><span class="none">닫기</span></button><button class="btn-pre" onclick="closeLayer()"><span class="none">이전</span></button><input type="text"><button class="btn-next" onclick="closeLayer()"><span class="none">다음</span></button></div>' +
            '<iframe src="https://m.blog.naver.com/PostView.naver?blogId=skrentacar_official&logNo=222932083473&referrerCode=0&searchKeyword=%EC%A0%9C%EC%A3%BC%20%EC%9C%84%EC%B9%98"></iframe>' +
            '</div>' +
            '</div>';

        const common = '<div class="commonpanel bottom" id="commonLayerPanel">'+
            '<div class="panel-header"></div>'+
            '<div class="panel-contents"><span class="commonLayer-icon">아이콘</span><dl><dt>알림</dt><dd></dd></dl><p></p>'+
            '<ul class="flex-center">'+
            '<li><button id="popup-action" onclick="location.reload();">처음으로</button></li>'+
            '</ul>'+
            '</div>'+
            '</div>';

        const quick = '<div class="quickpanel top" id="quickLayerPanel">'+
            '<div class="panel-header"></div>'+
            '<div class="panel-contents"><dl><dt>퀵메뉴 영역</dt><dd></dd></dl><p></p>'+
            '<ul class="flex-center">'+
            '<li><!--button id="popup-action" onclick="location.reload();">새로고침</button--></li>'+
            '</ul>'+
            '</div>'+
            '</div>';

        $(".cb-category-contentarea").css('height', '1000px');

        $(".bg-wrap").after(common).after(quick);
        $(".bg-wrap, #commonLayerPanel, #quickLayerPanel").addClass('active');
    }

</script>