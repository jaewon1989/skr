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
var action_url = rooturl+"/?r="+raccount+"&m="+module+"&a=getform_gsitm";

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
    apiData['hform'] = "gsitm";
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

$(document).on("submit", "form[name=user_login]", function() {
    $this = $(this);
    prevForm = $this.closest(".cb-chatting-form");
    var uitem = $this.closest(".bot_form");
    var btn_submit = $(uitem).find(".submit_request");
    var next_action = $(btn_submit).data("next");
    var user_id = $(uitem).find("input[name=user_id]");
    var user_pw = $(uitem).find("input[name=user_pw]");
    if(!$.trim(user_id.val())) {
        modal({type: "alert", title:"알림", text: "아이디를 입력해주세요.", callback:function(r) {}}); return false;
        return false;
    }
    if(!$.trim(user_pw.val())) {
        modal({type: "alert", title:"알림", text: "비밀번호를 입력해주세요.", callback:function(r) {}}); return false;
        return false;
    }
    
    var apiDataAdd = {};
    apiDataAdd['category'] = $(btn_submit).data("category");
    apiDataAdd['step'] = $(btn_submit).data("step");
    apiDataAdd['action'] = $(btn_submit).data("action");
    apiDataAdd['last_chat'] = $(btn_submit).data("lastchat");
    
    reserveData['user_id'] = $.trim(user_id.val());
    reserveData['user_pw'] = $.trim(user_pw.val());
    reserveData['next_action'] = next_action;
    apiDataAdd['r_data'] = JSON.stringify(reserveData);
    var _apiData = $.extend({}, apiData, apiDataAdd);
    
    $this.find("input, button").prop("disabled", true);
    getUserMsg();
    getReserveAjax(_apiData);
    return false;
});

$(document).on("click", ".submit_request, .submit_modify", function() {
    $this = $(this);
    prevForm = $this.closest(".cb-chatting-form");
        
    var category = $this.data("category");
    var step = $this.data("step");
    var action = $this.data("action") ? $this.data("action") : "";
    var last_chat = $this.data("lastchat");    
    
    var apiDataAdd = {};
    apiDataAdd['category'] = category;
    apiDataAdd['step'] = step;
    apiDataAdd['action'] = action;
    apiDataAdd['last_chat'] = last_chat;
    var _apiData = $.extend({}, apiData, apiDataAdd);
    
    if(step == "gsitm_confirm" || step == "gsitm_modify") {
        var uitem = $this.closest(".bot_form");
        
        if(step == "gsitm_confirm") {
            if(!$(uitem).find("select[name=gsitm_reqTySe] option:selected").val()) {
                modal({type: "alert", title:"알림", text: "요청유형을 선택해주세요.", callback:function(r) {}}); return false;
                return false;
            }
            if(!$(uitem).find("select[name=gsitm_reqCl] option:selected").val()) {
                modal({type: "alert", title:"알림", text: "요청분류를 선택해주세요.", callback:function(r) {}}); return false;
                return false;
            }
        }
        if(!$.trim($(uitem).find("input[name=gsitm_req_date]").val())) {
            modal({type: "alert", title:"알림", text: "완료희망일을 입력해주세요.", callback:function(r) {}}); return false;
            return false;
        }
        if(!$.trim($(uitem).find("input[name=gsitm_req_title]").val())) {
            modal({type: "alert", title:"알림", text: "제목을 입력해주세요.", callback:function(r) {}}); return false;
            return false;
        }
        if(!$.trim($(uitem).find("textarea[name=gsitm_req_content]").val())) {
            modal({type: "alert", title:"알림", text: "내용을 입력해주세요.", callback:function(r) {}}); return false;
            return false;
        }
        
        var _text = step == "gsitm_confirm" ? "전송" : "저장";
        modal({
			type: "confirm", title:"알림", text: _text+"하시겠습니까?", callback:function(r) {
			    if(r) {
			        if(step == "gsitm_confirm") getUserMsg();
			        
			        var formData = new FormData();
			        $.each(_apiData, function(key, value) {
			            formData.append('pData['+key+']', value);
			        });
                    
                    var inputData = getFormData($(uitem));
                    delete inputData.blFile;
                    reserveData = {};
                    $.each(inputData, function(key, value) {
                        reserveData[key] = value;
                    });
                    reserveData['gsitm_chgCmplHopeDt'] = inputData['gsitm_req_date'].replace(/[-]/g, '')+inputData['gsitm_req_hour']+inputData['gsitm_req_min'];
                    if(inputData.hasOwnProperty('gsitm_ocr_date')) {
                        if(step == "gsitm_confirm") {
                            reserveData['gsitm_icdtOcrDt'] = inputData['gsitm_ocr_date'].replace(/[-]/g, '')+inputData['gsitm_ocr_hour']+inputData['gsitm_ocr_min'];
                        } else {
                            reserveData['gsitm_icdtOcrDt'] = inputData['gsitm_ocr_date'];
                        }
                    }
                    
                    if($(uitem).find("span.gsitm_apprLines").length > 0) {
                        reserveData['gsitm_apprLinesStr'] = {};
                        $(uitem).find("span.gsitm_apprLines").each(function(idx) {
                            if(idx == 0) return true;
                            reserveData['gsitm_apprLinesStr'][(idx-1)] = $(this).text();
                        });
                    }
                    
                    formData.append('pData[r_data]', JSON.stringify(reserveData));
                    
                    if(fileForm != null) {
                        var files = fileForm.getFiles();
                        $.each(files, function(index, file) {
                            formData.append('files[' + index + ']', file);
                        });
                    }
                    getGSITMRequestApproval(formData);
                }
            }
        });
    }
});

