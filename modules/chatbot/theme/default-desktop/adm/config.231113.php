<?php
$_data = array();
$_data['bot'] = $bot;
$_data['mod'] ='form';
$getAdBot = $chatbot->getAdmBot($_data);

/*
if(strpos($_SERVER['HTTP_HOST'], "chatbot.bottalks.co.kr") !== false) {
    $getAdBot['bot_url'] = "http".($g['https_on'] ? "s" : "")."://".$getAdBot['id'].".".$g['chatbot_host'];
}
*/

if($getAdBot['bottype'] == 'call') {
    $aSpeaker = array();
    $aSpeaker['google']['ko-KR-Standard-A'] = "일반 1";
    $aSpeaker['google']['ko-KR-Standard-B'] = "일반 2";
    $aSpeaker['google']['ko-KR-Standard-C'] = "일반 3";
    $aSpeaker['google']['ko-KR-Standard-D'] = "일반 4";
    $aSpeaker['google']['ko-KR-Wavenet-A'] = "고품질 1";
    $aSpeaker['google']['ko-KR-Wavenet-B'] = "고품질 2";
    $aSpeaker['google']['ko-KR-Wavenet-C'] = "고품질 3";
    $aSpeaker['google']['ko-KR-Wavenet-D'] = "고품질 4";

    $aSpeaker['naver']['dara'] = "아라";
    $aSpeaker['naver']['dara_ang'] = "아라(화남)";
    $aSpeaker['naver']['nara_call'] = "아라(상담원)";
    $aSpeaker['naver']['nminyoung'] = "민영";
    $aSpeaker['naver']['nyejin'] = "예진";
    $aSpeaker['naver']['mijin'] = "미진";
    $aSpeaker['naver']['jinho'] = "진호";
    $aSpeaker['naver']['nbora'] = "보라";
    $aSpeaker['naver']['ndain'] = "다인";
    $aSpeaker['naver']['neunyoung'] = "은영";
    $aSpeaker['naver']['ngaram'] = "가람";
    $aSpeaker['naver']['ngoeun'] = "고은";
    $aSpeaker['naver']['nhajun'] = "하준";
    $aSpeaker['naver']['njaewook'] = "재욱";
    $aSpeaker['naver']['njihun'] = "지훈";
    $aSpeaker['naver']['njihwan'] = "지환";
    $aSpeaker['naver']['njinho'] = "진호";
    $aSpeaker['naver']['njiwon'] = "지원";
    $aSpeaker['naver']['njiyun'] = "지윤";
    $aSpeaker['naver']['njonghyun'] = "종현";
    $aSpeaker['naver']['njooahn'] = "주안";
    $aSpeaker['naver']['njoonyoung'] = "준영";
    $aSpeaker['naver']['nminsang'] = "민상";
    $aSpeaker['naver']['nminseo'] = "민서";
    $aSpeaker['naver']['nseonghoon'] = "성훈";
    $aSpeaker['naver']['nseungpyo'] = "승표";
    $aSpeaker['naver']['nsinu'] = "신우";
    $aSpeaker['naver']['nsiyoon'] = "시윤";
    $aSpeaker['naver']['nsujin'] = "수진";
    $aSpeaker['naver']['nsunhee'] = "선희";
    $aSpeaker['naver']['nsunkyung'] = "선경";
    $aSpeaker['naver']['ntaejin'] = "태진";
    $aSpeaker['naver']['nwontak'] = "원탁";
    $aSpeaker['naver']['nyoungil'] = "영일";
    $aSpeaker['naver']['nyujin'] = "유진";

    $getAdBot['tts_vendor'] = $getAdBot['tts_vendor'] ? $getAdBot['tts_vendor'] : "naver";
    $aSpeaker = $aSpeaker[$getAdBot['tts_vendor']];
    $getAdBot['tts_audio'] = $getAdBot['tts_audio'] ? $getAdBot['tts_audio'] : array_keys($aSpeaker)[0];
}

