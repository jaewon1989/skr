<?php
/*
 AR 에서 사용자 음성인식 후 txt로 변경해서 receiveChat() 을 실행하면
 userInput 값에 txt 값이 할당이 되어 jquery.bottalks.2.0.js 파일에서
 justProcessInput() 함수가 실행된다.

*/
$bot_id = $_GET['botid']?$_GET['botid']:($B['id']?$B['id']:$B_id);
$B = $chatbot->getBotDataFromId($bot_id);
$themeName ='default-mobile';
$emoticon_path = $g['dir_module'].'lib/emoticon/';
$cmod = $_GET['cmod'];
$bot_service = $cmod=='monitering'?'상담원':$B['bot_service'];
$bot_avatar_src = $B['bot_avatar_src'];
$B['bot_skin'] = $_GET['skin'] ? $_GET['skin'] : $B['bot_skin'];
$bot_skin = ($B['bot_skin'] && file_exists($g['path_core']."skin/css/".$B['bot_skin'])) ? $B['bot_skin'] : "skin.default.css";
$socketiourl = $g['web_socket_host'].":".$g['web_socket_port'];
$use_unity = $B['bottype'] == 'chat' && ($B['bot_interface'] == 'character' || $B['bot_interface'] == '3dmanual') ? 'on' : '';
?>

<link href="<?php echo $g['s']?>/_core/skin/css/<?=$bot_skin?>?<?=date("YmdHi")?>" rel="stylesheet">
<link href="<?php echo $g['s']?>/_core/skin/css/skin.theme.css" rel="stylesheet">
<link href="<?php echo $g['s']?>/_core/skin/css/intro.css" rel="stylesheet">
<link href="<?php echo $g['s']?>/_core/css/form.css?<?=date("YmdHi")?>" rel="stylesheet">
<link href="<?php echo $g['s']?>/_core/css/datepicker.css" rel="stylesheet">
<link href="<?php echo $g['s']?>/_core/css/picker.date.css" rel="stylesheet">
<link href="<?php echo $g['s']?>/_core/css/auto-complete.css" rel="stylesheet">
<link href="<?php echo $g['s']?>/_core/css/jquery.modal.css" rel="stylesheet">

<input type="hidden" name="huid" />
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
    var mbruid = '<?php echo $my['uid']?>';
    var isAdm = <?=($cmod =='monitering' ? 1 : 0)?>;

    $(function() {
        $('#chatBox-container').PS_chatbot({
            moduleName : '<?php echo $m?>',
            themeName : '<?php echo $themeName?>',
            emoticon_path : '<?php echo $emoticon_path?>',
            bot_service: '<?php echo $bot_service?>',
            bot_avatar_src: '<?php echo $bot_avatar_src?>',
            botId : '<?php echo $bot_id?>',
            vendor: '<?php echo $B['vendor']?>',
            bot: '<?php echo $B['bot']?>',
            dialog: '<?php echo $B['dialog']?>',
            cmod : '<?php echo $cmod?>',
            socketioUrl : '<?php echo $socketiourl?>',
            use_chatting : '<?php echo $B['use_chatting']?>',
            bot_interface : '<?php echo $B['bot_interface']?>',
            bot_type: '<?php echo $B['bottype']?>',
            bot_skin: '<?php echo str_replace('.css', '', $bot_skin)?>',
            bot_intro: '<?php echo $B['intro_use']?>',
            showNotice: true,
            isAdm: isAdm,
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
    });

    <? if($B['bot_interface'] =='voice'){?>
    // VOICE 초기화 함수
    function initChat(txt){
        var txt = txt == '' || txt == undefined ? '' : txt;
        var data = {'init':true, 'STT':txt};
        chatbot.voiceProcessInput(data);
    }

    function receiveChat(txt) {
        chatbot.voiceProcessInput(txt);
    }

    if(window.Unity !== undefined) Unity.call('init'); // 초기화 함수 호출 : unity 에서는 아무 동작 없이 초기화 함수 호출 한다.
    <?}?>
</script>
<style>
    .bg-wrap {
        position: absolute;
        width: 100%;
        height: 100vh;
        background: rgb(50, 50, 50, 0.7);
        top: 0;
        left: 0;
        z-index: 90;
        display: none;
    }

    .active {
        display: block !important;
    }
</style>