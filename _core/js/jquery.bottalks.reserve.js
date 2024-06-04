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
    if($(this).hasClass("btn_hotel_goods")) {
        getPriceGoods($(this).siblings(".add_input_info"));
    }
});
// 숙박 패키지 인원 관련
$(document).on("click", ".btn_rateplan", function() {
    var bCard = $(this).closest(".bot_form").find(".card_room").length;
    if($(this).closest(".rateplan").hasClass("on")) {
        $(this).closest(".rateplan").removeClass("on");
        $(this).parents("fieldset").find(".cardbox").removeClass("on");
        if(!bCard) {
            $(this).closest(".bot_form").removeClass("on");
            $(this).parents("fieldset").find(".cardbox").show();
        }
    } else {
        $(this).parents("fieldset").find(".rateplan, .cardbox").removeClass("on");
        $(this).closest(".cardbox").addClass("on");
        if(!bCard) {
            $(this).parents("fieldset").find(".cardbox").hide();
            $(this).closest(".bot_form").addClass("on");
            $(this).closest(".rateplan").addClass("on").closest(".cardbox").show();
        } else {
            $(this).closest(".rateplan").addClass("on");
        }
    }
});
$(document).on("click", ".add_input_info .btn_cnt", function() {
    $_wrap = $(this).closest(".add_input_info");
    $_type = $($_wrap).attr("_type");
    $_max = $($_wrap).parent(".rateplan").data("max") || $($_wrap).parent(".btn_radio").data("max");
    $nCnt = parseInt($(this).siblings("input.count_input").val());
    if($(this).hasClass("btn_cnt_minus")) {
        $nMinTemp = parseInt($(this).closest(".ul_count_float").data("min"));
        if($nCnt <= $nMinTemp) return false;
        $nCnt--;
    }
    if($(this).hasClass("btn_cnt_plus")) {
        $nTotalCnt = 0;
        $($_wrap).find("input.count_input").each(function() {$nTotalCnt += parseInt($(this).val());});
        if($nTotalCnt >= $_max) return false;
        $nCnt++;
    }
    $(this).siblings("input.count_input").val($nCnt).siblings(".count_cnt").find(".count_no").text($nCnt);
    if($_type == "person") {
        getPricePerson($_wrap);
    } else {
        getPriceGoods($_wrap);
    }
});
$(document).on("change", ".reserve_agree #checkall", function() {
    $(this).parent().next(".ul_check_agree").find("input[type=checkbox]").prop("checked", $(this).prop("checked"));
});
$(document).on("click", ".reserve_agree .btn_agree_open", function() {
    $(this).toggleClass("on");
    if($(this).hasClass("on")) $(this).parent().next(".agree_content").show();
    else $(this).parent().next(".agree_content").hide();
});
function getPricePerson(obj) {
    $_nights = parseInt($(obj).closest(".bot_form").find("input:hidden[name=reserve_nights]").val());
    $_data = $(obj).closest(".rateplan").data();
    $_min = parseInt($_data.min);
    $_price = parseInt($_data.price);
    $_extra_adult = parseInt($_data.adult);
    $_extra_child = parseInt($_data.child);
    $nCntAdult = parseInt($(obj).find("input:hidden[name=nAdult]").val());
    $nCntChild = parseInt($(obj).find("input:hidden[name=nChild]").val());
    $_add_adult_price = 0, $_add_child_price = 0, $nTotalPrice = 0;
    $nTotalCnt = ($nCntAdult+$nCntChild);
    if($nTotalCnt > $_min) {
        if($nCntAdult > $_min) {
            $_add_adult_price = ((($nCntAdult-$_min)*$_extra_adult)*$_nights);
            $_add_child_price = (($nCntChild*$_extra_child)*$_nights);
        } else {
            $_add_child_price = ((($nTotalCnt-$_min)*$_extra_child)*$_nights);
        }
        $nTotalPrice = ($_price + ($_add_adult_price + $_add_child_price));
    } else {
        $nTotalPrice = $_price;
    }
    $(obj).closest(".rateplan").find("input.extra_adult_price").val($_add_adult_price);
    $(obj).closest(".rateplan").find("input.extra_child_price").val($_add_child_price);
    $(obj).closest(".rateplan").find("input.total_price").val($nTotalPrice).parent(".total_price").find("em").text(commaSplit($nTotalPrice));
}
function getPriceGoods(obj) {
    $_data = $(obj).data();
    $_price = parseInt($_data.price);
    $_room_price = parseInt($(obj).closest(".form_field").find("input.total_room_price").val());
    $nTotalPrice = (parseInt($(obj).find("input.count_input").val())*$_price);
    $(obj).find("input.total_price").val($nTotalPrice).prev(".goods_price").find("em").text(commaSplit($nTotalPrice));
    $nTotalGoodsPrice = 0;
    $(obj).closest(".form_field").find(".btn_radio").each(function() {
        if($(this).hasClass("on")) $nTotalGoodsPrice += parseInt($(this).next(".add_input_info").find("input.total_price").val());
    });
    $nTotalAllPrice = ($_room_price + $nTotalGoodsPrice);

    $(obj).closest(".form_field").find("em.total_goods_price").text(commaSplit($nTotalGoodsPrice));
    $(obj).closest(".form_field").find("em.total_all_price").text(commaSplit($nTotalAllPrice));
    $(obj).closest(".form_field").find("input.total_goods_price").val($nTotalGoodsPrice);
    $(obj).closest(".form_field").find("input.total_all_price").val($nTotalAllPrice);
}

