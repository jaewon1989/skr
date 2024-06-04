<?php
$_data = array();
$_data['bot'] = $bot;
$_data['mod'] ='form';
$getAdBot = $chatbot->getAdmBot($_data);
$chatSkin = $getAdBot['chatSkin'] ? $getAdBot['chatSkin'] : "skin.default.css";

$pc_btn_bottom = $getAdBot['pc_btn_bottom'] ? $getAdBot['pc_btn_bottom'] : '30px';
$pc_btn_right = $getAdBot['pc_btn_right'] ? $getAdBot['pc_btn_right'] : '70px';
$m_btn_bottom = $getAdBot['m_btn_bottom'] ? $getAdBot['m_btn_bottom'] : '25px';
$m_btn_right = $getAdBot['m_btn_right'] ? $getAdBot['m_btn_right'] : '20px';

// 스킨 썸네일 읽어오기
$thumbDir = $g['path_core']."skin/css";
$aSkin = array();
$aDir = dir($thumbDir);
$i=1;
while ($chFileName = $aDir->read() ) {
	if ($chFileName == "." || $chFileName == "..") continue;
	$aTemp = explode(".", $chFileName);
	$ext = array_pop($aTemp);
	if (strtolower($ext) == "jpg" || strtolower($ext) == "png") {
	    $mTime = filemtime($thumbDir."/".$chFileName);
		$cssName = $aTemp[1];
		$cssFile = $aTemp[0].".".$aTemp[1].".css";
		if (!file_exists($thumbDir."/".$cssFile)) continue; // css 파일 여부 확인
		if ($chatSkin == $cssFile) {
			$aSkin[0] = array("fileName"=>$chFileName, "cssName"=>$cssName, "cssFile"=>$cssFile, "skinTitle"=>$aTemp[2]); continue;
		}
		$aSkin[$mTime] = array("fileName"=>$chFileName, "cssName"=>$cssName, "cssFile"=>$cssFile, "skinTitle"=>$aTemp[2]);
		$i++;
	}
}
ksort($aSkin);

//$defaultURI = $g['url_host']."/R2".$getAdBot['id'];
$defaultURI = "http".($g['https_on'] ? "s" : "")."://".$getAdBot['id'].".".$g['chatbot_host'];

// 임베딩 스크립트
$embedJS = '<script>
	window.bottalksSetting = {"botID": "'.$getAdBot['botId'].'"};
	(function(d,s,id) {
		var j,i=new Date().getTime(),t=d.getElementsByTagName(s)[0];
		var h=window.bottalksSetting.botID+".'.$g['chatbot_host'].'";
		if(d.getElementById(id)) {return;}
		j = d.createElement(s); j.id = id; j.async=1;
		j.src = "//"+h+"/plugin_bot/bottalks.plugin.cloud.js?"+i;
		t.parentNode.insertBefore(j, t);
	}(document, "script", "bottalks-embed"));
</script>
';
?>