$(document).on("click", ".cancel_reserve, .cancel_request", function() {    
    $this = $(this);
    $confirmName = $this.hasClass("cancel_request") ? "" : "예약을 ";
    modal({
        type: "confirm", title:"알림", text: $confirmName+"취소하시겠습니까?", callback:function(r) {
            if(r.result) {
                var category = $this.data("category");
                var step = $this.data("step");
                var action = $this.data("action") ? $this.data("action") : "";
                var last_chat = $this.data("lastchat");            
                
                var apiDataAdd = {};
                apiDataAdd['category'] = category;
                apiDataAdd['step'] = step;
                apiDataAdd['action'] = action;
                apiDataAdd['last_chat'] = last_chat;
                var _apiData = $.extend({}, apiData, apiDataAdd);
                
                apiData['r_data'] = JSON.stringify(reserveData);            
                getReserveAjax(_apiData);
            }
        }
    });
});
$(document).on("change", "select[name=gsitm_reqTySe]", function() {
    var uitem = $(this).closest(".bot_form");
    var reqTySe = $(">option:selected", this).val();
    if(reqTySe) {
        if(reqTySe == "0223") $(uitem).find(".ocr_date_wrap").removeClass("ocrdate_none");
        else $(uitem).find(".ocr_date_wrap").addClass("ocrdate_none");
            
        var apiDataAdd = {};
        apiDataAdd['step'] = reqTySe;
        apiDataAdd['action'] = "get_reqCl";
        apiDataAdd['reqType'] = $(this).closest(".searchbox").length > 0 ? "search" : "";
        var _apiData = $.extend({}, apiData, apiDataAdd);
        $.post(action_url,{
            pData : _apiData
        },function(response) {
            var result = $.parseJSON(response);
            $(uitem).find("select[name=gsitm_reqCl]").html(result.reqCls);
            if($(uitem).find("select[name=gsitm_servId]").length > 0) {
                $(uitem).find("select[name=gsitm_servId]").html(result.servIds);
            }
        });
        return false;
    } else {
        var html = "<option value=''>- "+($(uitem).find(".searchBox").length > 0 ? "전체" : "선택")+" -</option>";
        $(uitem).find("select[name=gsitm_reqCl]").html(html);
    }
});
$(document).on("change", "select[name=gsitm_reqCl], select[name=gsitm_servId], select[name=gsitm_tmlNo]", function() {
    getGSITMDataFormat($(this));
});