$(document).on("click", ".submit_reserve, .submit_request", function() {
    $this = $(this);
    prevForm = $this.closest(".cb-chatting-form");

    var category = $this.data("category");
    var hform = $this.data("hform");
    var step = $this.data("step");
    var action = $this.data("action") ? $this.data("action") : "";
    var last_chat = $this.data("lastchat");

    apiData['category'] = category;
    apiData['hform'] = hform;
    apiData['step'] = step;
    apiData['action'] = action;
    apiData['last_chat'] = last_chat;

    if(step == "start") {
        if(action != "modify") {
            var sys_date = $(prevForm).find(":input:hidden[name=sys_date]").val();
            var sys_week = $(prevForm).find(":input:hidden[name=sys_week]").val();
            var sys_time = $(prevForm).find(":input:hidden[name=sys_time]").val();
            var sys_nights = $(prevForm).find(":input:hidden[name=sys_nights]").val();
            if(sys_date) reserveData['date'] = sys_date;
            if(sys_week) reserveData['week'] = sys_week;
            if(sys_time) reserveData['time'] = sys_time;
            if(sys_nights) reserveData['nights'] = sys_nights;
        } else {
            apiData['action'] = "request";
            apiData['step'] = "auth";
        }

        getUserMsg();

        apiData['r_data'] = JSON.stringify(reserveData);
        getReserveAjax(apiData);
    }

    if(step == "auth") {
        if(category == "hotel") {
            var reserve_idx = $this.closest(".bot_form").find(":input[name=reserve_idx]");
            var uname = $this.closest(".bot_form").find(":input[name=reserve_uname]");
            if($.trim(reserve_idx.val()) == "") {
                jsModal("alert", "예약번호를 입력해주세요.").then(function(r) {if(r) reserve_idx.focus();});
                return false;
            }
            if($.trim(uname.val()) == "") {
                jsModal("alert", "예약자명을 입력해주세요.").then(function(r) {if(r) uname.focus();});
                return false;
            }
        } else {
            var uname = $this.closest(".bot_form").find(":input[name=reserve_uname]");
            var uphone = $this.closest(".bot_form").find(":input[name=reserve_uphone]");
            if(hform == "disability") {
                if(action == "request") {
                    var upart = $this.closest(".bot_form").find(":input[name=reserve_upart]");
                    if($.trim(upart.val()) == "") {
                        jsModal("alert", "소속(부서)명을 입력해주세요.").then(function(r) {
                            if(r) upart.focus();
                        });
                        return false;
                    }
                }
                if($.trim(uname.val()) == "") {
                    jsModal("alert", "이름을 입력해주세요.").then(function(r) {
                        if(r) uname.focus();
                    });
                    return false;
                }
            } else {
                if($.trim(uname.val()) == "") {
                    jsModal("alert", "예약자명을 입력해주세요.").then(function(r) {
                        if(r) uname.focus();
                    });
                    return false;
                }
            }
            if($.trim(uphone.val()) == "") {
                jsModal("alert", "휴대폰번호를 입력해주세요.").then(function(r) {
                    if(r) uphone.focus();
                });
                return false;
            }
            if(!$.trim(uphone.val().match(/01[016789][\d]{3,4}[\d]{4}/))) {
                jsModal("alert", "휴대폰번호가 정확하지 않습니다.").then(function(r) {
                    if(r) uphone.focus();
                });
                return false;
            }
        }

        if(action == "request") {
            var agree = $this.closest(".bot_form").find(":input:radio[name=reserve_uagree]:checked").val();
            if(agree != "true") {
                jsModal("alert", "개인정보의 수집·이용에 동의해주세요.").then(function(r) {
                });
                return false;
            }
        }

        getUserMsg();

        reserveData['uname'] = $.trim(uname.val());
        if(upart) reserveData['upart'] = $.trim(upart.val());
        if(uphone) reserveData['uphone'] = $.trim(uphone.val());
        if(reserve_idx) reserveData['reserve_idx'] = $.trim(reserve_idx.val());
        if(agree) reserveData['uagree'] = agree;
        apiData['r_data'] = JSON.stringify(reserveData);
        getReserveAjax(apiData);
    }

    if(step == "branch") {
        var uitem = $this.closest(".bot_form").find(".ul_button .btn_radio.on");
        if(uitem.length == 0) {
            jsModal("alert", "지점을 선택해주세요.").then(function(r) {
            });
            return false;
        }

        getUserMsg();

        reserveData['branch_idx'] = uitem.data("idx");
        reserveData['branch_name'] = uitem.data("name");
        apiData['r_data'] = JSON.stringify(reserveData);
        getReserveAjax(apiData);
    }

    if(step == "department") {
        var uitem = $this.closest(".bot_form").find(".ul_button .btn_radio.on");
        if(uitem.length == 0) {
            jsModal("alert", "진료과목을 선택해주세요.").then(function(r) {
            });
            return false;
        }

        getUserMsg();

        reserveData['department_idx'] = uitem.data("idx");
        reserveData['department_name'] = uitem.data("name");
        apiData['r_data'] = JSON.stringify(reserveData);
        getReserveAjax(apiData);
    }

    if(step == "doctor") {
        var uitem = $this.closest(".bot_form").find(".ul_button .btn_radio.on");
        if(uitem.length == 0) {
            jsModal("alert", "의사선생님을 선택해주세요.").then(function(r) {
            });
            return false;
        }

        getUserMsg();

        reserveData['doctor_idx'] = uitem.data("idx");
        reserveData['doctor_name'] = uitem.data("name");
        apiData['r_data'] = JSON.stringify(reserveData);
        getReserveAjax(apiData);
    }

    if(step == "date") {
        var datepicker = $this.closest(".bot_form").find(".reserve_date").datepicker().data("datepicker");
        var selectedDates = datepicker.selectedDates;

        //var date = $this.closest(".bot_form").find(":input:hidden[name=reserve_date]").val();
        if(apiData.category == "hotel") {
            if(selectedDates.length == 0) {
                jsModal("alert", "입실일, 퇴실일을 선택해주세요.").then(function(r) {});
                return false;
            } else {
                if(selectedDates.length < 2) {
                    jsModal("alert", "퇴실일을 선택해주세요.").then(function(r) {});
                    return false;
                }
            }
        } else {
            if(selectedDates.length == 0) {
                jsModal("alert", "예약일을 선택해주세요.").then(function(r) {});
                return false;
            }
        }

        if($this.closest(".bot_form").find(".ul_button .btn_radio").length > 0) {
            var uitem = $this.closest(".bot_form").find(".ul_button .btn_radio.on");
            if(uitem.length == 0) {
                jsModal("alert", "예약시간을 선택해주세요.").then(function(r) {
                });
                return false;
            }
        }

        getUserMsg();

        if(apiData.category == "hotel") {
            reserveData['checkin'] = getFormattedDate(selectedDates[0]);
            reserveData['checkout'] = getFormattedDate(selectedDates[1]);
        } else {
            reserveData['date'] = getFormattedDate(selectedDates[0]);
        }
        reserveData['week'] = getDayWeek(selectedDates[0]);
        if(uitem) {
            reserveData['time'] = uitem.data("time");
        }
        apiData['r_data'] = JSON.stringify(reserveData);
        getReserveAjax(apiData);
    }

    if(step == "contents") {
        var ucontent = $this.closest(".bot_form").find("textarea[name=ucontent]");
        if($.trim(ucontent.val()) == "") {
            jsModal("alert", "장애증상을 입력해주세요.").then(function(r) {
                if(r) ucontent.focus();
            });
            return false;
        }

        reserveData['ucontent'] = ucontent.val();
        apiData['r_data'] = JSON.stringify(reserveData);
        getReserveAjax(apiData);
    }

    if(step == "confirm") {
        if(!reserveData['uname'] || !reserveData['uphone']) {
            jsModal("alert", "예약자 정보가 부족합니다.").then(function(r) {});
            return false;
        }
        if(!reserveData['date'] || !reserveData['time']) {
            jsModal("alert", "예약일정 정보가 부족합니다.").then(function(r) {});
            return false;
        }

        getUserMsg();

        apiData['r_data'] = JSON.stringify(reserveData);
        getReserveAjax(apiData);
    }

    if(step == "hotel_room") {
        var uitem = $this.closest(".bot_form").find(".rateplan.on");
        if(uitem.length == 0) {
            jsModal("alert", "예약 객실을 선택해주세요.").then(function(r) {
            });
            return false;
        }

        getUserMsg();

        reserveData['hotel_room_nights'] = $this.closest(".bot_form").find("input:hidden[name=reserve_nights]").val();
        reserveData['hotel_room_idx'] = uitem.data("roomid");
        reserveData['hotel_room_name'] = uitem.siblings(".name").text();
        reserveData['hotel_room_min'] = uitem.data("min");
        reserveData['hotel_room_max'] = uitem.data("max");
        reserveData['hotel_room_basic_price'] = uitem.data("price");
        reserveData['hotel_room_rateplan_id'] = uitem.data("rateplanid");
        reserveData['hotel_room_rateplan_name'] = uitem.find(".rate_name").text();
        reserveData['hotel_room_rateplan_adult'] = uitem.data("adult");
        reserveData['hotel_room_rateplan_child'] = uitem.data("child");
        reserveData['hotel_room_adult'] = uitem.find("input:hidden[name=nAdult]").val();
        reserveData['hotel_room_child'] = uitem.find("input:hidden[name=nChild]").val();
        reserveData['hotel_room_add_adult_price'] = uitem.find("input:hidden[name=extra_adult_price]").val();
        reserveData['hotel_room_add_child_price'] = uitem.find("input:hidden[name=extra_child_price]").val();
        reserveData['hotel_room_price'] = uitem.find("input:hidden[name=total_price]").val();
        apiData['hotel_room_refunds'] = uitem.find("input:hidden[name=refunds]").val();
        apiData['r_data'] = JSON.stringify(reserveData);
        getReserveAjax(apiData);
    }

    if(step == "hotel_goods") {
        getUserMsg();

        reserveData['hotel_room_goods'] = [];
        var uitem = $this.closest(".bot_form").find(".ul_button .btn_radio.on");
        if(uitem.length > 0) {
            $(uitem).each(function() {
                var _ugoods = $(this).next(".add_input_info");
                if(_ugoods.find("input:hidden[name=nGoods]").val() > 0) {
                    var goods = {
                        'id':_ugoods.data("idx"), 'quantity':_ugoods.find("input:hidden[name=nGoods]").val(),
                        'basic_price':_ugoods.data("price"), 'price':_ugoods.find("input:hidden[name=total_price]").val(), 'name': $(this).find("span.name").text()
                    };
                    reserveData['hotel_room_goods'].push(goods);
                }
            });
        }
        reserveData['hotel_room_goods_price'] = $this.closest(".bot_form").find("input:hidden[name=total_goods_price]").val();
        reserveData['hotel_room_all_price'] = $this.closest(".bot_form").find("input:hidden[name=total_all_price]").val();

        apiData['r_data'] = JSON.stringify(reserveData);
        getReserveAjax(apiData);
    }

    if(step == "hotel_confirm") {
        var uitem = $this.closest(".bot_form");
        if(!$(uitem).find("select[name=reserve_upaymethod] option:selected").val()) {
            jsModal("alert", "결제방법을 선택해주세요.").then(function(r) {});
            return false;
        }
        var bchecked = true;
        $(uitem).find(".ul_check_agree input[type=checkbox]").each(function() {
            if($(this).hasClass("required") && !$(this).prop("checked")) {
                bchecked = false;
                jsModal("alert", $(this).next("label").text().replace(" 동의(필수)", "")+"에 동의해주세요.").then(function(r) {});
                return false;
            }
        });
        if(!bchecked) return false;

        if(!$.trim($(uitem).find("input[name=reserve_ulast_name]").val())) {
            jsModal("alert", "성을 입력해주세요.").then(function(r) {$(uitem).find("input[name=reserve_ulast_name]").focus();});
            return false;
        }
        if(!$.trim($(uitem).find("input[name=reserve_ufirst_name]").val())) {
            jsModal("alert", "이름을 입력해주세요.").then(function(r) {$(uitem).find("input[name=reserve_ufirst_name]").focus();});
            return false;
        }
        if(!$.trim($(uitem).find("input[name=reserve_uphone]").val())) {
            jsModal("alert", "휴대폰번호를 입력해주세요.").then(function(r) {$(uitem).find("input[name=reserve_uphone]").focus();});
            return false;
        }
        if(!isValidPhoneNumber($(uitem).find("input[name=reserve_uphone]").val())) {
            jsModal("alert", "휴대폰번호가 정확하지 않습니다.").then(function(r) {$(uitem).find("input[name=reserve_uphone]").focus();});
            return false;
        }
        if(!$.trim($(uitem).find("input[name=reserve_uemail]").val())) {
            jsModal("alert", "이메일을 입력해주세요.").then(function(r) {$(uitem).find("input[name=reserve_uemail]").focus();});
            return false;
        }
        if(!isValidEmail($(uitem).find("input[name=reserve_uemail]").val())) {
            jsModal("alert", "이메일 주소가 정확하지 않습니다.").then(function(r) {$(uitem).find("input[name=reserve_uemail]").focus();});
            return false;
        }

        jsModal("confirm", "예약하시겠습니까?").then(function(r) {
            if(r) {
                getUserMsg();
                reserveData['upaymethod'] = $(uitem).find("select[name=reserve_upaymethod] option:selected").val();
                reserveData['uagreecheck'] = bchecked;
                reserveData['ulast_name'] = $.trim($(uitem).find("input[name=reserve_ulast_name]").val());
                reserveData['ufirst_name'] = $.trim($(uitem).find("input[name=reserve_ufirst_name]").val());
                reserveData['uphone'] = $.trim($(uitem).find("input[name=reserve_uphone]").val());
                reserveData['uemail'] = $.trim($(uitem).find("input[name=reserve_uemail]").val());
                reserveData['udescription'] = $.trim($(uitem).find("input[name=reserve_udescription]").val());
                apiData['r_data'] = JSON.stringify(reserveData);
                getReserveAjax(apiData);
            }
        });
    }

    if(step == "gsitm_confirm") {
        var uitem = $this.closest(".bot_form");
        if(!$(uitem).find("select[name=gsitm_reqTySe] option:selected").val()) {
            jsModal("alert", "요청유형을 선택해주세요.").then(function(r) {});
            return false;
        }
        if(!$(uitem).find("select[name=gsitm_reqCl] option:selected").val()) {
            jsModal("alert", "요청분류를 선택해주세요.").then(function(r) {});
            return false;
        }
        if(!$.trim($(uitem).find("input[name=gsitm_req_title]").val())) {
            jsModal("alert", "제목을 입력해주세요.").then(function(r) {});
            return false;
        }
        if(!$.trim($(uitem).find("textarea[name=gsitm_req_content]").val())) {
            jsModal("alert", "내용을 입력해주세요.").then(function(r) {});
            return false;
        }

        jsModal("confirm", "전송하시겠습니까?").then(function(r) {
            if(r) {
                getUserMsg();
                reserveData['gsitm_reqTySe'] = $(uitem).find("select[name=gsitm_reqTySe] option:selected").val();
                reserveData['gsitm_reqCl'] = $(uitem).find("select[name=gsitm_reqCl] option:selected").val();
                reserveData['gsitm_servId'] = $(uitem).find("select[name=gsitm_servId] option:selected").val();
                reserveData['gsitm_req_title'] = $.trim($(uitem).find("input[name=gsitm_req_title]").val());
                reserveData['gsitm_req_content'] = $.trim($(uitem).find("textarea[name=gsitm_req_content]").val());
                apiData['r_data'] = JSON.stringify(reserveData);
                getReserveAjax(apiData);
            }
        });
    }

    if(step == "disability_confirm") {
        if(!reserveData['upart'] || !reserveData['uname'] || !reserveData['uphone']) {
            jsModal("alert", "신청 정보가 부족합니다.").then(function(r) {});
            return false;
        }
        if(!reserveData['ucontent']) {
            jsModal("alert", "장애증상 정보가 부족합니다.").then(function(r) {});
            return false;
        }

        getUserMsg();

        apiData['r_data'] = JSON.stringify(reserveData);
        getReserveAjax(apiData);
    }
});