// 문진봇 사용여부
if($getAdBot['use_mediExam']=='on'){
    $use_mediExam_checked = 'checked';
    $use_mediExam_disp = 'style="display:inline-block"';
}
else{
    $use_mediExam_checked = '';
    $use_mediExam_disp = 'style="display:none"';
}

// access_token 생성/저장 후 리턴
$_data['access_mod'] ='mediExamAdm';
$access_token = $chatbot->setBotAccessToken($_data);
$go_mediExam_url = $chatbot->getExUrl($_data['access_mod']).'/sAdmin/?access_token='.$access_token;


// 메뉴얼 사이트 사용여부
if($getAdBot['use_compManual']=='on'){
    $use_compManual_checked = 'checked';
    $use_compManual_disp = 'style="display:inline-block"';
}
else{
    $use_compManual_checked = '';
    $use_compManual_disp = 'style="display:none"';

}

// 채팅기능 사용여부
if($getAdBot['use_chatting']=='on'){
    $use_chatting_checked = 'checked';
    $use_chatting_disp = 'style="display:inline-block"';
}
else{
    $use_chatting_checked = '';
    $use_chatting_disp = 'style="display:none"';
}

// 인터페이스값  세팅
if(!$getAdBot['interface'] || $getAdBot['interface']=='default'){
    $if_default_checked = "checked";
    $if_voice_checked = "";
}else if($getAdBot['interface']=='voice'){
    $if_default_checked = "";
    $if_voice_checked = "checked";
}

// 상태 모드 세팅
if(!$getAdBot['active'] || $getAdBot['active']=='1'){
     $if_live_checked = "";
    $if_dev_checked = "checked";
}else if($getAdBot['active']=='2'){
    $if_live_checked = "checked";
    $if_dev_checked = "";

}

// access_token 생성/저장 후 리턴
$_data['access_mod'] ='compManual';
$access_token = $chatbot->setBotAccessToken($_data);
$go_compManual_url = $chatbot->getExUrl($_data['access_mod']).'/sAdmin/?access_token='.$access_token;

// 가이드 텍스트 추출
$_tmp = array();
$_tmp['page'] = str_replace('adm/','',$page);
$g_common = $chatbot->getGuideTxt($_tmp,'common');
$g_state = $chatbot->getGuideTxt($_tmp,'state');
$g_avatar = $chatbot->getGuideTxt($_tmp,'avatar');
$g_name = $chatbot->getGuideTxt($_tmp,'name');
$g_needName = $chatbot->getGuideTxt($_tmp,'needName');
$g_service = $chatbot->getGuideTxt($_tmp,'service');
$g_intro = $chatbot->getGuideTxt($_tmp,'intro');
$g_webUrl = $chatbot->getGuideTxt($_tmp,'webUrl');
$g_botUrl = $chatbot->getGuideTxt($_tmp,'botUrl');
$g_callNo = $chatbot->getGuideTxt($_tmp,'callNo');

// 업종
$data['vendor'] = $V['uid'];
$data['mod'] = 'adm';
$botCat = $chatbot->getBotCategory($data);
$upjong = $getAdBot['upjong'];

// 콜봇번호
if($getAdBot['callno']) {
    $aCallNo = explode(',', $getAdBot['callno']);
    $callno = '';
    foreach($aCallNo as $no) {
        $callno .=getStrToPhoneFormat($no).', ';
    }
    $callno = rtrim($callno, ', ');
}
?>

<link href="/_core/css/audioplayer.css" rel="stylesheet">
<link href="/_core/css/powerange.min.css" rel="stylesheet">
<script src="/_core/js/audioplayer.min.js"></script>
<script src="/_core/js/powerange.min.js"></script>
<style>
    .ul_float.tts_audio button {position:relative; width:130px;}
</style>

<!-- bootstrap css -->
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><?php echo $pageTitle?></h4>
        </div>
