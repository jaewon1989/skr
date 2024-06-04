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
$g_common = $chatbot->getGuideTxt($_tmp,'common');
$g_monitering = $chatbot->getGuideTxt($_tmp,'monitering');
$g_interface = $chatbot->getGuideTxt($_tmp,'interface');
$g_mediEx = $chatbot->getGuideTxt($_tmp,'mediEx');
$g_context = $chatbot->getGuideTxt($_tmp,'context');
$g_intentMV = $chatbot->getGuideTxt($_tmp,'intentMV');
$g_reserve = $chatbot->getGuideTxt($_tmp,'reserve');
$g_shopapi = $chatbot->getGuideTxt($_tmp,'shopapi');
$g_syscheckup = $chatbot->getGuideTxt($_tmp,'syscheckup');
$g_faqMV = $chatbot->getGuideTxt($_tmp,'faqMV');
$g_bargein = $chatbot->getGuideTxt($_tmp,'bargein');
$g_jusobot = $chatbot->getGuideTxt($_tmp,'jusobot');
$g_cschat = $chatbot->getGuideTxt($_tmp,'cschat');
?>

<link href="/plugins/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
<script src="/plugins/moment-with-locales/js/moment-with-locales.min.js"></script>
<script src="/plugins/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

<!-- bootstrap css -->
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><?php echo $pageTitle?></h4>
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
                        <label class="col-md-1 input">모니터링 <?php echo $g_monitering?></label>
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
                        <label class="col-md-1">인텐트 추천 점수<?php echo $g_intentMV?></label>
                        <div class="col-md-11">
                            <div class="input-group" style="width:200px;">
                                <input type="text" name="intentMV" class="form-control" value="<?php echo $getAdBot['intentMV']?>">
                                <span class="input-group-addon"> 점 이상 </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-1">FAQ 유사도 점수<?php echo $g_faqMV?></label>
                        <div class="col-md-11">
                            <div class="input-group" style="width:200px;">
                                <input type="text" name="faqMV" class="form-control" value="<?php echo $getAdBot['faqMV']?>">
                                <span class="input-group-addon"> 점 이상 </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-1">인터페이스<?php echo $g_interface?></label>
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

                    <?php if($getAdBot['bottype'] == 'call'):?>
                    <div class="form-group row bargein_config">
                        <label class="col-md-1 input">Barge In <?php echo $g_bargein?></label>
                        <div class="col-md-1">
                            <div class="checkbox checkbox-info">
                                <input type="checkbox" name="use_bargein" id="use_bargein" <?=($getAdBot['use_bargein']=='on' ? 'checked' : '')?>>
                                <label class="task-done" for="use_bargein">
                                    사용
                                </label>
                            </div>
                        </div>
                    </div>
                    <? if($my['cgroup'] == "skt" || $my['cgroup'] == "ccacs") {?>
                    <div class="form-group row bargein_config">
                        <label class="col-md-1 input">챗GPT 사용</label>
                        <div class="col-md-1">
                            <div class="checkbox checkbox-info">
                                <input type="checkbox" name="use_chatgpt" id="use_chatgpt" <?=($getAdBot['use_chatgpt']=='on' ? 'checked' : '')?>>
                                <label class="task-done" for="use_chatgpt">
                                    사용
                                </label>
                            </div>
                        </div>
                    </div>
                    <?}?>
                    <?php endif?>

                    <div class="form-group row">
                        <label class="col-md-1 input">시스템 점검<?php echo $g_syscheckup?></label>
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
                            <label class="col-md-1 input"></label>
                            <div class="col-md-1"><label class="col-md-11 input">점검 시간 :</label></div>
                            <div class="col-md-4">
                                <input type="text" name="syscheckup_start" data-role="syscheckup_date" id="syscheckup_start" class="input_normal" style="width:180px;" placeholder="년-월-일 시:분" autocomplete="off" value="<?=($getAdBot['syscheckup_start'] ? $getAdBot['syscheckup_start'] : date('Y-m-d H:i'))?>">
                                ~
                                <input type="text" name="syscheckup_end" data-role="syscheckup_date" id="syscheckup_end" class="input_normal" style="width:180px;" placeholder="년-월-일 시:분" autocomplete="off" value="<?=$getAdBot['syscheckup_end']?>">
                            </div>
                        </div>
                        <div class="col-md-11" style="margin-top:20px;">
                            <label class="col-md-1 input"></label>
                            <div class="col-md-1"><label class="col-md-11 input">안내 메세지 :</label></div>
                            <div class="col-md-4">
                                <textarea name="syscheckup_msg" id="syscheckup_msg" class="input_normal" style="width:100%; height:80px; text-align:left;"><?=$getAdBot['syscheckup_msg']?></textarea>
                            </div>
                        </div>
                    </div>
                    <?php if($getAdBot['upjong'] == '병원'):?>
                    <div class="form-group row">
                        <label class="col-md-1 input">문진봇<?php echo $g_mediEx?></label>
                        <div class="col-md-1">
                             <div class="checkbox checkbox-info">
                                <input type="checkbox" data-role="chkBox-tData" name="use_mediExam" id="use-mediExam" <?php echo $use_mediExam_checked?>>
                                <label class="task-done" for="use-mediExam">
                                    사용
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6" <?php echo $use_mediExam_disp?> >
                            <a href="<?php echo $go_mediExam_url?>" target="_blank" class="fcbtn btn btn-danger btn-outline btn-1c">문진내용 보기</a>
                        </div>
                    </div>
                    <?php endif?>
                    <?php if($getAdBot['upjong'] == '일반기업' || $getAdBot['upjong'] == '기업'):?>
                    <div class="form-group row">
                        <label class="col-md-1 input">메뉴얼사이트</label>
                        <div class="col-md-1">
                             <div class="checkbox checkbox-info">
                                <input type="checkbox" data-role="chkBox-tData" name="use_compManual" id="use-compManual" <?php echo $use_compManual_checked?>>
                                <label class="task-done" for="use-compManual">
                                    사용
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6" <?php echo $use_compManual_disp?> >
                            <a href="<?php echo $go_compManual_url?>" target="_blank" class="fcbtn btn btn-danger btn-outline btn-1c">메뉴얼사이트 관리</a>
                        </div>
                    </div>
                    <?php endif?>

                    <? if($getAdBot['id'] == '7e3d11b5d7caecf') {?>
                    <input type="hidden" name="use_jusobot" value="on">
                    <?}?>

                    <!--
                    <div class="form-group row">
                        <label class="col-md-1 input">주소봇<?php echo $g_jusobot?></label>
                        <div class="col-md-1">
                             <div class="checkbox checkbox-info">
                                <input type="checkbox" data-role="chkBox-tData" name="use_jusobot" id="use_jusobot" <?=($getAdBot['use_jusobot']=='on' ? "checked" : "")?>>
                                <label class="task-done" for="use_jusobot">
                                    사용
                                </label>
                            </div>
                        </div>
                    </div>
                    -->
                    <div class="form-group row">
                        <label class="col-md-1 input">예약기능<?php echo $g_reserve?></label>
                        <div class="col-md-1">
                            <div class="checkbox checkbox-info">
                                <input type="checkbox" data-role="use-reserve-check" name="use_reserve" id="use-reserve" <?php echo $use_reserve_checked?>>
                                <label class="task-done" for="use-reserve">
                                    사용
                                </label>
                            </div>
                        </div>
                        <? if($getAdBot['use_reserve']=='on'):?>
                        <div class="col-md-6 reserve_adm" style="display:<?=$reserve_manage == 'self' ? 'none' : ''?>">
                            <? if($reserve_manage == 'bottalks') {?>
                            <a href="#" data-toggle="modal" data-target="#modal_window" onclick="getModalReserve()" class="fcbtn btn btn-danger btn-outline btn-1c">예약관리 보기</a>
                            <? } else { ?>
                            <a href="<?php echo $go_reserve_url?>" target="_blank" class="fcbtn btn btn-danger btn-outline btn-1c">예약관리 보기</a>
                            <? } ?>
                        </div>
                        <?php endif?>
                    </div>

                    <div class="form-group reserve_config" style="display:<?=$reserve_api_disp?>">
                        <label class="col-md-1 input"></label>
                        <div class="col-md-3">
                            <div class="col-md-4"><label class="col-md-11 input">예약 유형 :</label></div>
                            <div class="col-md-4">
                                <select name="reserve_category" class="form-control">
                                    <option value="">예약 유형 선택</option>
                                    <? foreach($aReserveCategory as $key=>$val) {?>
                                    <option value="<?=$key?>" <?=($key == $reserve_category ? "selected" : "")?>><?=$val?></option>
                                    <?}?>
                                </select>
                            </div>
                        </div>
                        <? if($getAdBot['upjong'] == '병원' || $getAdBot['upjong'] == '숙박') {?>
                        <div class="col-md-5">
                            <div class="col-md-2"><label class="col-md-11 input">예약 관리 :</label></div>
                            <div class="col-md-8">
                                <div class="radio-list">
                                    <label class="radio-inline">
                                        <? if($getAdBot['upjong'] == '병원') {?>
                                        <input type="radio" data-role="use-reserve-manage" name="reserve_manage" value="erpbottalks" <?=$reserve_manage == 'erpbottalks' ? 'checked' : ''?>> 봇톡스 예약 관리
                                        <? }?>
                                    </label>
                                    <? if($getAdBot['upjong'] == '숙박') {?>
                                    <label class="radio-inline">
                                        <input type="radio" data-role="use-reserve-manage" name="reserve_manage" value="onda" <?=$reserve_manage == 'onda' ? 'checked' : ''?>> 온다 예약 관리
                                    </label>
                                    <? }?>
                                    <label class="radio-inline">
                                        <input type="radio" data-role="use-reserve-manage" name="reserve_manage" value="self" <?=$reserve_manage == 'self' ? 'checked' : ''?>> 자체 예약 관리
                                    </label>
                                </div>
                            </div>
                        </div>
                        <? } else { ?>
                        <input type="hidden" name="reserve_manage" value="bottalks" />
                        <? }?>
                    </div>

                    <? if($getAdBot['upjong'] == '숙박') {?>
                    <div class="form-group reserve_onda" style="display:<?=$reserve_manage == 'onda' ? '' : 'none'?>">
                        <label class="col-md-1 input"></label>
                        <div class="col-md-1" style="width:10%;"><label class="col-md-12 input">부킹엔진 URL:</label></div>
                        <div class="col-md-5">
                            <span style="margin-right:10px;">https://rsvn.onda.me/</span>
                            <input type="text" name="reserve_onda_suburl" class="form-control" style="width:150px;display:inline-block;" value="<?=$getAdBot['reserve_onda_suburl']?>">
                        </div>
                        <!--
                        <div class="col-md-1"><label class="col-md-11 input">벤더 ID :</label></div>
                        <div class="col-md-2">
                            <input type="text" name="reserve_onda_vendor" class="form-control" value="<?=$getAdBot['reserve_onda_vendor']?>">
                        </div>
                        <div style="clear:both; padding-top:25px;">
                            <label class="col-md-1 input"></label>
                            <div class="col-md-1"><label class="col-md-11 input">억세스토큰 :</label></div>
                            <div class="col-md-9">
                                <input type="text" name="reserve_onda_token" class="form-control form-control-line" value="<?php echo $getAdBot['reserve_onda_token']?>">
                            </div>
                        </div>
                        -->
                    </div>
                    <? }?>

                    <? if($getAdBot['upjong'] == '병원' || $getAdBot['upjong'] == '숙박') {?>
                    <div class="form-group reserve_api" style="display:<?=$reserve_manage == 'self' ? '' : 'none'?>">
                        <label class="col-md-1 input"></label>
                        <div class="col-md-1"><label class="col-md-11 input">예약 API주소 :</label></div>
                        <div class="col-md-4">
                            <input type="text" name="reserve_api" class="form-control form-control-line" value="<?=$getAdBot['reserve_api']?>">
                            <input type="hidden" name="reserve_domainkey" value="<?=$getAdBot['reserve_domainkey']?>">
                        </div>
                    </div>
                    <? }?>

                    <?php if($getAdBot['upjong'] == '쇼핑몰'):?>
                    <div class="form-group row">
                        <label class="col-md-1 input">쇼핑몰 API<?php echo $g_shopapi?></label>
                        <div class="col-md-1">
                            <div class="checkbox checkbox-info">
                                <input type="checkbox" data-role="use_shopapi_check" name="use_shopapi" id="use-shopapi" <?=($getAdBot['use_shopapi']=='on' ? 'checked' : '')?>>
                                <label class="task-done" for="use-shopapi">
                                    사용
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group shopapi_config" style="display:<?=($getAdBot['use_shopapi']=='on' ? '' : 'none')?>">
                        <label class="col-md-1 input"></label>
                        <div class="col-md-1"><label class="col-md-11 input">쇼핑몰 벤더 :</label></div>
                        <div class="col-md-4">
                            <div class="radio-list">
                                <label class="radio-inline">
                                    <input type="radio" data-role="shopapi_vendor" name="shopapi_vendor" value="cafe24" <?=($getAdBot['shopapi_vendor']=='' || $getAdBot['shopapi_vendor']=='cafe24' ? 'checked' : '')?>> 카페24 쇼핑몰
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" data-role="shopapi_vendor" name="shopapi_vendor" value="godo" <?=($getAdBot['shopapi_vendor']=='godo' ? 'checked' : '')?>> 고도몰
                                </label>
                            </div>
                        </div>
                        <div class="col-md-1"><label class="col-md-11 input">쇼핑몰 주소 :</label></div>
                        <div class="col-md-4">
                            <input type="text" name="shopapi_domain" class="form-control shopapi_mall_address" value="<?=$getAdBot['shopapi_domain']?>">
                        </div>

                        <div class="shopapi_key shopapi_key_cafe24" style="clear:both; padding-top:25px; display:<?=($getAdBot['shopapi_vendor']=='' || $getAdBot['shopapi_vendor']=='cafe24' ? '' : 'none')?>">
                            <label class="col-md-1 input"></label>
                            <div class="col-md-1"><label class="col-md-11 input">쇼핑몰 ID :</label></div>
                            <div class="col-md-4">
                                <input type="text" name="shopapi_mall_id" class="form-control form-control-line shopapi_mall_id" value="<?php echo $getAdBot['shopapi_mall_id']?>">
                            </div>
                            <div class="col-md-1">
                                <a href="javascript:;" id="shopapi_get_token" data-role="shopapi_get_token" mode="get_token" class="fcbtn btn btn-danger btn-outline btn-1c" style="display:<?=($getAdBot['shopapi_access_token'] != '' ? 'none' : '')?>">접속토큰 획득하기</a>
                                <a href="javascript:;" id="shopapi_get_retoken" data-role="shopapi_get_token" mode="refresh_token" class="fcbtn btn btn-danger btn-outline btn-1c" style="display:<?=($getAdBot['shopapi_access_token'] == '' ? 'none' : '')?>">접속토큰 갱신하기</a>
                            </div>
                            <input type="hidden" id="is_token" name="is_token" value="<?=($getAdBot['shopapi_access_token'] ? 'true' : '')?>" />
                        </div>
                        <div class="shopapi_key shopapi_key_godo" style="clear:both; padding-top:25px; display:<?=($getAdBot['shopapi_vendor']=='godo' ? '' : 'none')?>">
                            <label class="col-md-1 input"></label>
                            <div class="col-md-1"><label class="col-md-11 input">쇼핑몰 버전</label></div>
                            <div class="col-md-4">
                                <div class="radio-list">
                                    <label class="radio-inline">
                                        <input type="radio" name="shopapi_mall_type" value="godomall5" <?=($getAdBot['shopapi_mall_type']=='godomall5' || $getAdBot['shopapi_mall_type']=='' ? 'checked' : '')?>> 고도몰5
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="shopapi_mall_type" value="enamoo" <?=($getAdBot['shopapi_mall_type']=='enamoo' ? 'checked' : '')?>> e나무
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-1"><label class="col-md-11 input">Client Key :</label></div>
                            <div class="col-md-4">
                                <input type="text" name="shopapi_client_key" class="form-control form-control-line" value="<?php echo $getAdBot['shopapi_client_key']?>">
                            </div>
                        </div>
                    </div>
                    <?php endif?>

                    <? if($getAdBot['bottype'] != 'call') {?>
                    <div class="form-group row">
                        <label class="col-md-1 input">채팅상담<?php echo $g_cschat?></label>
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
                        <label class="col-md-1 input"></label>
                        <div class="col-md-5">
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
                        <label class="col-md-1 input"></label>
                        <div class="col-md-5">
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
                    <?}?>

                    <div class="form-group">
                        <label class="col-md-1">기본 컨텍스트<?php echo $g_context?></label>
                        <div class="col-md-11">
                            <textarea rows="3" name="default_context" class="form-control form-control-line" placeholder="영문/_ 으로 콤마(,)로 구분해서 입력해주세요. "><?php echo $getAdBot['default_context']?></textarea>
                       <!--      <p class="text-muted help-block">여기에 컨텍스트명을 등록해놓으면 대화상자 설정시 바로 사용할 수 있습니다.</p> -->
                        </div>

                    </div>
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
</script>