$(document).on("click", ".cancel_reserve, .cancel_request", function() {
    $this = $(this);
    $confirmName = $this.hasClass("cancel_request") ? "" : "예약을 ";
    jsModal("confirm", $confirmName+"취소하시겠습니까?").then(function(r) {
        if(r) {
            var category = $this.data("category");
            var hform = $this.data("hform");
            var step = $this.data("step");
            var action = $this.data("action") ? $this.data("action") : "";
            var last_chat = $this.data("lastchat");

            apiData['category'] = category;
            apiData['hform'] = hform;
            apiData['step'] = step;
            apiData['action'] = action;
            apiData['last_chat'] = last_chat;

            apiData['r_data'] = JSON.stringify(reserveData);
            getReserveAjax(apiData);
        }
    });
});

$(document).on("change", "select[name=gsitm_reqTySe]", function() {
    var uitem = $(this).closest(".bot_form");
    var reqTySe = $(">option:selected", this).val();
    if(reqTySe) {
        apiData['hform'] = "gsitm";
        apiData['step'] = reqTySe;
        apiData['action'] = "get_reqCl";
        var action_url = rooturl+"/?r="+raccount+"&m="+module+"&a=getform_"+apiData['hform'];
        $.post(action_url,{
            pData : apiData
        },function(response) {
            var result = $.parseJSON(response);
            $(uitem).find("select[name=gsitm_reqCl]").html(result.reqCls);
            $(uitem).find("select[name=gsitm_servId]").html(result.servIds);
        });
        return false;
    }
});
$(document).on("change", "select[name=gsitm_reqCl]", function() {
    var uitem = $(this).closest(".bot_form");
    var servId = $(">option:selected", this).attr("servId");
    $(uitem).find("select[name=gsitm_servId]").val(servId);
    if(servId) $(uitem).find("select[name=gsitm_servId]").prop("disabled", false);
    else $(uitem).find("select[name=gsitm_servId]").prop("disabled", true);
});

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

