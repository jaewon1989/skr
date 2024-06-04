<?
/*
$shost = $_SERVER['HTTP_HOST'];
if(strpos($shost, $g['chatbot_host']) !== false) {
	$aHost = explode(".", $shost);
	$socketioUrl = "https://".$aHost[0].".".$g['chatbot_host'].":3000";
} else {
	$socketioUrl = $g['url_root'].":3000";
}
*/
//$socketioUrl = $g['url_root'].":3000";
$socketioUrl = $g['web_socket_host'].":".$g['web_socket_port'];;
?>

<script type="text/javascript" src="<?php echo $g['url_module'].'/lib/js/jquery.caret.min.js'?>"></script>
<script type="text/javascript" src="<?php echo $g['url_module'].'/lib/js/jquery.tag-editor.js'?>"></script>

<?php getImport('socket.io-client','socket.io.min',false,'js') ?>
<script>
    var vendor = '<?=$vendor?>';
    var bot = '<?=$bot?>';
    var module ='<?=$m?>';
    var use_chatting = '<?=$use_chatting?>';
    var botId = '<?=$getAdBot['botId']?>';
    var chatUrl = '<?=$getAdBot['bot_url']?>';
    var bottype = '<?=$getAdBot['bottype']?>';
    var botname = '<?=$getAdBot['name']?>';
    var socketioUrl = '<?=$socketioUrl?>';
    var chatbotWrapper = $('[data-role="moniteringChatbot-wrapper"]');
    var chatbotWrapperGuide = $('[data-role="chatbotWrapper-guide"]');
    var chatHeaderContainer = '[data-role="chatHeaderContainer"]';
    var chatBodyContainer = '[data-role="chatBodyContainer"]';
    var botListWrapperGuide = $('[data-role="botListWrapper-guide"]');
    var botListWrapper = $('[data-role="botList-wrapper"]');
    var color = "success";
    var socket = null;
    var mysockid = null;

    // Create SocketIO instance, connect
    if(use_chatting == 'on' && socket == null) {
        socket = io.connect(socketioUrl+'/chatbot_'+botId,{secure:true});

        socket.on('connect',function() {
            var utype = bottype == 'chat' ? 'manager' : 'callmanager';
            socket.emit("send_data", {"role":"new_client", "type":utype, "vendor":vendor, "botid":botId, "bottype":bottype});
            console.log('Client has connected to the server!');
        });

        socket.on('connectInfo', function(data) {
            mysockid = data.sockid;

            // 접속 사용자 리스트 요청
            socket.emit("send_data", {"role":"user_list", "vendor":vendor, "botid":botId, "bottype":bottype});
        });

        socket.on('disconnect', function(data) {
            console.log('disconnected..');
        });

        socket.on("manager_msg", function(data) {
            console.log(data);
            switch(data.role) {
                // 접속 사용자 표시
                case "user_list" :
                    var tpl = '';
                    $.each(data.list, function(i, user) {
                        user['type'] = 'chatList';
                        if($(botListWrapper).find('[data-token="'+user.roomToken+'"]').length == 0) {
                            tpl += getTpl(user);
                        }
                    });

                    if(tpl) {
                        $(botListWrapperGuide).hide();
                        $(botListWrapper).html(tpl);
                    }
                    break;

                // 신규 접속자 표시
                case "new_user" :
                    var user = data.info;
                    if(user.vendor == vendor && user.botid == botId) {
                        // 콜봇일 때 같은 폰번호 존재할 경우 기존 폰번호 관련 정보 삭제
                        if(user.hasOwnProperty('phone') && user.phone) {
                            if($(botListWrapper).find('[data-phone="'+user.phone+'"]').length > 0) {
                                var _udata = $(botListWrapper).find('[data-phone="'+user.phone+'"]').data();
                                if($(chatBodyContainer).find('iframe[data-token="'+_udata.token+'"]').length > 0) {
                                    if(_udata.token in aHumanMod) {
                                        managerAction({"role": "changeHumanMod", "room": _udata.token, "to":_udata.sockid, "msg": "off"});
                                    }
                                    closeChatBot({"roomToken": _udata.token, "sockid": _udata.sockid});
                                }

                                $(botListWrapper).find('[data-phone="'+user.phone+'"]').remove();
                            }
                        }
                        user['type'] = 'chatList';
                        if($(botListWrapper).find('[data-token="'+user.roomToken+'"]').length == 0) {
                            var tpl = getTpl(user);
                            if(tpl) {
                                $(botListWrapperGuide).hide();
                                $(botListWrapper).append(tpl);
                            }
                        }
                    }
                    break;

                // unknown, sameq 카운트
                case "add_cnt" :
                    var countEle = $(botListWrapper).find('[data-token="'+data.roomToken+'"]').find('.roomUN');
                    countEle.find('.'+data.type).text(data.count);

                    data['unknownCnt'] = countEle.find('.unknown').text();
                    data['sameqCnt'] = countEle.find('.sameq').text();
                    var color = getStateColor(data);
                    var stateEle = $(botListWrapper).find('[data-token="'+data.roomToken+'"]').find('.roomState');
                    if(stateEle.attr('color') != color) {
                        stateEle.find('i').removeClass('state-'+stateEle.attr('color')).addClass('state-'+color);
                        stateEle.attr('color', color);
                    }

                    break;

                // 채팅창 오픈
                case "bot_open" :
                    if($(botListWrapper).find('[data-sockid="'+data.sockid+'"]').length > 0) {
                        if(data.action == 'open') {
                            $(botListWrapper).find('[data-sockid="'+data.sockid+'"]').addClass('active');
                        } else {
                            $(botListWrapper).find('[data-sockid="'+data.sockid+'"]').removeClass('active');
                        }
                    }
                    break;

                // 접속 해제 (리스트 삭제 및 열린 창 닫기)
                case "user_disconnected" :
                    if(data.sockid != undefined && data.sockid != '') {
                        $('[data-sockid="'+data.sockid+'"]').remove();
                    } else {
                        $('[data-token="'+data.roomToken+'"]').remove();
                    }
                    if($(botListWrapper).find('a').length == 0) {
                        $(botListWrapperGuide).show();
                    }
                    break;

                // 사용자 대화 로그 표시
                case "chat_log_send" :
                    var chatFrame = $(chatBodyContainer).find('iframe[data-sockid="'+data.from+'"]');
                    if($(chatFrame).length > 0) {
                        var chatObj = chatFrame[0].contentWindow.chatbot;
                        var chatContainer = $(chatFrame).contents().find(chatObj.chatLogContainer);
                        $(chatContainer).append(data.msg).promise().done(function() {
                            $(chatContainer).find('.kwd-item, .card-img-wrapper, .card-title, .card-link').css('pointer-events', 'none');
                            chatObj.resetScrollTop({'input_type':''});
                        });

                        if(data.hasOwnProperty('clean_input') && data.clean_input) {
                            data.role = 'response_hint';
                            managerAction(data);
                        }
                    }
                    break;

                // 콜봇 대화 로그 표시
                case "call_log_send" :
                    if(data.hasOwnProperty('log') && data.log.length > 0) {
                        var countEle = $(botListWrapper).find('[data-token="'+data.roomToken+'"]').find('.roomUN');

                        data.log.forEach(function(_data, index) {
                            if(_data.hasOwnProperty('sameq') || _data.hasOwnProperty('unknown')) {
                                var _sameq = _data['sameq'] ? 1 : 0;
                                var _unknown = _data['unknown'] ? 1 : 0;
                                if(_sameq || _unknown) {
                                    countEle.find('.sameq').text(parseInt(countEle.find('.sameq').text())+_sameq);
                                    countEle.find('.unknown').text(parseInt(countEle.find('.unknown').text())+_unknown);
                                } else {
                                    if(parseInt(countEle.find('.sameq').text()) > 0) {
                                        countEle.find('.sameq').text(parseInt(countEle.find('.sameq').text())-1);
                                    }
                                    if(parseInt(countEle.find('.unknown').text()) > 0) {
                                        countEle.find('.unknown').text(parseInt(countEle.find('.unknown').text())-1);
                                    }
                                }

                                data['unknownCnt'] = countEle.find('.unknown').text();
                                data['sameqCnt'] = countEle.find('.sameq').text();

                                var color = getStateColor(data);
                                var stateEle = $(botListWrapper).find('[data-token="'+data.roomToken+'"]').find('.roomState');
                                if(stateEle.attr('color') != color) {
                                    stateEle.find('i').removeClass('state-'+stateEle.attr('color')).addClass('state-'+color);
                                    stateEle.attr('color', color);
                                }
                            }

                            data.type = _data.sender == 'bot' ? 'botMsg' : 'userMsg';
                            data.msg = _data.msg.replace(new RegExp("\\\\", "g"), "");
                            data.content = getTpl(data);
                            getCallbotDisplay(data);
                        });
                    }
                    break;
            }
        });
    }

    var getStateColor = function(data){
        var color;
        var total = parseInt(data.unknownCnt)+parseInt(data.sameqCnt);
        if(total == 0) color = 'success';
        else if(total >= 1 && total < 3) color = 'warning';
        else if(total>=3) color = 'danger';

        // 채팅창 오픈안되어 있을 경우만 Notice
        /*
        if(total > 0 && !$("[data-role=botList-wrapper] [data-token="+data.roomToken+"]").hasClass("active")) {
            data['total'] = total;
            data['color'] = color;
            getStateNotice(data);
        }
        */
        return color;
    };

    var getStateNotice = function(data) {
        if($("#notifications").length == 0) {
            $("body").append('<div id="notifications" aria-live="assertive" aria-atomic="true" aria-relevant="additions"></div>');
        }

        var ttype = data.hasOwnProperty('phone') ? "Phone" : "roomToken";
        var ttitle = ttype == "Phone" ? data.phone : data.roomToken;
        var message = '<div class="noti_wrap"><div class="state '+data.color+'"></div><div class="info"><div class="sitem">'+ttype+'</div>';
        message +='<div class="item">'+ttitle+'</div><div class="ur">U/R : '+parseInt(data.unknownCnt)+'/'+parseInt(data.sameqCnt)+'</div></div></div>';

        $("#notifications").notifyr({
            message: message,
            location: 'bottom-right',
            closeButtonHtml:'<button class="notification-close">확인</button>',
            roomToken: data.roomToken,
            sticky:true
        });
    };

    // 챗봇 리스트 마크업
    var getTpl = function(data){
        var roomToken = data.token?data.token:data.roomToken;
        var phone = data.hasOwnProperty('phone') ? data.phone : '';
        var color = getStateColor(data);
        var tpl;
        var topOffset = 100;
        var height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
        height = height - topOffset;
        var iframeH = height-220;

        if(data.type == 'chatList') {
            tpl = '<a href="#" class="list-group-item" data-role="botList-item" data-sockid="'+data.sockid+'" data-token="'+roomToken+'" data-ip="'+data.ip+'" data-phone="'+phone+'">';
            tpl +=' <div class="roomToken">'+(phone ? phone : roomToken)+'</div>';
            tpl +=' <div class="roomUN"><span class="unknown">'+data.unknownCnt+'</span>/<span class="sameq">'+data.sameqCnt+'</span></div>';
            tpl +=' <div class="roomState" color="'+color+'"><i class="fa fa-circle state-'+color+'"></i></div>';
            tpl +='</a>';
        }else if(data.type =='chatHeader'){
            tpl = '<li data-token="'+roomToken+'" data-sockid="'+data.sockid+'" class="active" data-chatHeader="header-'+roomToken+'">';
            tpl +=' <a href="#'+roomToken+'" data-toggle="tab">'+(phone ? phone : roomToken)+'</a>';
            tpl +=' <i class="fa fa-times" arial-hidden="true" data-role="close-chatbot" data-sockid="'+data.sockid+'" data-token="'+roomToken+'" data-human="off"></i>';
            tpl +='</li>';
        }else if(data.type =='chatBody'){
            var chatBot = '<iframe id="chatbotFrame-'+roomToken+'" src="'+chatUrl+'?cmod=monitering&roomToken='+roomToken+'" style="height:'+iframeH+'px;" data-sockid="'+data.sockid+'" data-token="'+roomToken+'">';

            tpl ='<div class="tab-pane active" id="'+roomToken+'" data-chatBody="body-'+roomToken+'" data-sockid="'+data.sockid+'">';
            tpl +='<div class="chatbotFrame" data-role="chatbotFrame-'+roomToken+'">'+chatBot+'</div>';
            tpl +='</div>';
        }else if(data.type =='userMsg'){
            tpl = '<div class="cb-chatting-chatline">';
            tpl +=' <div class="cb-chatting-sent">';
            tpl +='     <div class="cb-chatting-info">';
            tpl +='         <span class="cb-chatting-date">'+data.date+'</span>';
            tpl +='     </div>';
            tpl +='     <div class="cb-chatting-balloon"><p>'+data.msg+'</p></div>';
            tpl +=' </div>';
            tpl +='</div>';
        }else if(data.type =='botMsg'){
            tpl = '<div class="cb-chatting-chatline">';
            tpl +=' <div class="cb-chatting-received">';
            tpl +='     <div class="cb-layout">';
            tpl +='         <div class="cb-left">';
            tpl +='             <div class="cb-chatting-sender" style="background-image:url('+data.bot_avatar+');"></div>';
            tpl +='         </div>';
            tpl +='         <div class="cb-right">';
            tpl +='             <div class="cb-chatting-info">';
            tpl +='                 <span class="cb-chatting-name">'+botname+'</span>';
            tpl +='                 <span class="cb-chatting-date-side">'+data.date+'</span>';
            tpl +='             </div>';
            tpl +='             <div class="cb-chatting-balloon"><p>'+data.msg+'</p></div>';
            tpl +='         </div>';
            tpl +='     </div>';
            tpl +=' </div>';
            tpl +='</div>';
        }

        return tpl;
    };

    // 멀티 채팅창 active 초기화
    var initTabActive = function(type){
        var tabHeader = $('[data-chatheader]');
        var tabBody = $('[data-chatbody]');

        if(type =='del'){
            $.each(tabHeader,function(){
                $(this).removeClass('active');
            });
            $.each(tabBody,function(){
                $(this).removeClass('active');
            });
        }else if(type == 'init'){
            $.each(tabHeader,function(i,ele){
                if(i==0) $(ele).addClass('active');
            });
            $.each(tabBody,function(i,ele){
                if(i==0) $(ele).addClass('active');
            });
        }
    };

    // 챗봇 출력
    var printChatBot = function(data){
        data['type'] = 'chatHeader';
        var chatHeader = getTpl(data);
        data['type'] = 'chatBody';
        var chatBody = getTpl(data);
        var roomToken =data.roomToken?data.roomToken:data.token

        // 멀티탭 active 지우기
        initTabActive('del');

        setTimeout(function(){
            $(chatHeaderContainer).append(chatHeader);
            $(chatBodyContainer).append(chatBody).promise().done(function() {
                if(bottype == 'call') {
                    $("#chatbotFrame-"+roomToken).on('load', function() {
                        getCallbotLog(data);
                    });
                }
            });
        },50);
    };

    // botList 클릭 > 채팅창 오픈
    $(botListWrapper).on('click','[data-role="botList-item"]',function(e){
        e.preventDefault();
        var target = e.currentTarget;
        var data = $(target).data();
        checkLogCountdown();

        if($(target).hasClass('active')) {
             alert('이미 오픈되어 있습니다.'); return false;
        }else{
            $(target).addClass('active');
            $(chatbotWrapperGuide).hide();
            $(chatbotWrapper).show();
            printChatBot(data);

            if($("#notifications").length > 0) {
                $(".notification").stop().animate({'opacity':0, 'right':'-5em'}, 250, 'easeInBack');
            }

            socket.emit("send_data", {"role":"bot_open", "action":"open", "vendor":vendor, "botid":botId, "sockid":data.sockid});
        }
    });

    // 채팅창 닫기 이벤트
    $(chatHeaderContainer).on('click','[data-role="close-chatbot"]',function(e){
        var target = e.currentTarget;
        var data = $(target).data();
        checkLogCountdown();

        if(data['human'] =='on'){
            alert('채팅 모드를 비활성화 해주세요');
            return false;
        }else{
            // 채팅창 닫기 함수 호출
            var data ={"roomToken": data['token'], "sockid": data['sockid']};
            closeChatBot(data);
        }
    });

    // 채팅창 닫기함수
    var closeChatBot = function(data){
        $('[data-chatheader="header-'+data.roomToken+'"]').remove();
        $('[data-chatbody="body-'+data.roomToken+'"]').remove();
        setTimeout(function(){
            initTabActive('init');
            $(botListWrapper).find('[data-token="'+data.roomToken+'"]').removeClass('active');
            if($(chatHeaderContainer).find('li').length == 0) {
                $(chatbotWrapperGuide).show();
                $(chatbotWrapper).hide();
            }
            socket.emit("send_data", {"role":"bot_open", "action":"close", "vendor":vendor, "botid":botId, "sockid":data.sockid});
        },50);
    };

    // 채팅창 오픈 시 해당 챗봇의 대화로그 요청
    var getChatLog = function(roomToken) {
        if(bottype == 'chat') {
            var chatFrame = $(chatBodyContainer).find('iframe[data-token="'+roomToken+'"]');
            var sockid = $(chatFrame).attr('data-sockid');
            var roomToken = $(chatFrame).attr('data-token');
            setTimeout(function() {
                socket.emit("send_data", {"role":"chat_log", "roomToken":roomToken, "to":sockid});
            },200);
        }
    };

    var managerAction = function(data) {
        checkLogCountdown();
        if(data.role =='changeHumanMod'){ // 챗봇 & 채팅 모드 전환 값 세팅
            var closeBotBtn = $('[data-chatheader="header-'+data.room+'"]').find('[data-role="close-chatbot"]');
            if(data.msg =='on') $(closeBotBtn).data("human","on");
            else if(data.msg == 'off') $(closeBotBtn).data("human","off");

            // humanMod 상태 등록
            if(bottype == "call") {
                $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=do_VendorAction',{
                    linkType: "set_humanMod",
                    vendor: vendor, bot: bot, bottype: bottype, roomToken: data.room, humanMod: data.msg
                },function(response) {
                    var result = $.parseJSON(response);
                });
            }

            var _data = {"role":"changeHumanMod", "roomToken":data.room, "to":data.to, "msg":data.msg};
            socket.emit("send_data", _data);

        }else if(data.role =='bot_input'){
            var _data = {"role":"bot_input", "roomToken":data.room, "to":data.to, "msg":data.msg};
            socket.emit("send_data", _data);

        }else if(data.role =='response_hint'){
            var resHintGuide = '[data-role="resHintWrapper-guide"]'; //응답 힌트 가이드
            var resHintWrapper = '[data-role="resHint-wrapper"]';
            var _data ={};
            _data['vendor'] = vendor;
            _data['bot'] = bot;
            _data['clean_input'] = data.clean_input;

            // 응답 힌트 세팅
            $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=do_VendorAction',{
                linkType: "get-responseHint",
                data: _data,
            },function(response) {
                var result = $.parseJSON(response);
                var hintList = result.content;
                $(resHintGuide).hide();
                $(resHintWrapper).html(hintList);
            });
        }
    }

    // 알림 출력
    var showNotify = function(container,message){
        var container = container?container:'body';
        var notify_msg ='<div id="kiere-notify-msg">'+message+'</div>';
        var notify = $('<div/>', { id: 'kiere-notify', html: notify_msg}).addClass('active').appendTo(container)
        setTimeout(function(){
            $(notify).removeClass('active').remove();
        }, 1500);
    };

    // 클립보드 카피
    var copyToclipboard = function(text){
        var notify_container = $('[data-role="tagFA-UL"]');

        var msg = '문장이 복사되었습니다';
        showNotify(notify_container,msg);

        setTimeout(function(){
            // var clipboard = new Clipboard(target);
            // console.log(clipboard);
             var t = document.createElement("textarea");
             document.body.appendChild(t);
             t.value = text;
             t.select();
             document.execCommand('copy');
             document.body.removeChild(t);
        },300);
    }

    // 콜봇 대화 이력 가져오기
    var getCallbotLog = function(data) {
        var roomToken = data.roomToken ? data.roomToken : data.token;
        $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=do_VendorAction',{
            vendor: vendor, bot: bot, roomToken: roomToken, linkType: 'getCallbotLog'
        }, function(response) {
            var result = $.parseJSON(response);
            data['content'] = result.content;
            setTimeout(function() {
                getCallbotDisplay(data);
            },300);
        });
    }

    var getCallbotDisplay = function(data) {
        var roomToken = data.roomToken ? data.roomToken : data.token;
        if($("#chatbotFrame-"+roomToken).length > 0) {
            setTimeout(function() {
                $("#chatbotFrame-"+roomToken).contents().find("[data-role=chatting-logContainer]").append(data.content).promise().done(function() {
                    $("#chatbotFrame-"+roomToken)[0].contentWindow.chatbot.resetScrollTop({'input_type':''});
                });
            },100);
        }
    }

    $(document).ready(function(){
        // FA : 자주 사용하는 문장 리스트 init
        $(".cloud-tags").prettyTag({
            vendor: vendor,
            bot: bot,
            module: module,
            tagicon: false,
        });

        // tagFA 아이템 클릭 이벤트
        $('[data-role="tagFA-UL"]').on('click','[data-role="tagFA-item"]',function(e){
             var target = e.currentTarget;
             var tagName = $(target).attr("data-tag");
             copyToclipboard(tagName);
        });
     });
</script>