// 문서함 select box
$(document).on("change", "select[name=gsitm_listtype]", function() {
    $this = $(this);
    var uitem = $this.closest(".bot_form");
    $(uitem).find("#frmSearch")[0].reset();
    if($(">option:selected", this).val() == "approval_approval") {
        $(uitem).find("input[name=gsitm_searchDateUse]").prop("checked", false);
    } else {
        $(uitem).find("input[name=gsitm_searchDateUse]").prop("checked", true);
    }
    getSearchBoxList($this);
});
// search box open
$(document).on("click", ".searchwrap .btn_search_open", function() {
    $(this).parent(".searchwrap").toggleClass("open");
    if($(this).parent(".searchwrap").hasClass("open")) {
        $(this).find("i").removeClass("fa-chevron-down").addClass("fa-chevron-up");
    } else {
        $(this).find("i").removeClass("fa-chevron-up").addClass("fa-chevron-down");
    }
});
// search btn
$(document).on("click", ".searchwrap .btn_search", function() {
    $this = $(this);
    getSearchBoxList($this);
});

function getSearchBoxList(obj) {
    $this = obj;
    var uitem = $this.closest(".bot_form");
    var sData = getFormData($(uitem).find(".searchwrap"));
    
    if(!sData.gsitm_fromDate){
        modal({type: "alert", title:"알림", text: "시작 날짜를 입력해주세요.", callback:function(r) {}}); return false;
        return false;
    }
    if(!sData.gsitm_toDate){
        modal({type: "alert", title:"알림", text: "종료 날짜를 입력해주세요.", callback:function(r) {}}); return false;
        return false;
    }
    if(sData.gsitm_fromDate > sData.gsitm_toDate){
        modal({type: "alert", title:"알림", text: "시작날짜가 종료날짜 보다 큽니다 다시 입력해 주세요.", callback:function(r) {}}); return false;
        return false;
    }
    var apiDataAdd = {};
    apiDataAdd['action'] = "get_search_list";
    
    if($(uitem).find("select[name=gsitm_listtype]").length > 0) {
        apiDataAdd['listtype'] = $(uitem).find("select[name=gsitm_listtype] option:selected").val();
    } else {
        apiDataAdd['listtype'] = $this.data("listtype");
    }
    
    apiDataAdd['r_data'] = JSON.stringify(sData);
    var _apiData = $.extend({}, apiData, apiDataAdd);
    $.post(action_url,{
        pData : _apiData
    },function(response) {
        var result = $.parseJSON(response);
        var resultData = result.data;
        if(resultData) {
            $(uitem).find(".ul_list_card").html(resultData.gsitm_req_list).promise().done(function() {
                $(uitem).find("input[name=gsitm_searchword]").val("");
                $(uitem).find(".submit_more").data("page", resultData.gsitm_page);
                $(uitem).find(".submit_more").data("pageall", resultData.gsitm_pageall);                
                var _dd = {input_type:"chat", userInputDisabled:false};
                chatbotObj.setInputDefault(_dd);
            });
            
            if(resultData.gsitm_pageall < 2) $(uitem).find(".submit_more").addClass("dispnone");
            else $(uitem).find(".submit_more").removeClass("dispnone");
        }
    });
    return false;
}