<!-- bootstrap css -->
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><?php echo $pageTitle?></h4>
        </div>
    </div>
    <!-- row -->
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="white-box">
                <form class="form-horizontal form-material" autocomplete="off" data-role="configBotForm">
                    <input type="hidden" name="uid" value="<?php echo $bot?>" />
                    <input type="hidden" name="chatSkin" value="<?php echo $chatSkin?>" />
                    <div class="form-group">
                        <label class="col-md-1">채팅 버튼 아이콘</label>
                        <div class="col-md-11">
                         <div class="text-muted" style="margin:0 0 10px 10px;">
                          임베딩 챗봇 사용 시에 사용되는 챗봇 버튼 아이콘을 등록하실 수 있습니다.
                         </div>
                            <div class="col-md-11">
                                <input type="hidden" data-role="img_url" name="chatBtn" value="<?=$getAdBot['chatBtn']?>">
                                <span class="botAvatar-wrapper" data-role="self-uploadImg" style="background-image: url(<?=$getAdBot['chatBtn']?>);width:45px;height:45px"></span>
                                <span class="small muted">(변경시 이미지 클릭)</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-1">채팅 상단 로고</label>
                        <div class="col-md-11">
                        	<div class="text-muted" style="margin:0 0 10px 10px;">
                        		채팅창 상단에 챗봇명 또는 로고를 표시할 수 있습니다.
                        	</div>
                            <div class="col-md-11">
                                <label class="radio-inline" style="padding-top:0;">
                                    <input type="radio" name="chatTop" value="title" <?=($getAdBot['chatTop'] == "title" ? "checked" : "")?> /> 챗봇명
                                </label>
                                <label class="radio-inline" style="padding-top:0;">
                                    <input type="radio" name="chatTop" value="logo" <?=($getAdBot['chatTop'] == "logo" ? "checked" : "")?> /> 로고 이미지 <span style="font-size:12px; font-weight:400;">(높이 : 40px 이하)</span>
                                </label>
                            </div>
                            <div id="chatLogo" class="col-md-11" style="margin-top:10px;display:<?=($getAdBot['chatTop'] == "title" ? "none" : "")?>;">
                                <input type="hidden" data-role="img_url" name="chatLogo" value="<?=$getAdBot['chatLogo']?>">
                                <span class="botAvatar-wrapper chatLogo" data-role="self-uploadImg" style="background-image:url(<?=$getAdBot['chatLogo']?>);width:300px;height:40px;border-radius:0;border:1px solid #aaa;background-size:auto !important;"></span>
                                <span class="small muted">(변경시 이미지 클릭)</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-1">채팅 버튼 위치</label>
                        <div class="col-md-11">
                         <div class="text-muted" style="margin:0 0 10px 10px;">
                            임베딩 챗봇 버튼의 위치를 지정하실 수 있습니다. (* px 또는 % 등의 단위를 반드시 입력해주세요.)
                         </div>
                            <div class="col-md-11">
                                <div style="padding-left:5px;">
                                    <span class="btn_pos_item" style="margin-right:10px;">PC웹</span>
                                    <span class="btn_pos">
                                        하단 : <input type="text" name="pc_btn_bottom" value="<?=$pc_btn_bottom?>" class="input_normal" style="width:60px;">
                                    </span>
                                    <span class="btn_pos">
                                        우측 : <input type="text" name="pc_btn_right" value="<?=$pc_btn_right?>" class="input_normal" style="width:60px;">
                                    </span>
                                </div>
                                <div style="padding-left:5px;margin-top:10px;">
                                    <span class="btn_pos_item" style="margin-right:10px;">모바일웹</span>
                                    <span class="btn_pos">
                                        하단 : <input type="text" name="m_btn_bottom" value="<?=$m_btn_bottom?>" class="input_normal" style="width:60px;">
                                    </span>
                                    <span class="btn_pos">
                                        우측 : <input type="text" name="m_btn_right" value="<?=$m_btn_right?>" class="input_normal" style="width:60px;">
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-1">채팅창 스킨 선택</label>
                        <div class="col-md-11">
                        	<div class="text-muted" style="margin:0 0 10px 10px;">
                        		다양한 채팅창 스킨과 함께 bottalks에서 제공하는 직접접속 URL 또는 임베드 스크립트로 챗봇을 사용하실 수 있습니다.
                        	</div>
                            <div class="row show-grid text-center" style="margin-top:0;">
                            	<?foreach($aSkin as $key=>$aData) {?>
                            	<div class="col-md-2 skin_box_outer <?=($chatSkin!=$aData['cssFile']?"notselect":"skin_selected")?>" skin="<?=$aData['cssName']?>">
                            		<div class="skin_box_inner">
	                            		<div class="skin_box">
		                            		<span><img src="<?=$g['url_root']?>/_core/skin/css/<?=$aData['fileName']?>" style="width:100%;"></span>
		                            	</div>
		                            	<div class="skin_name">
		                            		<?=$aData['skinTitle']?>
		                            		<div class="skin_box_mask">
		                            			<div class="skin_box_mask_inner" data-skin="<?=$aData['cssName']?>" data-css="<?=$aData['cssFile']?>">
		                            				<a href="javascript:;" class="skin_box_item skin_save" data-role="skin-save">
		                            					<span class="skin_box_icon"><i class="fa fa-check-circle" aria-hidden="true"></i></span><div class="skin_box_item_txt">적용</div>
		                            				</a>
		                            				<a href="javascript:;" class="skin_box_item" data-role="skin-preview">
		                            					<span class="skin_box_icon"><i class="fa fa-commenting" aria-hidden="true"></i></span><div class="skin_box_item_txt">미리보기</div>
		                            				</a>
		                            				<a href="javascript:;" class="skin_box_item" data-role="skin-code">
		                            					<span class="skin_box_icon"><i class="fa fa-file-text" aria-hidden="true"></i></span><div class="skin_box_item_txt">코드</div>
		                            				</a>
		                            			</div>
		                            			<div class="skin_box_mask_bg"></div>
		                            		</div>
		                            	</div>
		                            	<div class="skin_line"></div>
		                            </div>
                            	</div>
                            	<?}?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" style="overflow:hidden;">
                        <div class="col-md-offset-4 col-md-4">
                            <button class="btn btn-primary btn-block" data-role="btn-updateBot">저장</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- row -->
