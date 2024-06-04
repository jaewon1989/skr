<?php
$vendor = $V['uid'];
$_data = array();
$_data['vendor'] = $vendor;
$_data['bot'] = $bot;
$getBotIntro = $chatbot->getBotIntroData($_data);
$aIntroProfile = $getBotIntro['aIntroProfile'];
$aIntroMenu = $getBotIntro['aIntroMenu'];
// 가이드 텍스트 추출 
$_tmp = array();
$_tmp['page'] = str_replace('adm/','',$page);
$g_common = $chatbot->getGuideTxt($_tmp,'common');

$default_img = '/_core/images/bot_avatar_blank_gray.png';
?>

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
                    <input type="hidden" name="vendor" value="<?=$vendor?>" />
                    <input type="hidden" name="bot" value="<?=$bot?>" />
                    <input type="hidden" name="page" value="<?=$page?>" />
                    <input type="hidden" name="sescode" value="<?=$sescode?>" />
                    <input type="hidden" name="linkType" value="updateIntro" />
                    <div class="form-group">
                        <label class="col-md-1">인트로 화면</label>
                        <div class="col-md-11" style="padding-bottom:20px; border-bottom:1px solid #e9e9e9;">
                             <div class="radio-list">
                                <label class="radio-inline" style="padding-top:0;">
                                    <input type="radio" name="intro_use" value="1" <?=($getBotIntro['intro_use'] ? 'checked' : '')?>> 사용
                                </label>
                                <label class="radio-inline" style="padding-top:0;">
                                    <input type="radio" name="intro_use" value="0" <?=(!$getBotIntro['intro_use'] ? 'checked' : '')?>> 사용안함
                                </label>
                            </div>
                            <div class="small" style="color:#777; font-weight:normal; margin-top:10px;">
                                <i class="fa fa-exclamation-circle" aria-hidden="true"></i> 인트로 화면 미사용 시 채팅 화면이 바로 노출됩니다.
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-1">인트로 인사말</label>
                        <div class="col-md-11">
                             <div class="radio-list">
                                <textarea name="intro_greeting" class="form-control i_intro" style="padding:0 10px 10px 0 !important;" data-role="enter-text" data-type="length" data-limit="35"><?=$getBotIntro['intro_greeting']?></textarea>
                            </div>
                            <div class="small" style="color:#777; font-weight:normal; margin-top:10px;">
                                <i class="fa fa-exclamation-circle" aria-hidden="true"></i> 인사말은 35자까지 입력이 가능합니다.
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-1">인트로 부인사말</label>
                        <div class="col-md-11">
                             <div class="radio-list">
                                <textarea name="intro_sub_greeting" class="form-control i_intro" style="padding:0 10px 10px 0 !important;" data-role="enter-text" data-type="length" data-limit="60"><?=$getBotIntro['intro_sub_greeting']?></textarea>
                            </div>
                            <div class="small" style="color:#777; font-weight:normal; margin-top:10px;">
                                <i class="fa fa-exclamation-circle" aria-hidden="true"></i> 부인사말은 60자까지 입력이 가능합니다.
                            </div>
                        </div>
                    </div>
                    <div class="form-group">                  
                        <label class="col-md-1">프로필 이미지</label>
                        <div class="col-md-11">
                             <div class="radio-list">
                                <label class="radio-inline i_intro" style="padding-top:0;">
                                    <input type="radio" name="intro_profile" value="1" class="i_intro" <?=($getBotIntro['intro_profile'] ? 'checked' : '')?>> 사용
                                </label>
                                <label class="radio-inline i_intro" style="padding-top:0;">
                                    <input type="radio" name="intro_profile" value="0" class="i_intro" <?=(!$getBotIntro['intro_profile'] ? 'checked' : '')?>> 사용안함
                                </label>
                            </div>
                            <div class="small" style="color:#777; font-weight:normal; margin-top:10px;">
                                <i class="fa fa-exclamation-circle" aria-hidden="true"></i> 상담원 프로필 이미지는 최대 6개까지 등록 가능합니다.
                            </div>
                        </div>
                    </div>
                        
                    <div id="profileImgWrap" class="form-group">    
                        <label class="col-md-1"></label>
                        <div class="col-md-11" style="padding-bottom:20px; border-bottom:1px solid #e9e9e9;">                            
                            <ul id="intro_img" class="intro_img">
                            <? if(count($aIntroProfile)==0) {?>
                                <li>
                                    <div>
                                        <input type="hidden" class="srcUpload" data-role="profile_url" name="intro_profile_img[]" value="">
                                        <input type="hidden" name="intro_profile_uid[]" value="">
                                        <span class="intro_wrapper imageUpload i_profile" mod="profile" style="background-image: url(<?=$default_img?>);"></span>
                                    </div>
                                </li>
                            <?}else{?>
                                <? foreach($aIntroProfile as $aProfile) {?>
                                    <li>
                                        <div>
                                            <input type="hidden" class="srcUpload" data-role="profile_url" name="intro_profile_img[]" value="">
                                            <input type="hidden" name="intro_profile_uid[]" value="<?=$aProfile['uid']?>">
                                            <span class="intro_wrapper imageUpload i_profile" mod="profile" style="background-image: url(<?=($aProfile['value'] ? $aProfile['value'] : $default_img)?>);"></span>
                                            <a href="javascript:;" class="profile_delete i_profile" mod="svr" uid="<?=$aProfile['uid']?>"><i class="fa fa-times-circle" aria-hidden="true"></i></a>
                                        </div>
                                    </li>
                                <? }?>
                                <? if(count($aIntroProfile) < 6) {?>
                                    <li>
                                        <div>
                                            <input type="hidden" class="srcUpload" data-role="profile_url" name="intro_profile_img[]" value="">
                                            <input type="hidden" name="intro_profile_uid[]" value="">
                                            <span class="intro_wrapper imageUpload i_profile" mod="profile" style="background-image: url(<?=$default_img?>);"></span>
                                        </div>
                                    </li>
                                <?}?>
                            <?}?>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="form-group">                  
                        <label class="col-md-1">메뉴 연동</label>
                        <div class="col-md-11">
                             <div class="radio-list">
                                <label class="radio-inline i_intro" style="padding-top:0;">
                                    <input type="radio" name="intro_menu" value="1" class="i_intro" <?=($getBotIntro['intro_menu'] ? 'checked' : '')?>> 사용
                                </label>
                                <label class="radio-inline i_intro" style="padding-top:0;">
                                    <input type="radio" name="intro_menu" value="0" class="i_intro" <?=(!$getBotIntro['intro_menu'] ? 'checked' : '')?>> 사용안함
                                </label>
                            </div>
                            <div class="small" style="color:#777; font-weight:normal; margin-top:10px;">
                                <i class="fa fa-exclamation-circle" aria-hidden="true"></i> 메뉴는 최대 8개까지 등록 가능합니다.
                            </div>
                        </div>
                    </div>
                    <div class="form-group">    
                        <label class="col-md-1"></label>
                        <div class="col-md-11">                            
                            <ul id="intro_menu" class="intro_menu">
                                <? if(count($aIntroMenu)==0) {?>
                                <li>
                                    <div>
                                        <input type="text" name="intro_menu_name[]" class="form-control w_name menu_name i_menu" data-role="enter-text" data-type="length" data-limit="15" placeholder="메뉴명 입력" />
                                        <input type="text" name="intro_menu_url[]" class="form-control w_url menu_url i_menu" placeholder="이동 URL 입력" />
                                        <input type="hidden" name="intro_menu_uid[]" value="">
                                        <span class="button">
                                            <span class="btn_menu_add i_menu"><i class="fa fa-plus-circle" aria-hidden="true"></i></span>
                                            <span class="btn_menu_del i_menu" uid=""><i class="fa fa-minus-circle" aria-hidden="true"></i></span>
                                        </span>
                                    </div>
                                </li>
                                <?}else{?>
                                    <? foreach($aIntroMenu as $aMenu) {?>
                                        <li>
                                            <div>
                                                <input type="text" name="intro_menu_name[]" value="<?=$aMenu['name']?>" class="form-control w_name menu_name i_menu" data-role="enter-text" data-type="length" data-limit="15" placeholder="메뉴명 입력" />
                                                <input type="text" name="intro_menu_url[]" value="<?=$aMenu['url']?>" class="form-control w_url menu_url i_menu" placeholder="이동 URL 입력" />
                                                <input type="hidden" name="intro_menu_uid[]" value="<?=$aMenu['uid']?>">
                                                <span class="button">
                                                    <span class="btn_menu_add i_menu"><i class="fa fa-plus-circle" aria-hidden="true"></i></span>
                                                    <span class="btn_menu_del i_menu" uid="<?=$aMenu['uid']?>"><i class="fa fa-minus-circle" aria-hidden="true"></i></span>
                                                </span>
                                            </div>
                                        </li>
                                    <? }?>
                                <?}?>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="form-group">                  
                        <label class="col-md-1">하단 로고 설정</label>
                        <div class="col-md-11">
                             <div class="radio-list">
                                <label class="radio-inline i_intro" style="padding-top:0;">
                                    <input type="radio" name="intro_logo" value="1" class="i_intro" <?=($getBotIntro['intro_logo'] ? 'checked' : '')?>> 사용
                                </label>
                                <label class="radio-inline i_intro" style="padding-top:0;">
                                    <input type="radio" name="intro_logo" value="0" class="i_intro" <?=(!$getBotIntro['intro_logo'] ? 'checked' : '')?>> 사용안함
                                </label>
                            </div>                            
                        </div>
                        <div class="col-md-11" style="margin-top:10px;padding-bottom:20px; border-bottom:1px solid #e9e9e9;">
                            <input type="hidden" class="srcUpload" data-role="intro_logo_url" name="intro_logo_url" value="">
                            <input type="hidden" name="intro_logo_url_uid" value="<?=$getBotIntro['intro_logo_url_uid']?>">
                            <span class="botAvatar-wrapper imageUpload i_logo" mod="logo" style="background-image: url(<?=($getBotIntro['intro_logo_url'] ? $getBotIntro['intro_logo_url'] : $default_img)?>);"></span>
                            <span class="small muted">(변경시 이미지 클릭)</span>
                        </div>
                    </div>
                    
                    <div class="form-group">                  
                        <label class="col-md-1">채널 설정</label>
                        <div class="col-md-11">
                             <div class="radio-list">
                                <label class="radio-inline i_intro" style="padding-top:0;">
                                    <input type="radio" name="intro_channel" value="1" class="i_intro" <?=($getBotIntro['intro_channel'] ? 'checked' : '')?>> 사용
                                </label>
                                <label class="radio-inline i_intro" style="padding-top:0;">
                                    <input type="radio" name="intro_channel" value="0" class="i_intro" <?=(!$getBotIntro['intro_channel'] ? 'checked' : '')?>> 사용안함
                                </label>
                            </div>                            
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-offset-4 col-md-4">
                            <button type="button" class="btn btn-primary btn-block" data-role="btn-updateIntro">저장</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- row -->