// list more
$(document).on("click", ".submit_more", function() {
    $this = $(this);
    var uitem = $this.closest(".bot_form");
    var page = parseInt($this.data("page"));
    var pageall = parseInt($this.data("pageall"));
    var ntpage = parseInt($(uitem).find("input:hidden[name=ntpage]").val());
    var ntpageall = parseInt($(uitem).find("input:hidden[name=ntpageall]").val());
    
    var apiDataAdd = {};
    apiDataAdd['action'] = "get_more_list";
    apiDataAdd['listtype'] = $this.data("listtype");
    apiDataAdd['page'] = page;
    apiDataAdd['ntpage'] = ntpage;
    apiDataAdd['ntpageall'] = ntpageall;
    var _apiData = $.extend({}, apiData, apiDataAdd);
    $.post(action_url,{
        pData : _apiData
    },function(response) {
        var result = $.parseJSON(response);
        if(result.moreList) {
            $(uitem).find(".ul_list_card").append(result.moreList).promise().done(function() {
                if($(uitem).find("input:hidden[name=ntpage]").length > 0 && result.hasOwnProperty('ntpage')) {
                    $(uitem).find("input:hidden[name=ntpage]").val(result.ntpage);
                }
                $this.data("page", result.page); //$this.data("page", (page+1));
                var _dd = {input_type: 'hform'};
                chatbotObj.resetScrollTop(_dd);
            });
        }
    });
    return false;
});

// click card box
$(document).on("click", ".ul_list_card a", function() {
    $this = $(this);
    var uitem = $this.closest(".bot_form");
    
    var apiDataAdd = {};
    apiDataAdd['action'] = "get_view_data";
    apiDataAdd['listtype'] = $this.data("listtype");
    apiDataAdd['idx'] = $this.data("idx");
    apiDataAdd['coid'] = $this.data("coid");
    apiDataAdd['empid'] = $this.data("empid");
    apiDataAdd['bbsid'] = $this.data("bbsid");
    apiDataAdd['apprdetailid'] = $this.data("apprdetailid");
    var _apiData = $.extend({}, apiData, apiDataAdd);
    $.post(action_url,{
        pData : _apiData
    },function(response) {
        var result = $.parseJSON(response);
        getGSITMModalView(result.viewData);
    });
    return false;
});