</div>

<div id="modal-preview" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog skin_preview">
    	<iframe id="skin_iframe" class="skin_preview_iframe" src=""></iframe>
    </div>
</div>
<div id="modal-code" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog skin_code">
    	<div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" data-role="addModal-title">스킨 임베드 코드</h4>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-md-2 control-label">기본 URL</label>
                    <div class="col-md-10" style="margin-bottom:30px;">
                        <input id="chat_url" type="text" class="form-control" value="<?=$defaultURI?>" readonly>
                        <button type="button" class="btn btn-default btn-copy" data-clipboard-target="#chat_url" style="margin-top:5px;">URL 복사하기</button>
                        <div class="text-muted" style="margin-top:10px;">기본적으로 접근 가능한 챗봇 URL입니다.</div>
                    </div>
                    <label class="col-md-2 control-label">Embed 코드</label>
                    <div class="col-md-10">
                        <textarea id="chat_code" class="form-control ta-content" style="height:200px; overflow:auto; line-height:140%;" readonly><?=$embedJS?></textarea>
                        <button type="button" class="btn btn-default btn-copy" data-clipboard-target="#chat_code" style="margin-top:5px;">코드 복사하기</button>
                        <div class="text-muted" style="margin-top:10px;">웹사이트의 body 태그 내 최상단 또는 최하단에 위의 코드를 복사한 후 붙여넣기하여 주십시오.</div>
                    </div>
                </div>
                <div class="modal-footer" style="text-align:center;">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $("input:radio[name=chatTop]").on("change", function() {
        if($(this).val() == "logo") {
            $("#chatLogo").show();
        } else {
            $("#chatLogo").hide();
        }
    });
	$(".skin_box_outer").on("click", ".skin_box_item", function() {
		$mode = $(this).attr("data-role");
		$skinName = $(this).parent().attr("data-skin");
		$skinFile = $(this).parent().attr("data-css");
		if ($mode == "skin-save") {
			$skinPrev = $(".skin_selected").attr("skin");
			$skinPrevHtml = $(".skin_selected").html();
			$("input:hidden[name=chatSkin]").val($skinFile);
			$(".skin_selected").hide().html($("div[skin='"+$skinName+"']").html()).fadeIn();
			$("div[skin='"+$skinName+"']").hide().html($skinPrevHtml).fadeIn().attr("skin", $skinPrev);
			$(".skin_selected").attr("skin", $skinName);
			//$("[data-role=btn-updateBot]").trigger("click");
		}
		if ($mode == "skin-preview") {
			$("#skin_iframe").attr("src", "/R2<?=$getAdBot['id']?>?cmod=skin&skin="+$skinFile);
			$("#modal-preview").modal();
		}
		if ($mode == "skin-code") {
			$("#modal-code").modal();
		}
	});

	var clipboard = new Clipboard(".btn-copy");
	clipboard.on("success", function(e) {
	    e.clearSelection();
	    var msg = $(e.trigger).attr("data-clipboard-target") == "#chat_url" ? "기본 URL이" : "Embed 코드가";
	    showNotify(".skin_code", msg+" 복사되었습니다.");
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

	$("#modal-preview").on("hidden.bs.modal", function () {
		$("#skin_iframe").attr("src", "");
	});

	window.addEventListener("message", function(e) {
		if (e.data.bottalks_close == true) {
			$("#skin_iframe").attr("src", "");
			$("#modal-preview").modal('hide');
		}
	});
</script>