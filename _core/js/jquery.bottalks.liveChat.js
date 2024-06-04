
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if (typeof module === 'object' && module.exports) {
        // Node/CommonJS
        module.exports = function( root, jQuery ) {
            if ( jQuery === undefined ) {
                // require('jQuery') returns a factory that requires window to
                // build a jQuery instance, we normalize how we use modules
                // that require this pattern but the window provided is a noop
                // if it's defined (how jquery works)
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

    var BottalksLive = {

        // Instance variables
        // ==================

        $el: null,
        $el_id: null,
        prefix : 'bt',
        chatBox: null,
        chatBoxBody: null,      
        options: {},
        events: {
           'click [data-role="bt-showChatBox"]' : 'showChatBox',
           'click [data-role="bt-hideChatBox"]' : 'hideChatBox',
           'click [data-role="bt-maxChatBox"]' : 'maxChatBox',
           'click [data-role="bt-minChatBox"]' : 'minChatBox',
        },
         
        // Initialization
        init: function(options, el) {
            var self = this;
            this.$el = $(el);
            this.$el_id = '#'+this.$el.attr('id');
            this.options = $.extend(true, {}, this.getDefaultOptions(), options); 
            this.$el.addClass(this.options.containerClass); // 채팅박스 출력 container 에 class 추가 
            this.initChatBox(); // load 챗박스    
                    
        },

        // Default options
        getDefaultOptions: function(){
            return {
                leftPos : null,
                rightPos : '15px',
                bottomPos : '15px',
                topPos : null,
                chatBoxWidth : '505px',
                chatBoxHeight : '750px',
                defaultShow: true,
                barTitle: '현대HCN 상담챗봇',
                containerClass: 'bt-LiveChat' // 부모 container 에 세팅되는 class 명                 
            }            
        },
        
        getStyle : function(){
           var pfClass = this.options.containerClass;
           var rPos = this.options.rightPos;
           var bPos = this.options.bottomPos;
           var wBox = this.options.chatBoxWidth;
           var hBox = this.options.chatBoxHeight;

           var style =
            "<style>\n"
                + "#" + this.prefix +"-chatBox {position:absolute; width:"+wBox+";bottom:"+bPos+";right:"+rPos+";}\n"
                + "." + pfClass + " .chatBar {width:100%;height:20px;padding:15px 0;background:#029882;color:#ededed;}\n"
                + "." + pfClass + " .chatBar {font-size:20px;cursor:pointer;}\n"
                + "." + pfClass + " .chatBox {width:100%;height:100%}\n"
                + "." + pfClass + " .btnClose {position:absolute;width:15px;height:15px;padding:14px 0px;text-align:center;}\n"
                + "." + pfClass + " .btnClose {color:#fff;right:20px;top:0px;font-weight:200; }\n"
                + "." + pfClass + " .btnMin {position:absolute;width:15px;height:15px;padding:15px 0px;text-align:center;}\n"
                + "." + pfClass + " .btnMin {color:#fff;right:50px;top:0px; }\n"
            +"</style>\n"    

            return style;
        },

        getHtml : function(){
            var xImg = '<img src="http://58.229.208.139/layouts/chatbot-desktop/_images/btn_close.png">';
            var wBox = this.options.chatBoxWidth;
            var hBox = this.options.chatBoxHeight;
            var title = this.options.barTitle;
            var iframe = '<iframe src="http://58.229.208.139/R2q6nko0fRbD9J4QF?cmod=cs&call=external"  frameborder="0" scrolling="no" style="width:100%;height:100%;overflow:hidden;">';
            var html =
            "<div class='"+this.options.containerClass+"' style='position:relative;width:"+wBox+"; '>\n"
                 +"<div class='chatBar'>"
                       +"<div class='title' style='width:100%;text-align:center'  data-role='"+this.prefix+"-maxChatBox'>"+title+"</div>\n"
                       // +"<div class='btnMin' data-role='"+this.prefix+"-minChatBox'>-</div>\n"
                       +"<div class='btnClose' data-role='"+this.prefix+"-hideChatBox'>x</div>\n"
                  +"</div>\n"    
                  +"<div class='chatBox "+this.prefix+"-chatBoxBody' style='display:none;height:"+hBox+"'>"+iframe+"'</div>\n"
             +"</div>\n"  

            return html;
        },

        // chatting box 로딩 및 접속자 권한/관련 데이타 세팅  
        initChatBox : function(){
           var style = this.getStyle();
           var html = this.getHtml(); 
           var chatBox = $('<div/>', { id: this.prefix+'-chatBox', html: style + html});
           this.chatBox = '.'+this.prefix+'-chatBoxBody';
           this.chatBoxBody = '.'+this.prefix+'-chatBoxBody';
           this.minBtn = '[data-role='+this.prefix+'-hideChatBox]';
           $(chatBox).appendTo('body');
           this.AfterInitChatBox();
        },

         // chatbox min/max
        maxChatBox: function(e){
            console.log(e);
            var chatBoxBody = this.chatBoxBody;
            $(chatBoxBody).slideToggle('fast');//;
        },

        // chatbox min/max
        minChatBox: function(){
            var chatBoxBody = this.chatBoxBody;
         $(chatBoxBody).slideToggle('fast');

   
        },

         // chatbox min/max
        hideChatBox: function(){
            var chatBox = this.options.containerClass;
            $('#bt-chatBox').remove();
        },       
        
        // ChatBox 로딩 후 초기화 함수들 호출  
        AfterInitChatBox : function(){
           this.undelegateEvents(); // msg box 엘리먼트들 이벤트 바인딩 off 
           this.delegateEvents(); // msg box 엘리먼트들 이벤트 바인딩 on
           // var e = $.Event('shown.ps.chatbotLive', { relatedTarget: this.$el_id });
           // this.$el.trigger(e); 
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
        
    }  

    $.fn.PS_chatbotLive = function(options) {
        return this.each(function() {
            var chatbotLive = Object.create(BottalksLive);
            $.data(this, 'chatbotLive', chatbotLive);
            chatbotLive.init(options || {}, this);
        });
    };

	
}));