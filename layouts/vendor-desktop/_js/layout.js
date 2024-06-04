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
        botId: null,
        dialog: null,
        callIntent: null,
        callEntity: null,
        sescode: null,
        page: null,
        intent: {},
        entity: {},
        template: {}, // 각종 엘리먼트 html
        events: {
            'click [data-role="import-data"]' : 'importData',
            'change [data-role="importData-inputFile"]' : 'importFileInputChanged', // 데이타 import
            'click [data-role="self-uploadImg"]' : 'uploadImg',
            'change [data-role="upload-inputFile"]' : 'fileInputChanged', // 파일업로드
            'click [data-role="reset_botAvatar"]' : 'reset_botAvatar',
            'click [data-role="btn-updateBot"]' : 'updateBot',
            'click [data-role="page-item"]' : 'openRightPanel',
            'click [data-role="add-item"]' : 'openRightPanel',
            'click [data-role="close-rightPanel"]' : 'closeRightPanel',
            'click [data-role="clipboard-copy"]' : 'copyToClipBoard',
            'click [data-role="open-settingChannelModal"]' : 'openSettingChannelModal', // channel 세팅 모달 열기
            'click [data-role="delete-legacy"]' : 'deleteLegacy', // legacy 삭제
            'click [data-role="save-channelSettings"]' : 'saveChannelSettings', // channel 세팅 저장
            'click [data-role="issue-apiKey"]' : 'issueApiKey', // api secret, token 재발급
            'click [data-role="open-legacySettingsModal"]' : 'openLegacySettingsModal', // legacy 세팅 저장
            'click [data-role="save-vendorResponse"]' : 'saveVendorResponse', // vendor 응답 저장
            'click [data-role="open-setApiPanel"]' : 'openSetApiPanel', // api 세팅패널 열기
            'click [data-role="close-setApiPanel"]' : 'closeSetApiPanel', // api 세팅패널 닫기
            'click [data-role="showHide-ApiList"]' : 'showHideApiList', // api 리스트 보여주기
            'click [data-role="table-wrapper"]' : 'clickTableWrapper', // 테이블 wrapper 클릭 > 우측패널 닫기
            'click [data-role="select-all"]' : 'selectAllckBox', // 선택박스 전체선택
            'keyup [data-role="enter-text"]' : 'checkKeyUp', // 텍스트 입력 체크 (글자수, 비속어 등)
            'click [data-role="learning-intent"]' : 'learningIntent',
            'click [data-role="delete-item"]' : 'deleteItems',
            'click [data-role="use-reserve-check"]' : 'openReserveConfig',
            'click [data-role="use-reserve-manage"]' : 'openReserveAPI',
            'click [data-role="use_shopapi_check"]' : 'openShopApiConfig',
            'click [data-role="shopapi_vendor"]' : 'getShopApiVendor',
            'click [data-role="shopapi_get_token"]' : 'getShopAccessToken',
            'click [data-role="use_syscheckup"]' : 'openSysCheckup',
            'focus [data-role="syscheckup_date"]' : 'sysCheckupDate',
            'click [data-role="interface"]' : 'openBargein',
            'click [data-role="use_chatgpt"]' : 'openChatGPT',
            'click [data-role="use_quick"]' : 'openQuickMenu',
        },
        linkServerContent: null,

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
                settingChannelModal: '[data-role="settingChannelModal"]',
                settingLegacyModal: '[data-role="settingLegacyModal"]',
                jsonEditorContainer: document.getElementById("jsonEditor-wrapper"),
                callEntity: '엔터티',
                callIntent: '인텐트',
            }
        },

        // 레거시 api, req 삭제
        deleteLegacy: function(e){
            var target = e.currentTarget;
            var type = $(target).data('type');
            var legacyTbl = $('[data-role="tbl-legacyList"]');
            var api_arr = $(legacyTbl).find('input[name="legacy_uid[]"]:checked').map(function(){return $(this).val()}).get();

            if(type=='api'){ // api 삭제
                if(api_arr.length==0){
                    alert('데이터가 선택되지 않았습니다.');
                    return false;
                }else{
                    var cfm = confirm('선택하신 데이터를 삭제하시겠습니까?');
                    if(cfm ==false){
                        return false;
                    }
                    var dd = {linkType:'delete-api',api_arr: api_arr};
                    this.linkServerData(dd);
                }
            }else if(type=='req'){
                var req = $(target).data('req');
                var cfm = confirm('등록된 API를 삭제하시겠습니까?');
                if(cfm ==false){
                    return false;
                }
                var dd = {linkType:'delete-req', req: req};
                this.linkServerData(dd);
            }

        },

        checkKeyUp: function(e){
            var target = e.currentTarget;
            var checkType = $(target).data('type');
            if(checkType =='length'){
                var limit = $(target).data('limit');
                var length = $(target).val().length;
                if (length > limit) {
                    alert('글자 수는 ' + limit + '자를 초과할 수 없습니다.');
                    return false;
                }
            }
        },

        // 선택박스 체크 이벤트 핸들러
        selectAllckBox: function(e){
            var target = e.currentTarget;
            var parent = $(target).data('parent');
            $(parent).find('tbody input[type=checkbox]').prop("checked",$(target).prop("checked"));
        },

        // 우측 패널 바깥부분 클릭해서 우측패널 닫기
        clickTableWrapper: function(e){
            var self = this;
            var targetClass = e.target.className;
            var classArray = new Array();
            classArray = targetClass.split(' ');
            $.each(classArray,function(i,className){
                if(className=='table-wrapper')  self.closeRightPanel();
            })
        },

        saveVendorResponse: function(e){
            e.preventDefault();
            var form = $('[data-role="configVRForm"]');
            var resItem = $(form).find('input[name="vendorResponse[]"]');
            var data = {linkType: "save-vendorResponse", resArray: []};

            $.each(resItem,function(i,ele){
                var _data = $(ele).data();
                _data['content'] = $(ele).val();
                data['resArray'][i] = JSON.stringify(_data);

            });

            this.linkServerData(data);
        },

        // 문자 랜덤 섞기
        getRandomString: function(data){
            var src = data.src;
            var len = data.len;
            var chars,result;
            var str = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ=/";
            var num = "0123456789";

            if(src=='AN') chars = str+num;
            else if(src=='A') chars = str;
            else if(src=='N') chars = num + getTime();

            var str2 = '';
            for(var i=0;i<len;i++){
                str2 += str;
            }
            var result = '';
            var word = str2.split('');
            while (word.length > 0) {
               result +=  word.splice(word.length * Math.random() << 0, 1);
            }

           return result;

        },

        getFormJson: function(form){
            var data = {};
            var array = $(form).serializeArray();
            $.each(array, function () {
                if (data[this.name]) {
                    if (!data[this.name].push) {
                        data[this.name] = [data[this.name]];
                    }
                    data[this.name].push(this.value || '');
                } else {
                    data[this.name] = this.value || '';
                }
            });

            return data;

        },

        openLegacySettingsModal: function(e){
            e.preventDefault();
            var self = this;
            var modal = this.options.settingLegacyModal;
            var confirmBtn = $(modal).find('[data-role="save-legacySettings"]');
            var target = e.currentTarget;
            var input_arr = ["uid","name","description","url"];
            var uid =null;
            $.each(input_arr,function(i,name){
                var val = $(target).attr('data-'+input_arr[i]);
                $(modal).find('[name="'+input_arr[i]+'"]').val(val);

                if($(target).attr('data-uid')) uid = val;
            });

            // 저장 버튼 텍스트 변경
            if(uid) confirmBtn.text('수정하기');
            else confirmBtn.text('추가하기');

            setTimeout(function(){
                $(modal).modal();
            },20);

            $(confirmBtn).off('click').on('click',function(){
                var form = $(modal).find('[data-role="settingsLegacyForm"]');
                var data = self.getFormJson(form);
                data['linkType'] ='save-legacySettings';

                $.each(input_arr,function(i,name){
                    var val = data[input_arr[i]];
                    $(target).attr('data-'+input_arr[i],val);
                });
                if(data.name==''){
                    alert('레거시명을 입력해주세요');
                    setTimeout(function(){
                         $(form).find('input[name="name"]').focus();
                    },10);
                    return false;
                }else if(data.url==''){
                    alert('기본 URL을 입력해주세요');
                    setTimeout(function(){
                         $(form).find('textarea[name="url"]').focus();
                    },10);
                }else{
                    self.linkServerData(data);
                }
            });


        },

        // 재발급 api key
        issueApiKey: function(e){
            e.preventDefault();
            var target = e.currentTarget;
            var name = $(target).data('name');
            var src,len;

            if(name=='client_secret') len = 54;
            else if(name=='access_token') len = 270;

            var _data = {
                "linkType":"get-randString",
                "typeName": name,
                "srcName": "AN",
                "len": len
            };
            this.linkServerData(_data);
        },

        // Start of api 패널 관련 ######################################################################################
        openSetApiPanel: function(e){
            e.preventDefault();
            var target = e.currentTarget;
            var pageItem = $(target).parent().parent().parent().find('.page-item');
            var this_pageItem = $(target).parent().parent().find('.page-item');
            var data = $(target).data();
            $.each(pageItem,function(){
                 $(this).removeClass('active');
            });
            // 현재 버튼이 속한 tr 내부 page-item 만 active 처리
            $(this_pageItem).addClass('active');

            $(target).addClass('active');
            data['role'] ='open';

            this.controlSetApiPanel(data);
        },

        closeSetApiPanel: function(){
            var data = {role: "close"}
            this.controlSetApiPanel(data);
        },

        showHideApiList: function(e){
            e.preventDefault();
            var target = e.currentTarget;
            var api = $(target).data('api');
            var reqnum = $(target).data('reqnum');
            var apiListWrapper = $('[data-role="apiListWrapper-'+api+'"]');
            if(reqnum) $(apiListWrapper).toggle();
            else {
                alert('등록된 API가 없습니다');
            }
        },

        // 챗봇설정 > 레거시 설정 API 추가/수정 페이지
        controlSetApiPanel: function(data){
            var self = this;
            var rightPanel = this.options.rightPanel;
            var tableWrapper = this.options.tableWrapper;
            var rpw = this.options.rightPanelWidth;
            var tblw = 100-parseInt(rpw);
            // req 리스트에서 data- 형태로 받아오는 값
            var api = data.api; // apiList uid
            var req = data.req?data.req:null; // apiReq uid
            var role = data.role; // open, close
            var headerParamWrapperSelect ='[data-role="headerParam-wrapper"]';
            var queryParamWrapperSelect ='[data-role="queryParam-wrapper"]';
            var pathParamWrapperSelect ='[data-role="pathParam-wrapper"]';
            var formParamWrapperSelect ='[data-role="formParam-wrapper"]';
            var headerParamWrapper = $(rightPanel).find(headerParamWrapperSelect);
            var queryParamWrapper = $(rightPanel).find(queryParamWrapperSelect);
            var pathParamWrapper = $(rightPanel).find(pathParamWrapperSelect);
            var formParamWrapper = $(rightPanel).find(formParamWrapperSelect);
            var queryParamListWrapper = $(rightPanel).find('[data-role="queryParamList-wrapper"]');
            var pathParamListWrapper = $(rightPanel).find('[data-role="pathParamList-wrapper"]');
            var formParamListWrapper = $(rightPanel).find('[data-role="formParamList-wrapper"]');

            // Start of 기본 필드
            var apiEle = $(rightPanel).find('input[name="api"]'); // api uid input
            var reqEle = $(rightPanel).find('input[name="req"]'); // req uid  input
            var nameInput = $(rightPanel).find('.itemName'); // name input
            var nameEle = $(rightPanel).find('input[name="item_name"]'); // intro input
            var introEle = $(rightPanel).find('input[name="item_intro"]'); // intro input
            var urlEle = $(rightPanel).find('input[name="basic_url"]'); // url input
            var methodEle = $(rightPanel).find('input[name="method"]'); // method input
            var statusCodeEle = $(rightPanel).find('input[name="statusCode"]');
            // End of 기본필드
            var delIntentEle = $(rightPanel).find('[data-role="del-item"]');// delete intent btn
            var addParamBtn = $(rightPanel).find('[data-role="btn-addParam"]');
            var delParamBtn = $(rightPanel).find('[data-role="btn-delParam"]');
            var authModal = '[data-role="settingAuthModal"]';
            var settingAuthBtn = $(authModal).find('[data-role="save-AuthSettings"]');
            var chk_showPw = $(authModal).find('[data-role="chkBox-showPW"]');
            var userNameEle = $(authModal).find('[name="user_name"]');
            var pwEle = $(authModal).find('[name="pw"]');
            var methodWrapper = $('[data-role="method-wrapper"]');
            //body ele 세팅
            var bodyUidEle = $(rightPanel).find('input[name="body_uid"]');
            var bodyTypeEle = $(rightPanel).find('input[name="body_type"]'); // form, text
            var bodyValEle = $(rightPanel).find('textarea[name="body_val"]');
            var bodyTypeLabel = $(rightPanel).find('[data-role="bodyType-label"]');
            var bodyTypeTextWrapper = $(rightPanel).find('[data-role="bodyTypeText-wrapper"]');

            // api param html 추출
            var getApiParamInputRow = function(data){
                var ParamInputRowTpl = self.template['apiParamInput_row'];
                var uid = data.uid?data.uid:''; // param uid
                var val = data.val?data.val:'';
                var name = data.name?data.name:'';
                var type = data.type?data.type:'';

                var inputSplit = [];
                inputSplit['header'] = ':';
                inputSplit['query'] = '=';
                inputSplit['form'] = '=';
                inputSplit['path'] = '/';
                ParamInputRowTpl = ParamInputRowTpl.replace(/\{\$TD_checked\}/gi,'checked');
                ParamInputRowTpl = ParamInputRowTpl.replace(/\{\$param_name\}/gi,name);
                ParamInputRowTpl = ParamInputRowTpl.replace(/\{\$param_val\}/gi,val);
                ParamInputRowTpl = ParamInputRowTpl.replace(/\{\$param_uid\}/gi,uid);
                ParamInputRowTpl = ParamInputRowTpl.replace(/\{\$param_type\}/gi,type);
                ParamInputRowTpl = ParamInputRowTpl.replace(/\{\$param_split\}/gi,inputSplit[type]);

                return ParamInputRowTpl;
            };

            // Get legacy api param
            var  getApiParamHtml = function(paramArray){
                var html='';
                $.each(paramArray,function(i,r){
                    var data ={};
                    data['uid'] = r.uid;
                    data['type'] = r.position;
                    data['name'] = r.name;
                    data['val'] = r.varchar_val;
                    data['uid'] = r.uid;
                    html+= getApiParamInputRow(data);

                });

                return html;
            };

            // 패널값 리셋
            var init_panel = function(){
                var itemEle = $(tableWrapper).find('.page-item');
                // intent active 해제
                $.each(itemEle,function(){
                    $(this).removeClass('active');
                });

                // 값 초기화
                $(nameEle).val('');
                $(introEle).val('');
                $(reqEle).val('');
                $(apiEle).val('');
                $(urlEle).val('');
                $(statusCodeEle).val('');

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
                    },200);


                }else if(role=='close'){
                    $(tableWrapper).find('.intEnt-des').css("display","");
                    $(tableWrapper).find('.intEnt-ex').css("display","");
                    $(tableWrapper).css({"width":'100%'});

                    // param wrapper 숨김
                    $(queryParamListWrapper).hide();
                    $(pathParamListWrapper).hide();
                    setTimeout(function(){
                        $(rightPanel).css({"margin-right": '-'+ rpw+'%'});
                    },80);

                    // response 값 리셋
                    var codeWrapper = $(rightPanel).find('[data-role="apiSCD-wrapper"]');
                    var statusCodeEle = $(rightPanel).find('input[name="statusCode"]');
                    $(codeWrapper).removeClass('sc-fail');
                    $(codeWrapper).removeClass('sc-success');
                    $(codeWrapper).addClass('sc-default');

                    // 패널값 리셋
                    init_panel();
                }
            };

            // input 값 세팅
            var setInputVal = function(data){
                var reqData ={};
                $.each(data.apiReq,function(i,r){
                    reqData['name'] = r.name;
                    reqData['intro'] = r.description;
                    reqData['url'] = r.base_path;
                    reqData['method'] = r.method;
                    reqData['statusCode'] = r.statusCode;
                    reqData['bodyType'] = r.bodyType;
                });
                var name = reqData.name;
                var intro = reqData.intro;
                var url = reqData.url;
                var method = reqData.method;
                var statusCode = reqData.statusCode;
                var bodyType = reqData.bodyType;

                // input 값 세팅
                $(nameEle).val(name);
                $(introEle).val(intro);
                if(url) $(urlEle).val(url);
                $(statusCodeEle).val(statusCode);
                $(bodyTypeEle).val(bodyType);

                // 업데이트 method
                if(method) updateMethod(method);
                else updateMethod('GET');

                // 업데이트 bodyType
                if(bodyType) updateBodyType(bodyType);
                else updateBodyType('text');

                var queryParamHtml = getApiParamHtml(data.query);
                var headerParamHtml = getApiParamHtml(data.header);
                var pathParamHtml = getApiParamHtml(data.path);
                var formParamHtml = getApiParamHtml(data.form);

                $(headerParamWrapperSelect).html(headerParamHtml);
                $(queryParamWrapperSelect).html(queryParamHtml);
                $(pathParamWrapperSelect).html(pathParamHtml);
                $(formParamWrapperSelect).html(formParamHtml);


                // body 값 세팅
                var bodyData ={};
                $.each(data.body,function(i,r){
                    bodyData['name'] = r.name;
                    bodyData['uid'] = r.uid;
                    bodyData['text_val'] = r.text_val;
                });

                $(bodyValEle).val(bodyData.text_val);
                $(bodyUidEle).val(bodyData.uid);


            };

            // method 값 업데이트
            var updateMethod = function(method){
                var apiBodyGuide = $(rightPanel).find('[data-role="apiBody-guide"]');
                var apiBodyWrapper = $(rightPanel).find('[data-role="apiBody-wrapper"]');
                // 값 세팅
                $(methodWrapper).text(method);
                $(methodEle).val(method);

                // payloads(body) 세팅
                if(method=='POST'|| method=='PUT'){
                    $(apiBodyGuide).hide();
                    $(apiBodyWrapper).show();
                }else{
                    $(apiBodyGuide).show();
                    $(apiBodyWrapper).hide();
                }
            };

            // bodyType 값 업데이트
            var updateBodyType = function(bodyType){
                // 값 세팅
                $(bodyTypeLabel).text(bodyType);
                $(bodyTypeEle).val(bodyType);

                // payloads(body) 세팅
                if(bodyType =='text'){
                    $(formParamListWrapper).hide();
                    $(bodyTypeTextWrapper).show();
                }else{
                    $(formParamListWrapper).show();
                    $(bodyTypeTextWrapper).hide();
                }
            };

            // input data 가져오기
            var getInputData = function(){
                // 데이타 가져와서 세팅
                var _data = {
                    "linkType":"get-legacyApiParam",
                    "api": api,
                    "req": req
                };
                self.linkServerData(_data); // 예문 가져오기
            };

            // 시작함수
            var startFunc = function(data){
                var role = data.role;
                var req = data.req;
                var api = data.api;
                var url = data.url?data.url:null;
                self.initToolTip();
                if(role=='open'){
                    setCss(data); // css 변경

                    $(apiEle).val(api);
                    $(reqEle).val(req);
                    $(urlEle).val(url);

                    // input data 가져오기
                    getInputData();

                }else if(role=='get'){
                    setInputVal(data);

                }else if(role=='update'){
                    $(reqEle).val(req);

                    // input data 가져오기
                    getInputData();

                }else if(role=='close'){
                    initInputVal(data);
                    setCss(data);
                }
            };

            // header & query 파라미터 container 비우기
            var initInputVal = function(data){
                $(headerParamWrapper).html('');
                $(queryParamWrapper).html('');
                // jsonEditor 초기화
                $(rightPanel).find('#jsonEditor-wrapper').html('');
                $(rightPanel).find('[data-role="result-guide"]').show();

                // 화살표 active 해제
                $(rightPanel).find('[data-role="showHide-queryParam"]').removeClass('active');
                $(rightPanel).find('[data-role="showHide-pathParam"]').removeClass('active');

            };

            // header 파라미터 추가
            var addParam = function(data){
                var ParamInputRowTpl = getApiParamInputRow(data);

                if(data.type=='header') $(headerParamWrapper).append(ParamInputRowTpl);
                else if(data.type=='query') $(queryParamWrapper).append(ParamInputRowTpl);
                else if(data.type=='path') $(pathParamWrapper).append(ParamInputRowTpl);
                else if(data.type=='form') $(formParamWrapper).append(ParamInputRowTpl);

            };

            // base64 encode
            var b64Encode =function(str) {
                 return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function(match, p1){
                     return String.fromCharCode('0x' + p1);
                 }));
            }

            // Start of 파라미터 추가버튼
            $(addParamBtn).off('click').on('click',function(e){
                var target = e.currentTarget;
                var pType = $(target).attr('data-type'); // param 타입

                if(pType =='auth') $(authModal).modal();
                else{
                     var _data = {name: '',val: '',type: pType};
                     addParam(_data);
                }

            });
            // End of 파라미터 추가버튼

            // Start of 파라미터 삭제버튼
            $('body').on('click','[data-role="btn-delParam"]',function(e){
                e.preventDefault();
                var target = e.currentTarget;
                var pType = $(target).attr('data-type'); // param 타입
                var uid = $(target).attr('data-uid'); // param uid
                var this_paramRow = $(target).parent().parent();

                // ele 삭제
                $(this_paramRow).remove();

                // 데이타 삭제
                var data = {linkType: "del-legacyApiParam",uid: uid};
                self.linkServerData(data);

            });
            // End of 파라미터 삭제버튼

            // 쿼리 파라미터 관리패널 보이기/숨기기
            $(rightPanel).find('[data-role="showHide-queryParam"]').off('click').on('click',function(e){
                e.preventDefault();
                $(queryParamListWrapper).toggle();

            });

            // 패스 파라미터 관리패널 보이기/숨기기
            $(rightPanel).find('[data-role="showHide-pathParam"]').off('click').on('click',function(e){
                e.preventDefault();
                $(pathParamListWrapper).toggle();
            });

            // 쿼리 파라미너터리패널 보이기/숨기기
            $(rightPanel).find('[data-role="select-bodyType"]').off('click').on('click',function(e){
                e.preventDefault();
                var target = e.currentTarget;
                var type = $(target).data('type');

                // body type 입력
                $(bodyTypeEle).val(type);
                $(bodyTypeLabel).text(type);

                // 숨김/노출
                $(bodyTypeTextWrapper).toggle();
                setTimeout(function(){
                    $(formParamListWrapper).toggle();
                },10);
            });

            // 전송 데이타 추출 ( 저장, 테스트 발송 시 필요)
            var getSendData = function(){
                var dt = {};
                var qPNameEle = $(rightPanel).find('input[name="query_paramName[]"]');
                var qPValEle = $(rightPanel).find('input[name="query_paramVal[]"]');
                var qPUidEle = $(rightPanel).find('input[name="query_paramUid[]"]');
                var hPNameEle = $(rightPanel).find('input[name="header_paramName[]"]');
                var hPValEle = $(rightPanel).find('input[name="header_paramVal[]"]');
                var hPUidEle = $(rightPanel).find('input[name="header_paramUid[]"]');
                var pPNameEle = $(rightPanel).find('input[name="path_paramName[]"]');
                var pPValEle = $(rightPanel).find('input[name="path_paramVal[]"]');
                var pPUidEle = $(rightPanel).find('input[name="path_paramUid[]"]');
                var fPNameEle = $(rightPanel).find('input[name="form_paramName[]"]');
                var fPValEle = $(rightPanel).find('input[name="form_paramVal[]"]');
                var fPUidEle = $(rightPanel).find('input[name="form_paramUid[]"]');
                dt['api'] = $(apiEle).val();
                dt['req'] = $(reqEle).val();
                dt['name'] = $(nameEle).val();
                dt['description'] = $(introEle).val();
                dt['method'] = $(methodEle).val();
                dt['base_path'] = $(urlEle).val();
                dt['statusCode'] = $(statusCodeEle).val();
                dt['bodyVal'] = $(bodyValEle).val();
                dt['bodyUid'] = $(bodyUidEle).val();
                dt['bodyType'] = $(bodyTypeEle).val();
                dt['qParamName'] = $(qPNameEle).map(function(){return $(this).val()}).get();
                dt['qParamVal'] = $(qPValEle).map(function(){return $(this).val()}).get();
                dt['qParamUid'] = $(qPUidEle).map(function(){return $(this).val()}).get();
                dt['hParamName'] = $(hPNameEle).map(function(){return $(this).val()}).get();
                dt['hParamVal'] = $(hPValEle).map(function(){return $(this).val()}).get();
                dt['hParamUid'] = $(hPUidEle).map(function(){return $(this).val()}).get();
                dt['pParamName'] = $(pPNameEle).map(function(){return $(this).val()}).get();
                dt['pParamVal'] = $(pPValEle).map(function(){return $(this).val()}).get();
                dt['pParamUid'] = $(pPUidEle).map(function(){return $(this).val()}).get();
                dt['fParamName'] = $(fPNameEle).map(function(){return $(this).val()}).get();
                dt['fParamVal'] = $(fPValEle).map(function(){return $(this).val()}).get();
                dt['fParamUid'] = $(fPUidEle).map(function(){return $(this).val()}).get();

                return dt;
            };

            // 저장하기
            $(rightPanel).off('click').on('click','[data-role="btn-saveTest"]',function(e){
                var dt = getSendData();
                var type = $(this).data('type');
                var jsonData = JSON.stringify(dt);

                if(type =='save'){ // 저장
                    if(!dt['name']){
                        alert('API 이름을 입력해주세요');
                        setTimeout(function(){
                            $(nameEle).focus();
                        },10);

                        return false;
                    }else if(dt['statusCode']!='200'){
                        alert('API 전송을 실행하여 정상여부를 체크해주세요');
                        return false;
                    }else{
                        var _data = {linkType: "save-legacyApiParam", apiData: jsonData, type: type };
                        self.linkServerData(_data);
                    }
                }else if(type=='test'){ // 테스트
                    var resultContainer = '[data-role="apiResult-wrapper"]';
                    var jsonEditorContainer = self.options.jsonEditorContainer;
                    var _data = {
                        linkType: "test-legacyApiParam",
                        apiData: jsonData,
                        type: type,
                        resultContainer: resultContainer
                    };
                    self.linkServerData(_data);

                    // loader 출력
                    $(jsonEditorContainer).html('');
                    setTimeout(function(){
                        self.showLoader(resultContainer);
                    },30);

                }

            });


            // Start of auth 값 추가
            $(settingAuthBtn).off('click').on('click',function(){
                var headerParamRow = $(rightPanel).find('[data-role="header-paramRow"]');
                var userName = $(userNameEle).val();
                var pw = $(pwEle).val();
                var has_Row = true; // 빈 필드 row 가 있는지 여부 값

                if(!userName||!pw){
                   alert('username, password 값 모두 입력해주세요');
                   return false;
                }else{
                   var auth_name = 'Authorization';
                   var auth_val = 'Basic '+ b64Encode(userName+':'+pw);
                }

                if(headerParamRow){
                    $.each(headerParamRow,function(){
                        var nameEle = $(this).find('[name="header_paramName[]"]');
                        var valueEle = $(this).find('[name="header_paramVal[]"]');
                        var nameEleVal = $(nameEle).val();
                        var valueEleVal = $(valueEle).val();

                        // auth 필드가 있는 경우
                        if(nameEleVal==auth_name){
                            $(valueEle).val(auth_val);
                            has_Row = false;
                            return false;

                        }else{
                            if(nameEleVal=='' && valueEleVal ==''){
                                $(nameEle).val(auth_name);
                                $(valueEle).val(auth_val);
                                has_Row = false;
                                return false;
                            }
                        }
                    });
                }

                // 빈 필드 row 가 없는 경우
                if(has_Row){
                    var headerData = {name: auth_name, val: auth_val, type: 'header'};
                    addParam(headerData);
                }

                // 모달창 닫기
                setTimeout(function(){
                    $(authModal).modal('hide');

                },10);
            })
            // End of auth 값 추가

            // auth 패스워드 보이기/숨기기
            $(chk_showPw).on('click',function(e){
                var target = e.currentTarget;
                var pwType = $(pwEle).attr('type');
                setTimeout(function(){
                    if(pwType =='password') $(pwEle).attr("type","text");
                    else $(pwEle).attr("type","password");
                },20);

            });

            // method 변경 이벤트
            $(rightPanel).find('[data-role="method-item"]').on('click',function(e){
                var target = e.currentTarget;
                var method = $(target).data('method');

                // 업데이트 method
                updateMethod(method);

            });


            // name input 포커싱 in 이벤트
            $(nameInput).on('focusin',function(e){
                var target = e.currentTarget;
                var nameEleWrapper = $(target).parent();
                $(nameEleWrapper).css("border-bottom","solid 1px #"+self.options.highlightColor);
            });

            // name input 포커싱 out 이벤트
            $(nameInput).on('focusout',function(e){
                var target = e.currentTarget;
                var nameEleWrapper = $(target).parent();
                $(nameEleWrapper).css("border-bottom","solid 1px #"+self.options.normalizeColor);
            });

            // 최초시작
            startFunc(data);

        },
        // End of  api 패널 관련 ######################################################################################

        openRightPanel: function(e){
            var target = e.currentTarget;
            var mod = $(target).data('mod');
            var pageItem = $(target).parent().parent().parent().find('[data-role="page-item"]');
            var data = $(target).data();
            if(mod=='item'){
                $.each(pageItem,function(){
                   $(this).removeClass('active');
                });
                $(target).addClass('active');
                data['name'] = $(target).attr('data-name'); // 갱신되도록

            }
            // 저장 버튼 data-mod > mod 값 적용
            $('[data-role="btn-save"]').attr("data-mod", mod);

            data['role'] ='open';

            this.controlRightPanel(data);
        },

        closeRightPanel: function(){
            var data = {role: "close"}
            this.controlRightPanel(data);
            this.controlSetApiPanel(data);
        },

        // 같은 name 체크 > type, checkName 넘어온다.
        checkSameName: function(data){
            var self = this;
            var iExPN = this.options.rightPanel;
            var entityEx = $(iExPN).find('[name="entityEx[]"]').map(function(){return $(this).val()}).get();
            var intentEx = $(iExPN).find('[name="intentEx[]"]').map(function(){return $(this).val()}).get();
            var is_same = false;
            var type = data.type;
            var checkUid = data.checkUid;
            var checkName = data.checkName;

            // type 별 > nameArray 분기
            if(type=='entity') itemArray = this.entity;
            else if(type=='intent') itemArray = this.intent;

            $.each(itemArray,function(key,item){
                var name = item.name;
                var uid = item.uid;
                if(name==checkName && uid != checkUid) is_same = true;
            });

            return is_same;
        },

        // intentSet 페이지 > 엔터티 저장
        updateIntentSet: function(data){
            var self = this;
            var iExPN = this.options.rightPanel;
            var intentNameEle = $(iExPN).find('[name="item_name"]');
            var intent =  $(iExPN).find('[name="item_uid"]').val();
            var intentName = $(intentNameEle).val();
            var iEx_uid = $(iExPN).find('[name="intentEx_uid[]"]').map(function(){return $(this).val()}).get();
            var iEx_val = $(iExPN).find('[name="intentEx[]"]').map(function(){return $(this).val()}).get();
            var iEx_syn = $(iExPN).find('[name="intentEx_synonyms[]"]').map(function(){return $(this).val()}).get();
            var sys_intent = $(iExPN).find('input[name="chk_sys"]').prop("checked");
            var callIntent = this.options.callIntent;

            if(intentName){
                var dd = {type: "intent", checkName: intentName, checkUid: intent};
                var is_sameName = this.checkSameName(dd);
                if(is_sameName){
                    alert(callIntent+'명이 이미 존재합니다.');
                    setTimeout(function(){
                        $(intentNameEle).focus();
                    },100);

                }else{
                    var _data = {
                        linkType:"save-intent",
                        intent: intent,
                        intentName: intentName,
                        iEx_uid: iEx_uid,
                        iEx_val: iEx_val,
                        linkMod: data.mod,
                        sys_intent: sys_intent
                    };
                    this.linkServerData(_data);
                }

            }else{
                alert(callIntent+'명을 입력해주세요.');
                setTimeout(function(){
                    $(intentNameEle).focus();
                },100);
            }

        },

        // entitySet 페이지 > 엔터티 저장
        updateEntitySet: function(data){
            var self = this;
            var iExPN = this.options.rightPanel;
            var entityNameEle = $(iExPN).find('[name="item_name"]');
            var entity =  $(iExPN).find('[name="item_uid"]').val();
            var entityName = $(entityNameEle).val();
            var iEx_uid = $(iExPN).find('[name="entityEx_uid[]"]').map(function(){return $(this).val()}).get();
            var iEx_val = $(iExPN).find('[name="entityEx[]"]').map(function(){return $(this).val()}).get();
            var iEx_syn = $(iExPN).find('[name="entityEx_synonyms[]"]').map(function(){return $(this).val()}).get();
            var callEntity = this.options.callEntity;

            if(entityName){
                var dd = {type: "entity", checkName: entityName,checkUid: entity};
                var is_sameName = this.checkSameName(dd);
                if(is_sameName){
                    alert(callEntity+'명이 이미 존재합니다.');
                    setTimeout(function(){
                        $(entityNameEle).focus();
                    },100);

                }else{
                    var _data = {
                        linkType:"save-entity",
                        entity: entity,
                        entityName: entityName,
                        iEx_uid: iEx_uid,
                        iEx_val: iEx_val,
                        iEx_syn: iEx_syn,
                        linkMod: data.mod
                    };
                    this.linkServerData(_data);
                }

            }else{
                alert(callEntity+'명을 입력해주세요.');
                setTimeout(function(){
                    $(entityNameEle).focus();
                },100);
            }

        },

        controlRightPanel: function(data){
            var self = this;
            var mod = data.mod?data.mod:'item';
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
            var delEle = $(rightPanel).find('[data-role="del-item"]');// delete intent btn
            var exWrapperEle = $(rightPanel).find(exWrapper); //
            var callItem = type=='entity'?this.options.callEntity:this.options.callIntent;

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
            };

            // 예문 가져오기
            var getItemEx = function(_data){
                var uid = _data.uid?_data.uid:$(uidEle).val();
                var type = _data.type;
                var resultContainer =  exWrapper;
                var data = {"linkType":"getItemEx","uid": uid,"type": type,"resultContainer":resultContainer};
                self.linkServerData(data); // 예문 가져오기

                setTimeout(function(){
                    self.initToolTip();
                },100);

            };


            // 예문 저장하기
            var saveItem = function(_data){
                var uid = _data.uid;
                var type = _data.type;

                if(type=='entity') self.updateEntitySet(_data);
                else if(type=='intent') self.updateIntentSet(_data);

                // new 일때는 갸져오지 않는다.
                if(mod =='item'){
                    setTimeout(function(){
                        getItemEx(_data); // 다시 가져온다.
                    },100);
                }


            };

            // 예문 삭제후 예문이 1개도 없는 경우  no-data 출력
            var checkNoData = function(data){
                var type = data.type;
                var itemEx = $(exWrapperEle).find('.'+type+'Ex-item');
                var is = 0;
                var noDataTpl = self.template['no_data'];
                noDataTpl = noDataTpl.replace(/\{\$noData_msg}/gi,self.options.noEntityExMsg);
                $.each(itemEx,function(i,ele){
                    if(ele) is++;
                });
                // 예문 없는 경우
                if(is==0){
                    $(exWrapperEle).html(noDataTpl);
                }
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
                $(delEle).attr("data-uid",uid);
                if($(rightPanel).find('#chk_sys_wrap').length > 0 && $(uidEle).val() == "") {
                    $(rightPanel).find('#chk_sys_wrap').show();
                }
            };

            // 시작함수
            var startFunc = function(data){
                var role = data.role;
                if(role=='open'){
                    setInputVal(data); // uid, name 값 세팅
                    getItemEx(data); // 예문 세팅
                    setTimeout(function(){
                         setCss(data); // css 변경
                    },200);

                }else if(role=='close'){
                    setCss(data);
                }
            }
            // 예문폼 추가
            var addItemEx = function(data){
                var callEntity = self.options.callEntity;
                var callIntent = self.options.callIntent;
                var itemExTpl = self.template[data.type+'_row'];
                itemExTpl = itemExTpl.replace(/\{\$iEx_uid}/gi,'');
                itemExTpl = itemExTpl.replace(/\{\$iEx_val}/gi,'');
                itemExTpl = itemExTpl.replace(/\{\$iEx_syn}/gi,'');
                itemExTpl = itemExTpl.replace(/\{\$callEntity}/gi,callEntity);
                itemExTpl = itemExTpl.replace(/\{\$callIntent}/gi,callIntent);

                $(exWrapperEle).find('.no-data').remove();
                $(exWrapperEle).prepend(itemExTpl).promise().done(function() {
                    if(data.type == 'entityEx') self.entityTagit($(exWrapperEle));
                });
            };

            // 엔터티 > 예시 단어 삭제 이벤트
            $(rightPanel).off('click').on('click','[data-role="del-entityEx"]',function(e){
                var target = e.currentTarget;
                var uid = $(target).attr('data-uid');
                var entity = $(uidEle).val(); // 최상단 글로벌 변수
                var entityName = $(nameEle).val(); // 최상단 글로벌 변수
                var data = {linkType: 'delete-entityEx',entity: entity,entityName: entityName,entityEx: uid};
                self.linkServerData(data);

                if(uid){
                    // 해당 엘리먼트 지우기
                    $(rightPanel).find('[data-role="entityEx-item-'+uid+'"]').remove();
                }else{
                    $(this).parent().parent().remove();
                }

                setTimeout(function(){
                    var dd = {type: 'entity'};
                    checkNoData(dd);
                },100);

            });

            // 인텐트 > 예시문장 삭제 이벤트
            $(rightPanel).on('click','[data-role="del-intentEx"]',function(e){
                var target = e.currentTarget;
                var uid = $(target).attr('data-uid');
                var intent = $(uidEle).val(); // 최상단 글로벌 변수
                var intentName = $(nameEle).val(); // 최상단 글로벌 변수
                var data = {linkType: 'delete-intentEx',intent: intent,intentName: intentName,intentEx: uid};
                self.linkServerData(data);

                if(uid){
                    // 해당 엘리먼트 지우기
                    $(rightPanel).find('[data-role="intentEx-item-'+uid+'"]').remove();
                }else{
                    $(this).parent().parent().remove();
                }

                setTimeout(function(){
                    var dd = {type: 'intent'};
                    checkNoData(dd);
                },100);

            });

             // '관련 예시문장' 추가 이벤트
            $(rightPanel).find('[data-role="btn-save"]').off('click').on('click',function(e){
                var target = e.currentTarget;
                var data= $(target).data();
                saveItem(data);
            });

             // '관련 예시문장' 추가 이벤트
            $(rightPanel).find('[data-role="add-itemEx"]').off('click').on('click',function(e){
                var target = e.currentTarget;
                var data= $(target).data();
                addItemEx(data);
            });

            // item (인텐트/엔터티) 삭제 이벤트
            $(rightPanel).find('[data-role="del-item"]').off('click').on('click',function(e){
                var target = e.currentTarget;
                var type = $(target).data('type');
                var uid = $(target).attr('data-uid');
                if(type == 'entity') var data = {linkType: 'delete-entity',uid: uid};
                else if(type =='intent') var data = {linkType: 'delete-intent',uid: uid};
                self.linkServerData(data);
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

            // name input keyup 이벤트 > 변경 내용 page-item 에 적용
            $(nameEle).on('keyup',function(e){
                var name = $(this).val();
                var tableWrapper = $('[data-role="table-wrapper"]');
                var pageItem = $(tableWrapper).find('[data-role="page-item"]');
                var mark = type=='entity'?'@':'#';
                var dd = {type: type, checkName: name,checkUid: uid};
                var is_sameName = self.checkSameName(dd);
                if(is_sameName){
                    alert(callItem+'명이 이미 존재합니다.');
                    setTimeout(function(){
                        $(nameEle).val('');
                        $(nameEle).focus();
                    },10);
                    $.each(pageItem,function(i,ele){
                        if($(ele).hasClass('active')){
                            var oname = $(ele).attr('data-oname');
                            $(ele).attr('data-name',oname);
                            $(ele).text(mark+oname);
                        }
                    });
                }else{
                    $.each(pageItem,function(i,ele){
                        if($(ele).hasClass('active')){
                            $(ele).attr('data-name',name);
                            $(ele).text(mark+name);
                        }
                    });
                }

            });

            // ex-Name input keyup 이벤트 > 중복 체크
            $(rightPanel).off('blur').on('blur','[data-role="ex-name"]',function(e){
                var that = $(this);
                var this_name = $(this).val();
                var this_uid = $(this).attr("data-uid");
                var exNameEle = $(rightPanel).find('[data-role="ex-name"]');
                $.each(exNameEle,function(key,ele){
                    var name = $(ele).val();
                    var uid = $(ele).attr("data-uid");
                    if(this_name==name && this_uid!=uid){
                        alert("이미 등록된 내용입니다.");
                        setTimeout(function(){
                            $(that).val('');
                        },20);
                    }
                });

            });

            // entityEx synonyms > 중복 체크
            $(rightPanel).on('blur','[data-role="entityEx-syn"]',function(e){
                var that = $(this);
                var syn = $(this).val();
                var exNameEle = syn.split(',');
                var newVal_label = '';
                var newVal_arr = [];
                $.each(exNameEle,function(i,val){
                    var name = $.trim(val);
                    if($.inArray(name,newVal_arr)=== -1){
                        newVal_arr.push(name);
                        newVal_label+= name+',';
                    }else{
                        alert('이미 등록된 내용입니다.');
                        $(that).val(newVal_label);
                    }
                });

            });

            // 최초시작
            startFunc(data);

        },

        clearInput: function(text) {
            text = text.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, "");
            return $.trim(text);
        },

        getFormObj: function(form){
            var self = this;
            var obj = {};
            var arr = $(form).serializeArray();
            var names = (function(){
                var n = [],
                    l = arr.length - 1;
                for(; l>=0; l--){
                    n.push(arr[l].name);
                }

                return n;
            })();

            $(form).find('input[type="checkbox"]:not(:checked)').each(function(){
                if($.inArray(this.name, names) === -1){
                    arr.push({name: this.name, value: 'off'});
                }else{
                    arr.push({name: this.name, value: 'on'});
                }
            });

            $.each(arr,function(){
                var value = self.clearInput(this.value);
                obj[this.name] = value;
            });

            return obj;
        },

        // bot 기본정보 업데이트
        updateBot: function(e){
            var form = this.options.configBotForm;
            var data = this.getFormObj(form);
            data['linkType'] = "updateBot";
            e.preventDefault();

            if(this.page == 'adm/config') {
                if(data.name==''){
                    alert('챗봇명을 입력해주세요'); return false;
                }
                if(data.callno){
                    if(!isValidPhoneNumber(data.callno)) {
                        alert('콜봇번호가 정확하지 않습니다.'); return false;
                    }
                }
            }

            if(this.page == 'adm/skin') {
                // 챗봇 버튼 위치
                var regex = /^[0-9]+(\%|px|em|rem)/i;
                if(!regex.test(data.pc_btn_bottom) || !regex.test(data.pc_btn_right)) {
                    alert('PC웹의 채팅 버튼 하단, 우측 위치는 숫자와 단위(px, %)까지 입력해주세요'); return false;
                }
                if(!regex.test(data.m_btn_bottom) || !regex.test(data.m_btn_right)) {
                    alert('모바일웹의 채팅 버튼 하단, 우측 위치는 숫자와 단위(px, %)까지 입력해주세요'); return false;
                }
            }

            if(this.page == 'adm/operation') {
                if(data['use_reserve'] == 'on') {
                    if(data['reserve_category'] == '') {
                        alert('예약유형을 선택해주세요.'); return false;
                    }
                    if(data['reserve_manage'] == '') {
                        alert('예약 관리 형태를 선택해주세요.'); return false;
                    }
                    if(data['reserve_manage'] == 'onda') {
                        if(data['reserve_onda_suburl'] == '') {
                            alert('온다 부킹엔진 SubURL을 입력해주세요.'); return false;
                        }
                        /*
                        if(data['reserve_onda_vendor'] == '') {
                            alert('온다 벤더 ID를 입력해주세요.'); return false;
                        }
                        if(data['reserve_onda_token'] == '') {
                            alert('온다 억세스 토큰을 입력해주세요.'); return false;
                        }
                        */
                    }
                    if(data['reserve_manage'] == 'self') {
                        if(data['reserve_api'] == '') {
                            alert('예약 API 주소를 입력해주세요.'); return false;
                        } else {
                            if(!isValidHttpUrl(data['reserve_api'])) {
                                alert('올바른 예약 API 주소를 입력해주세요.'); return false;
                            }
                        }
                    }
                    if(data['reserve_manage'].indexOf('bottalks') > -1) data['reserve_api'] = '';
                }

                if(data['use_shopapi'] == 'on') {
                    if(data['shopapi_vendor'] == '') {
                        alert('쇼핑몰 벤더를 선택해주세요.'); return false;
                    }
                    if(data['shopapi_domain'] == '') {
                        alert('쇼핑몰 주소를 입력해주세요.'); return false;
                    } else {
                        if(!isValidHttpUrl(data['shopapi_domain'])) {
                            alert('올바른 쇼핑몰 주소를 입력해주세요.'); return false;
                        }
                    }
                    if(data['shopapi_vendor'] == 'cafe24') {
                        if(data['shopapi_mall_id'] == '') {
                            alert('쇼핑몰 ID를 입력해주세요.'); return false;
                        }
                        if(data['is_token'] != 'true') {
                            alert('접속 토큰을 획득해주세요.'); return false;
                        }
                    }
                    if(data['shopapi_vendor'] == 'godo') {
                        if(data['shopapi_client_key'] == '') {
                            alert('Client Key를 입력해주세요.'); return false;
                        }
                    }
                }

                if(data['use_syscheckup'] == 'on') {
                    if(data['syscheckup_start'] == '') {
                        alert('시스템 점검 시작 시간을 입력해주세요.'); return false;
                    }
                    if(data['syscheckup_end'] == '') {
                        alert('시스템 점검 종료 시간을 입력해주세요.'); return false;
                    }
                    if(data['syscheckup_msg'] == '') {
                        alert('시스템 점검 메세지를 입력해주세요.'); return false;
                    }
                }

                if(data['use_cschat'] == 'on') {
                    if(data['cschat_api'] == '') {
                        alert('채팅상담 솔루션을 선택해주세요.'); return false;
                    }
                }

                const quickMenu = [];

                // 20240426 spikecow
                $(".guick_config").find('input:checked').each(function(index){
                    quickMenu.push($(this).val());
                });

                if(quickMenu.indexOf('center') > -1){

                    if($('input[name="quick_center_phone"]').val() === ''){
                        alert('고객센터 전화번호를 입력해주세요.');
                        return false;
                    }
                    else{
                        quickMenu.splice(quickMenu.indexOf('center'),1,'center-' + $('input[name="quick_center_phone"]').val().replaceAll('-',''));
                    }
                }

                data['menu_quick'] = quickMenu.join(':');
            }

            this.linkServerData(data);
        },

        openReserveConfig: function(e) {
            var target = e.currentTarget;
            if($(target).is(':checked')) $('.reserve_config').show();
            else  $('.reserve_config').hide();
        },
        openReserveAPI: function(e) {
            var target = e.currentTarget;
            if($(target).val() == 'self') {
                $('.reserve_adm').hide();
                $('.reserve_onda').hide();
                $('.reserve_api').show();
            } else if($(target).val() == 'onda') {
                $('.reserve_adm').show();
                $('.reserve_onda').show();
                $('.reserve_api').hide();
            } else {
                $('.reserve_adm').show();
                $('.reserve_onda').hide();
                $('.reserve_api').hide();
            }
        },

        openShopApiConfig: function(e) {
            var self = this;
            var target = e.currentTarget;
            if($(target).is(':checked')) $('.shopapi_config').show();
            else  $('.shopapi_config').hide();
        },
        getShopApiVendor: function(e) {
            var self = this;
            var target = e.currentTarget;
            $('.shopapi_key').hide();
            $('.shopapi_key_'+$(target).val()).show();
        },
        getShopAccessToken: function(e) {
            var self = this;
            var target = e.currentTarget;

            var mall_id = $.trim($('.shopapi_mall_id').val());
            if(mall_id == '') {
                alert('쇼핑몰 ID를 입력해주세요.'); return false;
            }
            var mall_address = $.trim($('.shopapi_mall_address').val());
            if(mall_address == '') {
                alert('쇼핑몰 주소를 입력해주세요.'); return false;
            }
            var mall_vendor = $('input:radio[name=shopapi_vendor]:checked').val();

            var openUrl = rooturl+'/?r='+raccount+'&m='+self.module+'&a=oauth_login';
            var mode = $(target).attr('mode');
            if(mode == 'get_token') {
                var form = $('<form name="frmToken" method="post" target="token" action="'+openUrl+'"></form>');
                form.append($('<input/>', {type: 'hidden', name: 'vendor', value:self.vendor}));
                form.append($('<input/>', {type: 'hidden', name: 'bot', value:self.bot}));
                form.append($('<input/>', {type: 'hidden', name: 'mode', value:mode}));
                form.append($('<input/>', {type: 'hidden', name: 'mall_vendor', value:mall_vendor}));
                form.append($('<input/>', {type: 'hidden', name: 'mall_id', value:mall_id}));
                form.append($('<input/>', {type: 'hidden', name: 'mall_address', value:mall_address}));
                $('body').append(form);
                var pop = window.open("", "token", "toolbar=no,scrollbars=yes,resizable=no,width=980,height=650");
                form.submit();
                form.remove();
            } else {
                $.post(openUrl, {
                    vendor: self.vendor, bot: self.bot, mode: mode, mall_id: mall_id
                }, function(data) {
                    if(!data.result) {
                        alert('접속토큰 갱신 실패! 접속토큰을 새로 획득해주세요.');
                        $('#shopapi_get_token').show();
                        $('#shopapi_get_retoken').hide();
                        return false;
                    } else {
                        sh_data = {msg: "접속토큰이 갱신되었습니다."};
                        self.showToast(sh_data);
                        $(target).hide();
                    }
                },'json');
            }
        },

        openSysCheckup: function(e) {
            var target = e.currentTarget;
            if($(target).is(':checked')) $('.syscheckup_config').show();
            else  $('.syscheckup_config').hide();
        },

        openChatGPT: function(e) {
            var target = e.currentTarget;
            if($(target).is(':checked')) $('.gpt_config').show();
            else  $('.gpt_config').hide();
        },

        openQuickMenu: function(e) {
            var target = e.currentTarget;
            if($(target).is(':checked')) {
                $('.guick_config').show();
            }
            else{
                $('.guick_config').find('input').prop('checked', false);
                $('.guick_config').find('input[name="quick_center_phone"]').val('');
                $('.guick_config').hide();
            }
        },


        sysCheckupDate: function(e) {
            var target = e.currentTarget;
            $('#syscheckup_start').datetimepicker({
                locale: 'ko', format: 'YYYY-MM-DD HH:mm', sideBySide: true
            });
            $('#syscheckup_end').datetimepicker({
                locale: 'ko', format: 'YYYY-MM-DD HH:mm', sideBySide: true, useCurrent: false
            });
            $('#syscheckup_start').on("dp.change", function (e) {
                $('#syscheckup_end').data("DateTimePicker").minDate(e.date);
            });
            $('#syscheckup_end').on("dp.change", function (e) {
                $('#syscheckup_start').data("DateTimePicker").maxDate(e.date);
            });
        },
        openBargein: function(e) {
            var target = e.currentTarget;
            if($(target).val() == 'voice') $('.bargein_config').show();
            else $('.bargein_config').hide();
        },

        // ######################################################### import data
        importData: function(e){
            $('[data-role=importData-inputFile]').remove();
            var target = e.currentTarget;
            var type = $(target).data('type');
            var mod = $(target).data('mod');
            var sys = $(target).data('sys') != undefined ? $(target).data('sys') : "";
            var parent = $(target).parent();
            var fileInput = $('<input/>', {
                type: 'file',
                name: 'importFile',
                style: 'display:none',
                'data-role': 'importData-inputFile',
                'data-type': type,
                'data-mod': mod,
                'data-sys': sys
            });

            $(fileInput).appendTo(parent).click();
        },

        importFileInputChanged: function(e){
            var self = this;
            var sescode = this.sescode;
            var target = e.currentTarget;
            var type = $(target).data('type');
            var mod = $(target).data('mod');
            var sys = $(target).data('sys');
            var file = target.files[0];
            var data = new FormData();
            data.append("file",file); // 가상의 "file" 이라는 오브젝트를 만들어서 전송한다.
            data.append("linkType",(type == 'chat_TSTest' ? type : "importData"));
            data.append("sescode",sescode);
            data.append("type",type);
            data.append("mod",mod);
            data.append("sys",sys);
            data.append("vendor",this.vendor);
            data.append("bot",this.bot);

            if (!file.name.match(/\.(xls|xlsx)$/i)) {
                alert("엑셀파일만 등록가능합니다."); return false;
            }
            if (file.size > (1024*1024*2)) {
                alert("업로드 파일의 용량은 2M 이하여야 합니다."); return false;
            }

            $(".preloader").css("background", "transparent").show();

            $.ajax({
                type: "POST",
                url: rooturl+'/?r='+raccount+'&m='+this.module+'&a=do_VendorAction',
                data:data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    checkLogCountdown();
                    var result = $.parseJSON(response);
                    if(result !== null && typeof result === 'object' && result[0] == -1) {
                        if(result[1] == 401) {
                            goFrontSite();
                        } else {
                            $(".preloader").hide();
                            alert(result[1]);
                        }
                    } else {
                        if(type == 'chat_TSTest') {
                            $("#chatTestResult tbody").remove();
                            $("#chatTestResult").append(result.html);
                            $("[data-role='export-data']").attr("data-file", result.excel).show();
                            $(".preloader").hide();
                        } else {
                            location.reload();
                        }
                    }
                }
            });
        },
        // ######################################################### import data


        // ######################################################### 이미지 업로드
        uploadImg: function(e){
            $('[data-role=upload-inputFile]').remove();
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

        reset_botAvatar: function (e) {
            const _parentSpan = e.currentTarget.parentNode;
            const _parentSpanSameDepthHiddenInput = _parentSpan.parentNode.querySelector("[data-role='img_url']");
            const currentPageUrl = window.location.href;
            let _defaultBotAvatarImg;

            if (currentPageUrl.includes("adm/skin")){
                _defaultBotAvatarImg = "/_core/skin/images/btn_chatbot.png";
            } else { // adm/config
                _defaultBotAvatarImg = "/_core/skin/images/sender_ico_default.png";
            }

            _parentSpan.style.backgroundImage = `url(${_defaultBotAvatarImg})`;

            if (_parentSpanSameDepthHiddenInput) {
                _parentSpanSameDepthHiddenInput.value = _defaultBotAvatarImg;
            }

            $(e.currentTarget).hide();

            return false;
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

            if (!file.name.match(/\.(jpg|jpeg|gif|png)$/i)) {
                alert("이미지 포맷(JPG, GIF, PNG)만 등록가능합니다."); return false;
            }
            if (file.size > (1024*1024*2)) {
                alert("업로드 파일의 용량은 2MB 이하여야 합니다."); return false;
            }

            $.ajax({
                type: "POST",
                url: rooturl+'/?r='+raccount+'&m='+this.module+'&a=do_VendorAction',
                data:data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    checkLogCountdown();
                    var result = $.parseJSON(response);
                    if(result !== null && typeof result === 'object' && result[0] == -1) {
                        if(result[1] == 401) {
                            goFrontSite();
                        } else {
                            alert(result[1]);
                        }
                    } else {
                        var code=result[0];
                        if(code=='100') // code 값이 100 일때만 실행
                        {
                            const btnResetBotAvatar = preview_ele.find('[data-role="reset_botAvatar"]');
                            if (btnResetBotAvatar.length !== 0) {
                                btnResetBotAvatar.show();
                            }

                             var source = result[1];// path + tempname
                             var upuid = result[2]; // upload 테이블 저장 uid
                             $(imgUrl_ele).val(source);
                             $(preview_ele).css({
                                "background-image":"url('"+source+"')",
                                "background-repeat":"no-repeat",
                                "background-position":"center center",
                                "background-size":($(preview_ele).hasClass('chatLogo') ? "auto" : "cover")
                            });
                             setTimeout(function(){
                                 $(target).remove(); // 해당 input file 삭제
                             },10)

                        } else {
                            alert(result[1]);
                        }
                    }

                }
            });
        },
        // ######################################################### 이미지 업로드

        // 초기화 함수들
        initFunc: function(){
             $('[data-toggle=tooltip]').tooltip();

             // tooltip 초기화
             $('body').tooltip({
                 selector: '[data-tooltip=tooltip]',
                 container: 'body'
            });
        },

        // tooltip 초기화
        initToolTip: function(){
            $('body').find('.tooltip').remove();
        },

        // 기본 template 가져와서 세팅
        getTemplate: function(){
            var data = {"linkType":"getTemplate"};
            this.linkServerData(data);
        },

        copyToClipBoard: function(e){
            var self = this;
            var target = e.currentTarget;
            //var clipboard = new Clipboard(target);
            var notify_container = $(target).data('container');
            var msg = $(target).data('feedback');
            var targetText = $(target).attr("data-clipboard-text");

            if(window.clipboardData){
                window.clipboardData.setData("Text", targetText);
            }else{
                var copyText = $('<input/>', {
                    id: 'copyTextEle',
                    value: targetText,
                });
                $(copyText).appendTo('body');

                copyText.select();
                document.execCommand("Copy");

                setTimeout(function(){
                    $(copyText).remove();
                },50);

            }
            self.showNotify(notify_container,msg);

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
            this.bot = this.options.bot; // 챗봇 uid
            this.botId = this.options.botId;
            this.undelegateEvents(); // comment box 엘리먼트들 이벤트 바인딩 off
            this.delegateEvents(); // comment box 엘리먼트들 이벤트 바인딩 on
            this.callIntent = this.options.callIntent;
            this.callEntity = this.options.callEntity;
            this.sescode = this.options.sescode;
            this.page = this.options.page;

            if(this.bot) {
                // 템플릿 가져오기
                if($('#tbl-intentSet').length > 0 || $('#tbl-entitySet').length > 0 || $('#tbl-legacyList').length > 0) {
                    this.getTemplate();
                }

                // 초기 데이타(intent,entity,nodeList) 세팅
                //this.initData();
            }

            // 초기함수 호출
            this.initFunc();
        },

          // intent,entity,nodeList ...
        initData: function(){
           var data = {linkType: "initData"};
           this.linkServerData(data);
        },

        // 채널 세팅 모달 오픈
        openSettingChannelModal: function(e){
            e.preventDefault();
            var self = this;
            var target = e.currentTarget;
            var settingChannelModal = this.options.settingChannelModal;
            var sns = $(target).data('sns');
            var data = {linkType: 'open-settingChannelModal',sns: sns};
            this.linkServerData(data);

            setTimeout(function(){
                var guide = self.linkServerContent; // ajax content 값을 글로벌하게 세팅한 후 공통으로 사용한다.
                $(settingChannelModal).find('[data-role="settingChannelContent"]').html(guide).promise().done(function() {
                    //var clipboard = new Clipboard("[data-role=clipboard-copy]");
                });
                $(settingChannelModal).find('[data-role="save-channelSettings"]').attr("data-sns",sns); // 저장버튼 data-sns 속성값에 sns 저장

                $(settingChannelModal).modal();
            },100);

        },

        // 채널 세팅모달 내용 저장
        saveChannelSettings: function(e){
            e.preventDefault();
            var self = this;
            var target = e.currentTarget;
            var settingChannelModal = this.options.settingChannelModal;
            var sns = $(target).attr('data-sns');

            var data = {linkType: 'save-channelSettings',sns: sns};
            if(sns=='ntok')  data['auth_code'] = $(settingChannelModal).find('input[name="auth_code"]').val();
            else if(sns=='line'){
                data['channel_id'] = $(settingChannelModal).find('input[name="channel_id"]').val();
                data['channel_secret'] = $(settingChannelModal).find('input[name="channel_secret"]').val();
                data['access_token'] = $(settingChannelModal).find('textarea[name="access_token"]').val();

            }else if(sns =='fb'){
                data['verify_token'] = $(settingChannelModal).find('input[name="verify_token"]').val();
                data['access_token'] = $(settingChannelModal).find('textarea[name="access_token"]').val();

            }else if(sns = 'botks'){
                var apiForm = $('[data-role="settingsApiForm"]');
                data['client_secret'] = $(apiForm).find('[name="client_secret"]').val();
                data['access_token'] = $(apiForm).find('[name="access_token"]').val();
            }

            this.linkServerData(data);

            setTimeout(function(){
                var msg = self.linkServerContent; // ajax content 값을 글로벌하게 세팅한 후 공통으로 사용한다.
                if(msg =='OK'){
                    var data = {msg: "채널설정이 저장되었습니다."}
                    self.showToast(data);
                }
            },100);

        },

        // jsonEditor 출력
        showJsonEditor: function(result){
            var json = result.content;
            var statusCode = result.statusCode;
            var rightPanel = this.options.rightPanel;
            var jsonEditor;
            var container = this.options.jsonEditorContainer;//document.getElementById("jsonEditor-wrapper");
            var labelEle = $(rightPanel).find('input[name="nodePath_label"]');
            var pathInputEle = $(rightPanel).find('input[name="node_path"]');
            var setStatusCode = function(code){
                var codeWrapper = $(rightPanel).find('[data-role="apiSCD-wrapper"]');
                var statusCodeEle = $(rightPanel).find('input[name="statusCode"]');
                if(code=='200'){
                    $(codeWrapper).removeClass('sc-default');
                    $(codeWrapper).removeClass('sc-fail');
                    $(codeWrapper).addClass('sc-success');
                }else{
                    $(codeWrapper).removeClass('sc-default');
                    $(codeWrapper).removeClass('sc-success');
                    $(codeWrapper).addClass('sc-fail');
                }
                $(statusCodeEle).val(code);
            };
            options = {
                mode: 'tree',
                onEvent: function(node, e) {
                    if (e.type === 'click') {
                        var obj = jsonEditor.get();
                        var prettyPath = prettyPrintPath(node.path);
                        $(labelEle).val(prettyPath);
                        $(pathInputEle).val(node.path);
                    }
                    function prettyPrintPath(path) {
                        var str = '';
                        for (var i=0; i<path.length; i++) {
                            var element = path[i];
                            if (typeof element === 'number') {
                                str += '[' + element + ']'
                            }else {
                                if (str.length > 0) str += ' > ';
                                str += element;
                            }
                        }
                        return str;
                    }
                },
            };
            $(container).html('');
            $('[data-role="result-guide"]').hide();
            this.hideLoader(); // loader 숨기기

            setTimeout(function(){
                jsonEditor = new JSONEditor(container,options);
                jsonEditor.set(json);
                setStatusCode(statusCode);
            },100);

        },

        // 서버 작업 실행 함수
        linkServerData: function(data){
            var module = this.options.module;
            var self = this;
            var uid = data.uid?data.uid:null;
            var linkType = data.linkType;
            var resultContainer = data.resultContainer?data.resultContainer:null;
            var resultContainer2 = data.resultContainer2?data.resultContainer2:null;
            var resultContainer3 = data.resultContainer3?data.resultContainer3:null;
            var eTarget = data.eTarget?data.eTarget:null;
            var page = this.page;
            var callEntity = this.options.callEntity;
            var linkMod = data.linkMod;
            var itemType = data.type;
            var bottype = this.options.bottype;

            data['vendor'] = this.vendor;
            data['bot'] = this.bot;
            data['botId'] = this.botId;
            data['dialog'] = this.dialog;
            data['page'] = this.page;
            data['bottype'] = bottype;
            // var _data = $.param(data);
            $.ajax({
                url: rooturl+'/?r='+raccount+'&m='+module+'&a=do_VendorAction',
                type: 'post',
                data: data,
                cache: false,
                //success: linkServerCallBack,
                success: function(response){
                    checkLogCountdown();
                    var result=$.parseJSON(response);
                    if(result !== null && typeof result === 'object' && result[0] == -1) {
                        if(result[1] == 401) {
                            goFrontSite();
                        } else {
                            alert(result[1]); location.reload();
                        }
                    } else {
                        if(linkType=='updateBot' && result.content==self.bot){
                            const bottypeKor = "call" === bottype ? "콜봇" : "챗봇";
                            var st_data;
                            if(page =='adm/config') sh_data = {msg: bottypeKor + " 기본설정이 변경되었습니다."};
                            else if(page=='adm/operation') sh_data = {msg: bottypeKor + " 고급설정이 변경되었습니다."};
                            else if(page=='adm/skin') sh_data = {msg: bottypeKor + " 스킨이 변경되었습니다."};
                            else if(page=='adm/monitering') sh_data = {msg: "자주 사용하는 문장이 저장되었습니다."};

                            setTimeout(function(){
                                self.showToast(sh_data);
                            },50);
                            if(page!='adm/monitering'){
                                setTimeout(function(){
                                    location.reload();
                                },1700);
                            }

                        }else if(linkType=='getTemplate'){
                            self.setTemplate(result);
                        }else if(linkType=='getItemEx'){
                            $(resultContainer).html(result.content).promise().done(function() {
                                if(itemType == 'entity') self.entityTagit($(resultContainer));
                            });
                        }else if(linkType =='save-vendorResponse'){
                            if(result.content =='OK'){
                                var data = {msg: "챗봇 응답이 변경되었습니다."};
                                setTimeout(function(){
                                    self.showToast(data);
                                },300);
                            }
                        }else if(linkType =='save-legacySettings'){
                            if(result.content){
                                location.reload();
                            }

                        }else if(linkType =='save-legacyApiParam'){
                            var mod = result.mod;
                            if(mod=='new' || mod=='update'){
                                var msg_arr = [];
                                msg_arr["new"] = "API 가 추가되었습니다.";
                                msg_arr["update"] = "API 정보가 변경되었습니다."

                                var data = {msg: msg_arr[mod]};
                                setTimeout(function(){
                                    self.showToast(data);
                                },300);

                                var _data = {};
                                _data['role'] = 'update';
                                _data['req'] = result.req;
                                self.controlSetApiPanel(_data);
                            }

                        }else if(linkType =='get-legacyApiParam'){
                            var _data = result;
                            _data['role'] = 'get';
                            self.controlSetApiPanel(_data);

                        }else if(linkType =='test-legacyApiParam'){
                            self.showJsonEditor(result);

                        }else if(linkType =='initData'){
                            self.intent = result.intent;
                            self.entity = result.entity;

                        }else if(linkType =='save-entity'){
                            var entity_uid = result.entity_uid;
                            if(entity_uid){
                                if(linkMod =='item'){
                                    var data = {msg: "추가/변경 작업이 적용되었습니다."};
                                    setTimeout(function(){
                                        self.showToast(data);
                                    },300);
                                }else if(linkMod =='new'){
                                    self.closeRightPanel();
                                    setTimeout(function(){
                                        location.reload();
                                    },300);
                                }

                            }
                        }else if(linkType =='save-intent'){
                            var intent_uid = result.intent_uid;
                            if(intent_uid){
                                if(linkMod =='item'){
                                    var data = {msg: "추가/변경 작업이 적용되었습니다."};
                                    setTimeout(function(){
                                        self.showToast(data);
                                    },300);
                                }else if(linkMod =='new'){
                                    self.closeRightPanel();
                                    setTimeout(function(){
                                        location.reload();
                                    },300);
                                }
                            }
                        }else if(linkType=='delete-entityEx' || linkType =='delete-intentEx'){
                            var data = {msg: " 삭제되었습니다."};
                            setTimeout(function(){
                                self.showToast(data);
                            },300);
                        }else if(linkType =='delete-entity' || linkType =='delete-intent' || linkType=='delete-api' || linkType=='delete-req' || linkType =='delete-items'){
                            self.closeRightPanel();
                            setTimeout(function(){
                                location.reload();
                            },300);

                        }else if(linkType =='learning-intent'){
                            self.hideLoader();
                            if(result.fail) {
                                self.showNotify('#cb-footer', result.content);
                            } else {
                                var msg = result.content ? '인텐트 학습이 완료되었습니다.' : '인텐트 학습이 정상적으로 이루어지지 않았습니다.';
                                self.showNotify('#cb-footer', msg);
                            }
                        }else if(linkType =='get-randString'){
                            var form = $('[data-role="settingsApiForm"]');
                            $(form).find('[name="'+result.typeName+'"]').val(result.content); // hidden input
                            $(form).find('[data-role="'+result.typeName+'-wrapper"]').text(result.content); // 보여주는 div
                        }

                        setTimeout(function(){
                             self.initFunc();
                        },200);

                        self.linkServerContent = result.content;
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
            var hideAfter = data.hideAfter?data.hideAfter:1500;
            var hiddenAfter = parseInt(hideAfter)+5;
            var _position = data.position?data.position:'top-center';
            var stack = data.stack;
            $.toast({
                heading: msg,
                position: _position,
                topOffset: 70,
                loaderBg: '#009efb',
                icon: 'success',
                hideAfter: hideAfter,
                stack: stack, // 중복출력 방지
            });

            setTimeout(function(){
               $.toast().reset('all');
            },hiddenAfter);
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

        showLoader: function(container){
            var showContainer = container?container:'body';
            var loader = $('<div/>', {
                id: 'loader-wrapper',
                class: 'ajaxloader'
                }).prependTo(showContainer);
        },

        hideLoader: function(){
            $('body').find('#loader-wrapper').remove();
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

        // '인텐트 학습' 이벤트
        learningIntent: function() {
            var self = this;
            self.showLoader('.table-wrapper');
            setTimeout(function(){
                var data = {linkType: 'learning-intent'};
                self.linkServerData(data);
            },100);
        },

        // 엔터티 Tagit 적용
        entityTagit: function(entityExWrapper) {
            // 벨류 입력
            $(entityExWrapper).find('.tagit_val').tagit({tagLimit:1,allowSpaces:true,singleField:true, placeholderText:'엔터티 벨류 입력',
                afterTagAdded:function(evt, ui) {
                    var aTags=$(this).tagit('assignedTags');
                    if(aTags.length >= 1) $(this).next('ul.tagit').find('.tagit-new').hide();
                },
                afterTagRemoved:function(evt, ui) {
                    var aTags=$(this).tagit('assignedTags');
                    if(aTags.length == 0) $(this).next('ul.tagit').find('.tagit-new').show();
                },
            });
            // 유사어 입력
            $(entityExWrapper).find('.tagit_syn').tagit({allowSpaces:true,placeholderText:'유사어 입력'});

            // 엔터티 특수문자 입력 방지
            $(entityExWrapper).on('keydown','.tagit-new input',function(e){
                var RegExp = /[\{\}\[\]\/?.,;:|\)*~`!^\-_+┼<>@\#$%&\'\"\\\(\=]/gi;
                if(RegExp.test(e.key)) {
                    e.preventDefault();
                }
            });
        },

        // 인텐트, 엔터티 선택 삭제
        deleteItems: function(e) {
            var self = this;
            var target = e.currentTarget;
            var type = $(target).data('type');
            var itemName = (type == 'intent' ? this.options.callIntent : this.options.callEntity);
            var tableWrapper = this.options.tableWrapper;
            var uids = '';
            var uids = $(tableWrapper).find('td.intEnt-chk input[type=checkbox]:checked').map(function(){return $(this).attr('data-uid')}).get().join(',');
            if(uids == '') {
                alert('삭제할 '+itemName+'명을 선택해주세요.'); return false;
            }
            if(!confirm('선택한 '+itemName+'를 삭제하시겠습니까?')) return false;
            var data = {"linkType":"delete-items", "type":type, "uids":uids};
            self.linkServerData(data);
        },
    };

    $.fn.KRE_Admin = function(options) {
        return this.each(function() {
            var admin = Object.create(KRE_Admin);
            $.data(this, 'admin', admin);
            admin.init(options || {}, this);
            this.getAdminObj = function() {
                return admin;
            };
        });
    };
}));

var log_counter;
var counter = parseInt(sm_time);
function checkLogCountdown() {
    if(counter <= 120) return false;
    var mview = false;
    var rcounter = counter;
    if(log_counter) clearInterval(log_counter);
    log_counter = setInterval(function() {
        if(mview == false && rcounter <= 120) {
            mview = true;
            $("#logext").css("top", (($(window).height()-$("#logext").outerHeight(true))/2)+"px");
            $("#logext").show();
        } else if(rcounter <= 0) {
            checkLogLinkServer('out');
        }
        rcounter--;
    },1000);
}
function checkLogLinkServer(mod) {
    $.post(rooturl+'/?r='+raccount+'&m=chatbot&a=do_VendorAction', {
        linkType: 'check_logout', mod: mod
    },function(response) {
        var result=$.parseJSON(response);
        if((result[0] == -1 && result[1] == 401) || result.data == 'out') {
            goFrontSite();
        } else {
            $("#logext").hide();
            checkLogCountdown();
        }
    });
    return false;
}
function goFrontSite() {
    if(window.opener && !window.opener.closed) {
        window.opener.location.href = fronturl;
        window.close();
    } else {
        location.href=fronturl;
    }
}
$(document).on("click", ".btn_logext", function() {
    var mod = $(this).data("mod");
    checkLogLinkServer(mod);
});
//checkLogCountdown();