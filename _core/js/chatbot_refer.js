// 날짜 표현 
Date.prototype.yyyymmdd = function(){
    var yyyy = this.getFullYear().toString();
    var mm = (this.getMonth() + 1).toString();
    var dd = this.getDate().toString();
    return yyyy +"-"+(mm[1] ? mm : '0'+mm[0])+"-"+(dd[1] ? dd : '0'+dd[0]);
};

// 메세지 출력 함수 
var show__CB__Notify=function(container,msg){

    $.notify({
        // options
        message: msg 
 
    },{
        // settings
        element: container,  
        type: 'blackCB',
        placement:{
            from: 'bottom',
            align: "center"
        },
        animate: {
            enter: 'animated fadeInUp',
            exit: 'animated fadeOutDown'
        },
        z_index: 1031,
        offset : 50,
        timer: 500,
        delay : 500
    });

}

$(function(){
    var module ='chatbot';
    var emoticon_path = rooturl+'/modules/'+module+'/lib/emoticon/';
    var event_name;
    // 터치 스크린 체크 
    var checkMobile = function(){
       try{ document.createEvent("TouchEvent"); event_name='tap'; }
       catch(e){ event_name='click'; }
    }

    // Get user msg template  
    var getUserMsgTpl = function(msg,msg_type){
        // tpl_type : user or bot
        var date = (new Date).yyyymmdd();
        var show_msg;

        // msg 타입에 따른 msg 출력형태 다름 
        if(msg_type=='text') show_msg = msg;
        else if(msg_type=='emoticon') show_msg = '<span class="emoticon_wrap"><img src="'+emoticon_path+'/emo_'+msg+'.png"/></span>';
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
    }

    // focus user input 
    var focusInput = function(){
        var user_input = $(document).find('[data-role="bot-talks"]');
        setTimeout(function(){
           $(user_input).focus(); 
        },10); 
    }
    
    // reset scrollTop 
    var resetScrollTop = function(msgBox){
        var height = $(msgBox).scrollHeight;
        if(height) $(msgBox).scrollTop(height);
        else $(msgBox).scrollTop(10000);        
    }
    
    // 챗봇 인사
    var sayHello =function(){
        var msgBox = $(document).find('.cb-chatting-scroll');
        var user_input = $(document).find('[data-role="bot-talks"]');
        var botid = $(this).data('id');
        getBotMsg(botid,msgBox,'hi','say_hello');
        getAdMsg(botid,msgBox); // 광고 내역 가져오기  
    }

    var getAdMsg = function(botid,msgBox){
        $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=get_adMsg',{
            botid : botid,
        },function(response) {
            var result = $.parseJSON(response); 
            var ad_msg = result.content;
            $.each(ad_msg,function(key,msg){
                getBotMsg(botid,msgBox,msg,'show_adMsg');   
            });           
        });
    }

    // DB 에서 찾아서 답변 (msg_type : text, recommend product)
    var getBotMsg = function(botid,msgBox,msg,msg_type){
        var user_input = $(document).find('[data-role="bot-talks"]');
        var _lang = sessionStorage.getItem('now_lang');
        $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=get_reply',{
            botid : botid,
            message : msg,
            msg_type : msg_type,
            _lang : _lang
        },function(response) {
            var result = $.parseJSON(response); 
            var bot_msg = result.content;           
            showBotMsg(bot_msg);             
        });
    }

    // 봇 메세지 출력 
    var showBotMsg = function(bot_msg){
        var user_input = $(document).find('[data-role="bot-talks"]');
        var bot_id = $(user_input).data('id');
        var msgBox = $('[data-role="cb-box-'+bot_id+'"]');
        $(user_input).removeAttr('placeholder');
        $(user_input).removeAttr('disabled'); 
        setTimeout(function(){
           $(msgBox).append(bot_msg);
           resetScrollTop(msgBox);
            hideBtnSend(); // 전송버튼 숨김
        },300);
    }

    var hideBtnSend = function(){
       $(document).find('[data-role="btn-showRecGoods"]').show();
       $(document).find('[data-role="btn-send"]').hide();
    }
    
    // 전송버튼 숨김 
    $(document).on('click','.cb-chatting-scroll',function(){
         setTimeout(function(){
             hideBtnSend();
         },200);
    });
        
    // Print User Msg
    var showUserMsg = function(id,msgBox,msg,msg_type){
        var user_input = $(document).find('[data-role="bot-talks"]');
        var user_msg = getUserMsgTpl(msg,msg_type);
        $(user_input).val('');
        $(user_input).attr('placeholder', '답변 준비중... ');
        $(user_input).attr('disabled', 'disabled');
        $(msgBox).append(user_msg);
        resetScrollTop(msgBox);        
    }
    
    // 추천상품 출력 
    var getRecGoods = function(){
        var user_input = $(document).find('[data-role="bot-talks"]');
        var bot_id = $(user_input).data('id');
        var msgBox = $('[data-role="cb-box-'+bot_id+'"]');
        $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=do_UserAction',{
            act : 'show-recommendedGoods',
            bot_id : bot_id
        },function(response){
            var result = $.parseJSON(response);
            var bot_msg = result.content;
            showBotMsg(bot_msg);
        }); 
    }
    // 입력창 포커스 이벤트 
    $(document).on('focusin','[data-role="bot-talks"]',function(e) {
        $(document).find('[data-role="btn-showRecGoods"]').hide();
        $(document).find('[data-role="btn-send"]').show();          
    });

    // 입력창 blur 이벤트 
    // $(document).on('tap','body',function(e) {
    //     $(document).find('[data-role="btn-showRecGoods"]').show();
    //     $(document).find('[data-role="btn-send"]').hide();          
    // });

    $(document).on('click','[data-role="btn-send"]',function(e) {
        var user_input = $(document).find('[data-role="bot-talks"]');
        var id = $(this).data('id');
        var msgBox = $('[data-role="cb-box-'+id+'"]');
        var user_msg = $(user_input).val();
        var msg = $.trim(user_msg);
        var msg_type='text';
        if(msg){
            showUserMsg(id,msgBox,msg,msg_type); // Prompt show user msg in front side  
            if(msg.match('추천')) getRecGoods();
            else getBotMsg(id,msgBox,msg,msg_type); // Get bot msg from server side
        }else{
            show__CB__Notify('body','메세지를 입력해주세요. ');
            focusInput();
        }                                                                             
        
    });

    $(document).on('keypress','[data-role="bot-talks"]',function(e) {
        if (e.which == 13) {
            var id = $(this).data('id');
            var msgBox = $('[data-role="cb-box-'+id+'"]');
            var user_msg = $(this).val();
            var msg = $.trim(user_msg);
            var msg_type='text';
            if(msg){
                showUserMsg(id,msgBox,msg,msg_type); // Prompt show user msg in front side  
                if(msg.match('추천')) getRecGoods();
                else getBotMsg(id,msgBox,msg,msg_type); // Get bot msg from server side
            }else{
                show__CB__Notify('body','메세지를 입력해주세요. ');
                focusInput();
            } 
                                                                             
        }
    });
    // insert emoticon 
    $(document).on('click','[data-role="emoticon"]',function(){
        var emotion = $(this).data('emotion');
        var id = $(this).data('id');
        var msgBox = $('[data-role="cb-box-'+id+'"]');
        var msg = emotion;
        var msg_type='emoticon';
        showUserMsg(id,msgBox,msg,msg_type);
        getBotMsg(id,msgBox,msg,msg_type); // Get bot msg from server side
        $(this).parent().parent().slideToggle('fast');
    });
    
    // show/hide emoticon box
    $(document).on('click','[data-role="btn-showEmoticon"]',function(){
          $('[data-role="emoticon-box"]').slideToggle('fast');
    });

    // 채팅 페이지 오픈시 이벤트 (카운트 적용)
    $(document).on('tap','[data-role="open-chatbox"]',function(){
        var bot_id = $(this).data('id');
        $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=do_UserAction',{
            act : 'count-bot',
            bot_id : bot_id
        },function(response){
            var result = $.parseJSON(response);
            setTimeout(function(){
              sayHello();     
            },300);            
        }); 

    });

    // 마이챗봇 페이지 채팅창 오픈시 이벤트 (인사말출력)
    $(document).on('tap','[data-open="chatbox"]',function(){
         setTimeout(function(){
              sayHello();     
         },300);            
    });


    // 추천상품 출력 이벤트
    $(document).on('click','[data-role="btn-showRecGoods"]',function(){
        getRecGoods();
    });

    // 처음 시작하면 인사 
    window.onload=function(){
        sayHello();
        checkMobile();
    }
});