function getReserveSendAjax(data) {
    data['nonceVal'] = getRandString();
    data['r_data'] = encryptAES(data['r_data'], data['nonceVal']);

    var action_url = rooturl+"/?r="+raccount+"&m="+module+"&a=getform_"+data.hform;
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
                    var _dd = {input_type:"chat", userInputDisabled:false};
                    chatbotObj.setInputDefault(_dd);
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

function setReserveTimes(data) {
    $(".reserve_time_wrap").html(data.times);
}

function setReservedInfo(data) {
    reserveData = data;
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
        }
        // 전화번호 입력 폼
        if($(lastChatForm).find(":input[name=reserve_uphone]").length > 0) {
            $(lastChatForm).find(":input[name=reserve_uphone]").mask("00000000000");
        }
        // 일반숫자 입력 폼
        if($(lastChatForm).find(".input_number").length > 0) {
            $(lastChatForm).find(".input_number").mask("0#");
        }
        // 금액 입력 폼
        if($(lastChatForm).find(".input_money").length > 0) {
            $(lastChatForm).find(".input_money").mask("#,##0", {reverse: true});
        }
        // 라디오 버튼 id값 재설정
        if($(lastChatForm).find(".radio_agree").length > 0) {
            var cnt = $(".radio_agree").length;
            $(":input:radio[id=agree_true]").attr("id", "agree_true_"+cnt).next("label").attr("for", "agree_true_"+cnt);
            $(":input:radio[id=agree_false]").attr("id", "agree_false_"+cnt).next("label").attr("for", "agree_false_"+cnt);
        }
        // 객실 이미지 swipe
        if($(lastChatForm).find(".face.swiper-container").length > 0) {
            $(lastChatForm).find(".face.swiper-container").each(function(e){
                if($(this).find(".swiper-slide").length > 1) {
                    var tag = $('<div class="swiperButton prev swiper-button-prev'+$(this).data("roomid")+'"></div><div class="swiperButton next swiper-button-next'+$(this).data("roomid")+'"></div>');
                    $(this).append(tag);
                    var swiper = new Swiper(this,{
                        loop: true, spaceBetween: 0, slidesPerView: 1,
                        nextButton: '.swiper-button-next'+$(this).data("roomid"), prevButton: '.swiper-button-prev'+$(this).data("roomid")
                    });

                    $(this).css('width', swiper.width+'px');
                }
            });
        }
        if($(lastChatForm).find(".card_room").length > 0) {
            $(".card_room").each(function(e){
                if($(this).find(".cardbox").length > 1) {
                    var swiperRoom = new Swiper(this,{
                        loop: false, spaceBetween:10, slidesPerView:1, freeMode: false, width: ($(".bot_form.room").width()-15)
                    });
                }
            });
            $(".card_img.swiper-container").each(function(e){
                if($(this).find(".swiper-slide").length > 1) {
                    var tag = $('<div class="swiperButton prev swiper-rbutton-prev'+$(this).data("roomid")+'"></div><div class="swiperButton next swiper-rbutton-next'+$(this).data("roomid")+'"></div>');
                    $(this).append(tag);
                    var swiperThumb = new Swiper(this,{
                        loop: true, spaceBetween: 0, slidesPerView: 1,
                        nextButton: '.swiper-rbutton-next'+$(this).data("roomid"), prevButton: '.swiper-rbutton-prev'+$(this).data("roomid")
                    });
                }
            });
        }

        // datepicker 설정
        if($(lastChatForm).find(".reserve_date").length > 0) {
            if(result.dates.length > 0) {
                useDates = result.dates;
                dateRange = apiData.category == 'hotel' ? true : false;
                dateToggle = apiData.category == 'hotel' ? true : false;

                setTimeout(function() {
                    $(".reserve_date:last").datepicker({
                        language: 'ko',
                        range: dateRange,
                        toggleSelected: dateToggle,
                        minDate: new Date(),
                        onSelect: function(fDate, date) {
                            if(fDate) {
                                if($(lastChatForm).find(".reserve_time").length > 0) {
                                    apiData['step'] = "time";
                                    reserveData['date_temp'] = fDate;
                                    apiData['r_data'] = JSON.stringify(reserveData);
                                    getReserveAjax(apiData);
                                }
                            }
                        },
                        onRenderCell: function(d, type) {
                            if (type == 'day') {
                                var _class = d.getDay() == 0 ? '-sunday-' : '';
                                if(apiData.category != 'hotel' && useDates.length > 0) {
                                    var disabled = true, formatted = getFormattedDate(d);
                                    disabled = useDates.indexOf(formatted) == -1 ? true : false;
                                    return {classes: _class, disabled: disabled};
                                } else {
                                    return {classes: _class, disabled: false};
                                }
                            }
                        }
                    });
                    if(reserveData.date) {
                        if(reserveData.nights) {
                            var selecteDate_val = [new Date(useDates[0].replace(/-/g, "/")), new Date(useDates[1].replace(/-/g, "/"))];
                        } else {
                            var selecteDate_val = new Date(reserveData.date.replace(/-/g, "/"));
                        }
                        $('.reserve_date:last').datepicker().data("datepicker").selectDate(selecteDate_val);
                    }
                    if($(lastChatForm).find(".reserve_time").length > 0 && reserveData.time) {
                        $('.reserve_time:last [data-time="'+reserveData.time+'"]').addClass("on");
                    }
                }, 100);
            }
        }

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
