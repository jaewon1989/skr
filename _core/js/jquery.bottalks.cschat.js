var csChatting = {
    module: "chatbot",
    chatbotObj: null,
    csId: null,
    csInfo: null,
    csTimeObj: null,
    csTimeLimit: (30*1000),

    init: function(botObj) {
        self = this;
        self.chatbotObj = botObj;
        if(self.chatbotObj.cmod == ''){
            $(self.chatbotObj.chatScrollContainer).append("<button type='button' class='btn_cs' data-role='cs-chat'>채팅상담<br>연결</button>").promise().done(function(){
                $(document).on("click", "[data-role=cs-chat]", function(e) {
                    self.getCSChat(e);
                });
            });
        }
    },

    // 채팅상담
    getCSChat: function(e) {
        var self = this;
        var target = e.target;
        var _data = {"botid": self.chatbotObj.botId, "roomToken": self.chatbotObj.roomToken};
        if($(target).hasClass("on")) {
            _data['mode'] = "chat_end";
            self.getCSChatRequest(_data);
        } else {
            _data['mode'] = "chat_open";
            _data['target'] = target;

            // 사용자 개인정보 입력
            if(self.chatbotObj.options.cschat_userinfo == 'on'){
                $.post(rooturl+'/?r='+raccount+'&m='+self.module+'&a=chatapi_cs',{
                    mode: "userinfo", botid: self.chatbotObj.botId
                }, function(data) {
                    if(data.result && data.data) {
                        $(self.chatbotObj.chatLogContainer).append(data.data).promise().done(function() {
                            self.chatbotObj.setInputDefault({input_type:"chat"});
                            self.chatbotObj.init_afterAjax();

                            self.getCSChatUserInfo(_data);
                        });
                    }
                },'json');
            } else {
                self.getCSChatRequest(_data);
            }
        }
    },

    getCSChatUserInfo: function(_data) {
        var self = this;
        $(document).on("click", ".submit_uinfo", function() {
            $this = $(this);
            var uname = $this.closest(".bot_form").find(":input[name=cschat_uname]");
            var uphone = $this.closest(".bot_form").find(":input[name=cschat_uphone]");
            var uagree = $this.closest(".bot_form").find(":input:radio[name=cschat_uagree]:checked").val();
            if($.trim(uname.val()) == "") {
                jsModal("alert", "상담자명을 입력해주세요.").then(function(r) {
                    if(r) uname.focus();
                });
                return false;
            }
            if($.trim(uphone.val()) == "") {
                jsModal("alert", "휴대폰번호를 입력해주세요.").then(function(r) {
                    if(r) uphone.focus();
                });
                return false;
            }
            if(!$.trim(uphone.val()).match(/^01([016789])-?([\d]{3,4})-?([\d]{4})$/)) {
                jsModal("alert", "휴대폰번호가 정확하지 않습니다.").then(function(r) {
                    if(r) uphone.focus();
                });
                return false;
            }
            if(uagree != 'true') {
                jsModal("alert", "개인정보의 수집·이용에 동의해주세요.").then(function(r) {});
                return false;
            }
            _data['user_name'] = uname.val();
            _data['user_phone'] = uphone.val();
            _data['user_id'] = "";

            $prevForm = $this.closest(".cb-chatting-form");
            $prevForm.remove();

            self.getCSChatRequest(_data);
        });

        $(document).on("click", ".cancel_uinfo", function() {
            $this = $(this);
            $prevForm = $this.closest(".cb-chatting-form");
            $prevForm.remove();
        });
    },

    getCSChatRequest: function(_data) {
        var self = this;
        if(_data.mode == "chat_open") {
            $(_data.target).addClass("on").html("채팅상담<br>연결중");
            self.getNoticeTpl({action:"view", msg:"채팅상담 연결중입니다."});

            // client 챗봇창에서 soketio 초기화
            if(!self.chatbotObj.socket && self.chatbotObj.cmod == ''){
                self.chatbotObj.initSocketio();
            }
            self.getCSChatMessage();
        }
        delete _data['target'];

        $.post(rooturl+'/?r='+raccount+'&m='+self.module+'&a=chatapi_cs', _data, function(data) {
            // 채팅상담 연결 실패
            if(_data.mode == "chat_open") {
                if(!data.result) {
                    data['msg'] = "채팅상담 연결 실패.";
                    self.getCSChatEnd(data);
                    setTimeout(function() {
                        self.getNoticeTpl({action:"hidden", msg:""});
                    },2000);
                } else {
                    self.csTimeObj = setTimeout(function() {
                        data['msg'] = "채팅상담이 연결이 원활하지 않습니다.<br>다시 시도해주세요.";
                        self.getCSChatEnd(data);
                        setTimeout(function() {
                            self.getNoticeTpl({action:"hidden", msg:""});
                        },2000);
                    },self.csTimeLimit);
                }
            }
            // 채팅상담 종료
            if(_data.mode == "chat_end" && data.result) {
                if(self.csTimeObj !== null) {
                    clearTimeout(self.csTimeObj);
                    self.csTimeObj = null;
                }

                data['msg'] = "채팅상담이 종료되었습니다.";
                self.getCSChatEnd(data);
                setTimeout(function() {
                    self.getNoticeTpl({action:"hidden", msg:""});
                    location.reload();
                },2000);
            }
            return false;
        },'json');
    },

    getCSChatEnd: function(data) {
        var self = this;
        self.getNoticeTpl({action:"view", msg:data.msg});
        $("[data-role=cs-chat]").removeClass("on").html("채팅상담<br>연결");
        if(self.chatbotObj.options.use_chatting != 'on' && self.chatbotObj.socket){
            self.chatbotObj.socketClose();
            self.chatbotObj.csMod = null;
        } else {
            self.chatbotObj.csMod = null;
        }
    },

    getCSChatForceEnd: function(botObj) {

        var self = this;
        self.chatbotObj = botObj;
        var _data = {"botid": self.chatbotObj.botId, "roomToken": self.chatbotObj.roomToken, "mode":"chat_force_end"};

        $.post(rooturl+'/?r='+raccount+'&m='+self.module+'&a=chatapi_cs', _data, function(data) {
        });
    },

    getCSChatProcessInput: function(msg) {
        var self = this;
        $.post(rooturl+'/?r='+raccount+'&m='+self.module+'&a=chatapi_cs',{
            mode: 'user_msg',
            botid: self.chatbotObj.botId,
            roomToken: self.chatbotObj.roomToken,
            msg: msg,
            cs_id: self.csId
        }, function(data) {
            if(!data.result) {
                self.getNoticeTpl({action:"view", msg:"채팅상담 접속이 원활하지 않습니다.<br>다시 시도해주세요."});
                setTimeout(function() {
                    self.getNoticeTpl({action:"hidden", msg:""});
                },2000);
            }
            return false;
        },'json');
    },

    // notice box
    getNoticeTpl: function(data) {
        var self = this;
        var msgBox = self.chatbotObj.chatLogContainer;
        if(data.action == "view") {
            $("[data-role=chat-notice-box]").remove();

            var notice_tpl = '';
            notice_tpl +='<div class="bot_notice" data-role="chat-notice-box">';
                notice_tpl +='<div class="notice_inner">';
                    notice_tpl +='<div class="cb-spinner"></div>';
                    notice_tpl +='<div class="msg">'+data.msg+'</div>';
                notice_tpl +='</div>';
            notice_tpl +='</div>';
            $(msgBox).append(notice_tpl);
            self.chatbotObj.resetScrollTop(data);
        } else {
            $("[data-role=chat-notice-box]").remove();
        }
    },

    getCSChatMessage: function() {
        var self = this;
        self.chatbotObj.socket.on("user_msg", function(data) {
            switch(data.role) {
                // 채팅상담 응답
                case "cs_chat" :
                    if(self.chatbotObj.roomToken == data.roomToken){
                        if(data.action == "cs_connect") {
                            // 채팅서버 연결 성공
                            self.chatbotObj.csMod = true;
                            self.csId = data.cs_id;
                            self.csInfo = data.csInfo;
                            clearTimeout(self.csTimeObj);
                            self.csTimeObj = null;

                            self.getNoticeTpl({action:"view", msg:"채팅상담이 연결되었습니다."});

                        } else if(data.action == "cs_end") {
                            data['msg'] = "채팅상담이 종료되었습니다.";
                            self.getCSChatEnd(data);
                            setTimeout(function() {
                                self.getNoticeTpl({action:"hidden", msg:""});
                                location.reload();
                            },2000);

                        } else if(data.action == "cs_chat") {
                            if($("[data-role=chat-notice-box]").length > -1) {
                                self.getNoticeTpl({action:"hidden", msg:""});
                            }

                            // 상담사 응답
                            if(data.hasOwnProperty("messages")) {
                                if(data.mode == "text") {
                                    var _data = {'role': 'bot', 'msg': data.messages[0]};
                                    var _msg = self.chatbotObj.getMsgTpl(_data);
                                }

                                $(self.chatbotObj.chatLogContainer).append($(_msg));
                                self.chatbotObj.setInputDefault({input_type:"chat"});
                                self.chatbotObj.init_afterAjax();

                                // 모니터링 on일 경우 웹소켓으로 응답 전송
                                if(!self.chatbotObj.cmod && self.chatbotObj.options.use_chatting=='on' && self.chatbotObj.admSockId){
                                    self.chatbotObj.socketSend({"role":"chat_log_send", "roomToken":self.chatbotObj.roomToken, "to":self.chatbotObj.admSockId, "msg":_msg});
                                }
                            }
                        }
                    }
                    break;
            }
        });
    }

};
