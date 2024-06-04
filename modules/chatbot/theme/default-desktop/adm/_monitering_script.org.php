<script type="text/javascript" src="<?php echo $g['url_module'].'/lib/js/jquery.caret.min.js'?>"></script>
<script type="text/javascript" src="<?php echo $g['url_module'].'/lib/js/jquery.tag-editor.js'?>"></script>

<?php getImport('socket.io-client','socket.io.min',false,'js') ?>
<script>
$(function(){
    var module ='<?php echo $m?>';
    var chatUrl = '<?php echo $getAdBot['bot_url']?>';
    var socketioUrl = "https://<?php echo $getAdBot['id'].".".$g['chatbot_host']?>:3000";
    var socket = io.connect(socketioUrl,{secure:true});
    var liveActBot = [];
    var openedBot = [];
    var chatHeaderContainer = '[data-role="chatHeaderContainer"]';
    var chatBodyContainer = '[data-role="chatBodyContainer"]';

    // 각 사용자 챗봇에서 sender 로 들어오는 메세지 처리
    socket.on("sender",function(data){
        var msg = data.msg;
        var roomToken = data.room;
        var role = data.role;

        if(role=='checkLive'){ // active bot roomToken  배열 세팅
            if(msg=='live' && roomToken!='undefined'){
                liveActBot.push(roomToken);
            }
        }else if(role =='changeHumanMod'){ // 챗봇 & 채팅 모드 전환 값 세팅
            var closeBotBtn = $('[data-chatheader="header-'+roomToken+'"]').find('[data-role="close-chatbot"]');
            if(msg =='on') $(closeBotBtn).attr("data-human","on");
            else if(msg == 'off') $(closeBotBtn).attr("data-human","off");

        }else if(role =='userInput'){
            var resHintGuide = '[data-role="resHintWrapper-guide"]'; //응답 힌트 가이드
            var resHintWrapper = '[data-role="resHint-wrapper"]';
            var _data ={};
            _data['vendor'] = '<?php echo $vendor?>';
            _data['bot'] = '<?php echo $bot?>';
            _data['clean_input'] = msg;

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

    });

    // socket.on("serverMsg",function(data){
    //     var type = data.type;
    //     var msg = data.msg;
    //     var roomToken = data.room;

    // });

    var checkLive = function(){
        //liveActBot = [];
        socket.emit("sender",{
            role: "checkLive",
            bot: "<?php echo $bot?>"
        });

        // 오픈된 챗봇으로부터 답을 얻을 때까지 시간을 준다.
        setTimeout(function(){
            listUpBot();
        },1000);
    };

    var getStateColor = function(data){
        var UN = data.UN;
        var RS = data.RS;
        var color;
        var total = parseInt(UN)+parseInt(RS);
        if(total>=3) color = 'danger';
        else if(1<=total && total<3) color = 'warning';
        else color = 'success';

        return color;
    };


    // 챗봇 리스트 마크업
    var getTpl = function(data){
        var roomToken = data.token?data.token:data.roomToken;
        var UN = data.UN;
        var RS = data.RS;
        var ip = data.ip;
        var color = getStateColor(data);
        var type = data.type;
        var tpl;
        var topOffset = 100;
        var height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
        height = height - topOffset;
        var iframe_height = height-220;

        var chatBot = '<iframe src="'+chatUrl+'?cmod=monitering&roomToken='+roomToken+'" style="height:'+iframe_height+'px;" id="botIframe-'+roomToken+'" data-token="'+roomToken+'">';
        if(type =='botList'){
            tpl = '<a href="#" class="list-group-item" data-role="botList-item" data-token="'+roomToken+'" data-ip="'+ip+'">';
                tpl+='<div class="roomToken">'+ip+'</div>';
                tpl+='<div class="roomUN">'+UN+'/'+RS+'</div>';
                tpl+='<div class="roomState"><i class="fa fa-circle state-'+color+'"></i></div>';
            tpl+='</a>';
        }else if(type =='chatHeader'){
            tpl = '<li data-id="'+roomToken+'" class="active" data-chatHeader="header-'+roomToken+'">';
               tpl +='<a href="#'+roomToken+'" data-toggle="tab">'+ip+'</a>';
               tpl +='<i class="fa fa-times" arial-hidden="true" data-role="close-chatbot" data-token="'+roomToken+'" data-human="off"></i>';
            tpl +='</li>';
        }else if(type =='chatBody'){
            tpl ='<div class="tab-pane active" id="'+roomToken+'" data-chatBody="body-'+roomToken+'">';
                tpl +='<div class="chatbotFrame" data-role="chatbotFrame-'+roomToken+'">'+chatBot+'</div>';
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
            $(chatBodyContainer).append(chatBody);
        },50);


        // 챗봇 오픈되었는지 여부값 업데이트
        var dd = {type: "add", roomToken: roomToken};
        updateOpenedBot(dd);
    };

    // 챗봇 오픈된지 여부 저장
    var updateOpenedBot = function(data){
        var type = data.type;
        var roomToken = data.roomToken;
        if(type=='add') openedBot.push(roomToken);
        else if(type =='del'){
            for( var i = 0; i < openedBot.length; i++){
               if ( openedBot[i] === roomToken) {
                    openedBot.splice(i, 1);
                }
            }
        }
    };

    // botList 클릭 > 채팅창 오픈
    $('[data-role="botList-wrapper"]').on('click','[data-role="botList-item"]',function(e){
        e.preventDefault();
        var chatbotWrapper = $('[data-role="moniteringChatbot-wrapper"]');
        var chatbotWrapperGuide = $('[data-role="chatbotWrapper-guide"]');
        var target = e.currentTarget;
        var roomToken = $(target).data('token');
        var data = $(target).data();

        if($.inArray(roomToken,openedBot) > -1){
             alert('이미 오픈되어 있습니다.');
             return false;
        }else{
            $(chatbotWrapperGuide).hide();
            $(chatbotWrapper).show();
            printChatBot(data);
        }

    });

    // 채팅창 닫기함수
    var closeChatBot = function(data){
        var roomToken = data.roomToken;
        $('[data-chatheader="header-'+roomToken+'"]').remove();
        $('[data-chatbody="body-'+roomToken+'"]').remove();
        setTimeout(function(){
             initTabActive('init');
        },50);

        setTimeout(function(){
            // 챗봇 오픈되었는지 여부값 업데이트
            var dd = {type: "del", roomToken: roomToken};
            updateOpenedBot(dd);
        },70);
    };

    // 채팅창 닫기 이벤트
    $('[data-role="chatHeaderContainer"]').on('click','[data-role="close-chatbot"]',function(e){
        var target = e.currentTarget;
        var roomToken = $(target).data('token');
        var humanMod = $(target).attr('data-human');
        var data ={roomToken: roomToken};
        if(humanMod =='on'){
            alert('채팅 모드를 비활성화 해주세요');
            return false;
        }else{
            // 채팅창 닫기 함수 호출
            closeChatBot(data);
        }
    });

    var printBotList = function(array){
        var botListWrapper = $('[data-role="botList-wrapper"]');
        var botListWrapperGuide = $('[data-role="botListWrapper-guide"]');
        var botList ='';
        $.each(array,function(){
            var dt = $(this);
            var data = dt[0];
            data['type'] = 'botList';
            botList += getTpl(data);
        });

        if(botList!=''){
             $(botListWrapperGuide).hide();
             $(botListWrapper).html(botList);
        }

        liveActBot =[];

    };

    // live 챗봇 리스트업
    var listUpBot = function(){
        $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=do_VendorAction',{
            vendor: '<?php echo $vendor?>',
            bot: '<?php echo $bot?>',
            linkType: "get-liveActBot",
            liveActBot: liveActBot,
        },function(response) {
            var result = $.parseJSON(response);
            var data = result.content;
            printBotList(data);

        });
    }

    var startCheckLive = function(){
        setInterval(checkLive,5000);
    }

    // 알림 출력
    var showNotify = function(container,message){
        var container = container?container:'body';
        var notify_msg ='<div id="kiere-notify-msg">'+message+'</div>';
        var notify = $('<div/>', { id: 'kiere-notify', html: notify_msg})
              .addClass('active')
              .appendTo(container)
        setTimeout(function(){
            $(notify).removeClass('active');
            $(notify).remove();
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

    // active 챗봇 input 창에 입력 > ACI : Active Chatbot Input : 보류
    var sendACI = function(text){
        var tabHeader = $('[data-chatheader]');

        $.each(tabHeader,function(){
            if($(this).hasClass('active')){

            }
        });

    }


    $(document).ready(function(){
        // 초기 실행
         checkLive();
         startCheckLive();

         // FA : 자주 사용하는 문장 리스트 init
        $(".cloud-tags").prettyTag({
            // /randomColor: false,
            vendor: '<?php echo $vendor?>',
            bot: '<?php echo $bot?>',
            module: '<?php echo $m?>',
            tagicon: false,
        });

        // tagFA 아이템 클릭 이벤트
        $('[data-role="tagFA-UL"]').on('click','[data-role="tagFA-item"]',function(e){
             var target = e.currentTarget;
             var tagName = $(target).attr("data-tag");
             copyToclipboard(tagName);
            // sendACI(tagName);
        });

     });


});


</script>