</div>

<script>
    var chHtmlProfile = '';
    chHtmlProfile +='<li>';
    chHtmlProfile +='   <div>';
    chHtmlProfile +='       <input type="hidden" class="srcUpload" data-role="profile_url" name="intro_profile_img[]" value="">';
    chHtmlProfile +='       <input type="hidden" name="intro_profile_uid[]" value="">';
    chHtmlProfile +='       <span class="intro_wrapper imageUpload i_profile" mod="profile" style="background-image: url(<?=$default_img?>);"></span>';
    chHtmlProfile +='   </div>';
    chHtmlProfile +='</li>';
    
    var chHtmlMenu = '';
    chHtmlMenu +='<li>';
    chHtmlMenu +='  <div>';
    chHtmlMenu +='      <input type="text" name="intro_menu_name[]" value="" class="form-control w_name menu_name i_menu" data-role="enter-text" data-type="length" data-limit="15" placeholder="메뉴명 입력" />';
    chHtmlMenu +='      <input type="text" name="intro_menu_url[]" class="form-control w_url menu_url i_menu" placeholder="이동 URL 입력" />';
    chHtmlMenu +='      <input type="hidden" name="intro_menu_uid[]" value="">';
    chHtmlMenu +='      <span class="button">';
    chHtmlMenu +='          <span class="btn_menu_add i_menu"><i class="fa fa-plus-circle" aria-hidden="true"></i></span>';
    chHtmlMenu +='          <span class="btn_menu_del i_menu" uid=""><i class="fa fa-minus-circle" aria-hidden="true"></i></span>';
    chHtmlMenu +='      </span>';
    chHtmlMenu +='  </div>';
    chHtmlMenu +='</li>';
    
    $(document).on("change", "input[name=intro_use]", function() {
        if($(this).val() == 1) {
            $(".i_intro").removeClass("disabled").prop("disabled", false);
            if($("input[name=intro_profile]:checked").val() == 1) {
                $(".i_profile").removeClass("disabled").prop("disabled", false);
            } else {
                $(".i_profile").removeClass("disabled").prop("disabled", true);
            }
            if($("input[name=intro_menu]:checked").val() == 1) {
                $(".i_menu").removeClass("disabled").prop("disabled", false);
            } else {
                $(".i_menu").removeClass("disabled").prop("disabled", true);
            }
            if($("input[name=intro_logo]:checked").val() == 1) {
                $(".i_logo").removeClass("disabled").prop("disabled", false);
            } else {
                $(".i_logo").removeClass("disabled").prop("disabled", true);
            }
        } else {
            $(".i_intro").addClass("disabled").prop("disabled", true);
            $(".i_profile").addClass("disabled").prop("disabled", true);
            $(".i_menu").addClass("disabled").prop("disabled", true);
            $(".i_logo").addClass("disabled").prop("disabled", true);
        }
    });
    $(document).on("change", "input[name=intro_profile]", function() {
        if($(this).val() == 1) {
            $(".i_profile").removeClass("disabled").prop("disabled", false);
        } else {
            $(".i_profile").addClass("disabled").prop("disabled", true);
        }
    });
    $(document).on("change", "input[name=intro_menu]", function() {
        if($(this).val() == 1) {
            $(".i_menu").removeClass("disabled").prop("disabled", false);
        } else {
            $(".i_menu").addClass("disabled").prop("disabled", true);
        }
    });
    $(document).on("change", "input[name=intro_logo]", function() {
        if($(this).val() == 1) {
            $(".i_logo").removeClass("disabled").prop("disabled", false);
        } else {
            $(".i_logo").addClass("disabled").prop("disabled", true);
        }
    });
    
    $(document).on("click", ".imageUpload", function() {
        $('[data-role="image-inputFile"]').remove();
        
        var mod = $(this).attr("mod");
        if($(":input[name=intro_"+mod+"]:checked").val() != 1) return false;
        var fileInput = $('<input/>', {type: 'file', name: 'files', 'style': 'display:none', 'data-role': 'image-inputFile'});
        $(this).parent().append(fileInput);
        $(this).parent().find('[data-role="image-inputFile"]').click();
    });
    $(document).on("change", '[data-role="image-inputFile"]', function(e){
        var sescode = '<?=$sescode?>';
        var target = e.currentTarget;
        var preview_ele = $(target).parent().find('.imageUpload');
        var mod = $(preview_ele).attr("mod");
        var profile_uid = $(preview_ele).prev('input:hidden').val();
        var imgUrl_ele = $(target).parent().find('.srcUpload');
        var is_img = $(imgUrl_ele).val() ? true : false;
        var file = target.files[0];
        var data = new FormData();
        data.append("file",file); // 가상의 "file" 이라는 오브젝트를 만들어서 전송한다.
        data.append("linkType","uploadImg");
        data.append("sescode",sescode);
        data.append("vendor",'<?=$vendor?>');
        data.append("bot",'<?=$bot?>');
        data.append("dialog",'<?=$dialog?>');
        
        if (!file.name.match(/\.(jpg|jpeg|gif|png)$/i)) {
            alert("이미지 포맷(JPG, GIF, PNG)만 등록가능합니다."); return false;
        }
        if (file.size > (1024*1024*2)) {
            alert("업로드 파일의 용량은 2M 이하여야 합니다."); return false;
        }
        
        $.ajax({
            type: "POST",
            url: rooturl+'/?r='+raccount+'&m=chatbot&a=do_VendorAction',
            data:data,
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                checkLogCountdown();
                var result = $.parseJSON(response);
                if(result !== null && typeof result === 'object' && result[0] == -1) {
                    if(result[1] == 401) {
                        location.href=rooturl+'/?r='+raccount+'&mod=login';
                    } else {
                        alert(result[1]);
                    }  
                } else {
                    var code=result[0];
                    if(code=='100') { // code 값이 100 일때만 실행
                        var source = result[1];// path + tempname
                        var upuid = result[2]; // upload 테이블 저장 uid
                        $(imgUrl_ele).val(source);
                        $(preview_ele).css({"background-image":"url('"+source+"')"});
                        if(mod == 'profile') {
                            $(target).parent().append("<a href='javascript:;' class='profile_delete' mod='temp' uid='"+upuid+"'><i class='fa fa-times-circle' aria-hidden='true'></i></a>");
                            
                            if($('#intro_img li').length < 6 && profile_uid == '') {
                                $('#intro_img').append(chHtmlProfile);
                            }
                        }
                    } else {
                        alert(result[1]);
                    }
                }
                setTimeout(function(){
                    $(target).remove(); // 해당 input file 삭제
                },10)
            }
        });
    });
    
    $(document).on("click", ".profile_delete", function(e){
        $this = $(this);        
        $.post(rooturl+'/?r='+raccount+'&m=chatbot&a=do_VendorAction', {
            linkType: 'profile_delete', bot: '<?=$bot?>', mod: $this.attr('mod'), uid: $this.attr('uid')
        }, function(response) {
            checkLogCountdown();
            var result = $.parseJSON(response);
            if(result !== null && typeof result === 'object' && result[0] == -1) {
                if(result[1] == 401) {
                    location.href=rooturl+'/?r='+raccount+'&mod=login';
                } else {
                    alert(result[1]);
                }
            } else {
                if(result[0] == '100') {
                    $this.parent().parent().remove();
                    setTimeout(function() {
                        if($('#intro_img li').length == 0 || $('#intro_img .profile_delete').length == 5) {
                            $('#intro_img').append(chHtmlProfile);
                        }
                    },100);
                } else {
                    alert(result[0]);
                }
            }
        });
        return false;
    });
    
    $(document).on("click", ".btn_menu_add", function() {
        if($(":input[name=intro_menu]:checked").val() != 1) return false;
        if($('#intro_menu li').length >= 8) return false;
        
        var bBool = true;
        $("#intro_menu .menu_name").each(function() {
            if($.trim($(this).val()) == "") {
                alert("메뉴명을 입력해주세요."); bBool = false; return false;
            }
        });
        if(!bBool) return false;
        
        $("#intro_menu .menu_url").each(function() {
            if($.trim($(this).val()) == "") {
                alert("메뉴 URL을 입력해주세요."); bBool = false; return false;
            }
        });
        if(!bBool) return false;
        
        $("#intro_menu .menu_url").each(function() {
            if(!isValidHttpUrl($(this).val())) {
                alert("올바른 URL을 입력해주세요."); bBool = false; return false;
            }
        });
        if(!bBool) return false;
        
        $("#intro_menu").append(chHtmlMenu);
    });
    $(document).on("click", ".btn_menu_del", function() {
        $this = $(this);
        if($this.attr('uid') == '') {
            $this.parent().parent().parent().remove();
        } else {
            $.post(rooturl+'/?r='+raccount+'&m=chatbot&a=do_VendorAction', {
                linkType: 'intro_menu_delete', bot: '<?=$bot?>', uid: $this.attr('uid')
            }, function(response) {
                checkLogCountdown();
                var result = $.parseJSON(response);
                if(result !== null && typeof result === 'object' && result[0] == -1) {
                    if(result[1] == 401) {
                        location.href=rooturl+'/?r='+raccount+'&mod=login';
                    } else {
                        alert(result[1]);
                    }
                } else {
                    if(result[0] == '100') {
                        $this.parent().parent().parent().remove();
                    } else {
                        alert(result[0]);
                    }
                }
            });            
        }
        if($('#intro_menu li').length == 0) {
            $('#intro_menu').append(chHtmlMenu);
        }
        return false;
    });
    
    $(document).on("click", '[data-role="btn-updateIntro"]', function(e){
        if($(":input:radio[name=intro_use]:checked").val() == 1) {
            if(!$.trim($(":input[name=intro_greeting]").val())) {
                alert('인트로 인사말을 입력해주세요.'); return false;
            }
        }
        
        $.post(rooturl+'/?r='+raccount+'&m=chatbot&a=do_VendorAction',
        $('[data-role="configBotForm"]').serialize(),
        function(response) {
            checkLogCountdown();
            var result=$.parseJSON(response);
            if(result !== null && typeof result === 'object' && result[0] == -1) {
                if(result[1] == 401) {
                    location.href=rooturl+'/?r='+raccount+'&mod=login';
                } else {
                    alert(result[1]); location.reload();
                }
            } else {
                if(result[0] != '100') {
                    alert(result[0]);
                } else {
                    var sh_data = {msg: "챗봇 인트로 설정이 변경되었습니다."};
                    setTimeout(function(){
                        self.showToast(sh_data);
                    },50);
                    setTimeout(function(){
                        location.reload();
                    },1000);
                }
            }
        });
        return false;
    });
    
    $("h4.page-title").text("챗봇설정 > 인트로 설정");
</script>

