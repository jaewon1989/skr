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
!(function ($) {
  'use strict';

    var Chatbot = function (element, options) {
        this.options          = options
        this.$body            = $(document.body)
        this.$user_input      = $('[data-role="bot-talks"]')
        this.$module = 'chatbot'
    }


    Chatbot.VERSION  = '1.0.0'
    Chatbot.DEFAULTS = {}

    Chatbot.prototype.init = function(){
        focusInput();
    }

    var user_input = Chatbot.$user_input;

    var getUserMsgTpl = function(msg){
        var date = (new Date).yyyymmdd();
        var user_chat_tpl = '';
        user_chat_tpl +='<div class="cb-chatting-chatline">';
            user_chat_tpl +='<div class="cb-chatting-sent">';
                user_chat_tpl +='<div class="cb-chatting-info">';
                    user_chat_tpl +='<span class="cb-chatting-date">'+date+'</span>';
                user_chat_tpl +='</div>';
                user_chat_tpl +='<div class="cb-chatting-balloon">';
                   user_chat_tpl +='<p>'+msg+'</p>';
                user_chat_tpl +='</div>';
            user_chat_tpl +='</div>';
        user_chat_tpl +='</div>';    

        return user_chat_tpl;
    }

    // focus user input 
    var focusInput = function(){
        console.log($('input[name="user_input"]'));
           $(document).find('input[name="user_input"]').focus();   
    }
    
    // reset scrollTop 
    var scrollTomsg = function(msgBox){
        var height = msgBox[0].scrollHeight;
        msgBox.scrollTop(height);
        focusInput();
    }

    // 답변 출력 함수 msg_type : text, emoticon, recommend product
    var searchResponse = function(botid,cbox,msg,msg_type){
        $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=get_reply',{
            botid : botid,
            msg : msg,
            msg_type : msg_type
        },function(response) {
            var result = $.parseJSON(response);            
            $(cbox).append(result.content);
            scrollTomsg(cbox);
            if(msg_type=='text'){
               $(user_input).removeAttr('placeholder');
               $(user_input).removeAttr('disabled');  
            }
            
        }); 
    }
    
    // Print User Msg
    var printUserMsg = function(id,cbox,msg,msg_type){
        var user_msg = getUserMsgTpl(msg);
        if(msg_type=='text'){
            $(user_input).val('');
            $(user_input).attr('placeholder', '답변 준비중... ');
            $(user_input).attr('disabled', 'disabled');
            searchResponse(id,cbox,msg,msg_type);// 답변 출력 함수 호출
        }
        $(cbox).append(user_msg);
        scrollTomsg(cbox);        
    }

    $(document).on('keypress','[data-role="bot-talks"]',function(e) {
        if (e.which == 13) {
            var id = $(this).data('id');
            var cbox = $('[data-role="cb-box-'+id+'"]');
            var user_text = $(this).val();
            var msg = $.trim(user_text);
            var msg_type='text';
            if(msg){
                printUserMsg(id,cbox,msg,msg_type);  
            }else{
                show__CB__Notify('body','메세지를 입력해주세요. ');
                focusInput();
            } 
                                                                             
        }
    }); 

    var old = $.fn.chatbot

    $.fn.chatbot             = Plugin
    $.fn.chatbot.Constructor = Chatbot


    // chatbot NO CONFLICT
    // =================

    $.fn.chatbot.noConflict = function () {
        $.fn.chatbot = old
        return this
    }

    // chatbot PLUGIN DEFINITION
    // =======================

    function Plugin(option, _relatedTarget) {
        var options = $.extend({}, Chatbot.DEFAULTS,'', typeof option == 'object' && option)
        var chatbot = new Chatbot(this, options)
        chatbot.init(); // init. chatbot  
    } 

    Plugin.call();   

}(jQuery));
