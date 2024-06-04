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
                   user_msg_tpl +='<p>'+ show_msg +'</p>';
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
        var height = $(msgBox)[0].scrollHeight;
        console.log([msgBox,height]);
        $(msgBox).scrollTop(height);        
    }

    // DB 에서 찾아서 답변 (msg_type : text, recommend product)
    var getBotMsg = function(botid,msgBox,msg,msg_type){
        var user_input = $(document).find('[data-role="bot-talks"]');
        $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=get_reply',{
            botid : botid,
            message : msg,
            msg_type : msg_type
        },function(response) {
            var result = $.parseJSON(response); 
            var bot_msg = result.content;           
            $(user_input).removeAttr('placeholder');
            $(user_input).removeAttr('disabled'); 
            setTimeout(function(){
               $(msgBox).append(bot_msg);
               resetScrollTop(msgBox);
            },500);   
             
        }); 
    }
    
    // 챗봇 인사
    var sayHello =function(){
        var msgBox = $(document).find('.cb-chatting-scroll');
        var user_input = $(document).find('[data-role="bot-talks"]');
        var botid = $(this).data('id');
        $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=get_botSayHello',{
            botid : botid
        },function(response) {
            var result = $.parseJSON(response); 
            var bot_msg = result.content;           
            setTimeout(function(){
               $(msgBox).html(bot_msg);
            },300);   
             
        }); 
    }

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
     
    $(document).on('keypress','[data-role="bot-talks"]',function(e) {
        if (e.which == 13) {
            var id = $(this).data('id');
            var msgBox = $('[data-role="cb-box-'+id+'"]');
            var user_msg = $(this).val();
            var msg = $.trim(user_msg);
            var msg_type='text';
            if(msg){
                showUserMsg(id,msgBox,msg,msg_type); // Prompt show user msg in front side  
                getBotMsg(id,msgBox,msg,msg_type); // Get bot msg from server side
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
    $(document).on('click','[data-role="open-chatbox"]',function(){
        var bot_id = $(this).data('id');
        $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=do_UserAction',{
            act : 'count-bot',
            bot_id : bot_id
        },function(response){
            var result = $.parseJSON(response);
        }); 

    });

    // 추천상품 출력 이벤트
    $(document).on('click','[data-role="btn-showRecGoods"]',function(){
        var bot_id = $(this).data('id');
        var msgBox = $('[data-role="cb-box-'+bot_id+'"]');
        $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=do_UserAction',{
            act : 'show-recommendedGoods',
            bot_id : bot_id
        },function(response){
            var result = $.parseJSON(response);
            var bot_msg = result.content;
            setTimeout(function(){
                $(msgBox).append(bot_msg);
                resetScrollTop(msgBox);
                $(document).find('.cb-chatting-balloon-selection ul li:last').css("border-bottom","0");
            },100);
        }); 

    });

     // 처음 시작하면 인사 
    window.onload=function(){
        sayHello();
    }
        
    

});
