<?php
$_data = array();
$_data['bot'] = $bot;
$_data['mod'] ='form';
$getAdBot = $chatbot->getAdmBot($_data);

// 콜봇일 경우 모니터링 무효화
if($getAdBot['bottype'] == 'call' && $getAdBot['use_chatting'] == 'on') {
    //getDbUpdate('rb_chatbot_botSettings', "value='off'", "vendor='".$getAdBot['vendor']."' and bot='".$bot."' and name='use_chatting'");
    //$getAdBot = $chatbot->getAdmBot($_data);
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

if($getAdBot['upjong'] == '병원') {
    // access_token 생성/저장 후 리턴
    $_data['access_mod'] ='mediExamAdm';
    $access_token = $chatbot->setBotAccessToken($_data);
    $go_mediExam_url = $chatbot->getExUrl($_data['access_mod']).'/sAdmin/?access_token='.$access_token;
}


// 메뉴얼 사이트 사용여부
if($getAdBot['use_compManual']=='on'){
    $use_compManual_checked = 'checked';
    $use_compManual_disp = 'style="display:inline-block"';
}
else{
    $use_compManual_checked = '';
    $use_compManual_disp = 'style="display:none"';
}

if($getAdBot['upjong'] == '일반기업' || $getAdBot['upjong'] == '기업') {
    // access_token 생성/저장 후 리턴
    $_data['access_mod'] ='compManual';
    $access_token = $chatbot->setBotAccessToken($_data);
    $go_compManual_url = $chatbot->getExUrl($_data['access_mod']).'/sAdmin/?access_token='.$access_token;
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
if($getAdBot['bottype']=='chat') {
    if(!$getAdBot['interface'] || $getAdBot['interface']=='default'){
        $getAdBot['interface'] = "default";
    }
} else {
    $getAdBot['interface'] = "voice";
}

// 상태 모드 세팅
if(!$getAdBot['status'] || $getAdBot['status']=='live'){
    $if_live_checked = "checked";
    $if_dev_checked = "";
}else if($getAdBot['status']=='dev'){
    $if_live_checked = "";
    $if_dev_checked = "checked";
}

// 예약 사용여부
if($getAdBot['use_reserve']=='on'){
    $use_reserve_checked = 'checked';
    $reserve_api_disp = '';
} else{
    $use_reserve_checked = '';
    $reserve_api_disp = 'none';
}
// 예약 파라미터
$aReserveCategory = $chatbot->getReserveCategory($getAdBot['upjong']);
$reserve_category = $getAdBot['reserve_category'];
$reserve_manage = $getAdBot['reserve_manage'] ? $getAdBot['reserve_manage'] : 'erpbottalks';
$reserve_api = $getAdBot['reserve_api'];
$reserve_domainkey = $getAdBot['reserve_domainkey'];

// access_token 생성/저장 후 리턴
$_data['access_mod'] ='reserveAdm';
$access_token = $chatbot->setBotAccessToken($_data);
if($getAdBot['reserve_manage'] == 'erpbottalks') {
    $go_reserve_url = $chatbot->getExUrl($_data['access_mod']).'?access_token='.$access_token;
} else if($getAdBot['reserve_manage'] == 'onda') {
    $go_reserve_url = $chatbot->getExUrl('ondaAdm');
}

// 채팅상담 솔루션
$aCSChatAPI = $chatbot->csChatAPIs;

// 가이드 텍스트 추출
$_tmp = array();
$_tmp['page'] = str_replace('adm/','',$page);
$g_common = _changeBotNaming($chatbot->getGuideTxt($_tmp,'common'), $getAdBot['bottype']);
$g_monitering = $chatbot->getGuideTxt($_tmp,'monitering');
$g_interface = $chatbot->getGuideTxt($_tmp,'interface');
$g_mediEx = $chatbot->getGuideTxt($_tmp,'mediEx');
$g_context = $chatbot->getGuideTxt($_tmp,'context');
$g_intentMV = $chatbot->getGuideTxt($_tmp,'intentMV');
$g_reserve = $chatbot->getGuideTxt($_tmp,'reserve');
$g_shopapi = _changeBotNaming($chatbot->getGuideTxt($_tmp,'shopapi'), $getAdBot['bottype']);
$g_syscheckup = $chatbot->getGuideTxt($_tmp,'syscheckup');
$g_faqMV = $chatbot->getGuideTxt($_tmp,'faqMV');
$g_bargein = $chatbot->getGuideTxt($_tmp,'bargein');
$g_jusobot = $chatbot->getGuideTxt($_tmp,'jusobot');
$g_cschat = $chatbot->getGuideTxt($_tmp,'cschat');
$g_disability = $chatbot->getGuideTxt($_tmp,'disability');

function _changeBotNaming($guideText, $botType)
{
    return 'call' === $botType ? str_replace("챗봇", "콜봇", $guideText) : $guideText;
}
?>

<link href="/plugins/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
<script src="/plugins/moment-with-locales/js/moment-with-locales.min.js"></script>
<script src="/plugins/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

<!-- bootstrap css -->
<div class="container-fluid">
    <!--
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><?php echo $pageTitle?></h4>
        </div>
    </div>
    -->
    <div class="overview">
        <div class="page-title">고급설정</div>
        <div class="sub-frame">
            <div class="sub-title">SK telecom AICC / <?php echo $pageTitle?></div>
        </div>
    </div>
    <?php echo $g_common?>
    <!-- row -->
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="white-box">
                <form class="form-horizontal form-material" autocomplete="off" data-role="configBotForm">
                    <input type="hidden" name="uid" value="<?php echo $bot?>" />
                    <? //if($getAdBot['bottype'] != 'call') {?>
                    <div class="form-group row">
                        <div class="col-md-1"><label class="input">모니터링 <?php echo $g_monitering?></label></div>
                        <div class="col-md-1">
                             <div class="checkbox checkbox-info">
                                <input type="checkbox" data-role="chkBox-tData" name="use_chatting" id="use-chatting" <?php echo $use_chatting_checked?>>
                                <label class="task-done" for="use-chatting">
                                    사용
                                </label>
                            </div>
                        </div>
                    </div>
                    <?//}?>
                    <div class="form-group">
                        <div class="col-md-2" style="width:auto;"><label>인텐트 추천 점수<?php echo $g_intentMV?></label></div>
                        <div class="col-md-10">
                            <div class="input-group" style="width:200px;">
                                <input type="text" name="intentMV" class="form-control" value="<?php echo $getAdBot['intentMV']?>">
                                <span class="input-group-addon"> 점 이상 </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-2" style="width:auto;"><label>FAQ 유사도 점수<?php echo $g_faqMV?></label></div>
                        <div class="col-md-10">
                            <div class="input-group" style="width:200px;">
                                <input type="text" name="faqMV" class="form-control" value="<?php echo $getAdBot['faqMV']?>">
                                <span class="input-group-addon"> 점 이상 </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" style="display:none;">
                        <div class="col-md-1"><label>인터페이스<?php echo $g_interface?></label></div>
                        <div class="col-md-11">
                             <div class="radio-list">
                                <label class="radio-inline">
                                    <input type="radio" data-role="interface" name="interface" value="default" <?=($getAdBot['interface']=='default' ? 'checked' : '')?> /> 기본형
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" data-role="interface" name="interface" value="voice" <?=($getAdBot['interface']=='voice' ? 'checked' : '')?> /> 음성
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row" style="display: none;">
                        <div class="col-md-1"><label class="input">Barge In <?php echo $g_bargein?></label></div>
                        <div class="col-md-1">
                            <div class="checkbox checkbox-info">
                                <input type="checkbox" name="use_bargein" id="use_bargein" <?=($getAdBot['use_bargein']=='on' ? 'checked' : '')?>>
                                <label class="task-done" for="use_bargein">
                                    사용
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-1"><label class="input">챗GPT 사용</label></div>
                        <div class="col-md-1">
                            <div class="checkbox checkbox-info">
                                <input type="checkbox" data-role="use_chatgpt" name="use_chatgpt" id="use_chatgpt" <?=($getAdBot['use_chatgpt']=='on' ? 'checked' : '')?>>
                                <label class="task-done" for="use_chatgpt">
                                    사용
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group gpt_config" style="display:<?=($getAdBot['use_chatgpt']=='on' ? '' : 'none')?>">
                        <div class="col-md-1"><label class="input"></label></div>
                        <div class="col-md-8">
                            <div class="col-md-2"><label class="col-md-11 input">연동 메세지 입력 :</label></div>
                            <div class="col-md-8"><input type="text" name="msg_chatgpt" class="form-control" value="<?php echo $getAdBot['msg_chatgpt'];?>"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-1"><label class="input">시스템 점검<?php echo $g_syscheckup?></label></div>
                        <div class="col-md-1">
                            <div class="checkbox checkbox-info">
                                <input type="checkbox" data-role="use_syscheckup" name="use_syscheckup" id="use_syscheckup" <?=($getAdBot['use_syscheckup']=='on' ? 'checked' : '')?>>
                                <label class="task-done" for="use_syscheckup">
                                    사용
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group syscheckup_config" style="display:<?=($getAdBot['use_syscheckup']=='on' ? '' : 'none')?>">
                        <div class="col-md-11">
                            <div class="col-md-1"><label class="col-md-1"></label></div>
                            <div class="col-md-1"><label class="col-md-11 input">점검 시간 :</label></div>
                            <div class="col-md-4">
                                <input type="text" name="syscheckup_start" data-role="syscheckup_date" id="syscheckup_start" class="input_normal" style="width:180px;" placeholder="년-월-일 시:분" autocomplete="off" value="<?=($getAdBot['syscheckup_start'] ? $getAdBot['syscheckup_start'] : date('Y-m-d H:i'))?>">
                                ~
                                <input type="text" name="syscheckup_end" data-role="syscheckup_date" id="syscheckup_end" class="input_normal" style="width:180px;" placeholder="년-월-일 시:분" autocomplete="off" value="<?=$getAdBot['syscheckup_end']?>">
                            </div>
                        </div>
                        <div class="col-md-11" style="margin-top:20px;">
                            <div class="col-md-1"><label class="input"></label></div>
                            <div class="col-md-1"><label class="col-md-11 input">안내 메세지 :</label></div>
                            <div class="col-md-4">
                                <textarea name="syscheckup_msg" id="syscheckup_msg" class="input_normal" style="width:100%; height:80px; text-align:left;"><?=$getAdBot['syscheckup_msg']?></textarea>
                            </div>
                        </div>
                    </div>

                    <?php
                        if($getAdBot['bottype'] != 'call') {
                    ?>
                    <div class="form-group row">
                        <div class="col-md-1"><label class="input">채팅상담<?php echo $g_cschat?></label></div>
                        <div class="col-md-1">
                            <div class="checkbox checkbox-info">
                                <input type="checkbox" data-role="chkBox-tData" name="use_cschat" id="use_cschat" <?=($getAdBot['use_cschat']=='on' ? "checked" : "")?>>
                                <label class="task-done" for="use_cschat">
                                    사용
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group cschat_config" style="display:<?=($getAdBot['use_cschat']=='on' ? '' : 'none')?>">
                        <div class="col-md-1"><label class="input"></label></div>
                        <div class="col-md-8">
                            <div class="col-md-3"><label class="col-md-11 input">채팅상담 솔루션 :</label></div>
                            <div class="col-md-8">
                                <select name="cschat_api" class="form-control">
                                    <option value="">솔루션 선택</option>
                                    <? foreach($aCSChatAPI as $key=>$val) {?>
                                    <option value="<?=$key?>" <?=($key == $getAdBot['cschat_api'] ? "selected" : "")?>><?=$val['name']?></option>
                                    <?}?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group cschat_config" style="display:<?=($getAdBot['use_cschat']=='on' ? '' : 'none')?>">
                        <div class="col-md-1"><label class="input"></label></div>
                        <div class="col-md-8">
                            <div class="col-md-3"><label class="col-md-11 input">사용자 정보수집 :</label></div>
                            <div class="col-md-8">
                                <div class="checkbox checkbox-info">
                                    <input type="checkbox" data-role="chkBox-tData" name="cschat_userinfo" id="cschat_userinfo" <?=($getAdBot['cschat_userinfo']=='on' ? "checked" : "")?>>
                                    <label class="task-done" for="cschat_userinfo">
                                        사용
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php }?>

                    <div class="form-group">
                        <div class="col-md-2" style="width:auto;"><label>기본 컨텍스트<?php echo $g_context?></label></div>
                        <div class="col-md-10">
                            <textarea rows="3" name="default_context" class="form-control form-control-line" placeholder="영문/_ 으로 콤마(,)로 구분해서 입력해주세요. "><?php echo $getAdBot['default_context']?></textarea>
                       <!--      <p class="text-muted help-block">여기에 컨텍스트명을 등록해놓으면 대화상자 설정시 바로 사용할 수 있습니다.</p> -->
                        </div>

                    </div>

                    <?php
                        if($getAdBot['bottype'] != 'call') {
                    ?>
                    <div class="form-group guick_config" style="display:<?=($getAdBot['use_guick']=='on' ? '' : '')?>">
                        <div class="col-md-1"><label class="input">퀵 메뉴 사용<?php echo $g_context?></label></div>
                        <div class="col-md-8">
                            <div class="col-md-1 checkbox col-md-offset-0" style="width:auto;">
                                <input type="checkbox" name="menu_quick" id="quick_home" value="home" <?php echo strpos($getAdBot['menu_quick'],'home') !== false ? 'checked': '';?> />
                                <label class="task-done" for="quick_home">처음으로</label>
                            </div>

                            <div class="col-md-1 checkbox col-md-offset-0" style="width:auto;">
                                <input type="checkbox" name="menu_quick" id="quick_tip" value="tip" <?php echo strpos($getAdBot['menu_quick'],'tip') !== false ? 'checked': '';?> />
                                <label class="task-done" for="quick_tip">챗봇 팁</label>
                            </div>

                            <div class="col-md-1 checkbox col-md-offset-0" style="width:auto;">
                                <input type="checkbox" name="menu_quick" id="quick_faq" value="faq" <?php echo strpos($getAdBot['menu_quick'],'faq') !== false ? 'checked': '';?> />
                                <label class="task-done" for="quick_faq">자주하는 질문</label>
                            </div>

                            <div class="col-md-1 checkbox col-md-offset-0" style="width:auto;">
                                <input type="checkbox" name="menu_quick" id="quick_chat" value="chat" <?php echo strpos($getAdBot['menu_quick'],'chat') !== false ? 'checked': '';?> />
                                <label class="task-done" for="quick_chat">채팅상담</label>
                            </div>

                            <div class="col-md-1 checkbox col-md-offset-0" style="width:auto;top:-7px;">
                                <input type="checkbox" name="menu_quick" id="quick_center" value="center" <?php echo strpos($getAdBot['menu_quick'],'center') !== false ? 'checked': '';?> />
                                <label class="task-done" for="quick_center" style="padding-right: 3px;">고객센터</label>

                                <input type="text" name="quick_center_phone" value="<?php echo str_replace('-','',strstr($getAdBot['menu_quick'], '-'));?>" placeholder="전화번호 입력">
                            </div>
                        </div>
                    </div>
                    <?php } ?>

                    <div class="form-group">
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
    $(document).on("change", "#use_cschat", function(e){
        if($(this).is(":checked")) $('.cschat_config').show();
        else $('.cschat_config').hide();
    });

    function getModalReserve() {
    	modalSetting("modal_window", "/?r="+raccount+"&iframe=Y&m=chatbot&page=adm/_reserve_info&bot=<?=$bot?>");
    }
    function getModalDisability() {
    	modalSetting("modal_window", "/?r="+raccount+"&iframe=Y&m=chatbot&page=adm/_disability_info&bot=<?=$bot?>");
    }
</script>