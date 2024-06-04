
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

    var KRE_Admin = {

        // Instance variables
        // ==================

        $el: null,
        $el_id: null,
        backdrop: null,
        options: {},
        module: null,
        vendor: null, // 업체 
        bot: null, // 챗봇 
        dialog: null,
        callIntent: null,
        callEntity: null,
        sescode: null,
        template: {}, // 각종 엘리먼트 html 
        events: {
            'click [data-role="import-data"]' : 'importData',
            'change [data-role="importData-inputFile"]' : 'importFileInputChanged', // 데이타 import
            'click [data-role="self-uploadImg"]' : 'uploadImg',
            'change [data-role="upload-inputFile"]' : 'fileInputChanged', // 파일업로드
            'click [data-role="btn-updateBot"]' : 'updateBot',
            'click [data-role="page-item"]' : 'openRightPanel',
            'click [data-role="open-tempDataPanel"]' : 'openTempDataPanel',
            'click [data-role="close-tempDataPanel"]' : 'closeTempDataPanel',
            'click [data-role="close-rightPanel"]' : 'closeRightPanel',
            'click [data-role="chkBox-tData"]' : 'controlTempData',
            'keyup [data-role="input-TDlabel"]' : 'controlTempDataInput',
            'click [data-role="open-tempLabelPanel"]' : 'openTempLabelPanel',
            'click [data-role="close-tempLabelPanel"]' : 'closeTempLabelPanel',
            'click [data-role="control-botActive"]' : 'controlBotActive',

        },

        // Default options
        getDefaultOptions: function() {
            return {  
                highlightColor: '009efb', // 하이라이트 칼라  
                normalizeColor: 'd0dada', // 기본칼라  
                noIntentExMsg: '등록된 예문이 없습니다.',
                noEntityMsg: '등록된 단어가 없습니다.',
                configBotForm : '[data-role="configBotForm"]',
                rightPanel: '[data-role="rightPanel"]',
                tableWrapper: '[data-role="table-wrapper"]',
                rightPanelWidth: 77,
                highlightColor: '1caafc', // 하이라이트 칼라  
                normalizeColor: 'd0dada', // 기본칼라  
            }            
        },
        
        // 템플릿 활성화 설정 
        controlBotActive: function(e){
            var target = e.currentTarget;
            var pDiv = $(target).parent();
            var botuid = $(target).data('uid');
            var botActive_label = $(target).find('[data-role="botActive-label-'+botuid+'"]');

            if($(pDiv).hasClass("botSwitch-off")) {
                var data = {linkType: "control-botActive", active: true, uid: botuid};
                this.linkServerData(data);
                setTimeout(function(){
                    $(pDiv).removeClass("botSwitch-off");
                    $(pDiv).addClass("botSwitch-on");
                    $(botActive_label).text('ON');
                },180);                
                
                
            }else{
                var data = {linkType: "control-botActive", active: false, uid: botuid};
                this.linkServerData(data); 
                setTimeout(function(){
                    $(pDiv).addClass("botSwitch-off");
                    $(pDiv).removeClass("botSwitch-on");
                    $(botActive_label).text('OFF');
                },180);
                     
            } 
        },

        // nestable 초기화 
        init_nestable: function(){
            $('.nestable-menu').nestable();
        },

        controlTempDataInput: function(e){
            var target = e.currentTarget;
            var _data = $(target).data();
            var chkBox = $(target).parent().parent().find('[data-role="chkBox-tData"]');
            var label = $(target).val();
            _data["label"] = label;

            if($(chkBox).prop("checked") ===true){
                if(label){
                    _data["linkType"] = "editTempData";
                    this.linkServerData(_data);
                    
                }
            }
        },

        // 템플릿 데이타 추가 체크박스 클릭 
        controlTempData: function(e){
            var target = e.currentTarget;
            var _data = $(target).data();
            var label = $(target).parent().find('[data-role="input-TDlabel"]').val();
            var hideAfter = 1500;
            _data["label"] = label;
            if($(target).prop("checked") ===true){
                if(label){
                    _data["linkType"] = "addTempData";
                    this.linkServerData(_data);
                    
                }else{
                    var data = {msg: '라벨을 입력해주세요.', hideAfter: hideAfter, icon: 'error'};
                    this.showToast(data);
                    setTimeout(function(){
                        $(target).prop("checked",false);
                    },100);
                } 
            }else{
                _data['linkType'] = "delTempData";
                this.linkServerData(_data);
                
            }

        },
        
        // Start of ************** 템플릿 라벨 패널 관련 *****************************************         
        openTempLabelPanel: function(e){
            e.preventDefault();
            var target = e.currentTarget;
            var pageItem = $('[data-role="page-item"]');
            var data = $(target).data();
            $.each(pageItem,function(){
                 $(this).removeClass('active');
            });
            $(target).addClass('active');
            data['role'] ='open';

            this.controlTempLabelPanel(data);
        },

        closeTempLabelPanel: function(){
            var data = {role: "close"}
            this.controlTempLabelPanel(data);
        },

        controlTempLabelPanel: function(data){
            var self = this;
            var rightPanel = '[data-role="tempLabelPanel"]';
            var tableWrapper = this.options.tableWrapper;
            var rpw = 90;
            var tblw = 100-parseInt(rpw);
            var type = data.type; // intent, entity
            var uid = data.uid;
            var vendor = data.vendor;
            var bot = data.uid;
            var dialog = data.dialog;  
            var role = data.role; // open, close
            var exWrapper = '[data-role="tempLabelList-ul"]';
            var nestableDiv = '[data-role="tempLabelList-div"]';
           
            // 패널값 리셋  
            var init_panel = function(){
                var itemEle = $(tableWrapper).find('[data-role="page-item"]');
                // intent active 해제 
                $.each(itemEle,function(){
                    $(this).removeClass('active');
                });
                $(exWrapper).html(''); // 예시문장 div 
            };

            // graphTable 가져오기   
            var getLabelList = function(_data){
                var uid = _data.uid;
                var resultContainer =  exWrapper;
                var data = {"linkType":"getTempLabelList","bot": bot,"dialog": dialog,"resultContainer":resultContainer};
                self.linkServerData(data);   

            };
            
            // css 세팅 
            var setCss = function(data){
                var role = data.role;
                if(role=='open'){                    
                    
                    $(rightPanel).css({"margin-right": 0});
                    setTimeout(function(){
                        $(tableWrapper).find('.intEnt-des').css("display","none");
                        $(tableWrapper).find('.intEnt-ex').css("display","none"); 
                        $(tableWrapper).css({"width": tblw+'%'}); 
                    },100)                   
                                    
                }else if(role=='close'){
                    $(tableWrapper).find('.intEnt-des').css("display","");
                    $(tableWrapper).find('.intEnt-ex').css("display","");                        
                    $(tableWrapper).css({"width":'100%'});
                    setTimeout(function(){
                        $(rightPanel).css({"margin-right": '-'+ rpw+'%'});
                    },80);                     
                     
                    // 패널값 리셋  
                    init_panel();
                }
            };

            // input 값 세팅 
            var setInputVal = function(data){
                var uid = data.uid;
                var name = data.name;
                $(uidEle).val(uid);
                $(nameEle).val(name);
            };

            // 시작함수 
            var startFunc = function(data){
                var role = data.role;
                if(role=='open'){
                    getLabelList(data); // 대화그래프 상자 가져오기 
                    setTimeout(function(){
                         setCss(data); // css 변경 
                    },100);

                    // nestable div에 dialog, bot 값 할당 
                    $(nestableDiv).attr("data-vendor",vendor);
                    $(nestableDiv).attr("data-bot",bot);

                }else if(role=='close'){
                    setCss(data);
                }

                $('body').find('.tooltip').remove();  
            }

            // 라벨리스트 순서변경 이벤트 
            $(nestableDiv).on('change', function(e) {
                var target = e.currentTarget;
                var li_arr =$(target).find('li');
                var uid_arr = [];
                $.each(li_arr,function(){
                     var uid = $(this).data('uid');
                     uid_arr.push(uid);   
                });
                var data = {"linkType":"changeTempLabelOrder","bot": bot,"vendor": vendor,"uid_arr": uid_arr};
                self.linkServerData(data); 
                
            });          
            
            // 최초시작    
            startFunc(data);
            
        },
        // End of ************** 템플릿 라벨 패널 관련 ***************************************** 

        // Start of ************** 템플릿 데이타셋 패널 관련 *****************************************         
        openTempDataPanel: function(e){
            e.preventDefault();
            var target = e.currentTarget;
            var pageItem = $('[data-role="page-item"]');
            var data = $(target).data();
            $.each(pageItem,function(){
                 $(this).removeClass('active');
            });
            $(target).addClass('active');
            data['role'] ='open';

            this.controlTempDataPanel(data);
        },

        closeTempDataPanel: function(){
            var data = {role: "close"}
            this.controlTempDataPanel(data);
        },

        controlTempDataPanel: function(data){
            var self = this;
            var rightPanel = this.options.rightPanel;
            var tableWrapper = this.options.tableWrapper;
            var rpw = 90;
            var tblw = 100-parseInt(rpw);
            var type = data.type; // intent, entity
            var uid = data.uid;
            var vendor = data.vendor;
            var bot = data.uid;
            var dialog = data.dialog;  
            var role = data.role; // open, close
            var nameEle = $(rightPanel).find('input[name="item_name"]'); // name input
            var uidEle = $(rightPanel).find('input[name="item_uid"]'); // uid input
            var exWrapper = '[data-role="resultBody-wrapper"]';
           
            // 패널값 리셋  
            var init_panel = function(){
                var itemEle = $(tableWrapper).find('[data-role="page-item"]');
                // intent active 해제 
                $.each(itemEle,function(){
                    $(this).removeClass('active');
                });
                // 값 초기화 
                $(nameEle).val(''); // name input
                $(uidEle).val(''); // uid input 
                $(exWrapper).html(''); // 예시문장 div 
            };

            // graphTable 가져오기   
            var getGraphTable = function(_data){
                var uid = _data.uid;
                var resultContainer =  exWrapper;
                var data = {"linkType":"getGraphTable","bot": bot,"dialog": dialog,"resultContainer":resultContainer};
                self.linkServerData(data);   

            };
            
            // css 세팅 
            var setCss = function(data){
                var role = data.role;
                if(role=='open'){                    
                    
                    $(rightPanel).css({"margin-right": 0});
                    setTimeout(function(){
                        $(tableWrapper).find('.intEnt-des').css("display","none");
                        $(tableWrapper).find('.intEnt-ex').css("display","none"); 
                        $(tableWrapper).css({"width": tblw+'%'}); 
                    },100)                   
                                    
                }else if(role=='close'){
                    $(tableWrapper).find('.intEnt-des').css("display","");
                    $(tableWrapper).find('.intEnt-ex').css("display","");                        
                    $(tableWrapper).css({"width":'100%'});
                    setTimeout(function(){
                        $(rightPanel).css({"margin-right": '-'+ rpw+'%'});
                    },80);                     
                     
                    // 패널값 리셋  
                    init_panel();
                }
            };

            // input 값 세팅 
            var setInputVal = function(data){
                var uid = data.uid;
                var name = data.name;
                $(uidEle).val(uid);
                $(nameEle).val(name);
            };

            // 시작함수 
            var startFunc = function(data){
                var role = data.role;
                if(role=='open'){
                    setInputVal(data); // uid, name 값 세팅 
                    getGraphTable(data); // 대화그래프 상자 가져오기 
                    setTimeout(function(){
                         setCss(data); // css 변경 
                    },100);
                }else if(role=='close'){
                    setCss(data);
                }

                $('body').find('.tooltip').remove();  
            }
          
            
            // 최초시작    
            startFunc(data);
            
        },
        // End of ************** 템플릿 데이타셋 패널 관련 ***************************************** 


        openRightPanel: function(e){
            var target = e.currentTarget;
            var pageItem = $(target).parent().parent().parent().find('[data-role="page-item"]');
            var data = $(target).data();
            $.each(pageItem,function(){
                 $(this).removeClass('active');
            });
            $(target).addClass('active');
            data['role'] ='open';

            this.controlRightPanel(data);
        },

        closeRightPanel: function(){
            var data = {role: "close"}
            this.controlRightPanel(data);
        },

        controlRightPanel: function(data){
            var self = this;
            var rightPanel = this.options.rightPanel;
            var tableWrapper = this.options.tableWrapper;
            var rpw = this.options.rightPanelWidth;
            var tblw = 100-parseInt(rpw);
            var type = data.type; // intent, entity
            var uid = data.uid;  
            var role = data.role; // open, close
            var exWrapper = '[data-role="itemEx-wrapper"]';
            var nameEle = $(rightPanel).find('input[name="item_name"]'); // name input
            var uidEle = $(rightPanel).find('input[name="item_uid"]'); // uid input
            var delIntentEle = $(rightPanel).find('[data-role="del-item"]');// delete intent btn
            var exWrapperEle = $(rightPanel).find(exWrapper); // 
           
            // 패널값 리셋  
            var init_panel = function(){
                var itemEle = $(tableWrapper).find('[data-role="page-item"]');
                // intent active 해제 
                $.each(itemEle,function(){
                    $(this).removeClass('active');
                });
                // 값 초기화 
                $(nameEle).val(''); // name input
                $(uidEle).val(''); // uid input 
                $(exWrapperEle).html(''); // 예시문장 div 
                $(delIntentEle).addClass('hidden');   
            };
            
            // 예문 가져오기   
            var getItemEx = function(_data){
                var uid = _data.uid;
                var type = _data.type;
                var resultContainer =  exWrapper;
                var data = {"linkType":"getItemEx","uid": uid,"type": type,"resultContainer":resultContainer};
                self.linkServerData(data); // 예문 가져오기  

            };
            // css 세팅 
            var setCss = function(data){
                var role = data.role;
                if(role=='open'){                    
                    
                    $(rightPanel).css({"margin-right": 0});
                    setTimeout(function(){
                        $(tableWrapper).find('.intEnt-des').css("display","none");
                        $(tableWrapper).find('.intEnt-ex').css("display","none"); 
                        $(tableWrapper).css({"width": tblw+'%'}); 
                    },200)                   
                                    
                }else if(role=='close'){
                    $(tableWrapper).find('.intEnt-des').css("display","");
                    $(tableWrapper).find('.intEnt-ex').css("display","");                        
                    $(tableWrapper).css({"width":'100%'});
                    setTimeout(function(){
                        $(rightPanel).css({"margin-right": '-'+ rpw+'%'});
                    },80);                     
                     
                    // 패널값 리셋  
                    init_panel();
                }
            };

            // input 값 세팅 
            var setInputVal = function(data){
                var uid = data.uid;
                var name = data.name;
                $(uidEle).val(uid);
                $(nameEle).val(name);
            };

            // 시작함수 
            var startFunc = function(data){
                var role = data.role;
                if(role=='open'){
                    setInputVal(data); // uid, name 값 세팅 
                    getItemEx(data); // 예문 세팅 
                    setTimeout(function(){
                        $('.treeTable').treeTable();
                    },10);
                    setTimeout(function(){
                         setCss(data); // css 변경 
                    },200);
                    

                }else if(role=='close'){
                    setCss(data);
                }  
            }
            // 예문폼 추가 
            var addItemEx = function(data){
                var itemExTpl = self.template[data.type+'_row'];
                itemExTpl = itemExTpl.replace(/\{\$iEx_uid}/gi,'');
                itemExTpl = itemExTpl.replace(/\{\$iEx_val}/gi,'');
                itemExTpl = itemExTpl.replace(/\{\$iEx_syn}/gi,'');
                $(exWrapperEle).find('.no-data').remove();
                $(exWrapperEle).prepend(itemExTpl);
            };
            
             // '관련 예시문장' 추가 이벤트 
            $(rightPanel).find('[data-role="btn-save"]').off('click').on('click',function(e){
                var target = e.currentTarget;
                var data= $(target).data();
                //saveItem(data);
            });
             
             // '관련 예시문장' 추가 이벤트 
            $(rightPanel).find('[data-role="add-itemEx"]').off('click').on('click',function(e){
                var target = e.currentTarget;
                var data= $(target).data();
                addItemEx(data);
            });

             // name input 포커싱 in 이벤트 
            $(nameEle).on('focusin',function(e){
                var target = e.currentTarget;
                var nameEleWrapper = $(target).parent();
                $(nameEleWrapper).css("border-bottom","solid 1px #"+self.options.highlightColor);
            });

            // name input 포커싱 out 이벤트 
            $(nameEle).on('focusout',function(e){
                var target = e.currentTarget;
                var nameEleWrapper = $(target).parent();
                $(nameEleWrapper).css("border-bottom","solid 1px #"+self.options.normalizeColor);
            });
            
            // 최초시작    
            startFunc(data);
            
        },
        
        getFormObj: function(form){
            var obj = {};
            var arr = $(form).serializeArray();
            $.each(arr,function(){
                obj[this.name] = this.value;
            }); 

            return obj;
        },
        
        // bot 기본정보 업데이트  
        updateBot: function(e){
            var form = this.options.configBotForm;
            var data = this.getFormObj(form);
            data['linkType'] = "updateBot";
            e.preventDefault();
    
            this.linkServerData(data);
        }, 
        
        // ######################################################### import data   
        importData: function(e){
            var target = e.currentTarget;
            var type = $(target).data('type');
            var mod = $(target).data('mod');
            var parent = $(target).parent();
            var fileInput = $('<input/>', {
                type: 'file',
                name: 'importFile',
                style: 'display:none',
                'data-role': 'importData-inputFile',
                'data-type': type,
                'data-mod': mod
            });

            $(fileInput).appendTo(parent).click();      
        },

        importFileInputChanged: function(e){
            var self = this;
            var sescode = this.sescode;
            var target = e.currentTarget;
            var type = $(target).data('type');
            var mod = $(target).data('mod');
            var file = target.files[0];
            var data = new FormData();
            data.append("file",file); // 가상의 "file" 이라는 오브젝트를 만들어서 전송한다.
            data.append("linkType","importData");
            data.append("sescode",sescode);
            data.append("type",type);
            data.append("mod",mod);            
            data.append("vendor",this.vendor);
            data.append("bot",this.bot);
            
            $.ajax({
                type: "POST",
                url: rooturl+'/?r='+raccount+'&m='+this.module+'&a=do_VendorAction',
                data:data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                   var result = $.parseJSON(response);
                   location.reload();
                   
                }
            }); 
        },
        // ######################################################### import data 

           
        // ######################################################### 이미지 업로드  
        uploadImg: function(e){
            var target = e.currentTarget;
            var parent = $(target).parent();
            var fileInput = $('<input/>', {
                type: 'file',
                name: 'files',
                'style': 'display:none',
                'data-role': 'upload-inputFile'
            });

            $(fileInput).appendTo(parent).click();  

        },

        // input file changed  이벤트 
        fileInputChanged: function(e){
            var self = this;
            var sescode = this.sescode;
            var target = e.currentTarget;
            var preview_ele = $(target).parent().find('[data-role="self-uploadImg"]');
            var imgUrl_ele = $(target).parent().find('[data-role="img_url"]');
            var file = target.files[0];
            var data = new FormData();
            data.append("file",file); // 가상의 "file" 이라는 오브젝트를 만들어서 전송한다.
            data.append("linkType","uploadImg");
            data.append("sescode",sescode);
            data.append("vendor",this.vendor);
            data.append("bot",this.bot);
            data.append("dialog",this.dialog);

            $.ajax({
                type: "POST",
                url: rooturl+'/?r='+raccount+'&m='+this.module+'&a=do_VendorAction',
                data:data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    var result = $.parseJSON(response);
                    var code=result[0];
                    if(code=='100') // code 값이 100 일때만 실행 
                    {
                        var source = result[1];// path + tempname
                        var upuid = result[2]; // upload 테이블 저장 uid
                        $(imgUrl_ele).val(source);
                        $(preview_ele).css({
                            "background-image":"url('"+source+"')", 
                            "background-repeat":"no-repeat",
                            "background-position":"center center",
                            "background-size":"cover"
                        });
                         setTimeout(function(){
                             $(target).remove(); // 해당 input file 삭제 
                         },10)                        

                    } // success
           
                }
            }); 
        },
        // ######################################################### 이미지 업로드   
       
        // 초기화 함수들 
        initFunc: function(){

        },

        // 기본 template 가져와서 세팅 
        getTemplate: function(){
            var data = {"linkType":"getTemplate"};
            this.linkServerData(data);
        }, 

        // 템플릿 세팅  
        setTemplate: function(data){
            this.template = data; // template{} 는 dialog.php 에서 글로벌하게 설정해논 상태임.
            return;
        },
        
        // Initialization
        init: function(options, el) {
            var self = this;
            this.$el = $(el);
            this.$el_id = '#'+this.$el.attr('id');
            this.options = $.extend(true, {}, this.getDefaultOptions(), options);
            this.module = this.options.module;
            this.vendor = this.options.vendor; // 업체 정보 
            this.bot = this.options.bot; // 챗봇 정보
            this.undelegateEvents(); // comment box 엘리먼트들 이벤트 바인딩 off 
            this.delegateEvents(); // comment box 엘리먼트들 이벤트 바인딩 on  
            this.callIntent = this.options.callIntent;
            this.callEntity = this.options.callEntity;
            this.sescode = this.options.sescode;
           
            // 템플릿 가져오기  
            this.getTemplate();

              // 초기함수 호출 
            this.initFunc();
        },
        
        initTreeTable: function(){
            $('.treeTable').treeTable();
        },
       
        // 서버 작업 실행 함수 
        linkServerData: function(data){
            var module = this.options.module;
            var self = this;
            var linkType = data.linkType;
            var resultContainer = data.resultContainer?data.resultContainer:null;
            var eTarget = data.eTarget?data.eTarget:null;
            data['vendor'] = data.vendor?data.vendor:this.vendor;
            data['bot'] = data.bot?data.bot:this.bot;
            data['dialog'] = data.dialog?data.dialog:this.dialog;

            
            // var _data = $.param(data);
            $.ajax({
                url: rooturl+'/?r='+raccount+'&m='+module+'&a=do_AdminAction',
                type: 'post',
                data: data,
                cache: false,
                success: function(response){
                    var result=$.parseJSON(response);
                    if(linkType=='updateBot' && result.content==self.bot){
                        var data = {msg: "챗봇설정이 변경되었습니다."}
                        setTimeout(function(){
                            self.showToast(data);                            
                        },300);                        
                    }else if(linkType=='getTemplate'){
                        self.setTemplate(result);  
                    }else if(linkType=='getItemEx' || linkType =='getGraphTable'){
                        $(resultContainer).html(result.content);
                        if(linkType == 'getGraphTable'){
                            setTimeout(function(){
                                $('.treeTable').treeTable();
                            },100);
                        }
                    }else if(linkType=='addTempData' || linkType=='editTempData' || linkType =='delTempData'){
                        var hideAfter = 1500;
                        if(linkType=='addTempData'){
                            var dt = {msg: '데이타셋 설정이 완료되었습니다.', hideAfter: hideAfter};
                            self.showToast(dt);
                        }else if(linkType=='editTempData'){
                            var dt = {msg: '데이타셋 설정이 수정되었습니다.', hideAfter: hideAfter};
                            self.showToast(dt);
                        }else if(linkType =='delTempData'){
                            var dt = {msg: '데이타셋 설정이 해제되었습니다.', hideAfter: hideAfter, icon: 'info'};
                            self.showToast(dt);
                        }
                    }else if(linkType =='getTempLabelList'){
                        $(resultContainer).html(result.content);
                        setTimeout(function(){
                           self.init_nestable();
                        },50);
                    }else if(linkType =='changeTempLabelOrder'){
                        var dt = {msg: '라벨 순서가 변경되었습니다.', hideAfter: hideAfter};
                        self.showToast(dt);
                    } 
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

        bindEvents: function(unbind) {
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
                            else if(selector=='[data-role="drawer-backdrop"]'){
                                if(this.options.backdropClose) this.$el[bindFunction](eventName, selector, method);
                            }
                            else this.$el[bindFunction](eventName, selector, method);
                        }
                    }
                }
            }
        },
        
        // toast 알림 : jquery.toast
        showToast: function(data){
            var msg = data.msg;
            var hideAfter = data.hideAfter?data.hideAfter:3000;
            var icon = data.icon?data.icon:'success';
            var _position = data.position?data.position:'top-center';
            $.toast({
                heading: msg,
                position: _position,
                topOffset: 70,
                loaderBg: '#009efb',
                icon: icon,
                hideAfter: hideAfter,
            });   
        },

        // 알림 출력   
        showNotify : function(container,message){
            var container = container?container:'body';
            var notify_msg ='<div id="kiere-notify-msg">'+message+'</div>';
            var notify = $('<div/>', { id: 'kiere-notify', html: notify_msg})
                  .addClass('active')
                  .appendTo(container)
            setTimeout(function(){ 
                $(notify).removeClass('active');
                $(notify).remove();
            }, 1500);
        },

        // chat toekn 생성 
        getToken : function(type){
            var result;
            function chr4(){
               return Math.random().toString(16).slice(-4);
            }
            
            if(type=='group') result = chr4() + chr4() + chr4() + chr4() + chr4();
            else if(type=='item') result = chr4() + chr4() + chr4();
            
            return result;
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
        
      
    };

    $.fn.KRE_Admin = function(options) {
        return this.each(function() {
            var admin = Object.create(KRE_Admin);
            $.data(this, 'admin', admin);
            admin.init(options || {}, this);
        });
    };
}));