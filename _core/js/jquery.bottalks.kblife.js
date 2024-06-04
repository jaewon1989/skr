var module = "chatbot";
var chatbotObj;
var msgBox;
var userInputEle;
var userInputDisabled = true;
var reserveData = {};
var reservedInfo = {};
var apiData = {};
var prevForm;
var lastChatForm;
var useDates = {};
var dateRange = false;
var dateToggle = false;
var fileForm = null;
var action_url = rooturl+"/?r="+raccount+"&m="+module+"&a=getform_kblife";

function getChatbotCustomInit(botObj) {
    chatbotObj = botObj;
    msgBox = chatbotObj.chatLogContainer;
    userInputEle = chatbotObj.userInputEle;

    apiData['vendor'] = chatbotObj.vendor;
    apiData['botid'] = chatbotObj.botId;
    apiData['bot'] = chatbotObj.botUid;
    apiData['dialog'] = chatbotObj.dialog;
    apiData['roomToken'] = chatbotObj.roomToken;
    apiData['cmod'] = chatbotObj.cmod;
    apiData['bot_skin'] = chatbotObj.bot_skin;
    apiData['bot_type'] = chatbotObj.bot_type;
    apiData['channel'] = chatbotObj.channel;
}

$(document).on("click", ".btn_form_pop", function() {
    $(".bot_form_pop").show();
    $("#cb-chatting-body").addClass("no_scroll");
});
$(document).on("click", ".form_pop_close", function() {
    $(this).closest(".bot_form_pop").hide();
    $("#cb-chatting-body").removeClass("no_scroll");
});
$(document).on("click", ".btn_radio", function() {
    if($(this).hasClass("btn_toggle")) {
        $(this).toggleClass("on");
    } else {
        $(this).closest("fieldset").find(".btn_radio").removeClass("on");
        $(this).addClass("on");
    }
});
$(document).on("change", "select[name=sido]", function() {
    var sido = $("> option:selected", this).val();
    if(!sido) {
        $("select[name=sigugun]").html("<option value=''>시/구/군 선택</option>");
    } else {
        apiData['sido'] = sido;
        apiData['hform'] = "kblife";
        apiData['category'] = "";
        apiData['step'] = "sigugun";
        apiData['action'] = "get_sigugun";
        apiData['r_data'] = JSON.stringify(reserveData);
        getReserveAjax(apiData);
    }
});
$(document).on("change", "input:radio[name=exeRstCase]", function() {
    if($(this).val() == "call") {
        $(".phone_time").removeClass("dispnone");
    } else {
        $(".phone_time").addClass("dispnone");
    }
});

$(document).on("click", ".submit_request, .submit_modify", function() {
    $this = $(this);
    $uform = $this.closest(".bot_form");

    var _result = getCheckFormValidate($uform);
    if(_result != true) return false;

    if($this.hasClass("next") && $uform.next(".bot_form").length > 0) {
        getFormDisabled($uform);
        $uform.next(".bot_form").addClass("on");
        setTimeout(function() {
            $uform.next(".bot_form")[0].scrollIntoView({behavior: "smooth", block: "start", inline: "nearest"})
        },300);
        return false;
    }

    prevForm = $this.closest(".cb-chatting-form");

    apiData['hform'] = $this.data("hform");
    apiData['category'] = $this.data("category");
    apiData['step'] = $this.data("step");
    apiData['action'] = $this.data("action") ? $this.data("action") : "";
    apiData['last_chat'] = $this.data("lastchat");

    if(apiData['step'] == "start") {
        getUserMsg();
        apiData['r_data'] = JSON.stringify(reserveData);
        getReserveAjax(apiData);
    }

    if(apiData['step'] == "confirm") {
        var uitem = $this.closest(".bot_form");

        var _msg = apiData['action'] == "request" ? "등록하시겠습니까?" : "가입신청을 진행하시겠습니까?";
        jsModal("confirm", _msg).then(function(r) {
            if(r) {
			    getUserMsg();
			    var inputData = getFormData(prevForm);
			    $.each(inputData, function(key, value) {
			        reserveData[key] = value;
			    });
                apiData['r_data'] = JSON.stringify(reserveData);
                getReserveAjax(apiData);
            }
        });
    }
});