// get circular form
function getApprViewFunc(obj) {
    $this = $(obj);
    var uitem = $this.closest(".bot_form");
    var mode = $this.data("mode");
    
    if(mode == "circular") {
        var apiDataAdd = {};
        apiDataAdd['action'] = "get_circular_form";
        var _apiData = $.extend({}, apiData, apiDataAdd);
        $.post(action_url,{
            pData : _apiData
        },function(response) {
            var result = $.parseJSON(response);
            $(".gsitm_modal_view .pop_container").addClass("no_scroll");        
            
            var html = "<div class='pop_inner circular gsitm_circular_pop'><button class='pop_close gsitm_circular_close'>close</button>"+result.viewData+"</div>";
            $(".gsitm_modal").append(html).promise().done(function() {
                $(document).on('click', '.gsitm_circular_close', function(e) {
                    $('.gsitm_circular_pop').remove(); $(".gsitm_modal_view .pop_container").removeClass("no_scroll");
                });
            });
        });
        return false;
    }
    if(mode == "confirm" || mode == "return" || mode == "reject") {
        $_mode = mode == "confirm" ? "승인" : (mode == "return" ? "반려" : "기각");
        $_modePlaceHolder = mode == "confirm" ? "" : (mode == "return" ? "반려의견을 입력해주세요." : "기각의견을 입력해주세요.");
        $_modalType = mode == "confirm" ? "confirm" : "prompt";
        modal({
			type: $_modalType, title:"알림", text: $_mode+"하시겠습니까?", placeholder: $_modePlaceHolder, callback:function(r) {
			    if($_modalType == "prompt") {
			        if(!$.trim(r)) {
			            if($("#modal-window").find(".modal-prompt-input-warning").length == 0) {
			                $("input.modal-prompt-input").after("<div class='modal-prompt-input-warning'>"+$_mode+" 의견을 입력해주세요.</div>");
			            }
			            return false;
			        }
			    }
			    
                if(r) {
                    var apiDataAdd = {};
                    apiDataAdd['action'] = "get_appr_doc_submit";
                    apiDataAdd['actionMode'] = mode;
                    if($_modalType == "prompt") {
                        if(!$.trim(r)) {
                            $("input.modal-prompt-input").after("<div class='modal-prompt-input-warning'>"+$_mode+" 의견을 입력해주세요.</div>");
                            return false;
                        }                            
                        apiDataAdd['pApprReply'] = r;
                    }
                    var _apiData = $.extend({}, apiData, apiDataAdd);                
                    $.post(action_url,{
                        pData : _apiData
                    },function(response) {
                        var result = $.parseJSON(response);
                        modal({
                            type: "alert", title:"알림", text: result.msg, callback:function(r) {
                                if(r) {
                                    if(result.error == false) {
                                        $(".cb-chatting-rows .cb-chatting-form:last .ul_list_card a[data-idx="+$("input:hidden[name=appr_srId]").val()+"]").parent("li").remove();
                                        $('.gsitm_modal').remove(); $("#cb-chatting-body").removeClass("no_scroll");
                                    }
                                }
                            }
                        }); 
                        return false;
                    });
                    return false;
                }
            }
        });
    }
    if(mode == "temp_delete") {
        modal({
			type: "confirm", title:"알림", text: "삭제 하시겠습니까?", callback:function(r) {
                if(r) {
                    var apiDataAdd = {};
                    apiDataAdd['action'] = "get_appr_doc_submit";
                    apiDataAdd['actionMode'] = mode;
                    var _apiData = $.extend({}, apiData, apiDataAdd);
                    $.post(action_url,{
                        pData : _apiData
                    },function(response) {
                        var result = $.parseJSON(response);
                        modal({
                            type: "alert", title:"알림", text: result.msg, callback:function(r) {
                                if(r) {
                                    if(result.error == false) {
                                        $(".cb-chatting-rows .cb-chatting-form:last .ul_list_card a[data-idx="+$("input:hidden[name=appr_srId]").val()+"]").parent("li").remove();
                                        $('.gsitm_modal').remove(); $("#cb-chatting-body").removeClass("no_scroll");
                                    }
                                }
                            }
                        });
                    });
                    return false;
                }
            }
        });
    }
    if(mode == "modify") {
        var apiDataAdd = {};
        apiDataAdd['action'] = "get_appr_modify";
        apiDataAdd['listtype'] = $("input:hidden[name=appr_listtype]").val();
        apiDataAdd['idx'] = $("input:hidden[name=appr_srId]").val();
        apiDataAdd['coid'] = $("input:hidden[name=appr_createCoId]").val();
        apiDataAdd['empid'] = $("input:hidden[name=appr_createEmpId]").val();
        var _apiData = $.extend({}, apiData, apiDataAdd);
        $.post(action_url,{
            pData : _apiData
        },function(response) {
            var result = $.parseJSON(response);
            getGSITMModalView(result.viewData);
        });
    }
}
// get member select
$(document).on("click", ".circularMemberList tr", function() {
    $this = $(this);
    $this.toggleClass("selected");
});
$(document).on("click", ".btn_circular_targetadd", function() {
    $(".circularMemberList tr").each(function() {
        if($(this).hasClass("selected")) {
            $(this).removeClass("selected");
            if($(".circularTargetList tbody tr[data-userid="+$(this).data("userid")+"]").length == 0) {
                $clone = $(this).clone().append("<td><button type='button' class='btn_delete'></button></td>");            
                $clone.appendTo(".circularTargetList tbody");
            }
        }
    });
});
$(document).on("click", ".circularTargetList .btn_delete", function(e) {
    $(this).closest("tr").remove();
});
// 회람 처리
$(document).on("click", ".btn_circular_submit", function(e) {
    if($(".circularTargetList tr").length == 0) return false;
    
    modal({
        type: "confirm", title:"알림", text: "지정된 사용자들에게 회람을 하시겠습니까?", callback:function(r) {
            if(r) {
                var circularInfo = "";
                $(".circularTargetList tr").each(function(i) {
                    circularInfo += (i+1)+"#"+$(this).data("coid")+"#"+$(this).data("userid")+"%";
                });
                circularInfo = circularInfo.slice(0, -1);
                
                var apiDataAdd = {};
                apiDataAdd['action'] = "get_appr_doc_submit";
                apiDataAdd['actionMode'] = "circular";
                apiDataAdd['circularInfo'] = circularInfo;
                apiDataAdd['pCircularDesc'] = $.trim($("textarea[name=gsitm_circular_desc]").val());
                var _apiData = $.extend({}, apiData, apiDataAdd);
                $.post(action_url,{
                    pData : _apiData
                },function(response) {
                    var result = $.parseJSON(response);
                    modal({
                        type: "alert", title:"알림", text: result.msg, callback:function(r) {
                            if(r) {
                                if(result.error == false) {
                                    $(".gsitm_modal_view .pop_container").removeClass("no_scroll");
                                    $(".gsitm_circular_pop").remove();
                                }
                            }
                        }
                    });
                });
            }
        }
    });
});

