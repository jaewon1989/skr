(function() {
    if (!window.bottalksSetting.botID || window.bottalksSetting.botID == undefined) return false;
    var d=new Date().getTime();
    var h="https://"+window.bottalksSetting.botID+".chatbot.bottalks.co.kr", i=h+"/plugin_bot/bottalks.plugin.css?"+d;
    var bMobile = function(){
        try{ document.createEvent("TouchEvent"); return true; }
        catch(e){ return false; }
    };
    var availableBrowser = function() {
        var ua = window.navigator.userAgent, msie = ua.indexOf('MSIE ');
        if (msie > 0) {
            return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10) >= 10;
        }
        return true;
    };
    function m() {
        var e = document.getElementById("bottalks-plugin");
        if(e.classList.contains("open")) {
            e.classList.remove("open");
            if(bMobile()) document.body.classList.remove("bottalks-mobile");
        }else{
            var iframe = document.getElementById('bottaks-script-iframe');
            if(!iframe.getAttribute("cload")) {
                iframe.setAttribute("cload", true);
                iframe.contentWindow.postMessage({'bottalks_load':true, 'botid':window.bottalksSetting.botID}, '*');
            }
            e.classList.add("open");
            if(bMobile()) document.body.classList.add("bottalks-mobile");
        }
    }
    if (availableBrowser()) {
        if (!document.getElementById('bottalks-style')) {
            var e = document.createElement("link");
            e.setAttribute("id", "bottalks-style"), e.setAttribute("type", "text/css"), e.setAttribute("rel", "stylesheet"), e.setAttribute("href", i);
            document.head.appendChild(e);
        }
        if (!document.getElementById('bottalks-plugin')) {
            var e = document.createElement("div");
            e.setAttribute("id", "bottalks-plugin"), e.setAttribute("class", "bottalks-wrap");
            document.body.appendChild(e);
        }
        var root = document.getElementById("bottalks-plugin");
        root.innerHTML = "<div id='bottalks-script' class='bottalks-chat'><iframe id='bottaks-script-iframe' title='chatbot' class='bottalks-iframe'></iframe><div id='chat_load'></div></div>";
        var e = document.createElement("div");
        e.setAttribute("id", "bottalks-button"), e.setAttribute("class", "bottalks-button-wrapper");
        root.appendChild(e);
        
        var button = document.getElementById("bottalks-button");
        var e = document.createElement("div");
        e.setAttribute("id", "bottalks-button-chat"), e.setAttribute("class", "bottalks-button-chat"), e.addEventListener("click", m);
        button.appendChild(e);
        
        var iframe = document.getElementById('bottaks-script-iframe');
        iframe.src = h+"/pluginauth/"+window.bottalksSetting.botID;
        
        window.addEventListener("message", function(ev) {
            if (ev.data.bottalks_use) {
                var btnChatbot = h+(ev.data.btnChatbot ? ev.data.btnChatbot : "/_core/skin/images/btn_chatbot.png");
                var bc = document.getElementById("bottalks-button-chat");
                bc.style.backgroundImage = "url('"+btnChatbot+"')";
                bc.style.width = ev.data.width+"px";
                bc.style.height = ev.data.height+"px";
                
                var bb = document.getElementById("bottalks-button");
                if(ev.data.mobile == 1) {
                    bb.style.right = ev.data.m_btn_right;
                    bb.style.bottom = ev.data.m_btn_bottom;
                } else {
                    bb.style.right = ev.data.pc_btn_right;
                    bb.style.bottom = ev.data.pc_btn_bottom;
                }
                bb.style.display = "block";
            }
            if (ev.data.bottalks_close == true) m();
            if (ev.data.bottalks_loaded == true) {
                var chat_load = document.getElementById("chat_load");
                var parent = chat_load.parentNode;
                parent.removeChild(chat_load);
            }
        });
    }
})();