$(document).on("click", ".cancel_request", function() {
    $this = $(this);
    jsModal("confirm", "취소하시겠습니까?").then(function(r) {
        if(r) {
            apiData['hform'] = $this.data("hform");
            apiData['category'] = $this.data("category");
            apiData['step'] = $this.data("step");
            apiData['action'] = $this.data("action") ? $this.data("action") : "";
            apiData['last_chat'] = $this.data("lastchat");

            apiData['r_data'] = JSON.stringify(reserveData);
            getReserveAjax(apiData);
        }
    });
});

function getCheckFormValidate(obj) {
    var uform = obj;

    var item = $(uform).find("input:checkbox[name=agree]");
    if($(item).length > 0) {
        if(!$(item).is(":checked")) {
            jsModal("alert", "개인정보 수집 동의를 체크해주세요.").then(function(r) {$(item).focus();}); return false;
        }
    }
    var item = $(uform).find("input[name=uname]");
    if($(item).length > 0) {
        if(!$.trim($(item).val())) {
            jsModal("alert", "이름을 입력해주세요.").then(function(r) {$(item).focus();}); return false;
        }
    }
    var item = $(uform).find("input[name=umobile]");
    if($(item).length > 0) {
        if(!$.trim($(item).val())) {
            jsModal("alert", "휴대폰번호를 입력해주세요.").then(function(r) {$(item).focus();}); return false;
        }
        if(!isValidPhoneNumber($.trim($(item).val()))) {
            jsModal("alert", "휴대폰번호가 정확하지 않습니다.").then(function(r) {$(item).focus();}); return false;
        }
    }
    var item = $(uform).find("input[name=uemail]");
    if($(item).length > 0) {
        if(!$.trim($(item).val())) {
            jsModal("alert", "이메일 주소를 입력해주세요.").then(function(r) {$(item).focus();}); return false;
        }
        if(!isValidEmail($.trim($(item).val()))) {
            jsModal("alert", "이메일 주소가 정확하지 않습니다.").then(function(r) {$(item).focus();}); return false;
        }
    }
    var item = $(uform).find("input[name=ujumin1]");
    if($(item).length > 0) {
        if(!$.trim($(item).val())) {
            jsModal("alert", "생년월일 앞자리를 입력해주세요.").then(function(r) {$(item).focus();}); return false;
        }
        if(!$.trim($(item).val()).match(/^[0-9]{6}$/g)) {
            jsModal("alert", "생년월일 입력이 잘못되었습니다.").then(function(r) {$(item).focus();}); return false;
        }
    }
    var item = $(uform).find("input[name=ujumin2]");
    if($(item).length > 0) {
        if(!$.trim($(item).val())) {
            jsModal("alert", "생년월일 뒷자리를 입력해주세요.").then(function(r) {$(item).focus();}); return false;
        }
        if(!$.trim($(item).val()).match(/^[0-9]{1}$/g)) {
            jsModal("alert", "생년월일 입력이 잘못되었습니다.").then(function(r) {$(item).focus();}); return false;
        }
    }

    var item = $(uform).find("input:radio[name=exeRstCont]");
    if($(item).length > 0) {
        if(!$(item).is(":checked")) {
            jsModal("alert", "상담내용을 체크해주세요.").then(function(r) {$(item).focus();}); return false;
        }
    }
    var item = $(uform).find("input:radio[name=interestProd]");
    if($(item).length > 0) {
        if(!$(item).is(":checked")) {
            jsModal("alert", "관심상품을 체크해주세요.").then(function(r) {$(item).focus();}); return false;
        }
    }
    var item = $(uform).find("input:radio[name=interestTopic]");
    if($(item).length > 0) {
        if(!$(item).is(":checked")) {
            jsModal("alert", "관심주제를 체크해주세요.").then(function(r) {$(item).focus();}); return false;
        }
    }
    var item = $(uform).find("select[name=sido]");
    if($(item).length > 0) {
        if(!$(item).children("option:selected").val()) {
            jsModal("alert", "지역 시/도를 선택해주세요.").then(function(r) {$(item).focus();}); return false;
        }
    }
    var item = $(uform).find("select[name=sigugun]");
    if($(item).length > 0) {
        if(!$(item).children("option:selected").val()) {
            jsModal("alert", "지역 시/구/군을 선택해주세요.").then(function(r) {$(item).focus();}); return false;
        }
    }
    var item = $(uform).find("input:radio[name=exeRstCase]");
    if($(item).length > 0) {
        if($(item).filter(":checked").val() == "call") {
            var time1 = $(uform).find("select[name=counselTime1] option:selected").val();
            var time2 = $(uform).find("select[name=counselTime2] option:selected").val();
            if(!time1 || !time2) {
                jsModal("alert", "상담시간을 선택해주세요.").then(function(r) {}); return false;
            }
            if(parseInt(time1) > parseInt(time2)) {
                jsModal("alert", "상담시간을 올바르게 선택해주세요.").then(function(r) {}); return false;
            }
        }
    }
    var item = $(uform).find("textarea[name=contentText]");
    if($(item).length > 0) {
        if(!$.trim($(item).val())) {
            jsModal("alert", "내용을 입력해주세요.").then(function(r) {$(item).focus();}); return false;
        }
    }
    return true;
}
function getUserMsg() {
    var load_msg = chatbotObj.getLoadMsgTpl();
    $(msgBox).append(load_msg).promise().done(function() {
        var _dd = {input_type: 'hform'};
        chatbotObj.resetScrollTop(_dd);
    });
}