// get apprline form
$(document).on("click", ".btn_change_apprline", function() {
    $this = $(this);
    var uitem = $this.closest(".bot_form");
    
    var apiDataAdd = {}, apprLines = {};
    apiDataAdd['action'] = "get_apprline_form";
    $(uitem).find("ul.approvallines li span.gsitm_apprLines").each(function(i) {
        apprLines[i] = $(this).text();
    });
    var _apiData = $.extend({}, apiData, apiDataAdd);
    $.post(action_url,{
        pData : _apiData, apprLines : apprLines
    },function(response) {
        var result = $.parseJSON(response);
        
        if($(".gsitm_modal_view").length > 0) {
            $(".gsitm_modal_view .pop_container").addClass("no_scroll");
            
            var html = "<div class='pop_inner circular gsitm_circular_pop'><button class='pop_close gsitm_circular_close'>close</button>"+result.viewData+"</div>";
            $(".gsitm_modal").append(html).promise().done(function() {
                $(document).on('click', '.gsitm_circular_close', function(e) {
                    $('.gsitm_circular_pop').remove(); $(".gsitm_modal_view .pop_container").removeClass("no_scroll");
                });
            });
        } else {
            getGSITMModalView(result.viewData);
        }
    });
    return false;
});
$(document).on("click", ".btn_apprline_targetadd", function() {
    var apprTy = $(this).data('type');
    var apprTyNm = $(this).text();
    $(".circularMemberList tr").each(function() {
        if($(this).hasClass("selected")) {
            $(this).removeClass("selected");
            if($(".circularTargetList tbody tr[data-userid="+$(this).data("userid")+"]").length == 0) {
                $clone = $(this).clone().append("<td>"+apprTyNm+"</td>");
                $clone.attr("data-apprty", apprTy);
                $clone.attr("data-apprtynm", apprTyNm);
                if(apprTy == "A") {
                    if($(".circularTargetList tr[data-apprty=A]").length > 0) {
                        $clone.insertAfter(".circularTargetList tr[data-apprty=A]:last");
                    } else {
                        $clone.prependTo(".circularTargetList tbody");
                    }
                } else if(apprTy == "C") {
                    if($(".circularTargetList tr[data-apprty=A]").length > 0) {
                        $clone.insertAfter(".circularTargetList tr[data-apprty=A]:last");
                    } else {
                        $clone.appendTo(".circularTargetList tbody");
                    }
                } else if(apprTy == "S") {
                    $clone.appendTo(".circularTargetList tbody");
                }
            }
        }
    });
});
$(document).on("click", ".apprlineTargetList tr", function() {
    $this = $(this);
    $(".apprlineTargetList tr").removeClass("selected");
    $this.addClass("selected");
});
$(document).on("click", ".btn_apprline_func", function() {
    $target = $(".apprlineTargetList tr.selected");
    if($target.length == 0) return false;
    
    var apprTy = $target.data("apprty");
    var mode = $(this).data('type');    
    
    if(mode == "delete") $target.remove();
    if(mode == "up") {
        if($target.prev("tr").data("apprty") == apprTy) $target.insertBefore($target.prev("tr"));
    }
    if(mode == "down") {
        if($target.next("tr").data("apprty") == apprTy) $target.insertAfter($target.next("tr"));
    }
});
$(document).on("click", ".btn_apprline_submit", function() {
    $target = $(".apprlineTargetList");
    $apprlineTarget = $("#apprModify").length > 0 ? $("#apprModify ul.approvallines") : $(".cb-chatting-rows .bot_form:last ul.approvallines");
    if($target.find("tr").length == 0) return false;
    $apprlines = "";
    if($apprlineTarget.find("li").length > 0) {
        $apprlines +="<li>"+$apprlineTarget.find("li").html()+"</li>";
    }
    $target.find("tr").each(function(i) {
        $_apprInfo = {"userId": $(this).data("userid"), "userNm": $(this).data("usernm"),"apprTy": $(this).data("apprty"),"apprTyNm": $(this).data("apprtynm"),"titleNm": $(this).data("titlenm"),"posiNm": $(this).data("posinm"),"deptId": $(this).data("deptid"),"coId": $(this).data("coid"),"deptNm": $(this).data("deptnm")};
        $apprlines +="<li><div><span>"+$(this).data("usernm")+"/"+$(this).data("titlenm")+" ["+$(this).data("apprtynm")+"]</span><span class='gsitm_apprLines dispnone'>"+JSON.stringify($_apprInfo)+"</span></div></li>";
    });    
    $apprlineTarget.html($apprlines).parent(".approvallines_area").show();
    if($('#apprModify').length > 0) {
        $('.gsitm_circular_pop').remove(); $(".gsitm_modal_view .pop_container").removeClass("no_scroll");
    } else {
        $('.gsitm_modal').remove(); $("#cb-chatting-body").removeClass("no_scroll");
    }
});
$(document).on('click', '.gsitm_modal_close', function(e) {
    $('.gsitm_modal').remove(); $("#cb-chatting-body").removeClass("no_scroll");
});