<!--         <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12"> <a href="http://wrappixel.com/templates/pixeladmin/" target="_blank" class="btn btn-danger pull-right m-l-20 btn-rounded btn-outline hidden-xs hidden-sm waves-effect waves-light">Upgrade to Pro</a>
            <ol class="breadcrumb">
                <li><a href="#">Dashboard</a></li>
                <li class="active">Fontawesome Icons</li>
            </ol>
        </div> -->
        <!-- /.col-lg-12 -->
    </div>
    <?php echo $g_common?>
    <!-- row -->
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="white-box">
                <form class="form-horizontal form-material" autocomplete="off" data-role="configBotForm">
                    <input type="hidden" name="uid" value="<?php echo $bot?>" />
                    <input type="hidden" name="active" value="2" />
                    <div class="form-group">

                        <label class="col-md-1">아바타<?php echo $g_avatar?></label>
                        <div class="col-md-11">
                            <?php echo $getAdBot['bot_avatar_img']?>
                            <span class="small muted">(변경시 이미지 클릭)</span>
                        </div>
                    </div>

                    <div class="form-group">

                        <label class="col-md-1 input">챗봇명<?php echo $g_name?></label>

                        <div class="col-md-11">
                            <input type="text" name="name" value="<?php echo $getAdBot['name']?>"  class="form-control form-control-line" data-role="enter-text" data-type="length" data-limit="15">
                            <?php if(!$getAdBot['name']):?>
                            <?php echo $g_needName?>
                            <?php endif?>

                        </div>

                    </div>
                    <div class="form-group">
                        <label class="col-md-1 input">홈페이지<?php echo $g_webUrl?></label>
                        <div class="col-md-11">
                            <input type="text" placeholder="http://www.bottalks.co.kr" class="form-control form-control-line" name="website" value="<?php echo $getAdBot['website']?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-1 input">챗봇 URL<?php echo $g_botUrl?></label>
                        <div class="col-md-11">
                            <span class="form-control form-control-line">
                                <span id="bot_url" style="color:#999; display:inline-block;"><?php echo $getAdBot['bot_url']?></span>
                                <button type="button" class="btn btn-info btn-dark btn-middle btn-copy" data-clipboard-target="#bot_url" style="margin-left:10px;">복사하기</button>
                            </span>
                        </div>
                    </div>
                    <?if($getAdBot['bottype'] == 'call'){?>
                    <div class="form-group">
                        <label class="col-md-1 input"><?php echo $chatbot->getRoleTypeName('챗봇')?> 번호<?php echo $g_callNo?></label>
                        <div class="col-md-11" style="padding-top:8px;">
                            <?=$callno?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-1 input">봇ID</label>
                        <div class="col-md-11" style="padding-top:8px;">
                            <?=$getAdBot['botId']?>
                        </div>
                    </div>
                    <?}?>
                    <div class="form-group">
                        <label class="col-md-1 input">업종</label>
                        <div class="col-md-11" style="padding-top:8px;">
                            <?=$upjong?>
                            <input type="hidden" name="induCat" value="<?=$getAdBot['induCat']?>" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-1 input">서비스명<?php echo $g_service?></label>
                        <div class="col-md-11">
                            <input type="text" placeholder="서비스명" name="service" value="<?php echo $getAdBot['service']?>" class="form-control form-control-line" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-1">소개<?php echo $g_intro?></label>
                        <div class="col-md-11">
                            <textarea rows="5" name="intro" class="form-control form-control-line"><?php echo $getAdBot['intro']?></textarea>
                        </div>
                    </div>

                    <?if($getAdBot['bottype'] == 'call'){?>
                    <div class="form-group">
                        <label class="col-md-1 input">음성선택<?php echo $g_service?></label>
                        <div class="col-md-11">
                            <div>
                                <ul class="ul_float tts_audio">
                                    <? foreach($aSpeaker as $file=>$text) {?>
                                    <li><button type="button" audio="<?=$file?>" class="<?=($file == $getAdBot['tts_audio'] ? 'on' : '')?>"><?=$text?></button></li>
                                    <?}?>
                                </ul>
                            </div>
                            <input type="hidden" name="tts_vendor" value="<?=$getAdBot['tts_vendor']?>" />
                            <input type="hidden" name="tts_audio" value="<?=$getAdBot['tts_audio']?>" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-1 input">들어보기</label>
                        <div class="col-md-11">
                            <div style="width:310px;">
                                <span class="audiowrap"><audio id="tts_player" src="/_core/audio/<?=$getAdBot['tts_audio']?>.mp3" controls></audio></span>
                                <script>$("#tts_player").audioPlayer();</script>
                            </div>
                        </div>
                    </div>

                    <div class="form-group tts_pitch_wrap">
                        <label class="col-md-1 input">음역선택<?php echo $g_service?></label>
                        <div class="col-md-11">
                            <div class="slider-wrapper" style="width:250px;">
                                <input type="text" class="tts_pitch" />
                                <input type="text" name="tts_pitch" class="range-val tts_pitch_val" value="<?=(!$getAdBot['tts_pitch'] ? '0' : $getAdBot['tts_pitch'])?>" readonly />
                                <span></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group tts_speed_wrap">
                        <label class="col-md-1 input">속도선택<?php echo $g_service?></label>
                        <div class="col-md-11">
                            <div class="slider-wrapper" style="width:250px;">
                                <input type="text" class="tts_speed" />
                                <input type="text" name="tts_speed" class="range-val tts_speed_val" value="<?=(!$getAdBot['tts_speed'] ? '0' : $getAdBot['tts_speed'])?>" readonly />
                            </div>
                        </div>
                    </div>
                    <? }?>

                    <div class="form-group bot_save" style="margin-top:20px;">
                        <div class="col-md-offset-4 col-md-4">
                            <button class="btn btn-primary btn-block" data-role="btn-updateBot">저장</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- row -->
