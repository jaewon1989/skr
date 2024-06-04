
// bottalksLC 
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

    var BottalksLC = {

        // Instance variables
        // ==================

        $el: null,
        $el_id: null,
        module: null,
        cmod: null,
        themePath: null, // 플러그인 초기화시 지정 
        themeName: null, // 플러그인 초기화시 지정
        q_StartTime: null, // 질문 시작 시간 
        showTimer: null,
        options: {},
        template: {}, // 각종 엘리먼트 html
        recData: [], // 테스트 결과 인식된 데이타  
        recDataKey: [], // 테스트 엔터티  
        events: {
            'click [data-role="btn-startLC"]' : 'processLC',
            'click [data-role="btn-downLoadFail"]' : 'downLoadFail',
            'click [data-role="btn-showRecData"]' : 'showRecData', // 신규 생성 데이터 보기
            'click [data-role="btn-closeRecData"]' : 'hideRecData', // 신규 생성 데이터 보기 

        },
        
        // Default options
        getDefaultOptions: function(){
            return {
                LCSquare : '[data-role="lc-square"]', // 학습과정 출력 장소 
                LCTarget : '[data-role="lc-target"]', // 학습대상 챗봇 id 
                LCSource : '[data-role="lc-source"]', // 학습예문 단어/문장 
                LCExNum : '[data-role="lc-exNum"]', // 단어/문장 당 예문 갯수 
                btnStartLC : '[data-role="btn-startLC"]', // 학습시작 버튼
                failList: '[data-role="LCbot-failList"]',
                btnFailDown: '[data-role="btn-failDown"]', // 실패문장 다운로드 
                dataSetPanel: '[data-role="dataSetPanel"]',
            }            
        },

        // check mobile device   
        isMobile: function(){
            try{ document.createEvent("TouchEvent"); return true; }
            catch(e){ return false; }
        },
        
        // 신규 생성 데이터 패널 열기 
        showRecData: function(){
            var data = {role: 'open'};
            this.controlDataPanel(data); 
        },

        hideRecData: function(){
            var data = {role: 'close'};
            this.controlDataPanel(data); 
        },

        // 신규 생성 데이터 보기  
        controlDataPanel: function(data){
            var self = this;
            var dataSetPanel = this.options.dataSetPanel;
            var dataTableContainer = '[data-role="dataTable-container"]';
            var dataTableWrapper = '[data-role="dataTable-wrapper"]';
            var resultContainer = '[data-role="dataTable-scroll"]';
            var role = data.role;
            var getTable = function(){
                var data = {
                    linkType: "getDataTable",
                    resultContainer: resultContainer, // table 출력 
                    dataTableWrapper: dataTableWrapper, // table 출력 
                    dataTableContainer: dataTableContainer // width 동적으로 할당 

                };
                self.linkServerData(data);
            };    
            if(role=='open'){
               getTable();
               setTimeout(function(){
                  $(dataSetPanel).css("margin-right",0); 
               },50);                
            }else if(role=='close'){
                $(dataSetPanel).css("margin-right","-100%");
            }
            
        },

        // 신규 생성 데이타 세팅   
        setRecData: function(){
            var recData = this.recData;
            var recDataKey = this.recDataKey;
            var active;
            var sideMenuUl = '[data-role="sideMenu-ul"]';
            var dataHeader = '[data-role="dataHeader"]';
            var dataBody = '[data-role="dataBody"]';
            var btn ='<li><div class="center p-20">';
                btn+='<div class="btn btn-primary btn-block btn-outline waves-effect waves-light" data-role="btn-showRecData">생성데이터 보기</div>';
                btn+='</div></li>';

            $(sideMenuUl).append(btn);

            $.each(recDataKey,function(i,title){
                var ip = i+1;
                if(i==0) active = 'active';
                else active ='';
                var tab ='<li class="'+active+'">';
                    tab+='<a href="#tab-'+ip+'" data-toggle="tab">';
                    tab+= title+'</a></li>';
                var tabPane = '<div class="tab-pane '+active+'" id="tab-'+ip+'" data-role="dataTabPane-'+ip+'"></div>';    
                
                // tab & tab-pane 추가  
                $(dataHeader).append(tab);
                $(dataBody).append(tabPane);

            });

            setTimeout(function(){
                $.each(recData,function(i,data){
                    var order = data.order;
                    var sentence = data.sentence;
                    var type = data.type;
                    var value = data.value;
                    var tabPane = '[data-role="dataTabPane-'+order+'"]';
                    var dataList = '<div><span class="data-sen">'+sentence+'</span><span class="data-val">'+type+value+'</span></div>';
                    $(tabPane).append(dataList);

                });
            },300);
            

            //console.log(recDataKey);
            console.log(recData);
        
        },

        // Initialization
        init: function(options, el) {
            var self = this;
            this.$el = $(el);
            this.$el_id = '#'+this.$el.attr('id');
            this.$el.css("position","relative");           
            if(this.isMobile==true) this.$el.addClass('mobile');
            
            // Init options
            this.options = $.extend(true, {}, this.getDefaultOptions(), options);
            this.$el.addClass(this.options.containerClass); // 채팅박스 출력 container 에 class 추가 
            this.module = this.options.moduleName; // module name 값 세팅
            this.cmod = this.options.cmod; // cs or vod 
            this.botId = this.options.botId; // bot id 값 세팅
            this.themePath = this.options.themePath;
            this.themeName = this.options.themeName?this.options.themeName:null;
            this.undelegateEvents(); // comment box 엘리먼트들 이벤트 바인딩 off 
            this.delegateEvents(); // comment box 엘리먼트들 이벤트 바인딩 on  

            this.showTimer = this.options.showTimer;
            
            // 템플릿 세팅       
            this.getTemplate();

            //console.log(this);
              
        },

        downLoadFail: function(){
            var failData = $('input[name="fail_utt[]"]').map(function(){return $(this).val()}).get();
            if(failData[0]==undefined){
                alert('실패 데이타가 존재하지 않습니다.');
                return false;
            }else{
                var form = $('#downFailForm');
                $(form).find('input[name="failData"]').val(failData);
                $(form).submit();
            }
        },

        // 학습시작 
        processLC : function(){
            var botId = this.options.LCTarget;
            var source  = this.options.LCSource;
            var exNum  = this.options.LCExNum;
            var dataHeader = '[data-role="dataHeader"]';
            var dataBody = '[data-role="dataBody"]';

            $(dataHeader).html('');
            $(dataBody).html('');
            this.recDataKey = [];
            this.recData = []; 

            if($(botId).val()==''){
                alert('학습대상 챗봇을 선택해주세요');
                return false;

            }else if($(source).val()==''){
                alert('학습과 관련된 단어/문장을 입력해주세요.');
                setTimeout(function(){
                    $(source).focus();              
                },10);
                return false;

            }else if($(exNum).val()==''){
                alert('예문 갯수를 입력해주세요.');
                setTimeout(function(){
                    $(exNum).focus();              
                },10);
                return false;

            }else{
                var data = {
                    linkType:"initLC",
                    botId:$(botId).val(),
                    source:$(source).val(),
                    exNum:$(exNum).val(),
                    cmod: this.cmod,
                };
                // 광장 비우고 
                $(this.options.LCSquare).html('');

                // 실피 리스트 비우고 
                $(this.options.failList).html('');
                
                this.linkServerData(data);
            }
        },
        
        // 챗봇 학습전 기본값 세팅   
        initLC: function(data){
            var self = this;
            var keywordArray = data.keywordArray;
            var recDataKey = this.recDataKey;
            $.each(keywordArray,function(i,keyword){
                data['order'] = parseInt(i)+1;
                data['keyword'] = keyword;
                recDataKey.push(keyword);
                self.showBot(data);
            });
            
            // 테스트 시작 
            setTimeout(function(){
                $.each(keywordArray,function(i,keyword){
                    data['order'] = parseInt(i)+1;
                    data['keyword'] = keyword;
                    self.testBot(data);
                });
            },100)

            // 테스트 결과 > 신규 데이타 세팅 
            setTimeout(function(){
                self.setRecData();  
            },300); 
            
            
        },

        showBot: function(data){
            var self = this;
            var LCSquare = this.options.LCSquare;
            var template = this.template;
            var order = data.order;
            var bot_title = data.keyword;
            var botBox = template['LCbot_Box'];
            
            if(data.keyword){
                botBox = botBox.replace(/\{\$order}/gi,order);
                botBox = botBox.replace(/\{\$bot_title}/gi,bot_title);
                $(botBox).appendTo(LCSquare);
            }
            
        },
        
        // 챗봇 공격(테스트)  
        testBot: function(data){
            var _data = {
                linkType: "testBot",
                vendor: data.vendor,
                bot: data.bot,
                botId: data.botId,
                dialog: data.dialog,
                keyword: data.keyword,
                exNum: data.exNum,
                intentTrainData: JSON.stringify(data.intentTrainData),
                entityTrainData: JSON.stringify(data.entityTrainData),
                cmod: data.cmod,
                order: data.order
            }

            this.linkServerData(_data);

        },

        // 테스트 결과 출력 
        printResult: function(data){
            var self = this;
            var template = this.template;
            var recData = this.recData;
            var chatRows = data.chat;
            var order = data.order;
            var botBox = '[data-role="LCbot-content-'+order+'"]'; 
            var getEntityList = function(data){
                var ul_start = '<ul class="entity-list">';
                var ul_end = '</ul>';
                var no_list = '<li>엔터티 못찾음</il>';
                var is= 0; 
                var result;
                var is_list='';
                var sentence = data['sentence'];
                var entityArr = data['entityData'];
              
                $.each(entityArr,function(i,entity){
                    if(entity!=null){
                        var entityName = entity[5]?entity[5]:'';
                        var entityVal = entity[2]?entity[2]:'';
                        is_list+= '<li>@'+entityName+':'+ entityVal+'</il>'; 
                        var jsonDT = {
                            "order": order,
                            "sentence": sentence,
                            "type": "@",                            
                            "value": entityName+(entityVal?':'+entityVal:'')
                        };
                        recData.push(jsonDT); 
                        is++; 
                    }
                    
                });
                
                
                if(is) result = ul_start + is_list + ul_end;
                else result = ul_start + no_list + ul_end;

                return result;
            };
            var getStringFromJson = function(data){
                var type = data.type;
                var content = data.content;
                var json_cont = JSON.stringify(content);
                var resArray = $.parseJSON(json_cont);
                var result='';
                if(type=='text'){
                    result += content;
                }else if(type=='hMenu'||type=='card'||type=='img'){
                    
                    $.each(resArray,function(i,item){
                         var res = $.parseJSON(item);
                         if(type=='hMenu') result += '['+res.title+'] ';
                         else if(type=='card'||type=='img') result+= res.img_url; 
                    });
                }else if(type=='if'){
                    
                    result+='조건문 ';
                }
                
                
              
                return result;

            };
            var getResponse = function(response){
                // console.log(response);
                var itemType;
                var itemCont;
                var data;
                var resRow = template['LCbot_resRow'];
                var result = '';
                var res_text;
                if($.isArray(response)){
                    $.each(response,function(i,resItem){                        
                        if($.isArray(resItem[0])){
                            itemType = resItem[0][0];
                            itemCont = resItem[0][1];
                            data = {type: itemType,content: itemCont};
                            
                        }else{
                            itemType = resItem[0];
                            itemCont = resItem[1]; 
                            data = {type: itemType,content: itemCont};
                        }
                        res_text = getStringFromJson(data);
                        result += resRow.replace(/\{\$res_text}/gi,res_text);
                    });
                }else{
                    result += resRow.replace(/\{\$res_text}/gi,response);
                }                 

                return result;
            };
            var updateNum = function(data){
                var order = data.order;
                var type = data.type; // total, success, fail
                var ele = $('[data-role="LCbot-'+type+'-'+order+'"]');
                var old_num = $(ele).text();
                var new_num = parseInt(old_num)+1;
                $(ele).text(new_num); 
            };
            var getChatBlock = function(data){
                var template = self.template;
                var opense_utt = data['user_input']; // 공격 문장 
                var intent_name = data['intentName']?'#'+data['intentName']:'인텐트 없음'; 
                var chatBlock = template['LCbot_Block'];
                var depense_rows = getResponse(data['response']);
                var nodeName = data['nodeName'];
                chatBlock = chatBlock.replace(/\{\$opense_utt}/gi,opense_utt); 
                chatBlock = chatBlock.replace(/\{\$intent_name}/gi,intent_name);
                chatBlock = chatBlock.replace(/\{\$depense_rows}/gi,depense_rows);
                
                // entity 얻기 
                data['sentence'] = opense_utt;            
                var entity_list = getEntityList(data);
                chatBlock = chatBlock.replace(/\{\$entity_list}/gi,entity_list);
               
                 // intent 데이타 저장 
                 if(data['intentName']){
                    var jsonDT = {
                        "order": order,
                        "sentence": opense_utt,
                        "type": "#",
                        "value": data['intentName']
                    }
                    recData.push(jsonDT); 
                 }   

                // 성공으로  간주 
                if(nodeName && depense_rows && data['intentName']){
                    var _data = {order: order,type: "success"};
                    updateNum(_data);
                    chatBlock = chatBlock.replace(/\{\$class_succFail}/gi,' text-success');
                    //chatBlock = chatBlock.replace(/\{\$succFail_text}/gi,'성공');                
                }else{
                    var failList = '[data-role="LCbot-failList"]';
                    var failItemInput = '<input type="hidden" name="fail_utt[]" value="'+opense_utt+'" />';
                    var failItem = '<li class="text-danger text-fail">'+failItemInput+opense_utt+'</li>';
                    var _data = {order: order,type: "fail"};
                    updateNum(_data);
                    chatBlock = chatBlock.replace(/\{\$class_succFail}/gi,' text-danger');
                    //chatBlock = chatBlock.replace(/\{\$succFail_text}/gi,'실패');
                    $(failList).append(failItem);

                }
                
                // 전체 숫자 업데이트 
                var _data = {order:order,type: "total"};
                updateNum(_data);

                return chatBlock;
            };
  
            // chat block 순서대로 출력 
            $.each(chatRows,function(i,row){
                var chatRow = getChatBlock(row); // 공/수 셋트 
                var term = parseInt(i)*500;
                setTimeout(function(){
                    $(chatRow).appendTo(botBox);
                    $(botBox).scrollTop(1000000);
                },term);
                
            });           

        },
        
        getTemplate: function(){
            var data = {linkType: "getTemplate"};
            this.linkServerData(data);
        },

        // 엑셀 다운로드 함수 


        // 서버 작업 실행 함수 
        linkServerData: function(data){
            var module = this.module;
            var self = this;
            var linkType = data.linkType;
                     
            $.ajax({
                url: rooturl+'/?r='+raccount+'&m='+module+'&a=do_chatbotLC',
                type: 'post',
                data: data,
                cache: false,
                success: function(response){
                    var result=$.parseJSON(response);
                    if(linkType=='getIntent') self.intent = result.content;
                    else if(linkType=='getEntity') self.entity = result.content;
                    else if(linkType=='getTemplate') self.template = result; // 템플릿 세팅
                    else if(linkType=='initLC') self.initLC(result);
                    else if(linkType=='testBot') self.printResult(result.data);
             
                },
                xhr: function () {
                    var xhr = new window.XMLHttpRequest();
                    xhr.addEventListener("progress", function (evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            $('.progress').css({
                                width: percentComplete * 100 + '%'
                            });
                            if (percentComplete === 1) {
                                $('.progress').removeClass('deactive');
                            }
                        }
                    }, false);
                    return xhr;
                },
            });
   
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
            var container = data.container?data.container:this.chatLogContainer;
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
  
        // chat toekn 생성 
        getChatToken : function(){
            function chr4(){
               return Math.random().toString(16).slice(-4);
            }
            return chr4() + chr4() + '.' + chr4() + chr4() + chr4();
        },

        // 입력창 포커스 이벤트 
        focusInput : function(){
            var userInputEle = this.userInputEle;
            setTimeout(function(){
                $(userInputEle).focus();              
            },10);
        },
        
        getMicrotime : function(getAsFloat) {
            var s, now, multiplier;

            if(typeof performance !== 'undefined' && performance.now) {
                now = (performance.now() + performance.timing.navigationStart) / 1000;
                multiplier = 1e6; // 1,000,000 for microseconds
            }
            else {
                now = (Date.now ? Date.now() : new Date().getTime()) / 1000;
                multiplier = 1e3; // 1,000
            }

            // Getting microtime as a float is easy
            if(getAsFloat) {
                return now;
            }

            // Dirty trick to only get the integer part
            s = now | 0;
            
            return (Math.round((now - s) * multiplier ) / multiplier ) + ' ' + s;
        },
        
        // 질문시작 시간 
        get_Q_startTime : function(){
            var mtime = this.getMicrotime();
            var mtime_arr = mtime.split(' ');
            var q_StartTime = parseInt(mtime_arr[0])+parseInt(mtime_arr[1]);

            return q_StartTime;
        },

        // control timer
        setTimer : function(act){
            if(act=='start'){
               ContTimer.resetPlay(); // require jquery.timmer.js
               this.q_StartTime = this.get_Q_startTime(); // 질문 시작 시간 저장   
            }
            else if(act=='stop') ContTimer.stop();
            else if(act=='reset') ContTimer.reset();
        },

    };

    $.fn.PS_chatbotLC = function(options) {
        return this.each(function() {
            var bottalksLC = Object.create(BottalksLC);
            $.data(this, 'bottalksLC', bottalksLC);
            bottalksLC.init(options || {}, this);
        });
    };
	
}));