function getGSITMDataFormat(obj) {
    if($(obj).closest(".searchbox").length > 0) return false;
    
    var $thisName = $(obj).attr("name");
    var uitem = $(obj).closest(".bot_form");
    
    var reqTySe = reqCl = servId = tmlNo = "";
    
    reqTySe = $(uitem).find("select[name=gsitm_reqTySe] option:selected").val();
    reqCl = $(uitem).find("select[name=gsitm_reqCl] option:selected").val();
    if($thisName == "gsitm_servId") {
        servId = $(uitem).find("select[name=gsitm_servId] option:selected").val();
    } else {
        servId = $(uitem).find("select[name=gsitm_reqCl] option:selected").attr("servId");
    }
    if($thisName == "gsitm_tmlNo") {
        tmlNo = $(">option:selected", $(obj)).val();
    }
    
    var apiDataAdd = {};
    apiDataAdd['action'] = "get_format";
    apiDataAdd['reqTySe'] = reqTySe;
    apiDataAdd['reqCl'] = reqCl;
    apiDataAdd['servId'] = servId;
    apiDataAdd['tmlNo'] = tmlNo;
    var _apiData = $.extend({}, apiData, apiDataAdd);
    $.post(action_url,{
        pData : _apiData
    },function(response) {
        var result = $.parseJSON(response);
        if(result.hasOwnProperty("apprLines")) {
            $(uitem).find(".approvallines").html(result.apprLines);
        }
        if(result.hasOwnProperty("systemList")) {
            if($thisName != "gsitm_servId") {
                $(uitem).find("select[name=gsitm_servId]").html(result.systemList);
            }
        }
        if(result.hasOwnProperty("tmlList")) {
            $(uitem).find("select[name=gsitm_tmlNo]").html(result.tmlList).promise().done(function() {
                if($(this).find("option").length <= 1) $(this).prop("disabled", true);
                else $(this).prop("disabled", false);
            });
        }
        if(result.hasOwnProperty("tmlCnt")) {
            $(uitem).find("textarea[name=gsitm_req_content]").val(result.tmlCnt);
        }
    });
}