</div>

<script>
    var clipboard = new Clipboard(".btn-copy");
	clipboard.on("success", function(e) {
	    e.clearSelection();
	    showNotify(".bot_save", "챗봇 링크가 복사되었습니다.");
	});
	function showNotify(container,message){
	    var container = container?container:'body';
	    var notify_msg ='<div id="kiere-notify-msg">'+message+'</div>';
	    var notify = $('<div/>', { id: 'kiere-notify', html: notify_msg})
	    .addClass('active')
	    .appendTo(container)
	    setTimeout(function(){
	        $(notify).removeClass('active');
	        $(notify).remove();
	    }, 1500);
	}

	$(".tts_audio button").on("click", function() {
	    $(".tts_audio button").removeClass("on");
	    $(this).addClass("on");
	    $("input:hidden[name=tts_audio]").val($(this).attr("audio"));
	    $("#tts_player").attr("src", "/_core/audio/"+$(this).attr("audio")+".mp3");
	    $("#tts_player").stop();
	    $(".audioplayer").removeClass("audioplayer-playing").addClass("audioplayer-stopped");
	    $(".audioplayer-playpause").attr("title", "Play");
	    $(".audioplayer-time-current").text("00:00");
	    $(".audioplayer-bar-played").css("width", "0%");
	});

	if($(".tts_pitch").length > 0) {
    	var sliderPitch = document.querySelector('.tts_pitch');
    	var sliderStartPitch = $("input[name=tts_pitch]").val();
    	var initPitch = new Powerange(sliderPitch, {decimal:true, min:-5, max:5, step:1, start: sliderStartPitch});
    	sliderPitch.onchange = function() {
    	    $(".tts_pitch_val").val(sliderPitch.value);
    	};
    	$(".tts_pitch_wrap .range-min").text("높음");
    	$(".tts_pitch_wrap .range-max").text("낮음");
    }

	if($(".tts_speed").length > 0) {
    	var sliderSpeed = document.querySelector('.tts_speed');
    	var sliderStartSpeed = $("input[name=tts_speed]").val();
    	var initSpeed = new Powerange(sliderSpeed, {decimal:true, min:-5, max:5, step:1, start: sliderStartSpeed});
    	sliderSpeed.onchange = function() {
    	    $(".tts_speed_val").val(sliderSpeed.value);
    	};
    	$(".tts_speed_wrap .range-min").text("빠름");
    	$(".tts_speed_wrap .range-max").text("느림");
    }
</script>