function getFormattedDate(date) {
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var date = date.getDate();
    return year + "-" + (month < 10 ? "0"+month : month) + "-" + (date < 10 ? "0"+date : date);
}

function getDayWeek(date) {
    var week = ['일', '월', '화', '수', '목', '금', '토'];
    return week[new Date(date).getDay()];
}

function getFormDisabled(obj) {
    $(obj).find(".btn_wrap").remove();
    $(obj).find("input, button, select, textarea, div.btn_radio, .btn_cnt").prop("disabled", true).addClass("nopointer");
    $(obj).find(".datepicker").find(".datepicker--nav-action, .datepicker--cell").addClass("-disabled- nopointer");
    $(obj).find(".bot_form.onda").attr("style", "padding-bottom:15px !important");
    $(obj).find(".cardbox, .cardbox.btn_hotel_room").removeClass("on");
}

function getRandString() {
    function chr8(){
        return Math.random().toString(32).slice(-8);
    }
    return chr8()+chr8()+chr8()+chr8();
}

function setSigugun(data) {
    $("select[name=sigugun]").html(data);
}

function getReserveSendAjax(data) {
    data['nonceVal'] = getRandString();
    data['r_data'] = encryptAES(data['r_data'], data['nonceVal']);
    return $.post(action_url,{
        pData : data
    },function(response) {
        return $.Deferred(function(def) {
            def.resolveWith({},[response]);
        }).promise();
    });
}

function getReserveAjax(data) {
    getReserveSendAjax(data).done(function(res) {
        var result = $.parseJSON(res);
        if(result.error == true) {
            if(result.err_msg) {
                jsModal("alert", result.err_msg).then(function(r) {
                    if(r) {
                        var _dd = {input_type:"chat", userInputDisabled:false};
                        chatbotObj.setInputDefault(_dd);
                        if(data.action == "user_login") {
                            $("form[name=user_login]").find("input, button").prop("disabled", false);
                        }
                    }
                });
            }
            return false;
        } else {
            if(result.msg) {
                showBotHtmlFormMsg(result);
            }
            if(result.json_data) {
                if(result.func) {
                    window[result.func](result.json_data);
                }
            }
        }
    });
}