function getWelcomeMsg(data) {
    reserveData = {};
    if(data.is_login) {
        chatbotObj.setStorage('gsitm_texpire', data.expire);
        prevForm.remove();
    } else {
        getFormDisabled(prevForm);
    }
    var _dd = {input_type:"chat", userInputDisabled:false};
    chatbotObj.setInputDefault(_dd);
    chatbot.sayHello();
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
                modal({
                    type: "alert", title:"알림", text: result.err_msg, callback:function(r) {
                        if(r) {
                            var _dd = {input_type:"chat", userInputDisabled:false};
                            chatbotObj.setInputDefault(_dd);
                            if(data.action == "user_login") {
                                $("form[name=user_login]").find("input, button").prop("disabled", false);
                            }
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

function getGSITMRequestApproval(data) {
    $.ajax({
        url: action_url,
        data: data,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function(res) {
            var result = $.parseJSON(res);
            if(result.error == true) {
                if(result.err_msg) {
                    modal({
                        type: "alert", title:"알림", text: result.err_msg, callback:function(r) {
                            if(r) {
                                var _dd = {input_type:"chat", userInputDisabled:false};
                                chatbotObj.setInputDefault(_dd);
                            }
                        }
                    });
                }
                return false;
            } else {
                if($(".gsitm_modal").length > 0) {
                    modal({
                        type: "alert", title:"알림", text: result.err_msg, callback:function(r) {
                            if(r) {
                                $('.gsitm_modal').remove(); $("#cb-chatting-body").removeClass("no_scroll");
                            }
                        }
                    });
                } else {
                    if(result.msg) showBotHtmlFormMsg(result);
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

// 상세보기
function getGSITMModalView(data) {
    $(data).find(".pop_close").remove();
    
    if($(data).find(".pop_inner").length > 0) {        
        var data = $(data).find(".pop_inner").html();
    }
    var tpl = "<div class='gsitm_modal'><div class='pop_inner gsitm_modal_view'>"+data+"</div></div>";
    
    $("#cb-chatting-body").addClass("no_scroll");
    $('.gsitm_modal').remove();
    $("#cb-chatting-body").append(tpl).promise().done(function() {
        $(".gsitm_modal .pop_inner").prepend("<button class='pop_close gsitm_modal_close'>close</button>");        
        if($(".gsitm_modal").find(".input_date").length > 0) {
            $(".gsitm_modal").find(".input_date").pickadate();
        }
        if($(".gsitm_modal").find(".btn_files").length > 0) {
            fileForm = null;
            fileForm = $(".gsitm_modal").find(".btn_files").fileForm();
        }
    });
}

// 사용자 조회
function getCircularSearchMember() {
    var findEle = $("#frmSearchMember input[name=gsitm_circular_find]");
    var searchWord = $.trim(findEle.val());
    if(!searchWord) return false;
    
    var apiDataAdd = {};
    apiDataAdd['action'] = "get_circular_usersearch";
    apiDataAdd['searchWord'] = searchWord;
    var _apiData = $.extend({}, apiData, apiDataAdd);
    $.post(action_url,{
        pData : _apiData
    },function(response) {
        var result = $.parseJSON(response);        
        $(".circularMemberList tbody").html(result.viewData);
        findEle.val("");
    });
    return false;
}
