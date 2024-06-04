(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if (typeof module === 'object' && module.exports) {
        // Node/CommonJS
        module.exports = function( root, jQuery ) {
            if ( jQuery === undefined ) {
                if ( typeof window !== 'undefined' ) {
                    jQuery = require('jquery');
                }
                else {
                    jQuery = require('jquery')(root);
                }
            }
            factory(jQuery);
            return jQuery;
        };
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function($) {

    var Bottalks = {

        // Instance variables
        // ==================

        $el: null,
        $el_id: null,
        room: null,
        module: null,
        vendor: null,
        botId: null, // bot id
        botUid: null,
        botActive: null, // 1=dev or 2=live
        botName: null,
        cmod: null,
        dialog: null,
        bot_avatar_src: null,
        themePath: null, // 플러그인 초기화시 지정
        themeName: null, // 플러그인 초기화시 지정
        msgTpl: {},
        userLevel: 0,
        userGroup: 0,
        currentPage: 1,
        recnum: null, // 출력갯수
        orderby: null, // asc, desc 기준
        sort: null, // sort 기준
        totalPage: null,
        totalRow: null,
        is_login: memberid!=''?true:false,
        emoticon_path: null,
        emoticonPath : null,
        userInputEle: null,
        userInputText: null,
        inputUNRS: null,
        chatScrollContainer: null,
        chatLogContainer: null,
        btnShowRecGoods: null,
        btnSend: null,
        mbruid:null,
        options: {},
        context: null, // 컨텍스트 데이타
        getTypeInput: null, // url get 방식으로 들어오는 문장처리
        socket: null, // soketio 준비
        admSockId: null,
        humanMod: false, // 상담사 모드 초기화
        roomToken: null, // 채팅방 토큰
        faq_usable: null,
        bot_skin: null,
        bot_type: null,
        channel: 'web',
        cgroup: null,
        unityInstance: null,
        csMod: null,
        chatlogs: [], // chatlogs
        events: {
            'scroll [data-role="chatting-logContainer"]' : 'checkScrollTop', // 스크롤 이벤트 (채팅내역 더 가져오기)
            'click [data-role="btn-showEmoticon"]' : 'showEmoticonBox', // 이모티콘 박스 보여주기
            'click [data-role="emoticon"]' : 'insertEmoticon', // 이모티콘 입력
            'keyup [data-role="bot-talks"]' : 'enterUserMsg',
            'focusin [data-role="bot-talks"]' : 'showBtnSend',
            'focusout [data-role="bot-talks"]' : 'resetScrollBlur',
            'click body' : 'hideBtnSend',
            'click [data-role="btn-forSquare"]' : 'sayHello', // 첫번째 메세지 출력
            'click [data-role="btn-send"]' : 'processInput',
            'click [data-role="menuType-resItem"]' : 'getMenuRespond', // multi item 클릭
            'click [data-role="cardType-resItem"]' : 'getCardRespond', // card  item 클릭
            'click [data-role="chat-active"]' : 'chatActive', // card  item 클릭
            'click [data-role="chat-exit"]' : 'chatExit', // 채팅창 닫기
            'click [data-role="chat-start"]' : 'initChatStart', // 인트로 화면에서 채팅 시작
        },

        // check mobile device
        isMobile: function(){
            try{ document.createEvent("TouchEvent"); return true; }
            catch(e){ return false; }
        },

        // Initialization
        init: function(options, el) {
            var self = this;
            this.$el = $(el);
            this.$el_id = '#'+this.$el.attr('id');
            this.$el.css("position","relative");
            if(this.isMobile()) this.$el.addClass('mobile');

            // Init options
            this.options = $.extend(true, {}, this.getDefaultOptions(), options);
            this.$el.addClass(this.options.containerClass); // 채팅박스 출력 container 에 class 추가
            this.module = this.options.moduleName; // module name 값 세팅
            this.cmod = this.options.cmod; // cs or vod
            this.botId = this.options.botId; // bot id 값 세팅
            this.themePath = this.options.themePath;
            this.themeName = this.options.themeName?this.options.themeName:null;
            this.bot_avatar_src = this.options.bot_avatar_src?this.options.bot_avatar_src:null;
            this.bot_service = this.options.bot_service?this.options.bot_service:null;
            this.recnum = this.options.recnum?this.options.recnum:10; // 출력 갯수
            this.orderby = this.options.orderby?this.options.orderby:'asc';
            this.sort = this.options.sort?this.options.sort:'uid';
            this.userInputEle = this.options.userInputEle;
            this.chatScrollContainer = this.options.chatScrollContainer;
            this.chatLogContainer = this.options.chatLogContainer;
            this.btnShowRecGoods = this.options.btnShowRecGoods;
            this.btnSend = this.options.btnSend;
            this.emoticonBox = this.options.emoticonBox;
            this.emoticon_path = this.options.emoticon_path;
            this.mbruid = this.options.mbruid;
            this.bot_type = this.options.bot_type;
            this.bot_skin = this.options.bot_skin;
            this.initMsgTpl(); // msg template 초기화
            this.chatLoadBox = this.options.chatLoadBox;
            // roomToken 세팅 방식 분기
            var _roomToken = this.getUrlParam("roomToken");
            this.roomToken = _roomToken ? _roomToken : this.getRoomToken();
            this.room = this.roomToken;
            // set storage
            this.setStorage('roomToken', this.roomToken);

            // intro 설정되어 있지만 인터페이스가 voice일 경우는 인트로 무시
            if(this.options.bot_interface == 'voice' || this.cmod) {
                this.initChatBox(); // load 챗박스
            } else {
                if(parseInt(this.options.bot_intro)==1) this.getChatIntro();
                else this.initChatBox();
            }
            this.faq_usable = this.options.faq_usable;
        },

        // 컨텍스트 초기화
        initContext: function(){
             $.post(rooturl+'/?r='+raccount+'&m='+this.module+'&a=do_UserAction',{
                act : "init-context",
            },function(response) {

            });
        },

        // 메제시 템플릿 초기화 함수 (type : me,other,notice)
        initMsgTpl : function(){
            var self = this;
            var chat_id = this.room;
            var themeName = this.themeName;
            var tmp_obj = {};
            $.get(
                rooturl+'/?r='+raccount+'&m='+this.module+'&a=get_Msg_Tpl',
                {chat_id: chat_id,themeName:themeName },
                function(response){
                    var result = $.parseJSON(response);
                    self.msgTpl = $.extend(self.msgTpl,result);
                }
            );
        },

        // 메세지 template 추출 함수
        getMsgTpl : function(data){
            var d = new Date();
            var hour = d.getHours();
            var date = (hour < 12 ? '오전 '+hour : '오후 '+((hour-12)==0?12:(hour-12)))+':'+d.getMinutes();
            var role = data.role;
            var cmod = this.cmod;

            // 템플릿 추출 및 지정
            var msgTpl = this.msgTpl;
            var tpl = msgTpl[data.role]; // bot, user 결정

            // 템플릿 $var 값 치환
            if(data.type=='photo') last_tpl = tpl.replace(/\{\$photo_src}/gi,data.photo_src); // 포토 scr 치환
            else if(data.type=='file') last_tpl = tpl.replace(/\{\$file_src}/gi,data.file_src); // 파일 scr 치환
            else if(data.type=='emoticon') last_tpl = tpl.replace(/\{\$emoticon_src}/gi,data.emoticon_src); // 이모티콘 scr 치환
            else{
                if(role=='bot'){
                    var name_label;
                    //if(this.humanMod) name_label ='상담원';
                    name_label = this.options.bot_service;
                    tpl = tpl.replace(/\{\$selection_class\}/gi,'');
                    tpl = tpl.replace(/\{\$bot_name\}/gi,name_label); // 서비스명 치환
                    tpl = tpl.replace(/\{\$bot_service\}/gi,name_label); // 서비스명 치환
                    tpl = tpl.replace(/\{\$bot_avatar_src\}/gi,this.options.bot_avatar_src); // 서비스명 치환
                    tpl = tpl.replace(/\{\$response\}/gi,'<span>'+data.msg+'</span>'); // 메세지 치환
                    tpl = tpl.replace(/\{\$date\}/gi,date); // 날짜 치환
                }else{
                    tpl = tpl.replace(/\{\$message\}/gi,data.msg); // 메세지 치환
                    tpl = tpl.replace(/\{\$date\}/gi,date); // 날짜 치환
                }
            }
            return tpl;
        },

        // 채팅 or 봇팅 전환
        chatActive: function(e){
            var self = this;
            var target = e.currentTarget;
            var pDiv = $(target).parent();
            var roomToken = $(target).data('token');
            var chatActive_label = $('[data-role="chatActive-label-'+roomToken+'"]');
            var _to = $('iframe[data-token="'+roomToken+'"]', parent.document).attr('data-sockid');

            if($(pDiv).hasClass("botSwitch-off")) {
                $(pDiv).removeClass("botSwitch-off");
                var msg = 'on';
                this.humanMod = true;
                var _dd = {input_type:"chat"};
                self.setInputDefault(_dd);

            }else{
                $(pDiv).addClass("botSwitch-off");
                var msg = 'off';
                $(chatActive_label).text('off');
                this.humanMod = false;
                var _dd = {placeholder: "채팅 기능을 활성화 해주세요"};
                self.setInputDisabled(_dd);
            }

            $(chatActive_label).text(msg);
            parent.managerAction({role: "changeHumanMod",msg: msg, room: roomToken, to:_to});
        },

        // socketio 초기화
        initSocketio : function() {
            var self = this;
            var socketioUrl = this.options.socketioUrl;
            var name = this.options.userNameText; // 사용자명
            var room = this.room;
            var msg_data ={};
            var cmod = this.cmod;

            // Create SocketIO instance, connect
            this.socket = io.connect(socketioUrl+'/chatbot_'+self.botId,{secure:true});

            this.socket.on('connect',function() {
                // 클라이언트 접속 정보 전송
                self.socketSend({"role":"new_client", "type":"user", "vendor":self.vendor, "botid":self.botId, "roomToken":self.roomToken});
                console.log('Client has connected to the server!');
            });

            this.socket.on('connectInfo', function(data) {
            });

            this.socket.on('disconnect', function() {
                console.log('disconnected');
            });

            // 메세지 수신
            this.socket.on("user_msg", function(data) {
                switch(data.role) {
                    case "chat_log" :
                        if(self.roomToken == data.roomToken){
                            self.admSockId = data.from;

                            var htmlData = $(self.chatLogContainer).html();
                            self.socketSend({"role":"chat_log_send", "roomToken":self.roomToken, "to":data.from, "msg":htmlData});
                        }
                        break;

                    case "changeHumanMod" :
                        if(data.from == self.admSockId) {
                            self.humanMod = data.msg == 'on' ? true : false;
                        }
                        break;

                    case "bot_input" :
                        if(self.roomToken == data.roomToken){
                            setTimeout(function() {
                                var msgBox = self.chatLogContainer;
                                var scrollBox = self.chatScrollContainer;
                                $(msgBox).append($(data.msg));
                                $(scrollBox).scrollTop($(scrollBox).prop("scrollHeight"));
                            },10);
                        }
                        break;
                }
            });
        },

        socketSend: function(data) {
            var self = this;
            if(self.socket) {
                self.socket.emit("send_data", data);
            }
        },
        socketClose: function() {
            var self = this;
            if(self.socket) self.socket.disconnect();
            self.socket = null;
        },

        // Default options
        getDefaultOptions: function(){
            return {
                userInputEle : '[data-role="bot-talks"]',
                chatLogContainer : '[data-role="chatting-logContainer"]',
                chatScrollContainer : '[data-role="chatting-ScrollContainer"]',
                chatNoticeBox : '[data-role="chatting-noticeBox"]',
                emoticonBox: '[data-role="emoticon-box"]',
                btnShowRecGoods: '#show-firstKwd',
                btnSend : '[data-role="btn-send"]',
                learningDataForm : '#form-learningData',
                orderby: 'desc', // 출력 순서 기본값
                recnum: 10, // 출력갯수
                useEnterSend: true,
                userInputText: null,
                voiceResponse: function(data) {},
                getMic: function(data) {},
                chatLoadBox : '[data-role="chat-load-box"]',
            }
        },

        // 채팅 더 가져오기 이벤트
        checkScrollTop : function(e){
            var msg_box = this.chatLogContainer;
            var scrollTop = $(this.chatScrollContainer).scrollTop();
            var msg_row = $(msg_box).find('[data-role="msg-row"]:first');
            var currentPage = this.currentPage;
            var totalPage = this.totalPage;
            if((scrollTop<50) && (currentPage<totalPage)){
                this.getMoreChat(currentPage);
                this.currentPage++;
            }
        },

        // 채팅내역 더 가져오기
        getMoreChat : function(currentPage){
            var msg_box = this.chatLogContainer;
            $.get(rooturl+'/?r='+raccount+'&m='+this.module+'&a=do_userAct',{
                act : 'getMoreChat',
                currentPage : currentPage,
                sort : this.sort,
                orderby : this.orderby,
                recnum : this.recnum,
                bid : this.botId,
                themeName : this.themeName
            },function(response) {
                var result = $.parseJSON(response);
                var error = result.error;
                if(error){
                    var error_msg = result.error_msg;
                    var noti_data = {"container":null,"msg":error_msg};
                    self.showNotify(noti_data);
                }else{
                    $(msg_box).prepend(result.content);
                }
            });
        },

        // chatting box 로딩 및 접속자 권한/관련 데이타 세팅
        initChatBox : function(){
            var self = this;
            var botId = this.botId;
            var container = this.$el;
            var msgBox = this.chatLogContainer; // 메세지 출력 박스
            var scrollContainer = this.chatScrollContainer; // scroll 대상
            var themeName = this.themeName; // 테마명
            var mbruid = this.mbruid?this.mbruid:localStorage.getItem("mbruid");
            var cmod = this.cmod;
            var roomToken = this.roomToken;

            if(self.options.bot_interface == '3dmanual' && !self.cmod) return false;

            $.post(rooturl+'/?r='+raccount+'&m='+this.module+'&a=get_Chatting_Box',{
                botid : botId,
                theme_name: themeName,
                mbruid : mbruid,
                cmod : cmod,
                roomToken : roomToken,
            },function(response) {
                var result = $.parseJSON(response);
                self.userLevel = result.userLevel; // 접속자 level
                self.userGroup = result.userGroup; // 접속자 group
                self.bot_avatar_src = result.bot_avatar_src; // 봇 아바타
                self.vendor = result.vendor;
                self.botUid = result.botUid;
                self.botActive = result.botActive;
                self.cgroup = result.cgroup;
                if(self.cmod == 'dialog') {
                    var tdialog = self.getUrlParam('dialog');
                    self.dialog = tdialog ? tdialog : result.dialog;
                } else {
                    self.dialog = result.dialog;
                }
                self.mbruid = result.mbruid; // 방문회원인 경우 챗팅박스 초기화시 자동 가입

                $(container).append(result.chat_box).promise().done(function() {
                    var _dd = {input_type: "say_hello"};
                    self.resetScrollTop(_dd);
                    self.AfterInitChatBox();
                });
            });
        },

        // get param 값 가져오기
        getUrlParam: function(name){
            var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
            if (results==null) return null;
            return decodeURI(results[1]) || 0;
        },

        // ChatBox 로딩 후 초기화 함수들 호출
        AfterInitChatBox : function(){
            var self = this;
            localStorage.setItem("mbruid",this.mbruid);
            this.undelegateEvents(); // msg box 엘리먼트들 이벤트 바인딩 off
            this.delegateEvents(); // msg box 엘리먼트들 이벤트 바인딩 on
            var e = $.Event('shown.ps.chatbot', { relatedTarget: this.$el_id });
            this.$el.trigger(e);
            var getTypeInput = this.getUrlParam('user_input');

            if (window.self == window.top) $('[data-role="chat-exit"]').hide();
            else window.parent.postMessage({'bottalks_loaded':true}, '*');

            // url get user_input 값 있으면 바로 진행
            if(getTypeInput){
                this.getTypeInput = getTypeInput;
                this.processInput();
            }else{
                // 인사말 : 첫번째 노드값을 출력한다.
                if(this.cmod != 'monitering') {
                    // gsitm
                    //if (self.cgroup == "gsitm" && window.self == window.top) {
                    if (self.cgroup == "gsitm") {
                        var tokenExpire = this.getStorage('gsitm_texpire');
                        tokenExpire = tokenExpire ? parseInt(tokenExpire) : 0;
                        var _nowTime = Math.floor(new Date().getTime()/1000);
                        var gsitm_log = (tokenExpire < _nowTime) ? true : false;
                    }
                    if(gsitm_log === true) {
                        // gsitm login form 출력
                        this.getLoginForm();
                    } else {
                        this.sayHello();
                    }
                }
            }

            if(this.options.use_chatting=='on' && this.cmod == ''){
                // client 챗봇창에서 soketio 초기화
                self.initSocketio();
            }

            if(this.cmod =='monitering'){
                // 입력창 비활성
                var _dd = {placeholder: "채팅 기능을 활성화 해주세요"};
                this.setInputDisabled(_dd);

                // 모니터링일 경우 챗박스 로딩 알림
                parent.getChatLog(self.roomToken);
            } else {
                if(this.faq_usable) self.getAutoComplete();
            }

            getChatbotCustomInit(this);

            //cs chat init
            if(this.options.use_cschat == 'on') {
                csChatting.init(this);
            }
        },

        // Get user msg template
        getUserMsgTpl : function(data){
            // tpl_type : user or bot
            var d = new Date();
            var hour = d.getHours();
            var date = (hour < 12 ? '오전 '+hour : '오후 '+((hour-12)==0?12:(hour-12)))+':'+d.getMinutes();
            var emoticon_path = this.emoticon_path;
            var msg = data.msg;
            var msg_type = data.msg_type;
            var show_msg;

            // msg 타입에 따른 msg 출력형태 다름
            if(msg_type=='text' || msg_type=='hMenu') show_msg = msg;
            else if(msg_type=='emoticon') show_msg = '<span class="emoticon_wrap"><img src="'+emoticon_path+'emo_'+msg+'.png"/></span>';
            var user_msg_tpl = '';
            user_msg_tpl +='<div class="cb-chatting-chatline">';
                user_msg_tpl +='<div class="cb-chatting-sent">';
                    user_msg_tpl +='<div class="cb-chatting-info">';
                        user_msg_tpl +='<span class="cb-chatting-date">'+ date +'</span>';
                    user_msg_tpl +='</div>';
                    user_msg_tpl +='<div class="cb-chatting-balloon">';
                       user_msg_tpl +='<p><span>'+ show_msg +'</span></p>';
                    user_msg_tpl +='</div>';
                user_msg_tpl +='</div>';
            user_msg_tpl +='</div>';

            return user_msg_tpl;
        },

        delegateEvents: function() {
            this.bindEvents(false);
        },

        undelegateEvents: function() {
            this.bindEvents(true);
        },

        bindEvents: function(unbind){
            var bindFunction = unbind ? 'off' : 'on';
            for (var key in this.events) {
                var eventName = key.split(' ')[0];
                var selector = key.split(' ').slice(1).join(' ');
                var methodNames = this.events[key].split(' ');
                for(var index in methodNames) {
                    if(methodNames.hasOwnProperty(index)) {
                        var method = this[methodNames[index]];
                        // Keep the context
                        method = $.proxy(method, this);

                        if (selector == '') {
                            this.$el[bindFunction](eventName, method);
                        } else {
                            // scroll 이벤트는 해당 엘리먼트에 직접 바인딩 해야한다.
                            if(eventName=='scroll') $(selector)[bindFunction](eventName,method);
                            else this.$el[bindFunction](eventName, selector, method);
                        }
                    }
                }
            }
        },

        // 알림 출력
        showNotify : function(data){
            var container = data.container?data.container:this.$el;
            var msg = data.msg;
            var notify_msg ='<div id="kiere-notify-msg">'+msg+'</div>';
            var notify = $('<div/>', { id: 'kiere-notify', html: notify_msg})
                  .addClass('active')
                  .appendTo(container)
            setTimeout(function(){
                $(notify).removeClass('active');
                $(notify).remove();
            }, 2000);
        },

        // emoticon 박스 보여주기
        showEmoticonBox: function(){
            $(this.emoticonBox).slideToggle('fast');
        },

        // emoticon 입력
        insertEmoticon: function(e){
            var self = this;
            var ele = e.currentTarget;
            var msg = $(ele).data('emotion');
            var msg_type = 'emoticon';
            var msg_data = this.setMsgData(msg,msg_type);
            this.showUserMsg(msg_data);
            setTimeout(function(){
               self.getBotMsg(msg_data);
            },200);
            $(this.emoticonBox).slideToggle('fast');
        },
         // chat toekn 생성
        getRoomToken : function(){
            return (Math.floor(Math.random()*1000).toString()+new Date().getTime().toString(32))+(Math.random().toString(32).substr(2,8));
        },

        // 입력창 포커스 이벤트
        focusInput : function(){
            var userInputEle = this.userInputEle;
            setTimeout(function(){
                $(userInputEle).focus();
            },10);
        },

        // User enter input
        enterUserMsg : function(e){
            var keycode = (e.keyCode ? e.keyCode : e.which);
            if (keycode == 13) {
                this.processInput();
            } else {
                if ($(this.userInputEle).val().length > 0) {
                    $(this.$el).addClass("cb-input");
                } else {
                    $(this.$el).removeClass("cb-input");
                }
            }
        },

        getCardRespond: function (e) {
            var self = this;
            var target = e.currentTarget;
            var data = $(target).data();
            data['bot'] = self.botUid;
            data['botid'] = self.botId;
            data['cmod'] = self.cmod;
            data['roomToken'] = self.roomToken;
            data['botActive'] = self.botActive;
            data['bot_type'] = self.bot_type;
            data['channel'] = self.channel;
            data['cgroup'] = self.cgroup;

            //aramjo
            var clickattr = $(target).attr('onclick');
            if (typeof clickattr !== typeof undefined && clickattr !== false) {
                data['vendor'] = self.vendor;
                data['mbruid'] = self.mbruid ? self.mbruid : localStorage.getItem("mbruid");
                data['printType'] = 'C';
                data['msg'] = data['title'];

                $.post(rooturl+'/?r='+raccount+'&m='+self.module+'&a=get_chatLog',{
                    data: data
                }, function(){
                });
                self.setInputDefault(data);
                return false;
            }
        },

        // User click send-btn
        processInput : function(){
            var self = this;
            var getTypeInput = this.getTypeInput;
            var user_input = this.userInputEle;
            var msg = getTypeInput ? getTypeInput: $(user_input).val();
            var msg_user = userClearInput(msg);
            var msg_type = 'text';
            var room =this.room;
            var inputer = this.options.isAdm ? 'bot' : 'user';
            var humanMod = this.humanMod;

            if(msg){
                var msg_data = self.setMsgData(msg,msg_type);
                if(!getTypeInput) {
                    self.checkUNRS(msg_data);
                    var msg_user_data = self.setMsgData(msg_user,msg_type);
                    self.showUserMsg(msg_user_data);
                }
                if(!humanMod && !self.csMod) self.getBotMsg(msg_data);
                $(self.$el).removeClass("cb-input");

            }else{
                $(user_input).val("");
                self.focusInput();
            }
        },

        checkUNRS: function(data) {
            var self = this;
            if(!self.cmod && self.options.use_chatting=='on' && !self.humanMod) {
                if(self.inputUNRS == null) self.inputUNRS = {'unknown':0, 'sameq':0};

                if(data.type == 'unknown') {
                    self.inputUNRS['unknown']++;
                    data['count'] = self.inputUNRS['unknown'];
                } else {
                    data.msgval = getRmSpecialChar(data.msg);
                    if(self.userInputText != data.msgval) {
                        self.userInputText = data.msgval;
                    } else {
                        self.inputUNRS['sameq']++;
                        data['type'] = 'sameq';
                        data['count'] = self.inputUNRS['sameq'];
                    }
                }

                self.socketSend({"role":"add_cnt", "type":data.type, "count":data.count, "vendor":self.vendor, "botid":self.botId, "roomToken":self.roomToken});
            }
        },

        // 채팅 메세지 저장
        saveChatMsg: function(data){
            var self = this;
            data['vendor'] = this.vendor;
            data['bot'] = this.botUid;
            data['roomToken'] = this.roomToken;
            data['botid'] = this.botId;
            data['cmod'] = this.cmod;
            data['botActive'] = this.botActive;
            data['humanMod'] = this.humanMod;
            data['bot_type'] = this.bot_type;
            data['text'] = data.msg; // botChatLog 용
            data['content'] = data.msg; // chatLog 용
            $.post(rooturl+'/?r='+raccount+'&m='+this.module+'&a=do_UserAction',{
                act : 'save-chatMsg',
                data: data
            },function(response) {
                var result = $.parseJSON(response);
                var error = result.error;
            });
        },

        init_afterAjax: function(){
            RC_initSwiper(); // require rc.swiper.js
            RC_initPhotoSwipe();
            if($(':input[name=reserve_uphone]').length > 0) $(':input[name=reserve_uphone]').mask('00000000000');
            if($('.reserve_date').length > 0) {
                $('.reserve_date').datepicker({
                    language: 'ko',
                    minDate: new Date(),
                    onSelect: function(fDate, date) {
                        if(fDate) console.log(fDate);
                    }
                });
            }
        },


        // reset scrollTop
        resetScrollTop : function(data){
            var scrollBox = this.chatScrollContainer;
            var nSHeight = $(scrollBox).prop('scrollHeight');
            var nCHeight = $(scrollBox).prop('clientHeight');
            var inputType = data.input_type?data.input_type:'';
            var user_input = this.userInputEle;

            if(this.isMobile()) {
                var lastY = 0;
                $(scrollBox).on('touchstart', function(e) {
                    if(e.hasOwnProperty('touches')) {
                        lastY = e.touches[0].clientY;
                    }
                });

                $(scrollBox).on('touchmove', function(e) {
                    if(e.hasOwnProperty('touches')) {
                        var top = e.touches[0].clientY;
                        var scrollTop = $(e.currentTarget).scrollTop();
                        var direction = (lastY - top) < 0 ? "up" : "down";

                        // FIX IT!
                        if (scrollTop == 0 && direction == "up") {
                            e.preventDefault();
                        } else if (scrollTop >= (e.currentTarget.scrollHeight - e.currentTarget.outerHeight()) && direction == "down") {
                            e.preventDefault();
                        }
                        lastY = top;
                    }
                });
            }

            $(user_input).val('');
            if($(".cb-chatting-form:last").find(".bot_form.gsitm_start").length > 0) {
                $(".cb-chatting-form:last").find(".bot_form.gsitm_start")[0].scrollIntoView(true)
            } else {
                $(scrollBox).stop().animate({scrollTop:(nSHeight-nCHeight)},200);
            }
        },
        resetScrollBlur : function() {
            var scrollBox = this.chatScrollContainer;
            $(scrollBox).scrollTop(($(scrollBox).scrollTop()+1));
        },

        // 챗봇 인사
        sayHello : function(){
            var msg_data = this.setMsgData('hi','say_hello');
            this.getBotMsg(msg_data);
        },

        // set msg data : msg, msg_type ....
        setMsgData : function(msg,msg_type){
            var msg_data = {"msg":msg,"msg_type":msg_type}
            return msg_data;
        },

        // DB 에서 찾아서 답변 (msg_type : text, recommend product)
        getBotMsg : function(data){
            var self = this;
            var msg = data.msg;
            var msg_type = data.msg_type;
            var _data = {};
            _data['req_type'] = 'text';
            _data['botid'] = this.botId;
            _data['msg'] = msg;
            _data['msg_type'] = msg_type;
            _data['_lang'] = sessionStorage.getItem('now_lang');
            _data['cmod'] = this.cmod;
            _data['mbruid'] = this.mbruid?this.mbruid:localStorage.getItem("mbruid");
            _data['vendor'] = data.vendor?data.vendor:this.vendor;
            _data['bot'] = data.bot?data.bot:this.botUid;
            _data['dialog'] = data.dialog?data.dialog:this.dialog;
            _data['roomToken'] = this.roomToken;
            _data['botActive'] = this.botActive;
            _data['bot_type'] = this.bot_type;
            _data['bot_skin'] = this.bot_skin;
            _data['channel'] = this.channel;
            _data['cgroup'] = this.cgroup;

            $.post(rooturl+'/?r='+raccount+'&m='+this.module+'&a=get_reply',{
                pData : _data
            },function(res) {
                var result = $.parseJSON(res);
                var reply = result.reply;
                var response = reply.response;
                var res_type = reply.res_type;
                if($.isArray(response)){
                    $.each(response,function(i,resItem){
                        if($.isArray(resItem[0])){
                            var itemType = resItem[0][0];
                            var itemCont = resItem[0][1];
                        }else{
                            var itemType = resItem[0];
                            var itemCont = resItem[1];
                        }
                        if($.isArray(itemCont)){
                            $.each(itemCont,function(i,item){
                                var resData = {"type":item.res_type,"msg":item.content,"input_type": msg_type};
                                self.showBotMsg(resData);
                            });
                        }else{
                            var resData = {"type":itemType,"msg":itemCont,"input_type": msg_type};
                            self.showBotMsg(resData);
                        }
                    });
                }else{
                    var resData = {"type":reply.res_type,"msg":result.content,"input_type": msg_type};
                    self.showBotMsg(resData);
                }

                // 관리자 모드 dialog 작성 테스트 모드
                if(msg && msg_type !='say_hello' && self.cmod=='dialog'){
                    var data = result.reply; // 최종 모든 결과값
                    data['userInput'] = msg;
                    self.showTestLog(data);
                }
                self.init_afterAjax();

                // 모니터링 unknown 전송
                if(reply.hasOwnProperty('unknown') && reply.unknown){
                    self.checkUNRS({'type':'unknown'});
                }
            });
        },

        showTestLog: function(data){
            // 엘리먼트 정의
            var testLogPanel = $('#testLogPanel',parent.document);
            var userInputEle = $(testLogPanel).find('[data-role="testLogPanel-userInput"]');
            var mopAnalEle = $(testLogPanel).find('[data-role="mopAnalBox"]');
            var intentNameEle = $(testLogPanel).find('[data-role="intentName"]');
            var intentScoreEle = $(testLogPanel).find('[data-role="intentScore"]');
            var intentScoreListEle = $(testLogPanel).find('[data-role="intentScoreList"]');
            var entityListEle = $(testLogPanel).find('[data-role="entityListBox"]');
            var nodeNameEle = $(testLogPanel).find('[data-role="nodeName"]');
            var nodeResAnalEle = $(testLogPanel).find('[data-role="nodeResAnalBox"]');

            // userInput
            $(userInputEle).text(data.userInput);
            $(mopAnalEle).html(data.mopData);

            // intent
            var intentName = data.intentName?'#'+data.intentName:'';
            var intentScore = data.intentScore?data.intentScore:'';
            if(data.intentScore && data.intentScore < data.intentMV) {
                intentScore += "<span class='intent-mv'>(인텐트 분류 기준 미달!)</span><div class='intent-msg'>* 예문을 추가 학습해주세요.</div>";
            }
            $(intentNameEle).html(intentName);
            $(intentScoreEle).html(intentScore);
            $(intentScoreListEle).html(data.intentScoreList);

            // entity
            var entityList = data.entityList?data.entityList:'';
            $(entityListEle).html(entityList);

            // node
            var nodeName = data.nodeName ? '!'+data.nodeName : '';
            nodeName = nodeName && data.node_id ? '<a href="javascript:;" data-role="test_node_link" node_id="'+data.node_id+'">'+nodeName+'</a>' : nodeName;
            $(nodeNameEle).html(nodeName);

            $(testLogPanel).css('margin-bottom',0);
        },

        setInputDefault: function(data){
            var self = this;
            var user_input = this.userInputEle;
            var udisabled = data.hasOwnProperty('userInputDisabled') ? data.userInputDisabled : false;
            $(user_input).attr('placeholder', '메세지를 입력하세요.').prop('disabled', udisabled);
            $(self.chatLoadBox).remove();
            self.getAutoCompleteHide();
            setTimeout(function() {
                self.resetScrollTop(data);
            },100);
        },

        // input 엘리먼트 disabled 처리
        setInputDisabled: function(data){
            var user_input = this.userInputEle;
            var placeholder = data.placeholder?data.placeholder:'';
            $(user_input).attr("placeholder",placeholder).prop("disabled", true);
        },

        checkUrlScheme: function(link){
            var result;
            if(!link.match(/^[a-zA-Z]+:\/\//)) result = "http://"+ link;
            else result = link;
            return result;
        },

        // 링크 이동 함수
        moveLocation: function(data){
            var self = this;
            var link = data.msg;
            this.setInputDefault(data);
            var last_link = this.checkUrlScheme(link);

            setTimeout(function(){
                window.open(last_link);
            },20);
        },

        // 봇 컨텐츠 출력 : 엔터티 or 상품
        showBotContent : function(container,content,result){
            var self = this;
            var msgBox = this.chatLogContainer;
            var user_input = this.userInputEle
            var data = {};
            data['input_type'] = 'content';

            if (content != undefined && content != '') $(self.chatLoadBox).remove();
            if(container) $(container).html(content);
            else $(msgBox).append(content);
            $(user_input).removeAttr('placeholder', '').prop('disabled', false);
            this.resetScrollTop(data);
         },

        // Print Bot Msg
        showBotMsg : function(data){
            var self = this;
            var user_input = this.userInputEle;
            var msgBox = this.chatLogContainer;
            var msg;
            if(data.type=='node'){
                data['node'] = data.msg;
                this.getNodeRespond(data);
            }else if(data.type=='page-link'){
                this.moveLocation(data);

            }else if(data.type=='hform'){
                // hform일 경우 hform 관련 js에서 처리
                data['userInputDisabled'] = true;
                showBotHtmlFormMsg(data);
                return false;
            } else{
                var resItem = data.msg;

                if($.isArray(resItem)){
                    var arr = resItem[0];
                    if(arr['res_type'] == 'form') msg = arr['content'];
                }else{
                    if(typeof resItem == 'object') msg = resItem['response'];
                    else msg = data.msg;
                }
                $(msgBox).append(msg);
            }

            // 모니터링 on일 경우 웹소켓으로 응답 전송
            if(!self.cmod && self.options.use_chatting=='on' && self.admSockId){
                self.socketSend({"role":"chat_log_send", "roomToken":self.roomToken, "to":self.admSockId, "msg":msg});
            }

            // input 버튼 및 전송 버튼 초기화
            this.setInputDefault(data);

            // getTypeInput 값 초기화
            this.getTypeInput = null;
        },

        // Print User Msg
        showUserMsg : function(data){
            var self = this;
            var inputer = self.options.isAdm ? 'bot' : 'user';
            var msgBox = self.chatLogContainer;
            var user_input = self.userInputEle;

            if(inputer == 'user') {
                var user_msg = self.getUserMsgTpl(data);
                $(msgBox).append(user_msg);
                if(!self.humanMod) {
                    var load_msg = self.getLoadMsgTpl();
                    $(msgBox).append(load_msg);
                    $(user_input).attr('placeholder', '답변 준비중... ');
                }
                self.resetScrollTop(data);

                // 모니터링 on일 경우 웹소켓으로 발화문 전송
                if(self.options.use_chatting=='on' && self.socket){
                    var _emit_data = {"role":"chat_log_send", "roomToken":self.roomToken, "to":self.admSockId, "msg":user_msg};

                    if(self.humanMod) {
                        var _data = {"humanMod": self.humanMod,"msg": data.msg,"type": data.msg_type,"role": inputer};
                        self.saveChatMsg(_data);

                        _emit_data['clean_input'] = data.msg;
                    }
                    self.socketSend(_emit_data);
                }

                // 채팅상담 on일 경우 채팅서버로 발화문 전송
                if(self.options.use_cschat=='on' && self.csMod){
                    csChatting.getCSChatProcessInput(data.msg);
                }

            } else {
                if(self.humanMod) {
                    data['role'] = inputer;
                    var user_msg = self.getMsgTpl(data);
                    $(msgBox).append(user_msg);
                    self.resetScrollTop(data);

                    var _to = $('iframe[data-token="'+self.roomToken+'"]', parent.document).attr('data-sockid');
                    parent.managerAction({role: "bot_input",msg: user_msg, room: self.roomToken, to:_to});

                    var _data = {"humanMod": self.humanMod,"msg": data.msg,"type": data.msg_type,"role": inputer};
                    self.saveChatMsg(_data);
                }
            }
        },

        // (버튼)메뉴 응답 출력 함수
        getMenuRespond: function(e){
            var self = this;
            var target = e.currentTarget;
            var data = $(target).data();
            data['req_type'] = 'hMenu';
            data['botid'] = self.botId;
            data['cmod'] = self.cmod;
            data['roomToken'] = self.roomToken;
            data['botActive'] = self.botActive;
            data['bot_type'] = self.bot_type;
            data['channel'] = self.channel;
            data['cgroup'] = self.cgroup;

            if(self.cmod =='monitering') return false;

            //aramjo
            var clickattr = $(target).attr('onclick');

            if (typeof clickattr !== typeof undefined && clickattr !== false) {
                data['vendor'] = self.vendor;
                data['mbruid'] = self.mbruid ? self.mbruid : localStorage.getItem("mbruid");
                data['printType'] = 'B';
                data['msg'] = data['title']+'에 대해서 문의드립니다.';

                $.post(rooturl+'/?r='+raccount+'&m='+self.module+'&a=get_chatLog',{
                    data: data
                }, function(){
                });
                self.setInputDefault(data);
                return false;
            }

            var msg_data = self.setMsgData(data['title']+'에 대해서 문의드립니다.','hMenu');
            self.showUserMsg(msg_data); //

            $.post(rooturl+'/?r='+raccount+'&m='+self.module+'&a=get_reply',{
               pData: data,
            },function(response) {
                var result = $.parseJSON(response);
                var content = result.content;
                $.each(content,function(i,res){
                    var resType = res.res_type;
                    var content = res.content;

                    if(resType=='text'||resType=='img'||resType=='tel'||resType=='form'||resType=='hform'){
                        var _data = {"type":resType,"msg":content};
                        self.showBotMsg(_data);
                    }else if(resType=='link'){
                        var _data = {"type":resType,"msg":content};
                        self.moveLocation(_data);
                    }else if(resType=='node'){
                        data['node'] = content;
                        self.getNodeRespond(data);
                    }
                });
                self.init_afterAjax();
            });
        },

        // 첫번째 메세지 출력 함수
        showFstMessge: function(e){
            var data = {};
            data['vendor'] = this.vendor;
            data['bot'] = this.botUid;
            data['botid'] = this.botId;
            data['dialog'] = this.dialog;
            data['node'] = 1; // 첫번째 메세지 출력시 node = 1
            this.getNodeRespond(data);
        },

        // 응답 형식이 대화상자 이동(점프) 인 경우
        getNodeRespond: function(data){
            var self = this;
            var _data = {};
            _data['req_type'] = 'hMenu';
            _data['botid'] = self.botId;
            _data['vendor'] = data.vendor ? data.vendor : self.vendor;
            _data['bot'] = data.bot ? data.bot : self.botUid;
            _data['dialog'] = data.dialog ? data.dialog : self.dialog;
            _data['cmod'] = self.cmod;
            _data['jump'] = data.node;
            _data['roomToken'] = self.roomToken;
            _data['botActive'] = self.botActive;
            _data['bot_type'] = self.bot_type;
            _data['channel'] = self.channel;
            _data['cgroup'] = self.cgroup;

            $.post(rooturl+'/?r='+raccount+'&m='+self.module+'&a=get_reply',{
               pData: _data
            },function(response) {
                var result = $.parseJSON(response);
                var content = result.content;

                $.each(content,function(i,resItem){
                    if($.isArray(resItem[0])){
                        var itemType = resItem[0][0];
                        var itemCont = resItem[0][1];
                        if($.isArray(itemCont)){
                            if(itemCont[0].hasOwnProperty('res_type') && itemCont[0].hasOwnProperty('content')) {
                                var itemType = itemCont[0]['res_type'];
                                var itemCont = itemCont[0]['content'];
                            }
                        }
                    }else{
                        var itemType = resItem[0];
                        var itemCont = resItem[1];
                    }
                    var resData = {"type":itemType,"msg":itemCont};
                    self.showBotMsg(resData);
                });
                self.init_afterAjax();
            });
        },

        // 추천상품 버튼 숨김 및 전송버튼 노출
        showBtnSend : function(){
           //$(this.btnShowRecGoods).hide();
           $(this.btnSend).show();
        },

        // 추천상품 버튼 노출 및 전송버튼 숨김
        hideBtnSend : function(){
           //$(this.btnShowRecGoods).show();
           $(this.btnSend).hide();
        },

        // 채팅창 닫기
        chatExit: function(e) {
        	if (window.self != window.top) {
        		window.parent.postMessage({'bottalks_close':true}, '*');
        	} else if(typeof(opener) != "undefined") {
        		window.close();
        	}
        },

        // Bot msg load template
        getLoadMsgTpl : function(data){
            var sender_img = '<img src="'+this.options.bot_avatar_src+'">';
            var load_msg_tpl = '';
            load_msg_tpl +='<div class="cb-chatting-chatline" data-role="chat-load-box">';
                load_msg_tpl +='<div class="cb-chatting-received load">';
                    load_msg_tpl +='<div class="cb-layout">';
                        load_msg_tpl +='<div class="cb-left">';
                            load_msg_tpl +='<div class="cb-chatting-sender">'+sender_img+'</div>';
                        load_msg_tpl +='</div>';
                        load_msg_tpl +='<div class="cb-right">';
                            load_msg_tpl +='<div class="cb-chatting-info">';
                                load_msg_tpl +='<div class="cb-spinner"></div>';
                            load_msg_tpl +='</div>';
                        load_msg_tpl +='</div>';
                    load_msg_tpl +='</div>';
                load_msg_tpl +='</div>';
            load_msg_tpl +='</div>';
            return load_msg_tpl;
        },

        getChatIntro: function() {
            var self = this;
            var botId = this.botId;
            var container = this.$el;
            var msgBox = this.chatLogContainer; // 메세지 출력 박스
            var scrollContainer = this.chatScrollContainer; // scroll 대상
            var themeName = this.themeName; // 테마명
            var cmod = this.cmod;
            var roomToken = this.roomToken;

            $.post(rooturl+'/?r='+raccount+'&m='+this.module+'&a=get_Chatting_Intro',{
                botid : botId,
                cmod : cmod,
                roomToken : roomToken,
            },function(response) {
                var result = $.parseJSON(response);
                $(container).append(result.chat_intro);
                if (window.self == window.top) $('[data-role="chat-exit"]').hide();
                else window.parent.postMessage({'bottalks_loaded':true}, '*');
                self.undelegateEvents(); // msg box 엘리먼트들 이벤트 바인딩 off
                self.delegateEvents(); // msg box 엘리먼트들 이벤트 바인딩 on
            });
        },

        initChatStart: function() {
            if($('#intro_window').length > 0) $('#intro_window').remove();
            this.initChatBox(); // load 챗박스
        },

        getAutoComplete : function() {
            var self = this;
            var _data = {};
            _data['req_type'] = 'text';
            _data['vendor'] = self.vendor;
            _data['bot'] = self.botUid;
            _data['dialog'] = self.dialog;
            _data['cmod'] = self.cmod;
            _data['roomToken'] = self.roomToken;
            _data['botActive'] = self.botActive;
            _data['botid'] = self.botId;
            _data['bot_type'] = self.bot_type;
            _data['channel'] = self.channel;
            _data['cgroup'] = self.cgroup;
            _data['mode'] = 'faq';

            $(self.userInputEle).autoComplete({
                wrapper:'#chatBox-container',
                css: {bottom:'49px', left:'0px', width:'100%'},
                minChars:2,
                delay: 50,
                cache: false,
                source: function(term, response) {
                    _data['faq_mod'] = 'search';
                    _data['msg'] = term;
                    $.post(rooturl+'/?r='+raccount+'&m='+self.module+'&a=get_reply',{
                        pData : _data
                    },function(res) {
                        var result = $.parseJSON(res);
                        response(result.search);
                    });
                },
                renderItem: function(item, search) {
                    return "<div class='autocomplete-suggestion' data-uid='"+item.uid+"' data-val='"+search+"'>"+item.question+"</div>";
                },
                onSelect: function(e, term, item) {
                    $(self.$el).removeClass("cb-input");
                    if(item) {
                        _data['faq_mod'] = 'get_answer';
                        _data['faq_uid'] = item.data('uid');
                        _data['msg'] = item.text();

                        var msg_user_data = self.setMsgData(_data['msg'], 'text');
                        self.showUserMsg(msg_user_data);

                        $.post(rooturl+'/?r='+raccount+'&m='+self.module+'&a=get_reply',{
                            pData : _data
                        },function(res) {
                            var result = $.parseJSON(res);
                            var reply = result.reply;
                            var res_type = reply.res_type;
                            var resData = {"type":reply.res_type,"msg":result.content[0][1]};
                            self.showBotMsg(resData);

                            self.init_afterAjax();
                        });
                    }
                }
            });
        },

        getAutoCompleteHide: function() {
            if($('.autocomplete-suggestions').length > 0) {
                $('.autocomplete-suggestions').hide();
                $('.autocomplete-mask').hide();
            }
        },

        // 음성 processInput 함수
        voiceProcessInput: function(data){
            var self = this;
            if((typeof data === 'object' && data.init === true) || data == "init") {
                var bInit = true;
                var msg = "hi";
                var msg_type = "say_hello";
            } else {
                var bInit = false;
                var msg = data;
                var msg_type = "text";
            }

            var _data = {};
            _data['botid'] = this.botId;
            _data['msg'] = msg;
            _data['msg_type'] = msg_type;
            _data['_lang'] = sessionStorage.getItem('now_lang');
            _data['cmod'] = this.cmod;
            _data['mbruid'] = this.mbruid?this.mbruid:localStorage.getItem("mbruid");
            _data['vendor'] = this.vendor;
            _data['bot'] = this.botUid;
            _data['dialog'] = this.dialog;
            _data['roomToken'] = this.roomToken;
            _data['botActive'] = this.botActive;
            _data['bot_type'] = this.bot_type;
            _data['channel'] = this.channel;
            _data['cgroup'] = this.cgroup;
            _data['api'] = true;

            $.post(rooturl+'/?r='+raccount+'&m='+this.module+'&a=get_voiceResponse',{
                 pData : _data
            },function(response) {
                var result = $.parseJSON(response);
                var content = result.content;
                var resContent = '', resType;
                if($.isArray(content)){
                    $.each(content,function(i,resItem){
                        if(resItem.type == 'text' || resItem.type == 'tel' || resItem.type == 'form') {
                            resContent += resItem.content;
                        } else if(resItem.type == 'link') {
                        } else if(resItem.type == 'hMenu') {
                        } else if(resItem.type == 'img') {
                        } else if(resItem.type == 'card') {
                        }
                    });
                } else {
                    resContent = result.content;
                }
                if(resContent) self.voiceOutputResponse(resContent, bInit);
            });
        },

        voiceOutputResponse: function(data, bInit) {
            self = this;
            var aResponse = data.split("[CODE]");
            var arData = {};
            arData['type'] = 'text';
            arData['msg'] = aResponse[0];
            arData['code'] = bInit ? 'on' : (aResponse[1] === undefined ? "" : aResponse[1]);
            if(self.unityInstance) {
                if(webSpeech) webSpeech.getPlayTTS(arData);
            } else {
                arData = typeof arData == 'object' ? JSON.stringify(arData) : arData;
                console.log(arData);
                if(window.Unity !== undefined) Unity.call(arData);
            }
        },

        // 세션스토리지
        setStorage: function(key, value) {
            sessionStorage.setItem(this.botId + "__" + key, value);
        },
        getStorage: function(key) {
            return sessionStorage.getItem(this.botId + "__" + key);
        },

        // 로그인폼
        getLoginForm : function(){
            var msg_data = this.setMsgData('login','login_form');
            this.getBotMsg(msg_data);
        },

    };

    $.fn.PS_chatbot = function(options) {
        return this.each(function() {
            var bottalks = Object.create(Bottalks);
            $.data(this, 'bottalks', bottalks);
            bottalks.init(options || {}, this);
            this.getBottalksObj = function() {
                return bottalks;
            };
        });
    };
}));