function showBotHtmlFormMsg(result) {
    $(msgBox).append($(result.msg)).promise().done(function() {
        if($(result.finish)) userInputDisabled = false;
        lastChatForm = $(msgBox).children(".cb-chatting-form:last-child"); //$(".cb-chatting-form:last");

        // 이전 폼 disabled
        getFormDisabled(prevForm);

        // 취소 버튼 시 전체 폼 disabled
        if(apiData.hasOwnProperty("step")) {
            if(apiData['step'].indexOf("cancel") > -1) {
                var prevChatForm = $(".cb-chatting-form");
                getFormDisabled(prevChatForm);
                apiData.step = "";
                reserveData = {};
                userInputDisabled = false;
            }
        }

        // 신규 예약 process일 경우 이전 폼 전체 disabled
        if($(lastChatForm).hasClass("cb-form-start")) {
            var prevChatForm = $(".cb-form-start:last").prevAll(".cb-chatting-form");
            getFormDisabled(prevChatForm);
            reserveData = {};
            if($(lastChatForm).find(".searchbox").length > 0) {
                userInputDisabled = false;
                $(userInputEle).prop("disabled", userInputDisabled);
            }
        }

        // 일반숫자 입력 폼
        if($(lastChatForm).find(".input_number").length > 0) {
            $(lastChatForm).find(".input_number").mask("0#");
        }
        // 금액 입력 폼
        if($(lastChatForm).find(".input_money").length > 0) {
            $(lastChatForm).find(".input_money").mask("#,##0", {reverse: true});
        }

        if($(lastChatForm).find(".input_date").length > 0) {
            setTimeout(function() {
                $(lastChatForm).find(".input_date").pickadate();
            }, 100);
        }

        if($(lastChatForm).find(".btn_files").length > 0) {
            fileForm = null;
            fileForm = $(lastChatForm).find(".btn_files").fileForm();
        }
        if($(lastChatForm).find(".searchbox").length > 0) userInputDisabled = false;

        // 모니터링 on일 경우 웹소켓으로 응답 전송
        if(!chatbotObj.cmod && chatbotObj.options.use_chatting=='on' && chatbotObj.admSockId){
            setTimeout(function() {
                var msg = $(msgBox).children(".cb-chatting-form:last-child").clone().wrapAll("<div/>").parent().html();
                chatbotObj.socketSend({"role":"chat_log_send", "roomToken":self.roomToken, "to":chatbotObj.admSockId, "msg":msg});
            },200);
        }

        // input 버튼 및 전송 버튼 초기화
        var _dd = {input_type:"chat", userInputDisabled:userInputDisabled};
        chatbotObj.setInputDefault(_dd);
        chatbotObj.getTypeInput = null;
    });
}

/* custom alert, confirm */
function jsModal(mode, msg) {
    return new Promise(function(resolve, reject) {
        if(mode == 'confirm') {
            var btns = "<span class='kwd-item modal_cancel'>취소</span><span class='kwd-item modal_ok ok'>확인</span>";
        } else {
            var btns = "<span class='kwd-item modal_ok'>확인</span>";
        }
        var html = "";
        html +="<div class='d_modal'>";
        html +="    <div class='modal_inner'>";
        html +="        <div class='modal_wrap'>";
        html +="            <div class='modal_msg'>"+msg+"</div><div class='modal_btns'>"+btns+"</div>";
        html +="        </div>";
        html +="    </div>";
        html +="</div>";

        $("#cb-chatting-body").addClass("no_scroll");
        $('.d_modal').remove();
        var container = $('#chatBox-container');
        container.append(html).promise().done(function() {
            $(document).on('click', '.modal_ok', function(e) {
                $('.d_modal').remove(); $("#cb-chatting-body").removeClass("no_scroll"); resolve(true);
            });
            $(document).on('click', '.modal_cancel', function(e) {
                $('.d_modal').remove(); $("#cb-chatting-body").removeClass("no_scroll"); resolve(false);
            });
        });
    });
}