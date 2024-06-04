
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
}(function($){

    var KRE_Panel = {

        // Instance variables
        // ==================

        $el: null,
        $el_id: null,
        backdrop: null,
        options: {},
        module: null,
        vendor: null, // 업체 
        bot: null, // 챗봇 
        dialog: null, // dialog
        graph: null, // mxGraph 쪽에서 접근(패널 오픈시)할때 가져온다.
        nodeObj: null, // mxgrap 쪽에서 사용할때 필요 
        node: null, // node id: mxGraph 쪽에서 접근(패널 오픈시)할때 가져온다.
        nodeParent: 0, // node parent id : mxGraph 쪽에서 접근(패널 오픈시)할때 가져온다.
        nodeName: null, // node 명칭 : mxGraph 쪽에서 접근(패널 오픈시)할때 가져온다.
        intent: {},
        entity: {},
        vendorEntity: {}, // vendor 전용 entity
        context: {},
        nodeList: {},
        apiList: {},
        template: {}, // 각종 엘리먼트 html 
        activeInput: null, // 현재 포커싱된 인텐트 or 엔터티 설정 input 
        resHeaderActive: null, // 응답그룹 헤더 active 값 
        itemHeaderActive: [], // 멀티 아이템 active header  값 배열   
        resBodyActive: null, // 응답그룹 바디 active 값
        testBotUrl : null, 
        nowActiveMultiMenuTab : null, // 멀티메뉴(버튼, if ..) 탭 중 어떤것이 acitve 인지  
        multiMenuHeaderData: null,
        events: {
           'focus [data-role="input-filterData"]' : 'showFilterBox',
           'keyup [data-role="input-filterData"]' : 'keyUpFilterInput', // 인풋 필터 input 창 keyup (검색용)
           'click .ele-hideFilterBox' : 'hideFilterBox',
           'click [data-role="hide-filterBox"]' : 'hideFilterBox',
           'click [data-role="filter-item"]' : 'showFilterData',           
           'click [data-role="intent-item"]' : 'setFilterData',
           'click [data-role="entity-item"]' : 'setFilterData',
           'click [data-role="add-inputFilter"]' : 'addInputFilter', // 인텐트/엔터티 input 추가 
           'click [data-role="delete-inputFilter"]' : 'delInputFilter', // 인텐트/엔터티 input 삭제
           // if (조건타입) 응답전용 
           'focus [data-role="input-ifFilterData"]' : 'if_showFilterBox',
           'keyup [data-role="input-ifFilterData"]' : 'if_keyUpFilterInput', // 인풋 필터 input 창 keyup (검색용) 
           'click .ele-hideFilterBox' : 'hideFilterBox',
           'click [data-role="hide-ifFilterBox"]' : 'if_hideFilterBox',
           'click [data-role="ifFilter-item"]' : 'if_showFilterData',           
           'click [data-role="ifIntent-item"]' : 'if_setFilterData',
           'click [data-role="ifEntity-item"]' : 'if_setFilterData',
           'click [data-role="add-ifInputFilter"]' : 'if_addInputFilter', // 인텐트/엔터티 input 추가 
           'click [data-role="delete-ifInputFilter"]' : 'if_delInputFilter', // 인텐트/엔터티 input 삭제 

           'click [data-role="btn-addRespond"]' : 'addRespond', // 답변 추가(테스트,이미지 등 타입)
           'click [data-role="btn-delRespond"]' : 'delRespond', // 답변 삭제(테스트,이미지 등 타입)
           'click [data-role="btn-showHideRespond"]' : 'showHideRespond', // 답변 숨김/보이기
           'click [data-role="btn-addResItem"]' : 'addResItem', // 답변 아이템 추가
           'click [data-role="btn-delResItem"]' : 'delResItem', // 답변 아이템 삭제
           //'click [data-role="btn-showHideResItem"]' : 'showHideResItem', //  답변 아이템 숨김/보이기
           'click [data-role="btn-submit"]' : 'submitPanel', // 패널 내용 저장/취소(닫기)
           'click [data-role="btn-uploadImg"]' : 'uploadImg',
           'click [data-role="resHeaderItem"]' : 'changeResActive',
           'click [data-role="itemResHeaderLi"]' : 'changeItemResActive',
           'click [data-role="btn-openChatbotPanel"]' : 'openChatbotPanel',
           'click [data-role="btn-chatBoxTopRight"]' : 'closeChatbotPanel',
           'click [data-role="btn-refreshChatBox"]' : 'refreshChatbotPanel',
           'click [data-role="btn-closeTestLogPanel"]' : 'closeTestLogPanel',
           'click [data-role="add-filterItem"]' : 'addFilterItem', // intent, entity 추가 
           'click [data-role="change-panelMod"]' : 'changePanelMod', // 우상단 버튼 클릭 이벤트 
           // 'click [data-role="intent-panelItem"]' : 'showIntentDetail', // 인텐트 패널 > 인텐트 클릭 이벤트
           'click [data-role="btn-intentPNsubmit"]' : 'intentPNsubmit', // 인텐트 패널 > submit (save or cancel)
           'click [data-role="btn-entityPNsubmit"]' : 'entityPNsubmit', // 엔터티 패널 > submit (save or cancel)
           'click [data-role="ai-recommend"]' : 'openRecommendPanel', // 엔터티 패널 > submit (save or cancel)
           'click [data-role="get-recommendData"]' : 'getRecommendData', // 엔터티 패널 > submit (save or cancel)
           'click [data-role="close-recommendPanel"]' : 'closeRecommendPanel', // 엔터티 패널 > submit (save or cancel)
           'click [data-role="btn-closeDataSetPanel"]' : 'closeDataSetPanel',
           'keyup [data-role="input-nodeName"]' : 'updateNodeName', // 패널창 노드명 변경/업데이트
           'keyup [data-role="rec-keyword"]' : 'keyUpAiRecommend',
           'change [data-role="upload-inputFile"]' : 'fileInputChanged', // 파일업로드 input change
           'change [data-role="addHmenu-byEntity"]' : 'addHmenuByEntity', // 엔터티그룹으로 버튼메뉴 만들기
           'keypress [data-role="rec-keyword"]' : 'enterAiRecommend',
           'blur [data-role="input-EI"]' : 'saveEntityData',
           'focus [data-role="input-EI"]' : 'focusInDataSetInput',
           'click [data-role="select-outputMenu"]' : 'selectOutputMenu', // 아웃풋 메뉴 선택 
           'click [data-role="add-contextRow"]' : 'addContextRow', // context row 추가 
           'click [data-role="del-contextRow"]' : 'delContextRow', // context row 삭제  
           'click [data-role="add-multiMenuContextRow"]' : 'addMultiMenuContextRow', // multiMenu context row 추가 
           'click [data-role="del-multiMenuContextRow"]' : 'delMultiMenuContextRow', // multiMenu context row 삭제
           'click [data-role="select-linkMethod"]' : 'selectLinkMethod', // 링크방식 선택 - move / api 
           'click [data-role="itemResHeaderLi"]' : 'stopitemResHeaderScroll', // 멀티답변 각 헤더 아이콘 클릭시 스크롤 방지 > 순서변경 해야 함.
           'blur [data-role="itemResHeaderLi"]' : 'keepitemResHeaderScroll', // 멀티답변 각 헤더 아이콘 클릭시 스크롤 방지 > 순서변경 해야 함.
           'click [data-role="config-api"]' : 'openSetApiPanel',
           'click [data-role="close-setApiPanel"]' : 'closeSetApiPanel',
           'click [data-role="clipboard-copy"]' : 'copyToClipBoard',
        },

        // Default options
        getDefaultOptions: function() {
            return {  
                highlightColor: '1caafc', // 하이라이트 칼라  
                normalizeColor: 'd0dada', // 기본칼라  
                filterBox : '[data-role="selectFilterBox"]', // 인텐트, 엔터티 필터 선택 박스 
                filterBoxTitle: '[data-role="filterBox-title"]', // 필터박스 타이틀 
                filterListBox: '[data-role="filterListBox"]', // 인텐트, 엔터티 출력되는 곳 
                filterInput : '[data-role="input-filterData"]', // 인텐트, 엔터티 입력 input  
                filterInputBox : '[data-role="inputFilter-Box"]', // input, and/or ㅡminus/plus
                btnAddInputFilter: '[data-role="add-inputFilter"]',  // inputFilter 추가 버튼 
                btnDelInputFilter: '[data-role="delete-inputFilter"]',  // inputFilter 추가 버튼 
                recognizeInput: 'input[name="recognize[]"]',
                commonInputClass: '.panel-input', // 전체 input focus 제거할 때 사용  
                filterBoxTitleText: '아래에서 검색대상을 선택해주세요',
                intentListBoxTitleText: '아래애서 <strong>#'+callIntent+'</strong>를 선택해주세요',
                entityListBoxTitleText: '아래애서 <strong>@'+callEntity+'</strong>를 선택해주세요',
                if_filterBoxTitleText: '검색대상을 선택해주세요',
                if_intentListBoxTitleText: '<strong>#'+callIntent+'</strong>를 선택해주세요',
                if_entityListBoxTitleText: '<strong>@'+callEntity+'</strong>를 선택해주세요', 
                lastInputFilter: $('[data-role="inputFilter-Box"]:last'),
                inputFilterWrapper: $('[data-role="inputFilter-wrapper"]'),
                inputFilterPlaceholder: callIntent+' or '+callEntity,
                resHeaderContainer: '[data-role="resHeaderContainer"]', // 답변그룹 탭메뉴 container 
                resBodyContainer: '[data-role="resBodyContainer"]', // 답변그룹 content container
                resHeaderItem: '[data-role="resHeaderItem"]', // 답변그룹 탭메뉴 Item
                resBodyItem: '[data-role="resBodyItem"]', // 답변그룹 content Item
                inputNodeName: '[data-role="input-nodeName"]', // 노드 이름 input
                inputNodeId: '[data-role="input-nodeId"]', // 노드 id input
                inputNodeParentId: '[data-role="input-nodeParentId"]', // 노드 parent id input
                sortableContainer: 'respondGroup-tab', // sotable Container element id
                mxAct: false,
                defaultImg: '/layouts/chatbot-desktop/_images/card-blank.png',
                dynamicSotableType: [".hMenu",".hMenu-tabs"], // 동적 sotable 사용할 respond type
                callBackGraph: function(data) {},  // mxgraph 쪽으로 callback 해주는 함수 
                setNodePanel: '[data-role="setNodePanel"]',
                setNodePanel_Width: 48, // %
                chatbotPanel: '[data-role="chatbotPanel"]',
                intentPanel: '[data-role="intentPanel"]',
                intentPanel_Left: '[data-role="intentPanel-left"]',
                intentPanel_Right: '[data-role="intentPanel-right"]',
                intentPanel_Width: 30, // %
                entityPanel_Left: '[data-role="entityPanel-left"]',
                entityPanel_Right: '[data-role="entityPanel-right"]',
                entityPanel_Width: 30, // %
                dialogSpace: '[data-role="dialogSpace"]',
                chatbotPanel_Width: 30, // % 
                noIntentExMsg: '등록된 예문이 없습니다.',
                noEntityMsg: '등록된 단어가 없습니다.',
                callIntent: callIntent,  // dialog 페이지에서 설정 
                callEntity: callEntity,  // dialog 페이지에서 설정 
                callContext: callContext, // dialog 페이지에서 설정
                recommendPanel: '[data-role="recommend-Panel"]',
                testLogPanel: '[data-role="testLogPanel"]',
                recommendTextLoader: '#textAni-loader', // AI 추천 
                dataSetPanel: '[data-role="dataSetPanel"]',
                contextListContainer: '[data-role="nodeContext-container"]', 
                contextListWrapper: '[data-role="nodeContextList-wrapper"]',
                contextListRow: '[data-role="contextList-row"]',
                setApiPanel: '[data-role="setApiPanel"]',
                jsonEditorContainer:  document.getElementById("jsonEditor-wrapper"),
            }            
        },

        copyToClipBoard: function(e){
            var self = this;
            var target = e.currentTarget;
            var clipboard = new Clipboard(target);
            var notify_container = $(target).data('container');
            var msg = $(target).data('feedback');
            var copyTarget = $(target).attr("data-clipboard-text"); 
            
            if(copyTarget) self.showNotify(notify_container,msg);
            else alert("죄측 패널에서 사용하 결과값을 클릭해주세요");
        },

         // tooltip 초기화 
        initToolTip: function(){
            $('body').find('.tooltip').remove();
        },

        // Start of api 패널 관련 ######################################################################################
        // jsonEditor 출력 
        showJsonEditor: function(result){
            var json = result.content;
            var statusCode = result.statusCode;
            var rightPanel = this.options.setApiPanel;
            var jsonEditor;
            var container = this.options.jsonEditorContainer;//document.getElementById("jsonEditor-wrapper");
            var labelEle = $(rightPanel).find('input[name="nodePath_label"]');
            var pathInputEle = $(rightPanel).find('input[name="node_path"]');
            var showApiResEle = $(rightPanel).find('[data-role="show-apiResEle"]');
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
                        var prettyPath = prettyPrintPath(node.path);
                        var apiTpl = '['+prettyPath+']';       
                        $(showApiResEle).val(prettyPath);
                        $(showApiResEle).parent().find('[data-role="clipboard-copy"]').attr("data-clipboard-text",apiTpl);
                    }
                    function prettyPrintPath(path) {
                        var str = '';
                        for (var i=0; i<path.length; i++) {
                            var element = path[i];
                            // if (typeof element === 'number') {
                            //     str += '[' + element + ']'
                            // }else {
                                if (str.length > 0) str += '.';
                                str += element; 
                            //}
                        }
                        return str;
                    }
                },
            };
            $(container).html('');
            
            setTimeout(function(){
                jsonEditor = new JSONEditor(container,options);    
                jsonEditor.set(json);
                setStatusCode(statusCode);
            },100);

        },  
        openSetApiPanel: function(e){
            e.preventDefault();
            var self = this;
            var target = e.currentTarget;
            var data = $(target).data();
      
            data['role'] ='open';
            this.controlSetApiPanel(data);
        },

        closeSetApiPanel: function(){
            var data = {role: "close"}
            this.controlSetApiPanel(data);
        },

        // 응답 > 설정 API 추가/수정 페이지 
        controlSetApiPanel: function(data){
            var self = this;
            var rightPanel = this.options.setApiPanel;
            var tableWrapper = this.options.tableWrapper;
            var rpw = this.options.rightPanelWidth;
            var tblw = 100-parseInt(rpw);
            // req 리스트에서 data- 형태로 받아오는 값 
            var api = data.api; // apiList uid 
            var req = data.req?data.req:null; // apiReq uid  
            var itemOC = data.itemoc?data.itemoc:null; // itemOC uid
            var role = data.role; // open, close
            var headerParamWrapperSelect ='[data-role="headerParam-wrapper"]';
            var queryParamWrapperSelect ='[data-role="queryParam-wrapper"]';
            var pathParamWrapperSelect ='[data-role="pathParam-wrapper"]';
            var formParamWrapperSelect ='[data-role="formParam-wrapper"]';
            var headerParamWrapper = $(rightPanel).find(headerParamWrapperSelect);
            var queryParamWrapper = $(rightPanel).find(queryParamWrapperSelect);
            var pathParamWrapper = $(rightPanel).find(pathParamWrapperSelect);
            var formParamWrapper = $(rightPanel).find(formParamWrapperSelect);
            var headerParamListWrapper = $(rightPanel).find('[data-role="headerParamList-wrapper"]');
            var queryParamListWrapper = $(rightPanel).find('[data-role="queryParamList-wrapper"]');
            var pathParamListWrapper = $(rightPanel).find('[data-role="pathParamList-wrapper"]');
            var formParamListWrapper = $(rightPanel).find('[data-role="formParamList-wrapper"]');

            // Start of 기본 필드 
            var apiEle = $(rightPanel).find('input[name="api"]'); // api uid input
            var reqEle = $(rightPanel).find('input[name="req"]'); // req uid  input
            var itemOCEle = $(rightPanel).find('input[name="itemOC"]');
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
            var btnAddParamWrapper = $('[data-role="btnAddParam-wrapper"]'); // body form 파라미터 추가버튼 
            // api output 
            var apiResTextEle = $(rightPanel).find('[data-role="apiRes-text"]');

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
                dt['itemOC'] = $(itemOCEle).val();
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
                dt['apiTextVal'] = $(apiResTextEle).val();

                //console.log(qPNameEle);

                return dt;
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
                    //console.log(arr);
                    html+= getApiParamInputRow(data);

                });

                return html;
            }; 

            // 패널값 리셋  
            var init_panel = function(){
                     
                // 값 초기화 
                $(rightPanel).find('input[type=text],input[type=hidden],textarea').each(function(){
                    var _this = $(this);
                    $(this).val('');
                });

            };
            
            // css 세팅 
            var setCss = function(data){
                var role = data.role;
                if(role=='open'){                    
                    $(rightPanel).css({"margin-right": self.options.setNodePanel_Width+"%"});
                }else if(role=='close'){
         
                    setTimeout(function(){
                        $(rightPanel).css({"margin-right": "-100%"});
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
            // 대화상자 > api 설정 > 답변 설정시 각 response 객체를 선택하기 위해 필요한 것  
            var getApiResult = function(){
                var resultContainer = '[data-role="apiResult-wrapper"]'; 
                var dt = getSendData();
                var jsonData = JSON.stringify(dt);
                var _data = {
                    linkType: "test-legacyApiParam", 
                    "api": api,
                    "req": req,
                    // apiData: jsonData, 
                    resultContainer: resultContainer 
                };
                self.linkServerData(_data);
            } 

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
                
                // 업데이트 method, bodyType
                updateMethod(reqData);

                var queryParamHtml = getApiParamHtml(data.query);
                var headerParamHtml = getApiParamHtml(data.header);
                var pathParamHtml = getApiParamHtml(data.path);
                var formParamHtml = getApiParamHtml(data.form);
           
                // header 세팅 
                if(headerParamHtml){
                    $(headerParamListWrapper).show();
                    $(headerParamWrapperSelect).html(headerParamHtml);   
                }else{
                    $(headerParamListWrapper).hide();
                }
                
                // query 세팅 
                if(queryParamHtml){
                    $(queryParamListWrapper).show();
                    $(queryParamWrapperSelect).html(queryParamHtml);   
                }else{
                    $(queryParamListWrapper).hide();
                }
                
                //path 세팅 
                if(pathParamHtml){
                    $(pathParamListWrapper).show();
                    $(pathParamWrapperSelect).html(pathParamHtml);   
                }else{
                    $(pathParamListWrapper).hide();
                }
                
                // form 세팅 
                if(formParamHtml){
                    $(formParamListWrapper).show();
                    $(formParamWrapperSelect).html(formParamHtml);   
                }else{
                    $(formParamListWrapper).hide();
                } 

                // body 값 세팅  : php > controlLegacyApiData 참조 
                var bodyData ={};
                $.each(data.body,function(i,r){
                    bodyData['name'] = r.name;
                    bodyData['uid'] = r.uid;
                    bodyData['text_val'] = r.text_val;
                });

                $(bodyValEle).val(bodyData.text_val);
                $(bodyUidEle).val(bodyData.uid);

                // output 값 세팅  php > controlLegacyApiData 참조 
                var outData ={}; 
                $.each(data.apiOutput,function(i,r){
                    outData['text_val'] = r.text_val;
                });
                $(apiResTextEle).val(outData.text_val);

            };

            // method 값 업데이트             
            var updateMethod = function(data){
                var apiBodyGuide = $(rightPanel).find('[data-role="apiBody-guide"]');
                var apiBodyWrapper = $(rightPanel).find('[data-role="apiBody-wrapper"]');
                var method = data.method?data.method:'GET';
                var bodyType = data.bodyType;

                // method 값 세팅 
                $(methodWrapper).text(method);
                $(methodEle).val(method);  
                
                // bodyType 값 세팅 
                $(bodyTypeLabel).text(bodyType);
                $(bodyTypeEle).val(bodyType); 

                // payloads(body) 세팅
                if(method=='POST' ||method=='PUT'){
                    $(apiBodyGuide).hide();
                    $(apiBodyWrapper).show();

                    if(bodyType =='text'){
                        $(formParamListWrapper).hide();
                        $(bodyTypeTextWrapper).show();
                        $(btnAddParamWrapper).hide();
                    }else{
                        $(formParamListWrapper).show();
                        $(bodyTypeTextWrapper).hide();
                        $(btnAddParamWrapper).show();
                    }  
                }else{
                    $(apiBodyGuide).show();
                    $(apiBodyWrapper).hide();
                    $(btnAddParamWrapper).hide();
                }
               
            };

            // input data 가져오기 
            var getInputData = function(){
                // 데이타 가져와서 세팅 
                var _data = {
                    "linkType":"get-legacyApiParam",
                    "api": api,
                    "req": req,
                    "itemOC": itemOC
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
                    $(itemOCEle).val(itemOC);
                    $(urlEle).val(url);

                    // input data 가져오기 
                    getInputData();

                    // 샘플 파라미터로 api 결과 가져와서 세팅
                    setTimeout(function(){
                         getApiResult();
                    },500);

            
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
                $(pathParamWrapper).html('');
                $(formParamWrapper).html('');

                // jsonEditor 초기화 
                $(rightPanel).find('#jsonEditor-wrapper').html('');
                $(rightPanel).find('[data-role="result-guide"]').show();

            };
            
            // 파라미터 추가  
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

         
            // 적용하기 
            $(rightPanel).off('click').on('click','[data-role="btn-applyApi"]',function(e){
                var dt = getSendData();
                var type = $(this).data('type');
                var jsonData = JSON.stringify(dt);
                var dt = getSendData();
                var jsonData = JSON.stringify(dt);
                var _data = {
                    linkType: "save-dialogResApiParamOutput", 
                    api: dt["api"],
                    req: dt["req"],
                    apiData: jsonData, 
                    itemOC: dt["itemOC"]
                };
                self.linkServerData(_data);  
                console.log(dt);
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
        
        //멀티답변 각 헤더 아이콘 클릭시 스크롤 방지 > 순서변경 해야 함.
        stopitemResHeaderScroll: function(e){
            var target = e.currentTarget;
            var activeVal = $(target).attr("data-active");
            var activeVal_arr = activeVal.split('-');
            var groupid = activeVal_arr[0];
            var itemid = activeVal_arr[1];
            var navTabs_wrapper = $('[data-role="NTW-'+groupid+'-'+itemid+'"]');
            //$(navTabs_wrapper).addClass('stop-scrolling');

            this.nowActiveMultiMenuTab = activeVal;
        },

        //멀티답변 각 헤더 아이콘 클릭시 스크롤 적용
        keepitemResHeaderScroll: function(e){
            var target = e.currentTarget;
            var activeVal = $(target).attr("data-active");
            var activeVal_arr = activeVal.split('-');
            var groupid = activeVal_arr[0];
            var itemid = activeVal_arr[1];
            var navTabs_wrapper = $('[data-role="NTW-'+groupid+'-'+itemid+'"]');
            $(navTabs_wrapper).removeClass('stop-scrolling');
        },

        selectLinkMethod: function(e){
            var target = e.currentTarget;
            var groupid  = $(target).data('groupid');
            var itemid  = $(target).data('itemid');
            var method = $(target).data('method');

            $('#'+groupid+'-'+itemid+'-link').find('.linkMethodWrapper').addClass('hide');
            $('[data-role="'+groupid+'-'+ itemid +'-link'+ method +'Wrapper"]').removeClass('hide');

        },
        
        // 컨텍스트 리스트 출력 
        showContextList: function(){
            var contextListContainer = this.options.contextListContainer;
            $(contextListContainer).removeClass('hide');  
        },

        // 컨텍스트 리스트 숨김  
        hideContextList: function(){
            var contextListContainer = this.options.contextListContainer;
            $(contextListContainer).addClass('hide'); 
        },
        
        // 아웃풋 메뉴 선택  
        selectOutputMenu: function(e){
            var target = e.currentTarget;
            var type = $(target).data('type');
           
            if(type=='contextForm') this.showContextList();
        },  

        focusInDataSetInput: function(e){
            var target = e.currentTarget;
            $(target).parent().css("border","solid 1px #1caafc");

        },       
             
        saveEntityData: function(e){
            var target = e.currentTarget;
            var data = $(target).data();
            $(target).parent().css("border","solid 1px #ddd");
            data['linkType'] = "saveEntityData";
            data['value'] = $(target).val();
            this.linkServerData(data);
        },
        
        // AI 추천시 text 에니메이션 효과 div 초기화 
        initTextAiLoader: function(){
            var panel = this.options.recommendPanel;
            var textLoader = this.options.recommendTextLoader;
            $(panel).find('[data-role="recommendList-Box"]').html('');
            $(textLoader).css('display','block');
            $(textLoader).html('');
        },
        
        // ai recommend input keyup 이벤트         
        keyUpAiRecommend : function(e){
            this.initTextAiLoader();
        },

        // ai recommend input enter 이벤트  
        enterAiRecommend : function(e){
            if (e.which == 13) {
                this.getRecommendData(e);                                                   
            }  
        },

        getRecommendData: function(e){
            this.initTextAiLoader();
            var target = e.currentTarget;
            var type = $(target).attr("data-type");
            var keyword = $('[data-role="rec-keyword"]').val();
            var resultContainer = '[data-role="recommendList-Box"]';
            var data = {linkType: "get-recommendData",keyword: keyword,type:type,resultContainer: resultContainer};
            this.linkServerData(data); 
        },

        closeDataSetPanel: function(e){
             var dataSetPanel = this.options.dataSetPanel;
             $(dataSetPanel).css("margin-right",'-100%');
        },
        
        // 추천 패널 오픈하기 
        openRecommendPanel: function(e){
            var target = e.currentTarget;
            var rtype = $(target).data('rtype');
            var panel = this.options.recommendPanel;
            var textLoader = this.options.recommendTextLoader;
            // 값 초기화 
            $(panel).find('[data-role="rec-keyword"]').val('');
            $(panel).find('[data-role="recommendList-Box"]').html('');
            
            // AI 추천시 text 에니메이션 효과 div 초기화
            this.initTextAiLoader();

            $(panel).find('[data-role="rec-keyword"]').attr("data-type",rtype); // 검색창 
            $(panel).find('[data-role="get-recommendData"]').attr("data-type",rtype); // 검색 버튼 
            $(panel).css("margin-right",0).addClass('opened'); 
        },

        // 추천 패널 닫기 
        closeRecommendPanel: function(){
            var panel = this.options.recommendPanel;
            $(panel).css("margin-right","-30%").removeClass('opened'); 
        },

        // 테스트 로그 패널 닫기 
        closeTestLogPanel: function(e){
            var panel = this.options.testLogPanel;
            $(panel).css("margin-bottom","-200px").removeClass('opened');  
        }, 

        // 우상단 패널변경 버튼 클릭
        changePanelMod: function(e){
            var target = e.currentTarget;
            var type = $(target).data('type');

            // 패널들 컨트롤 
            if(type=='chat'){
                this.openChatbotPanel();
                // intent, entity 패널 닫기 
                var data = {role: "close-left"};
                this.controlIntentPanel(data);
                this.controlEntityPanel(data); 
            
            }else if(type=='intent'){
                var data = {role: 'open-all',name: ''};  
                this.controlIntentPanel(data);

                // entity panel 닫기 
                var _data = {role: "close-left"};
                this.controlEntityPanel(_data);
            
            }else if(type=='entity'){
                var data = {role: 'open-all',name: ''};  
                this.controlEntityPanel(data);   
            
            }else if(type=='graph'){
                this.closeChatbotPanel();
                // intent, entity 패널 닫기 
                var data = {role: "close-left"};
                this.controlIntentPanel(data);
                this.controlEntityPanel(data);
            }else if(type=='data'){
                var data = {role: "open"};
                this.controlDataTable(data);
                
            } 

            // 버튼 active 처리 
            if(type=='chat'){
                $(target).siblings().find('button').removeClass('btn-primary').addClass('btn-default');
                $(target).find('.icon-comment').addClass('active');
            }else{
                // 해당 버튼만 primary 처리 
                $(target).siblings().find('button').removeClass('btn-primary').addClass('btn-default');
                $(target).find('button').addClass('btn-primary'); 

                // chatbot 이미지 active 해제 
                $(target).parent().find('.icon-comment').removeClass('active');
            }

               
        },

        controlDataTable: function(data){
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
            }
            
        },
        
        // 인텐트 패널 submit 이벤트   
        intentPNsubmit: function(e){
            var self = this;
            var target = e.currentTarget;
            var act = $(target).data('iact');
            var iExPN = this.options.intentPanel_Right;
            var intentNameEle = $(iExPN).find('[name="panelIntent_name"]');
            var intent =  $(iExPN).find('[name="panelIntent_uid"]').val();
            var intentName = $(intentNameEle).val();
            var iEx_uid = $(iExPN).find('[name="intentEx_uid[]"]').map(function(){return $(this).val()}).get();
            var iEx_val = $(iExPN).find('[name="intentEx[]"]').map(function(){return $(this).val()}).get();
            var callIntent = this.options.callIntent;
            var checkSameName = function(newName){
                var intentArray = self.intent;
                var is_same = false;
                $.each(intentArray,function(key,arr){
                    var name = arr.name;
                    if(name==newName) is_same = true;
                }); 

                return is_same;  
            };

            if(act=='save'){
                if(intentName){
                    var is_sameName = checkSameName(intentName);
                    if(!intent && is_sameName){
                        this.showNotify('#intentEx-submitForm', callIntent+'명이 이미 존재합니다.');
                        setTimeout(function(){
                            $(intentNameEle).focus(); 
                        },100); 

                    }else{
                        var data = {
                            linkType:"save-intent", 
                            intent: intent,
                            intentName: intentName, 
                            iEx_uid: iEx_uid, 
                            iEx_val: iEx_val
                        };
                        this.linkServerData(data);          
                    }
                    
                }else{
                    this.showNotify('#intentEx-submitForm', callIntent+'명을 입력해주세요.');
                    setTimeout(function(){
                        $(intentNameEle).focus(); 
                    },100);
                }               
            
            }else if(act=='close-left' || act=='close-right'){
                var data = {role: act}
                this.controlIntentPanel(data);
            }
        },

        // 엔터티 패널 submit 이벤트   
        entityPNsubmit: function(e){
            var self = this;
            var target = e.currentTarget;
            var act = $(target).data('iact');
            var iExPN = this.options.entityPanel_Right;
            var entityNameEle = $(iExPN).find('[name="panelEntity_name"]');
            var entity =  $(iExPN).find('[name="panelEntity_uid"]').val();
            var entityName = $(entityNameEle).val();
            var iEx_uid = $(iExPN).find('[name="entityEx_uid[]"]').map(function(){return $(this).val()}).get();
            var iEx_val = $(iExPN).find('[name="entityEx[]"]').map(function(){return $(this).val()}).get();
            var iEx_syn = $(iExPN).find('[name="entityEx_synonyms[]"]').map(function(){return $(this).val()}).get(); 
            var callEntity = this.options.callEntity;
            var checkSameName = function(newName){
                var entityArray = self.entity;
                var is_same = false;
                $.each(entityArray,function(key,arr){
                    var name = arr.name;
                    if(name==newName) is_same = true;
                }); 

                return is_same;  
            };

            if(act=='save'){
                if(entityName){
                    var is_sameName = checkSameName(entityName);
                    if(!entity && is_sameName){
                        this.showNotify('#entityEx-submitForm', callEntity+'명이 이미 존재합니다.');
                        setTimeout(function(){
                            $(entityNameEle).focus(); 
                        },100); 

                    }else{
                        var data = {
                            linkType:"save-entity", 
                            entity: entity,
                            entityName: entityName, 
                            iEx_uid: iEx_uid, 
                            iEx_val: iEx_val,
                            iEx_syn: iEx_syn
                        };
                        this.linkServerData(data);          
                    }
                    
                }else{
                    this.showNotify('#entityEx-submitForm', callEntity+'명을 입력해주세요.');
                    setTimeout(function(){
                        $(entityNameEle).focus(); 
                    },100);
                }               
            
            }else if(act=='close-left' || act=='close-right'){
                var data = {role: act}
                this.controlEntityPanel(data);
            }
        },

        // 필터(인텐트/엔터티) 추가 함수    
        addFilterItem: function(e){
            var target = e.currentTarget;
            var filter = $(target).data('filter');
            var new_item = $(target).data('name');
    
            if(filter=='#') {
                var data = {role: 'add-intent',name: new_item};  
                this.controlIntentPanel(data);
            }
            
        },
        
        // Intent panel 컨트롤 
        controlIntentPanel: function(data){
            var self = this;
            var Left = this.options.intentPanel_Left;
            var Right = this.options.intentPanel_Right;
            var nameEle = $(Right).find('input[name="panelIntent_name"]'); // name input
            var uidEle = $(Right).find('input[name="panelIntent_uid"]'); // uid input
            var delIntentEle = $(Right).find('[data-role="del-intent"]');// delete intent btn
            var exWrapperEle = $(Right).find('[data-role="intentEx-wrapper"]'); // 예시문장 div 
            var intentEle = $('[data-role="intent-panelItem"]'); // intent item 
            var gap = this.options.intentPanel_Width;
            var role = data.role;
            var init_panel = function(){
                var intentEle = $(Left).find('[data-role="intent-panelItem"]');
                // intent active 해제 
                $.each(intentEle,function(){
                    $(this).removeClass('active');
                });
                // 값 초기화 
                $(nameEle).val(''); // name input
                $(uidEle).val(''); // uid input 
                $(exWrapperEle).html(''); // 예시문장 div 
                $(delIntentEle).addClass('hidden');   
            };
            // 인텐트 예문폼 추가 
            var addIntentEx = function(e){
                var intentExWrapper = $(Right).find('[data-role="intentEx-wrapper"]');
                var intentExTpl = template['intentEx_row'];
                intentExTpl = intentExTpl.replace(/\{\$iEx_uid}/gi,'');
                intentExTpl = intentExTpl.replace(/\{\$iEx_val}/gi,'');
                $(intentExWrapper).find('.no-data').remove();
                $(intentExWrapper).prepend(intentExTpl);
            };

            // 인텐트 active 처리 
            var setActiveIntent = function(){
                var activeUid = $(uidEle).val();
                var intentEle = $(Left).find('[data-role="intent-panelItem"]');
                $.each(intentEle,function(i,ele){
                    var thisUid = $(ele).data('uid');
                    if(thisUid==activeUid) $(ele).addClass('active');
                    else $(ele).removeClass('active');                     
                });
            };
            var getIntent = function(){
                var getIntentMod = {role:'intent-panelItem', filter:'#'}; 
                var intentList = self.getIntentList(getIntentMod);   
                $(Left).find('[data-role="intentList-Box"]').html(intentList);
            }; 

            var getIntentEx = function(uid){
                var uid = uid?uid:$(Right).find('input[name="panelIntent_uid"]').val();
                var resultContainer =  '[data-role="intentEx-wrapper"]';
                var data = {"linkType":"getIntentEx","uid": uid,"resultContainer":resultContainer};
                self.linkServerData(data); // 인텐트 예문 가져오기  

                // ai recommendPanel 닫기 
                self.closeRecommendPanel();
            };
            var open_Right = function(data){         
                // intent data 값 저장 
                if(data.name){
                    var name = data.name;  
                    $(nameEle).val(name);  
                } 
                if(data.uid){
                   $(uidEle).val(data.uid);
                   getIntentEx(data.uid);
                } 

                // 좌측 패널이 좌로 30% 더 이동 
                $(Left).css("margin-right", gap+'%').addClass('opened');
                $(Right).css("margin-right", 0).addClass('opened'); 

            };
            var open_Left = function(data){
                var getIntentMod = {role:'intent-panelItem', filter:'#'}; 
                var intentList = self.getIntentList(getIntentMod);   
                $(Left).find('[data-role="intentList-Box"]').html(intentList);
                setTimeout(function(){
                    var margin;
                    if($(Right).hasClass('opened')) margin = gap;
                    else margin = 0;

                    $(Left).css("margin-right", margin+'%').addClass('opened'); 

                    // active 처리 
                    setActiveIntent();
                },100);                
            };
            var close_Left = function(){
                $(Left).css("margin-right","-"+gap+'%').removeClass('opened');
            };
            var close_Right = function(){
                $(Right).css("margin-right","-"+gap+'%').removeClass('opened');
                init_panel(); // 값 초기화                   
            };
            
            // 예문 삭제후 예문이 1개도 없는 경우  no-data 출력  
            var checkNoData = function(){
                var intentEx = $(exWrapperEle).find('.intentEx-item'); 
                var is = 0;
                var noDataTpl = template['no_data'];
                noDataTpl = noDataTpl.replace(/\{\$noData_msg}/gi,self.options.noIntentExMsg); 
                $.each(intentEx,function(i,ele){
                    if(ele) is++;
                });
                // 예문 없는 경우
                if(is==0){
                    $(exWrapperEle).html(noDataTpl);
                }
            };

            // 인텐트 패널 노출분기(좌/우)  
            if(role=='add-intent' || role=='showIntentDetail') open_Right(data); // 우측패널 열기 
            else if(role=='open-intent') open_Left(data); // 좌측패널 열기 
            else if(role=='open-all'){
                open_Left(data);
                open_Right(data);
            }else if(role=='close-left'|| role=='close-right'){
                close_Left();
                close_Right();
            }
            else if(role=='add-new') init_panel();                   
            else if(role=='deleted-intent'){
                init_panel();
                open_Left(data);              
            }

            // '의도생성' 이벤트 
            $(Left).find('[data-role="add-intent"]').on('click',function(){
                 init_panel();
                 $(nameEle).focus();

                 // ai recommendPanel 닫기 
                self.closeRecommendPanel(); 
            }); 
            
            // 인텐트 클릭 이벤트     
            $(Left).on('click','[data-role="intent-panelItem"]',function(e){
                e.preventDefault();
                var uid = $(this).data('uid');
                var name = $(this).data('name');
                var type = $(this).data('type');
                $(nameEle).val(name);
                $(uidEle).val(uid);

                // vendor 가 등록한 인텐트만 삭제할 수 있게 한다.
                if(type=='V'){
                    $(delIntentEle).attr("data-uid",uid);
                    $(delIntentEle).removeClass('hidden');  
                }else{
                    $(delIntentEle).attr("data-uid",'');
                    $(delIntentEle).addClass('hidden'); 
                }
               
                getIntentEx(uid);
                setActiveIntent();
            });

            // '인텐트' 삭제 이벤트 
            $(Right).find('[data-role="del-intent"]').off('click').on('click',function(e){
                var target = e.currentTarget;
                var uid = $(target).attr('data-uid');
                var data = {linkType: 'delete-intent',uid: uid};
                self.linkServerData(data);
            }); 

            // '관련 예시문장' 추가 이벤트 
            $(Right).find('[data-role="add-intentEx"]').off('click').on('click',function(e){
                 addIntentEx();
            }); 

            // '관련 예시문장' 삭제 이벤트 
            $(Right).on('click','[data-role="del-intentEx"]',function(e){
                var target = e.currentTarget;
                var uid = $(target).attr('data-uid'); 
                var intent = $(uidEle).val(); // 최상단 글로벌 변수
                var intentName = $(nameEle).val(); // 최상단 글로벌 변수
                var data = {linkType: 'delete-intentEx',intent: intent,intentName: intentName,intentEx: uid};
                self.linkServerData(data);

                if(uid){
                    // 해당 엘리먼트 지우기 
                    $(Right).find('[data-role="intentEx-item-'+uid+'"]').remove();
                }else{
                    $(this).parent().parent().remove(); 
                }

                setTimeout(function(){
                    checkNoData();
                },100);              

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

        },

        // Entity panel 컨트롤 
        controlEntityPanel: function(data){
            var self = this;
            var Left = this.options.entityPanel_Left;
            var Right = this.options.entityPanel_Right;
            var nameEle = $(Right).find('input[name="panelEntity_name"]'); // name input
            var uidEle = $(Right).find('input[name="panelEntity_uid"]'); // uid input
            var delEntityEle = $(Right).find('[data-role="del-entity"]');// delete entity btn
            var exWrapperEle = $(Right).find('[data-role="entityEx-wrapper"]'); // 예시문장 div 
            var entityEle = $('[data-role="entity-panelItem"]'); // entity item 
            var gap = this.options.entityPanel_Width;
            var role = data.role;
            var init_panel = function(){
                var entityEle = $(Left).find('[data-role="entity-panelItem"]');
                // entity active 해제 
                $.each(entityEle,function(){
                    $(this).removeClass('active');
                });
                // 값 초기화 
                $(nameEle).val(''); // name input
                $(uidEle).val(''); // uid input 
                $(exWrapperEle).html(''); // 예시문장 div 
                $(delEntityEle).addClass('hidden');   
            };
            // 엔터티 예문폼 추가 
            var addEntityEx = function(e){
                var entityExWrapper = $(Right).find('[data-role="entityEx-wrapper"]');
                var entityExTpl = template['entityEx_row'];
                entityExTpl = entityExTpl.replace(/\{\$iEx_uid}/gi,'');
                entityExTpl = entityExTpl.replace(/\{\$iEx_val}/gi,'');
                entityExTpl = entityExTpl.replace(/\{\$iEx_syn}/gi,'');
                $(entityExWrapper).find('.no-data').remove();
                $(entityExWrapper).prepend(entityExTpl);
            };

            // 엔터티 active 처리 
            var setActiveEntity = function(){
                var activeUid = $(uidEle).val();
                var entityEle = $(Left).find('[data-role="entity-panelItem"]');
                $.each(entityEle,function(i,ele){
                    var thisUid = $(ele).data('uid');
                    if(thisUid==activeUid) $(ele).addClass('active');
                    else $(ele).removeClass('active');                     
                });
            };
            var getEntity = function(){
                var getEntityMod = {role:'entity-panelItem', filter:'@'}; 
                var entityList = self.getEntityList(getEntityMod);   
                $(Left).find('[data-role="entityList-Box"]').html(entityList);
            }; 

            var getEntityEx = function(uid){
                var uid = uid?uid:$(Right).find('input[name="panelEntity_uid"]').val();
                var resultContainer =  '[data-role="entityEx-wrapper"]';
                var data = {"linkType":"getEntityEx","uid": uid,"resultContainer":resultContainer};
                self.linkServerData(data); // 엔터티 예문 가져오기 

                // ai recommendPanel 닫기 
                self.closeRecommendPanel(); 
            };
            var open_Right = function(data){         
                // entity data 값 저장 
                if(data.name){
                    var name = data.name;  
                    $(nameEle).val(name);  
                } 
                if(data.uid){
                   $(uidEle).val(data.uid);
                   getEntityEx(data.uid);
                } 

                // 좌측 패널이 좌로 30% 더 이동 
                $(Left).css("margin-right", gap+'%').addClass('opened');
                $(Right).css("margin-right", 0).addClass('opened');   

            };
            var open_Left = function(data){
                var getEntityMod = {role:'entity-panelItem', filter:'@'}; 
                var entityList = self.getEntityList(getEntityMod);   
                $(Left).find('[data-role="entityList-Box"]').html(entityList);
                setTimeout(function(){
                    var margin;
                    if($(Right).hasClass('opened')) margin = gap;
                    else margin = 0;

                    $(Left).css("margin-right", margin+'%').addClass('opened'); 

                    // active 처리 
                    setActiveEntity();
                },100);                
            };
            var close_Left = function(){
                $(Left).css("margin-right","-"+gap+'%').removeClass('opened');
            };
            var close_Right = function(){
                $(Right).css("margin-right","-"+gap+'%').removeClass('opened');
                init_panel(); // 값 초기화                   
            };
            
            // 예문 삭제후 예문이 1개도 없는 경우  no-data 출력  
            var checkNoData = function(){
                var entityEx = $(exWrapperEle).find('.entityEx-item'); 
                var is = 0;
                var noDataTpl = template['no_data'];
                noDataTpl = noDataTpl.replace(/\{\$noData_msg}/gi,self.options.noEntityExMsg); 
                $.each(entityEx,function(i,ele){
                    if(ele) is++;
                });
                // 예문 없는 경우
                if(is==0){
                    $(exWrapperEle).html(noDataTpl);
                }
            };

            // 엔터티 패널 노출분기(좌/우)  
            if(role=='add-entity' || role=='showEntityDetail') open_Right(data); // 우측패널 열기 
            else if(role=='open-entity') open_Left(data); // 좌측패널 열기 
            else if(role=='open-all'){
                open_Left(data);
                open_Right(data);
            }else if(role=='close-left'|| role=='close-right'){
                close_Left();
                close_Right();
            }
            else if(role=='add-new') init_panel();                   
            else if(role=='deleted-entity'){
                init_panel();
                open_Left(data);              
            }

            // '의도생성' 이벤트 
            $(Left).find('[data-role="add-entity"]').on('click',function(){
                 init_panel();
                 $(nameEle).focus();

                 // ai recommendPanel 닫기 
                self.closeRecommendPanel(); 
            }); 
            
            // 엔터티 클릭 이벤트     
            $(Left).off().on('click','[data-role="entity-panelItem"]',function(e){
                e.preventDefault();
                var uid = $(this).data('uid');
                var name = $(this).data('name');
                var type = $(this).data('type');
                $(nameEle).val(name);
                $(uidEle).val(uid);

                // vendor 가 등록한 인텐트만 삭제할 수 있게 한다.
                if(type=='V'){
                    $(delEntityEle).attr("data-uid",uid);
                    $(delEntityEle).removeClass('hidden');  
                }else{
                    $(delEntityEle).attr("data-uid",'');
                    $(delEntityEle).addClass('hidden'); 
                }
               
                getEntityEx(uid);
                setActiveEntity();
            });

            // '인텐트' 삭제 이벤트 
            $(Right).find('[data-role="del-entity"]').off('click').on('click',function(e){
                var target = e.currentTarget;
                var uid = $(target).attr('data-uid');
                var data = {linkType: 'delete-entity',uid: uid};
                self.linkServerData(data);
            }); 

            // '관련 예시문장' 추가 이벤트 
            $(Right).find('[data-role="add-entityEx"]').off('click').on('click',function(e){
                 addEntityEx();
            }); 

            // '관련 예시문장' 삭제 이벤트 
            $(Right).on('click','[data-role="del-entityEx"]',function(e){
                var target = e.currentTarget;
                var uid = $(target).attr('data-uid'); 
                var entity = $(uidEle).val(); // 최상단 글로벌 변수
                var entityName = $(nameEle).val(); // 최상단 글로벌 변수
                var data = {linkType: 'delete-entityEx',entity: entity,entityName: entityName,entityEx: uid};
                self.linkServerData(data);

                if(uid){
                    // 해당 엘리먼트 지우기 
                    $(Right).find('[data-role="entityEx-item-'+uid+'"]').remove();
                }else{
                    $(this).parent().parent().remove(); 
                }

                setTimeout(function(){
                    checkNoData();
                },100);              

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

        },

        // ndoe panel 답변그룹 초기화  
        resetNodePanelRes: function(){
            var setNodePanel = this.options.setNodePanel;
            var resHeaderContainer = this.options.resHeaderContainer;
            var resBodyContainer = this.options.resBodyContainer;

            // 답변 헤더/바디를 비워준다. :  sortable 에러 때문 
            $(setNodePanel).find(resHeaderContainer).html('');
            $(setNodePanel).find(resBodyContainer).html('');
        },

        openSetNodePanel: function(e){
            var setNodePanel = this.options.setNodePanel;
            var chatbotPanel = this.options.chatbotPanel;
            var c_w = this.options.chatbotPanel_Width;
            var s_w = this.options.setNodePanel_Width;
            
            if($(chatbotPanel).hasClass('opened')){
                $(setNodePanel).css("margin-right",c_w+"%").addClass('opened');
            }else{
                $(setNodePanel).css("margin-right",0).addClass('opened'); 
            }  

            // 답변그룹 초기화 
            this.resetNodePanelRes();

        },

        closeSetNodePanel: function(){
           var setNodePanel = this.options.setNodePanel;
           var dialogSpace = this.options.dialogSpace;
           var commonInputClass = this.options.commonInputClass;

           $(commonInputClass).blur();
           this.hideFilterBox();

           $(setNodePanel).css("margin-right","-48%").removeClass('opened');

           // 답변그룹 초기화 
           this.resetNodePanelRes();

           // 컨텍스트 리스트 숨김 
           this.hideContextList();
        },

        openChatbotPanel: function(e){
            var chatbotPanel = this.options.chatbotPanel;
            var c_w = this.options.chatbotPanel_Width;
            var dialogSpace = this.options.dialogSpace;
            var botUrl = this.testBotUrl;

            $(chatbotPanel).css("margin-right", "0").addClass('opened');
            if($(setNodePanel).hasClass('opened')){
                // setTimeout(function(){
                     $(setNodePanel).css("margin-right", c_w+"%");  
                //},200);
                      
            } 
            setTimeout(function(){
                $(chatbotPanel).find('iframe').attr("src",botUrl); 
            },30); 
                    

        },

        // 챗봇 패널 reload
        refreshChatbotPanel: function(){
            var chatbotPanel = this.options.chatbotPanel;
            var botUrl = this.testBotUrl;
            $(chatbotPanel).find('iframe').attr("src",botUrl); 
        },

        closeChatbotPanel: function(e){
            var chatbotPanel = this.options.chatbotPanel;
            var c_w = this.options.chatbotPanel_Width;
            var setNodePanel = this.options.setNodePanel;

            $(chatbotPanel).css("margin-right", "-"+c_w+"%").removeClass('opened'); 
            if($(setNodePanel).hasClass('opened')){               
                   $(setNodePanel).css("margin-right", 0);    
            }             
            $(chatbotPanel).find('iframe').attr("src",'');

            // 로그 패널도 닫기 
            this.closeTestLogPanel();

        },

        // showHie respondGroup & respondItem
        showHideRespond: function(e){
            var self = this;
            var target = e.currentTarget;
            var group_uid = $(target).data('uid');
            var state = $(target).attr('data-state');
            var target_tab = $(target).parent().parent().parent().parent();
            var data;
        
            if(state=='show'){
                data = {"linkType":"hide-resGroup","group_uid":group_uid};
                this.linkServerData(data);
                setTimeout(function(){
                    $(target).attr("data-state","hide");
                    $(target).find('span').text("노출");
                    $(target).find('.fa').removeClass('fa-eye-slash').addClass('fa-eye');
                    $(target_tab).addClass("tab-hidden");
                },20);
            }else{
                data = {"linkType":"show-resGroup","group_uid":group_uid};
                this.linkServerData(data);
                setTimeout(function(){
                    $(target).attr("data-state","show");
                    $(target).find('span').text("숨김");
                    $(target).find('.fa').removeClass('fa-eye').addClass('fa-eye-slash');
                    $(target_tab).removeClass("tab-hidden");
                },20);
            }      
        }, 

        // 응답그룹 헤더탭 active 값 저장   
        changeResActive: function(e){
            var self = this;
            var target =e.currentTarget;
            var group_id = $(target).data('id');
            var type = $(target).data('type');
            this.resHeaderActive = group_id;
            
            // 멀티 메뉴인 경우 sortable 적용 
            if($.inArray(type,this.options.dynamicSotableType)!=-1){
                 var hMenuTab = $('[data-role="itemResHeaderUl"]');
                 $.each(hMenuTab,function(i,ele){
                     var dynamicId = $(ele).attr('id');
                     self.setDynamicSortable(dynamicId);   
                 }); 
            }
        },
        
        // 멀티메뉴 응답그룹 멀티탭 active 값 저장  
        changeItemResActive: function(e){
            var self = this;
            var target = e.currentTarget;
            var active = $(target).data('active');            
            $(target).parent().find('input[name="itemResHeaderActive[]"]').val(active);
            console.log(e);
        },

        // submit 버튼 클릭 이베트 
        submitPanel: function(e){
            e.preventDefault();
            var target = e.currentTarget;
            var act = $(target).data('pact');
            if(act=='cancel'){
                this.closeSetNodePanel();
            }else if(act=='save'){
                this.saveNode();
            }
        },

        // 이미지 업로드 함수 
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
            var target = e.currentTarget;
            var preview_ele = $(target).parent().find('[data-role="btn-uploadImg"]');
            var imgUrl_ele = $(target).parent().find('[data-role="img_url"]');
            var imgUrl_ele2 = $(target).parent().find('[data-role="res-img"]');
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
                url: rooturl+'/?r='+raccount+'&m='+module+'&a=do_dialogPanelAction',
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
                         if(imgUrl_ele2) $(imgUrl_ele2).val(source);
                         $(preview_ele).css({"background-image":"url('"+source+"')", "background-repeat":"no-repeat", "background-position":"center center","background-size":"cover"});
                         setTimeout(function(){
                             $(target).remove(); // 해당 input file 삭제 
                         },10)
                         

                    } // success
           
                }
            }); 
        },

        // save Node 함수 
        saveNode: function(){
            var nodeName = $(this.options.inputNodeName).val();
            var nodeId = $(this.options.inputNodeId).val();
            var nodeParentId = $(this.options.inputNodeParentId).val();
            var and_or = $('select[name="and_or[]"]').map(function(){return $(this).val()}).get(); // and or 조건 
            var filter_label = $('input[name="filter_label[]"]').map(function(){return $(this).val()}).get(); // 사용자에게 보여지는 부분  
            var recognize = $('input[name="recognize[]"]').map(function(){return $(this).val()}).get(); // 조건 
            var resGroupHeader = $('input[name="respondGroupHeader[]"]').map(function(){return $(this).val()}).get(); // 응답그룹 헤더 
            var resGroupBody = {};
            var resItem = $('[data-role="resItem"]');
            var data;
            var itemResHeaderActive = $('input[name="itemResHeaderActive[]"]').map(function(){return $(this).val()}).get();
            var contextName = $('input[name="contextName[]"]').map(function(){return $(this).val()}).get(); // 컨텍스트 변수명
            var contextValue = $('input[name="contextValue[]"]').map(function(){return $(this).val()}).get(); // 컨텍스트 값 
            // context name/value 추출 
            var getContextVal = function(item){
                var contextName = $(item).find('input[name="multiMenuContextName[]"]').map(function(){return $(this).val()}).get();
                var contextValue = $(item).find('input[name="multiMenuContextValue[]"]').map(function(){return $(this).val()}).get(); // 컨텍스트 값
                var context = '';
                $.each(contextName,function(i,name){
                    if(name) context += name+'|'+contextValue[i]+',';
                    else context +='';
                });

                return context;
            };
            var getItemResGroup = function(item){
                var header = $(item).find('[name="itemResHeader[]"]').map(function(){return $(this).val()}).get();
                var itemResGroup = {};
                $.each(header,function(i,type){
                    var input = $(item).find('[data-role="res-'+type+'"]'); // 어떤 형태의 item 이든지 필수 
                    var uid = $(input).data('uid');
                    var value;
                    if(type=='context') value = getContextVal(item); 
                    else value  = $(input).val();

                    itemResGroup[type] = {"uid":uid,"val":value};
                }); 

                return itemResGroup;
            };
            var graph = this.graph;
            var encoder = new mxCodec();
            var node = encoder.encode(graph.getModel());
            var source = mxUtils.getXml(node);
            //console.log(nodeName);
            // return false;

            $.each(resItem,function(i,ele){
               var data = {};
               var item_uid = $(ele).data('uid')?$(ele).data('uid'):0; // 있으면 수정 
               var group_id = $(ele).data('groupid');
               var item_id = $(ele).data('itemid');              
               var resType = $(ele).data('type'); 
               var title = $(ele).find('[data-role="title"]').val();
               var summary = $(ele).find('[data-role="summary"]').val();
               var content = $(ele).find('[data-role="content"]').val();
               var img_url = $(ele).find('[data-role="img_url"]').val();
               var link1 = $(ele).find('[data-role="link1"]').val();
               var link2 = $(ele).find('[data-role="link2"]').val();
               var link3 = $(ele).find('[data-role="link3"]').val(); 
               var itemResGroup = getItemResGroup(ele);

               data = {
                 "item_uid": item_uid,
                 "group_id": group_id,
                 "item_id": item_id,
                 "resType": resType,
                 "title": title,
                 "summary": summary,
                 "content": content,
                 "img_url": img_url,
                 "link1": link1,
                 "link2": link2,
                 "link3": link3,
                 "itemResGroup": itemResGroup,
            
               }
               // 조건타입일 경우 
               if(resType=='if'){
                  var recQry = $(ele).find('[data-role="if-recQry"]').map(function(){return $(this).val()}).get(); // 조건 쿼리 
                  var recCondition = $(ele).find('[data-role="if-recCondition"]').map(function(){return $(this).val()}).get(); // 조건 데이타 
                  var recLabel = $(ele).find('[data-role="input-ifFilterData"]').map(function(){return $(this).val()}).get(); // 조건 데이타
                  
                  data["if_recQry"] = recQry;
                  data["if_recCondition"] = recCondition;
                  data['if_recLabel'] = recLabel;
               } 
               resGroupBody[i] = JSON.stringify(data);
               // console.log(resGroupBody[i]);

            });
            data = {
                 "linkType": "saveNode",
                 "nodeName": nodeName,
                 "graph" : source,
                 "andOr": and_or,
                 "filterLabel": filter_label,
                 "recognize": recognize,
                 "resGroupHeader": resGroupHeader,
                 "resGroupBody": resGroupBody,
                 "resHeaderActive" : this.nowActiveMultiMenuTab,
                 "contextName" : contextName?contextName:null,
                 "contextValue" : contextValue?contextValue:null,
            }; 

            console.log(data);

                        
           // save Node 
           this.linkServerData(data);  
        },

        // 템플릿 세팅  
        setTemplate: function(data){
            template = data; // template{} 는 dialog.php 에서 글로벌하게 설정해논 상태임.
            return;
        },
        
        // respondGroup 탭 메뉴 sortable 처리 
        setSortable: function(){
            var sortableContainer = this.options.sortableContainer;
            var el = document.getElementById(sortableContainer);
            var sortable = Sortable.create(el,{
                ghostClass: 'sotable-placeholder',
                onEnd: function (e) {
                    var itemEl = e.item;  // dragged HTMLElement
                    e.to;    // target list
                    e.from;  // previous list
                    e.oldIndex;  // element's old index within old parent
                    e.newIndex;  // element's new index within new parent
                    //console.log([e.newIndex]);
                },
                filter: ".ignore-sortable",
            });
        },
        
        // 조건타입(if) 응답박스에 추가버튼 세팅 
        setIfAddBtn: function(){
            var self = this;
            var ifInputFilter_wrapper = $(document).find('[data-role="ifInputFilter-wrapper"]');
            if(ifInputFilter_wrapper){
               $.each(ifInputFilter_wrapper,function(){                   
                    var data = {ifMenuInputWrapper: $(this)};
                    self.if_setInputFilterAddBtn(data);
               });    
            }
                     
        },  

        // dynamic sotable 자동적용 함수 : options 에 해당 class 명을 등록해둔다.   
        applyDynamicSotable: function(){
            var el = this.$el_id;
            var self = this;
            var panel = this.options.setNodePanel;
            var classArray = this.options.dynamicSotableType;
            $.each(classArray,function(i,cls){
                var ele = $(panel).find(cls);
                //console.log(ele);
                if(ele){
                    $.each(ele,function(){
                        var id = $(this).attr("id");
                        if(id) self.setDynamicSortable(id);
                    });   
                }
                              
            });            
        },  

        // 가로메뉴 Respond hMenu 탭 메뉴 sortable 처리 
        setDynamicSortable: function(dynamicId){
            setTimeout(function(){
                var el = document.getElementById(dynamicId);
                var sortable = Sortable.create(el,{
                    ghostClass: 'sotable-placeholder',
                    onEnd: function (e) {
                        var itemEl = e.item;  // dragged HTMLElement
                        e.to;    // target list
                        e.from;  // previous list
                        e.oldIndex;  // element's old index within old parent
                        e.newIndex;  // element's new index within new parent
                        //console.log([e.newIndex]);
                    },
                    filter: ".ignore-sortable",
                }); 
            },50);            
        },  

        init_atwho: function(){
            var self = this;
            var who_target =["textarea","input[type=text]"];
            var ta = $(document).find('textarea','input[type=text]');
            var module = this.module;
            var init_intent = function(ta){
                $(ta).atwho({
                    at: "#",
                    callbacks: {
                        /*
                         It function is given, At.js will invoke it if local filter can not find any data
                         @param query [String] matched query
                         @param callback [Function] callback to render page.
                        */
                        remoteFilter: function(query, callback) {
                                                   
                            var data = [];
                            var data_arr = self.intent;
                            $.each(data_arr,function(key,arr){
                                var insert = arr['name']+'('+arr['uid']+')';
                              
                                data.push(insert);       
                            });
                            //console.log(data);
                            callback(data);
                        }
                            
                    }
                   
                });
            };
            var init_entity = function(ta){
                $(ta).atwho({
                    at: "@",
                    callbacks: {
                        /*
                         It function is given, At.js will invoke it if local filter can not find any data
                         @param query [String] matched query
                         @param callback [Function] callback to render page.
                        */
                        remoteFilter: function(query, callback) {
                          
                            var data = [];
                            var data_arr = self.entity;
                            $.each(data_arr,function(key,arr){
                                var insert = arr['name']+'('+arr['uid']+')';
                                data.push(insert);       
                            });

                            callback(data);
                        }
                            
                    }
                   
                });
            };
            var init_context = function(ta){
                $(ta).atwho({
                    at: "$",
                    callbacks: {
                        /*
                         It function is given, At.js will invoke it if local filter can not find any data
                         @param query [String] matched query
                         @param callback [Function] callback to render page.
                        */
                        remoteFilter: function(query, callback) {
                          
                            var data = [];
                            var data_arr = self.context;
                            $.each(data_arr,function(key,arr){
                                var insert = arr['name']+'('+arr['uid']+')';
                                data.push(insert);       
                            });
                            callback(data);
                        }
                            
                    }
                   
                });
            };
            $.each(who_target,function(i,selector){
                var ta = $(document).find(selector);
                $.each(ta,function(i,ele){
                    init_intent(ele); 
                    init_entity(ele);
                });
            });

            console.log('ddd');
            
        },

        // 초기화 함수들 
        initFunc: function(){
            this.setSortable();
            this.init_atwho();
            this.applyDynamicSotable(); 
            this.setIfAddBtn(); // 조건타입 답변의 추가버튼 체크 및 세팅 
        },
 
        
        // 답변 아이템 삭제 
        delResItem: function(e){
            var target = e.currentTarget;
            var data = $(target).data();
            $(target).parent().remove();
            data['linkType'] = "delete-resItem";
            this.linkServerData(data);     
        },

        // 답변 아이템 추가  
        addResItem: function(e){
            var target = e.currentTarget; 
            var group_id = $(target).data('id');
            var item_id = this.getRespondToken('item');
            var type = $(target).data('type');
            var resItemWrapper = $(target).parent().find('[data-role="rItem-wrapper"]'); 
            var newItem = this.template['respond_'+type+'_item'];
        
            if(type=='hMenu' || type=='if' ){
                var data = {"group_id": group_id,"item_id": item_id,"type": type};
                menuItem_resGroup = this.getMultiMenuResGroup(data); // 멀티메뉴 응답그룹 html 가져오기  
                newItem = newItem.replace(/\{\$menuItem_resGroup}/gi,menuItem_resGroup);
            }

             // 조건 타입 (if) 인 경우 inputfilter 추가 
            if(type=='if'){
                var inputFilterBox = this.template['if_inputFilter_inputBox'];
                inputFilterBox = inputFilterBox.replace(/\{\$filter_label}/gi,'');
                inputFilterBox = inputFilterBox.replace(/\{\$filter_val}/gi,'');
                inputFilterBox = inputFilterBox.replace(/\{\$input_order}/gi,1);
                newItem = newItem.replace(/\{\$inputFilterBox}/gi,inputFilterBox);
                newItem = newItem.replace(/\{\$callIntent}/gi,callIntent);
                newItem = newItem.replace(/\{\$callEntity}/gi,callEntity); 

            }

            newItem = newItem.replace(/\{\$group_id}/gi,group_id); // group_id 치환
            newItem = newItem.replace(/\{\$item_id}/gi,item_id); // item_id 치환
            newItem = this.replaceHtmlDefault(newItem); // $ 값 디폴트 처리 
            $(newItem).appendTo(resItemWrapper);

            this.initFunc();
        
        },      

        
        // 엔터티그룹으로 버튼메뉴 만들기
        addHmenuByEntity: function(e){
            var self = this;
            var target = e.currentTarget;
            var group_id = $(target).data('id');
            var type = $(target).data('type');
            var resItemWrapper = $(target).parent().find('[data-role="rItem-wrapper"]'); 
            var entity_uid = $(target).val();
            var entityArray = this.entity;
            var entityVal;
            var i = 0;
            $.each(entityArray,function(i,entity){
                 var e_uid = entity.uid;
                 if(e_uid==entity_uid) entityVal = entity.value; 
            });
            $.each(entityVal,function(i,ev){
                var item_id = self.getRespondToken('item');
                var data = {"group_id": group_id,"item_id": item_id,"type": type};
                var menuItem_resGroup = self.getMultiMenuResGroup(data); // 멀티메뉴 응답그룹 html 가져오기  
                var newItem = self.template['respond_'+type+'_item'];
                newItem = newItem.replace(/\{\$menuItem_resGroup}/gi,menuItem_resGroup);
                newItem = newItem.replace(/\{\$group_id}/gi,group_id); // group_id 치환
                newItem = newItem.replace(/\{\$item_id}/gi,item_id); // item_id 치환
                newItem = newItem.replace(/\{\$res_title}/gi,ev.name);
                newItem = self.replaceHtmlDefault(newItem); // $ 값 디폴트 처리 
                $(newItem).appendTo(resItemWrapper);
                self.applyDynamicSotable(); 
                                  
            });      

                
            
        },

        // 멀티메뉴 응답 헤더 html 추출함수 
        getMultiMenuResHeader: function(resType,val){
            var label = val[0];
            var label_icon = val[1];
            var resHeader = this.template['respond_menuItem_resHeader'];

            resHeader = resHeader.replace(/\{\$resType}/gi,resType);
            resHeader = resHeader.replace(/\{\$label}/gi,label);
            resHeader = resHeader.replace(/\{\$label_icon}/gi,label_icon);
            if(resType=='text'){
                resHeader = resHeader.replace(/\{\$active}/gi,' active');
                resHeader = resHeader.replace(/\{\$true_false}/gi,'true');
            }else{
                resHeader = resHeader.replace(/\{\$active}/gi,'');
                resHeader = resHeader.replace(/\{\$true_false}/gi,'false');
            }

            return resHeader;
        },

        // 멀티메뉴 응답그룹 html 추출함수 
        getMultiMenuResGroup: function(data){
            var self = this;
            var group_id = data.group_id;
            var item_id = data.item_id;
            var resType = data.type;
            var resGroup = this.template['respond_menuItem_resGroup']; 
            var headerItemHtml = '';
            var guideText;
            var multiMenuContext = this.getMultiMenuContextRowTpl(); // 멀티메뉴 컨텍스트 Tpl 추출 
            multMenuContext = multiMenuContext.replace(/\{\$contextOrder}/gi,1);
            var HeaderData = this.multiMenuHeaderData;
            // 헤더 배열 배치 
            $.each(HeaderData,function(resType,val){
                headerItemHtml += self.getMultiMenuResHeader(resType,val);
            });
            
            if(resType=='hMenu'){
                guideText = '본 메뉴 선택시 액션 및 '+callContext+' 할당';
            }else if(resType=='if'){
                guideText = '본 조건 해당시 액션 및 '+callContext+' 할당';
            }

            // node list > select option
            var nodeList = this.nodeList;
            var nodeOption ='';
            $.each(nodeList,function(i,node){
                 var id = node.id;
                 var name = node.name;  
                 nodeOption += '<option value="'+id+'">!'+name+'</option>'; 
            });

            // legacy api list > select option
            var apiList = this.apiList;
            var apiOption ='';
            $.each(apiList,function(i,api){
                 var uid = api.uid;
                 var name = api.name;  
                 apiOption += '<option value="'+uid+'">'+name+'</option>'; 
            });

            // 치환  
            resGroup = resGroup.replace(/\{\$guideText}/gi,guideText);
            resGroup = resGroup.replace(/\{\$text_active}/gi,' active');
            resGroup = resGroup.replace(/\{\$resGroup_header}/gi,headerItemHtml);
            resGroup = resGroup.replace(/\{\$res_nodeSelect}/gi,nodeOption);
            resGroup = resGroup.replace(/\{\$res_apiSelect}/gi,apiOption);
            resGroup = resGroup.replace(/\{\$res_context}/gi,multiMenuContext);
            resGroup = resGroup.replace(/\{\$link_active}/gi,'');
            resGroup = resGroup.replace(/\{\$img_active}/gi,'');
            resGroup = resGroup.replace(/\{\$tel_active}/gi,'');
            resGroup = resGroup.replace(/\{\$node_active}/gi,'');
            resGroup = resGroup.replace(/\{\$api_active}/gi,'');
            resGroup = resGroup.replace(/\{\$context_active}/gi,'');
            resGroup = resGroup.replace(/\{\$resText_uid}/gi,'');
            resGroup = resGroup.replace(/\{\$resLink_uid}/gi,'');
            resGroup = resGroup.replace(/\{\$resImg_uid}/gi,'');
            resGroup = resGroup.replace(/\{\$resItem_backImg}/gi,'');
            resGroup = resGroup.replace(/\{\$resNode_uid}/gi,'');
            resGroup = resGroup.replace(/\{\$resTel_uid}/gi,'');
            resGroup = resGroup.replace(/\{\$resContext_uid}/gi,'');
            resGroup = resGroup.replace(/\{\$res_text}/gi,'');
            resGroup = resGroup.replace(/\{\$res_link}/gi,'');
            resGroup = resGroup.replace(/\{\$res_tel}/gi,'');
            resGroup = resGroup.replace(/\{\$hide_Move}/gi,'');
            resGroup = resGroup.replace(/\{\$hide_Api}/gi,' hide');

            // 템플릿 데이타셋 체크박스 체크 
            resGroup = resGroup.replace(/\{\$text_checkbox_dlabel}/gi,'');
            resGroup = resGroup.replace(/\{\$link_checkbox_dlabel}/gi,'');
            resGroup = resGroup.replace(/\{\$tel_checkbox_dlabel}/gi,'');
            resGroup = resGroup.replace(/\{\$img_checkbox_dlabel}/gi,'');


            //resGroup = resGroup.replace(/\{\$(.+)\}/,'');

            return resGroup;
        }, 

        // 답변그룹 추가 
        addRespond: function(e){
            var self = this;
            var resHeaderContainer = this.options.resHeaderContainer;
            var resBodyContainer = this.options.resBodyContainer;
            var type;

            // 추가버튼 클릭 이벤트와 기본추가 분기 
            if(e){
               e.preventDefault();      
               var target = e.currentTarget;
               type = $(target).data('type');    
            }else{
               type = 'text';
            }            
            var res_header = this.template['respond_header'];
            var res_header_menuIcon = this.template['respond_header_menuIcon'];
            var type_label = this.template['respond_'+type+'_label'];
            var res_body = this.template['respond_'+type+'_body'];
            var res_item = this.template['respond_'+type+'_item'];             
            var resHeaderItem = $(this.options.resHeaderItem);
            var resBodyItem = $(this.options.resBodyItem);             
            var group_id = this.getRespondToken('group');
            var item_id = this.getRespondToken('item');
    
            // Header item active 제거 
            $.each(resHeaderItem,function(i,ele){
                $(ele).removeClass('active');
            });

            // Body item active 제거 
            $.each(resBodyItem,function(i,ele){
                $(ele).removeClass('active');
            });
            
            // 추가 엘리먼트 헤더(탭메뉴) append & active
            res_header = res_header.replace(/\{\$type_label}/gi,type_label); // 라벨 치환 
            res_header = res_header.replace(/\{\$group_id}/gi,group_id); // group_id 치환 
            res_header = res_header.replace(/\{\$resType}/gi,type); // type value 치환
            res_header = res_header.replace(/\{\$res_active}/gi,'');
            res_header = res_header.replace(/\{\$group_uid}/gi,'');
            res_header = res_header.replace(/\{\$group_uid}/gi,'');
            res_header = res_header.replace(/\{\$resHeaderMenuIcon}/gi, res_header_menuIcon);


            // 엔터티 select 추가
            if(type=='hMenu'){
                var entityArray = this.entity;
                var sel_opt='';
                $.each(entityArray,function(key,e){
                    sel_opt+= '<option value="'+e.uid+'">@'+e.name+'</option>';
                });
                res_body = res_body.replace(/\{\$call_entity}/gi,this.options.callEntity);
                res_body = res_body.replace(/\{\$entity_select}/gi,sel_opt);
 
            }

            // 멀티그룹 respond html 세팅 
            if(type=='hMenu' || type=='if'){
                var data = {"group_id": group_id,"item_id": item_id,"type": type};
                menuItem_resGroup = this.getMultiMenuResGroup(data); // 멀티메뉴 응답그룹 html 가져오기  
                res_item = res_item.replace(/\{\$menuItem_resGroup}/gi,menuItem_resGroup);
            }

            // 조건 타입 (if) 인 경우 inputfilter 추가 
            if(type=='if'){
                var inputFilterBox = this.template['if_inputFilter_inputBox'];
                inputFilterBox = inputFilterBox.replace(/\{\$filter_label}/gi,'');
                inputFilterBox = inputFilterBox.replace(/\{\$filter_val}/gi,'');
                inputFilterBox = inputFilterBox.replace(/\{\$input_order}/gi,1);
                res_item = res_item.replace(/\{\$inputFilterBox}/gi,inputFilterBox);
                res_item = res_item.replace(/\{\$callIntent}/gi,callIntent);
                res_item = res_item.replace(/\{\$callEntity}/gi,callEntity); 

            } 
                     
            // 추가 엘리먼트 바디 append & active
            res_item = res_item.replace(/\{\$group_id}/gi,group_id);
            res_item = res_item.replace(/\{\$item_id}/gi,item_id);
            res_item = res_item.replace(/\{\$checkbox_dLabel}/gi,'');
            res_item = this.replaceHtmlDefault(res_item); // $ 값 디폴트 처리 
            res_body = res_body.replace(/\{\$resItem}/gi,res_item); // group_id 치환  
            res_body = res_body.replace(/\{\$res_active}/gi,'');
            res_body = res_body.replace(/\{\$group_id}/gi,group_id);
            

            
            if(!e){
                $(resHeaderContainer).html('');
                $(resBodyContainer).html('');    
            }
            $(res_header).addClass('active').appendTo(resHeaderContainer); 
            $(res_body).attr("id",group_id).addClass('active').appendTo(resBodyContainer); 
            
            // 초기함수 재호출 
            setTimeout(function(){
               self.initFunc(); 
            },300);
            
        },

        // $값 빈값 처리 
        replaceHtmlDefault: function(html){
            html = html.replace(/\{\$group_uid}/gi,'');
            html = html.replace(/\{\$item_uid}/gi,'');
            html = html.replace(/\{\$res_title}/gi,''); 
            html = html.replace(/\{\$res_summary}/gi,'');
            html = html.replace(/\{\$res_content}/gi,'');
            html = html.replace(/\{\$res_imgUrl}/gi,'');
            html = html.replace(/\{\$res_link1}/gi,'');
            html = html.replace(/\{\$res_link2}/gi,'');
            html = html.replace(/\{\$res_link3}/gi,'');
            html = html.replace(/\{\$res_link3}/gi,'');
            html = html.replace(/\{\$res_active}/gi,'');

            // 템플릿 데이타셋 체크박스 체크 
            html = html.replace(/\{\$checkbox_dlabel}/gi,'');

            return html;

        },

        // 답변그룹  삭제 
        delRespond: function(e){
            e.preventDefault();
            var self = this;
            var resHeaderContainer = this.options.resHeaderContainer;
            var resBodyContainer = this.options.resBodyContainer;
            var resHeaderItem = $(this.options.resHeaderItem);
            var resBodyItem = $(this.options.resBodyItem); 
            var target = e.currentTarget;
            var group_id = $(target).data('id');
            var group_uid = $(target).data('uid');  
            
            if(group_uid){
                if(!confirm('등록한 답변 내용 모두 삭제하시겠습니까?')){
                    return false;
                }    
            }
            

            // Header item 제거 
            $.each(resHeaderItem,function(i,ele){
                var data_id = $(ele).data('id');
                if(data_id == group_id) $(ele).remove();
            });

            // Body item 제거 
            $.each(resBodyItem,function(i,ele){                
                var data_id = $(ele).attr('id');
                if(data_id == group_id) $(ele).remove();
            });  
            
            // 헤더/바디 active 처리
            this.resHeaderActive = null;
            this.setActiveResGroup();

            // DB 데이타 삭제 
            var data = {"linkType":"delete-resGroup","resGroup":group_id};
            this.linkServerData(data);

        },
   
        // 인풋 필터 개채 수 체크 
        getInputFilterBoxTotal: function(){
           var filterInputBox = $(this.options.filterInputBox);
           var total= 0;
           $.each(filterInputBox,function(key,ele){
               if(ele) total++;
           });
          
           return total;
        },
        
        // filter operator 가 있는지 체크 
        hasFilterOperator: function(data){
            var str = data.str;
            var result = str.match(/(:|::|:!|::!|<=|>=|>|<|!=|!)/g);  

            return result; 
        },

        // filter input 입력/백스페이스 체크  
        keyUpFilterInput: function(e){
            var self = this;
            var target = e.currentTarget;
            var val = $(target).val();
            if(val==''){
                this.showFilterBox(e);
            }else{
                var filterItem = '#filter-wrapper .list-group-item'; 
                var filter ='';
                var data = {str: val}
                var entity_operator = this.hasFilterOperator(data);
                var is_match = null;
                // 필터 검색 
                $(filterItem).each(function () {
                    var name = $(this).data('name');
                    if ($(this).text().search(val) > -1) {
                        $(this).show();
                        is_match =true;    
                    } else {
                        $(this).hide();
                        is_match = false;
                    }
                }); 
                
                //console.log([is_match,entity_operator]); 
                             
                // // 검색된 것이 하나도 없는 경우 추가 
                // if(!is_match && entity_operator ==null){
                //     var role_arr = {"#":"intent-item","@":"entity-item"};
                //     var data = {"role":role_arr[filter],"filter": filter,"name": val.replace(filter,''), "uid":null,"is_new":true};
                //     var filterHtml = this.getFilterListTpl(data);
                //     $(filterItem).parent().append(filterHtml);
                    
                //     //this.delegateEvents();
                // }else{

                //     this.hideFilterBox(e);
                // }

                if(entity_operator!=null){
                    this.hideFilterBox();
                }else{
                    this.showFilterBox(e);
                }
            }
            this.activeInput = target; // 현재 작업 중인 input 지정  
        },

        // input 필터 추가 
        addInputFilter: function(e){
            e.preventDefault();
            var self = this;
            var ele = e.currentTarget;
            var order = $(ele).data('order');
            var new_order = parseInt(order)+1;
            var inputFilterWrapper = this.options.inputFilterWrapper;
            var inputFilterBox_tpl = this.template['inputFilter_inputBox'];
            var andOr_tpl = this.template['inputFilter_andOr'];
            
            // inputFilterBox_tpl = inputFilterBox_tpl.replace(/\{\$filter_label}/gi,'');
            // inputFilterBox_tpl = inputFilterBox_tpl.replace(/\{\$filter_val}/gi,'');
            // inputFilterBox_tpl = inputFilterBox_tpl.replace(/\{\$input_order}/gi,new_order);

            // inputFilter Box 추가 
            $(inputFilterBox_tpl).appendTo(inputFilterWrapper);

            // 본인 자신 삭제 (플러스 아이콘) 
            $(ele).remove();

            // 추가 inputFilterBox attr 수정 
            setTimeout(function(){
                var newEle = $(self.options.filterInputBox+':last-child');
                var newEleInput = $(newEle).find(self.options.filterInput);
                var newEleBtnDel = $(newEle).find(self.options.btnDelInputFilter);
                var newEleRecognizeInput = $(newEle).find(self.options.recognizeInput); // 필터 저장 

                // andOr select 추가 
                $(newEle).prepend(andOr_tpl); 
                
                // 기존값 초기화 
                $(newEleInput).val(''); // 라벨값 삭제
                $(newEleInput).attr("placeholder",self.options.inputFilterPlaceholder);

                $(newEleRecognizeInput).val('');             
                 
                 console.log($(newEle).val());

                // order 값 증가  
                $(newEle).attr('data-order',new_order);
                $(newEleInput).attr("data-order",new_order);
                $(newEleBtnDel).attr("data-order",new_order);
            },10);    
            
        },

        // input 필터 삭제  
        delInputFilter: function(e){
            e.preventDefault();
            var ele = e.currentTarget;
            var order = $(ele).data('order');
            var filterInputBox = $(this.options.filterInputBox);
            var total = 0;
            $.each(filterInputBox,function(key,eleBox){
                var boxOrder = $(eleBox).data('order');
                if(order==boxOrder) $(eleBox).remove();
            }); 
                       
            // 모두 삭제된 경우 기본 input Box 추가 
            var inputBoxTotal = this.getInputFilterBoxTotal();
            if(!inputBoxTotal) this.setDefaultInputFilter();  
            else this.setInputFilterAddBtn(); 
           
            // plus 버튼 뒤 andor 삭제 
            $(this.options.btnAddInputFilter).parent().next('.and-or').remove();

        },
        
        // 필터(# or @) 선택시 이벤트 
        setFilterData: function(e){
            e.preventDefault();
            var self = this;
            var target = e.currentTarget;
            var filterName = $(target).data('name');
            var filterUid = $(target).data('uid');
            var filterMark = $(target).data('filter');
            var filterType = $(target).data('type');
            var filterLabel = $(target).text();
            var activeInput = this.activeInput;
            var activeRecognize = $(activeInput).parent().parent().find('input[name="recognize[]"]');
            
            // 선택한 값 저장  
            $(activeInput).val(filterLabel);
            $(activeRecognize).val(filterMark+'|'+filterUid+'|'+filterName+'|'+filterType);
            setTimeout(function(){
                self.hideFilterBox();
            },10);

            // 쿼리 엘리먼트 보여주기 
            this.showQueryEle(activeInput);            
        },
        
        // inputFilter 추가버튼 생성   
        setInputFilterAddBtn: function(){
            var self = this;
            var lastInputFilter = $('[data-role="inputFilter-Box"]:last-child');
            var order = $(lastInputFilter).data('order');
            var addBtn_tpl = this.template['inputFilter_addBtn'];  
            var inputFilter = $(lastInputFilter).find(this.options.recognizeInput);
            
            // 마지막 inputBox input 에 값이 있는 경우에 add btn 추가 
            if($(inputFilter).val()){                
                $(lastInputFilter).append(addBtn_tpl);
                setTimeout(function(){
                    var AddBtn = $(self.options.btnAddInputFilter);
                    $(AddBtn).attr("data-order",order);
                },10);  
            }            
        },

        // 일풋 필터 값 세팅 후 쿼리 엘리먼트 노출 
        showQueryEle: function(ele){
           var self = this; 
           var activeInputBox = $(ele).parent().parent();
           var order = $(activeInputBox).data('order'); // 인풋 갯수 
           var inputFilterBoxTotal = this.getInputFilterBoxTotal();
           var addBtn_tpl = this.template['inputFilter_addBtn'];

           // 추가버튼 생성   
           this.setInputFilterAddBtn();
        },

        // 기본 Intent 가져와서 세팅 
        setIntent: function(){
            var data = {"linkType":"getIntent"};
            this.linkServerData(data);
        },

        // 기본 Entity 가져와서 세팅 
        setEntity: function(){
            var data = {"linkType":"getEntity"};
            this.linkServerData(data);
        },

        // 기본 template 가져와서 세팅 
        getTemplate: function(){
            var data = {"linkType":"getTemplate"};
            this.linkServerData(data);
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
            this.botId = this.options.botId;// 챗봇 id 정보
            this.dialog = this.options.dialog; // dialog 정보
            this.graph = this.options.graph; // 대화 graph 객체를 가져와서 세팅(mxGraph 쪽에서)  
            this.nodeObj = this.options.nodeObj;
            this.undelegateEvents(); // comment box 엘리먼트들 이벤트 바인딩 off 
            this.delegateEvents(); // comment box 엘리먼트들 이벤트 바인딩 on  
            this.testBotUrl = '/R2'+this.botId+'?cmod=dialog';
                
            // mxGraph 에서 패널 컨트롤 
            if(this.options.mxAct){
                var data = this.options.mxData; // mxGraph 에서 넘어온 data 
                var act = data.act;
                
                if(act=='closeSetNodePanel') this.closeSetNodePanel();
                else if(act=='openSetNodePanel'){
                    var node = data.nodeObj;
                    this.nodeObj = node;
                    this.node = node.uid;  
                    this.nodeParent = node.bumo?node.bumo:0;
                    this.nodeName = node.value;
                    $(this.options.inputNodeName).val(this.nodeName);
                    $(this.options.inputNodeId).val(this.node);
                    $(this.options.inputNodeParentId).val(this.nodeParent);

                    this.openSetNodePanel();

                    // node 데이타 가져오기
                    var data = {"linkType":"getNodeData"};
                    self.linkServerData(data); 
                    
                    // node 데이터 가져온 후 프로세스 
                    setTimeout(function(){
                        $('#nodeScroll').scrollTop(0); // scroll top
                        //self.setInputFilterAddBtn(); // 추가 버튼 세팅                         
                    },50); 
     
                }else if(act=='saveGraph'){
                   this.saveGraph();                   
                }else if(act=='addNode'){
                    var data = {"linkType":"addNode","nodeParent":data.nodeParent,"nodeName":data.nodeName};
                    this.linkServerData(data);
                }else if(act=='deleteNode'){
                    var nodeObj = data.nodeObj;
                    var node = nodeObj.uid;
                    var data = {"linkType":"deleteNode","node":node};
                    this.linkServerData(data);
                }     
                this.template = template; // template {} 은 dialog.php 에서 최조에 규정했다. 
                 
            }else{
                // 패널 오픈/클로즈 시에는 가져오지 않고 최초에 세팅된 것을 사용한다. 
                this.getTemplate(); // template 세팅
                this.saveGraph();
            } 

            // 초기 데이타(intent,entity,nodeList) 세팅 
            this.initData();
            
            // 초기함수 호출 
            this.initFunc();

        },
        
        // intent,entity,nodeList ... 
        initData: function(){
           var data = {linkType: "initData"};
           this.linkServerData(data);  
        },

        // 다이얼로그 그래프 저장 함수  
        saveGraph: function(){
            var graph = this.graph;
            var encoder = new mxCodec();
            var node = encoder.encode(graph.getModel());
            var source = mxUtils.getXml(node);
            var data = {"linkType":"save-graph","graph":source};
 
            this.linkServerData(data);              
        },   
        
        // 패널창 노드명 변경시 해당 노드명 업데이트 
        updateNodeName: function(e){
            var _this = e.currentTarget;
            var val = $(_this).val();
            var target = 'name';
            var node = this.nodeObj;
            var data = {"node":node,"target":target,"val":val};
          
            // 노드명 업데이트  
            this.changeGraphNode(data);
        }, 

        // node 상태 함수 : mxgraph 의 graph 객체를 이용해서 특정 노드에 액션을 취한다. 
        changeGraphNode: function(data){
            var graphModel = this.graph.model;
            var node = data.node; // 변경 노드 
            var target = data.target; // 변경 대상  
            var val = data.val; // 변경값 
            if(target=='name') graphModel.setValue(node,val);

        }, 

        // node 데이타 가져와서 세팅 : init > this.options.mxAct > linkServerData({'linkType':'getNodeData'}) > getNodeData
        // ajax 리턴값에 해당 node row 데이타(node,recgnize,resGroupHeader,resGroupBody)를 배열로 받아서 넘긴다.
        setNodeData: function(data){
            var self = this;
            var inputFilterWrapper = this.options.inputFilterWrapper; // inputFilter wrapper
            var inputFilterHtml = data.inputFilterHtml;
            var resHeaderContainer = this.options.resHeaderContainer; // respond Header wrapper
            var resBodyContainer = this.options.resBodyContainer; // respond Body wrapper
            var contextListWrapper = this.options.contextListWrapper; // 컨텍스트 리스트 wrapper
            var resGroupHeaderHtml = data.resGroupHeaderHtml; // 응답 헤더 
            var resGroupBodyHtml = data.resGroupBodyHtml; // 응답 바디 
            var contextListHtml = data.contextListHtml; // 컨텍스트 html 
            
            // 조건 세팅  
            if(inputFilterHtml){
                $(inputFilterWrapper).html(inputFilterHtml);  
            }else{
               this.setDefaultInputFilter(); 
            }

            // 컨텍스트 세팅 
            if(contextListHtml){
                $(contextListWrapper).html(contextListHtml);  
                // 컨텍스트 리스트 출력 
               this.showContextList();
            }else{
               this.setDefaultContextHtml(); 
               this.hideContextList();
            }
            setTimeout(function(){
                self.setInputFilterAddBtn(); // 조건 추가 버튼 세팅
                self.setContextAddBtn(); // 컨텍스트 조건 추가 버튼 세팅
            },100);
            
            //응답 그룹 세팅   
            if(resGroupHeaderHtml){
               $(resHeaderContainer).html(resGroupHeaderHtml);
               $(resBodyContainer).html(resGroupBodyHtml); 
           
            }else{
               this.setDefaultRespondGroup(); 
            }
            
            setTimeout(function(){
                self.initFunc(); 
            },100);

            // 응답그룹 헤더/바디 acitve 처리
            this.setActiveResGroup();         

        },
        
        // 응답그룹 헤더/바디 acitve 처리    
        setActiveResGroup: function(){
            var resHeaderItem = $(this.options.resHeaderItem);
            var resBodyItem = $(this.options.resBodyItem);
            var resHeaderActive = this.resHeaderActive;
            if(resHeaderActive!=null){
                $.each(resHeaderItem,function(i,ele){
                    var group_id = $(ele).data('id');
                    if(group_id==resHeaderActive) $(ele).addClass('active');
                });
            }else{
                $(resHeaderItem).first().addClass('active');
            }   

            if(resHeaderActive!=null){
                $.each(resBodyItem,function(i,ele){
                    var group_id = $(ele).data('id');
                    if(group_id==resHeaderActive) $(ele).addClass('active');
                });
            }else{
                $(resBodyItem).first().addClass('active');
            } 
        },

        // inputFilter 기본 세팅 
        setDefaultInputFilter: function(){
            var inputFilterWrapper = this.options.inputFilterWrapper;
            var inputBox = this.template['inputFilter_inputBox'];  
            inputBox = inputBox.replace(/\{\$filter_label}/gi,'');
            inputBox = inputBox.replace(/\{\$filter_val}/gi,'');
            inputBox = inputBox.replace(/\{\$input_order}/gi,1);
            $(inputFilterWrapper).html(inputBox);
        },

        // 컨텍스트 추가 버튼 세팅  
        setContextAddBtn: function(){
            var self = this;
            var contextRow = this.options.contextListRow;
            var lastContextRow = $(contextRow).last();
            var order = $(lastContextRow).data('order');
            var addBtn_tpl = this.template['contextRow_addBtn']; 
                
            $(lastContextRow).find('[data-role="context-action"]').append(addBtn_tpl);
            setTimeout(function(){
                var AddBtn = $(lastContextRow).find('[data-role="add-contextRow"]');
                $(AddBtn).attr("data-order",order);
            },10);  
                        
        },
        
        // get ContextRow  tpl
        getContextRowTpl: function(){
            var contextRow = this.template['nodeContext_row'];  
            contextRow = contextRow.replace(/\{\$contextName}/gi,'');
            contextRow = contextRow.replace(/\{\$contextValue}/gi,'');
            contextRow = contextRow.replace(/\{\$callContext}/gi,callContext);

            return contextRow;
        },

        // context 리스트 기본 세팅 
        setDefaultContextHtml: function(){
            var self = this;
            var contextListWrapper = this.options.contextListWrapper;
            var contextRow = this.getContextRowTpl();  
            contextRow = contextRow.replace(/\{\$contextOrder}/gi,1);
            $(contextListWrapper).html(contextRow);
        },
        
        // multi Menu Context Row  
        getMultiMenuContextRowTpl: function(){
            var contextRow = this.template['multiMenuContext_row'];
            var addBtn = this.template['multiMenuContextRow_addBtn'];  
            contextRow = contextRow.replace(/\{\$contextName}/gi,'');
            contextRow = contextRow.replace(/\{\$contextValue}/gi,'');
            contextRow = contextRow.replace(/\{\$callContext}/gi,callContext);
            contextRow = contextRow.replace(/\{\$addBtn}/gi,addBtn);
       
            return contextRow; 
        },
        
        // 멀티메뉴 컨텍스트 추가 버튼 세팅  
        setMultiMenuContextAddBtn: function(data){
            var self = this;
            var groupid = data.groupid;
            var itemid = data.itemid;
            var contextListWrapper = $('#'+groupid+'-'+itemid+'-context').find('[data-role="multiMenuContextList-wrapper"]');
            var contextRow = $(contextListWrapper).find('[data-role="multiMenuContextList-row"]');
            var lastContextRow = $(contextRow).last();
            var order = $(lastContextRow).data('order');
            var addBtn_tpl = this.template['multiMenuContextRow_addBtn']; 
            addBtn_tpl = addBtn_tpl.replace(/\{\$group_id}/gi, groupid);
            addBtn_tpl = addBtn_tpl.replace(/\{\$item_id}/gi,itemid);
                
            $(lastContextRow).find('[data-role="multiMenuContext-action"]').append(addBtn_tpl);
            setTimeout(function(){
                var AddBtn = $(lastContextRow).find('[data-role="add-multiMenuContextRow"]');
                $(AddBtn).attr("data-order",order);
            },10);  
                        
        },

        // multiMenu contextRow 추가 
        addMultiMenuContextRow: function(e){
            var self = this;
            var target = e.currentTarget;
            var order = $(target).data('order');
            var groupid = $(target).data('groupid');
            var itemid = $(target).data('itemid');
            var contextListWrapper = $('#'+groupid+'-'+itemid+'-context').find('[data-role="multiMenuContextList-wrapper"]');
            var contextRow = this.getMultiMenuContextRowTpl();  
            contextRow = contextRow.replace(/\{\$contextOrder}/gi,order+1);
            contextRow = contextRow.replace(/\{\$group_id}/gi, groupid);
            contextRow = contextRow.replace(/\{\$item_id}/gi,itemid);
            $(contextListWrapper).append(contextRow);
             // 추가버튼 자신 삭제 및 리세팅  
            $(target).remove();
            // setTimeout(function(){
            //     var data = {groupid:groupid,itemid:itemid};
            //     self.setMultiMenuContextAddBtn(data);
            // },50);          
        },

        // multiMenuu ContextRow 삭제 
        delMultiMenuContextRow: function(e){
            var self = this;
            var target = e.currentTarget;
            var order = $(target).data('order');
            var groupid = $(target).data('groupid');
            var itemid = $(target).data('itemid');
            var delOrder = $(target).data('order');
            var contextListWrapper = $('#'+groupid+'-'+itemid+'-context').find('[data-role="multiMenuContextList-wrapper"]');
            var contextRow = $(contextListWrapper).find('[data-role="multiMenuContextList-row"]');
                   
            // order 값 으로 지우기 
            $(contextRow).each(function(){
                var order = $(this).data('order');
                if(order==delOrder){
                    if(order>1) $(this).remove();  
                    else $(this).find('input').val(''); 
                } 
            });

            if(delOrder>1){
                // 추가버튼 세팅  
                setTimeout(function(){
                     var data = {groupid:groupid,itemid:itemid};
                     self.setMultiMenuContextAddBtn(data);
                },50);
            }
                      
        },

        // contextRow 추가 
        addContextRow: function(e){
            var self = this;
            var target = e.currentTarget;
            var order = $(target).data('order');
            var contextListWrapper = this.options.contextListWrapper;
            var contextRow = this.getContextRowTpl();  
            contextRow = contextRow.replace(/\{\$contextOrder}/gi,order+1);
            $(contextListWrapper).append(contextRow);
            
            // 추가버튼 자신 삭제 및 리세팅  
            $(target).remove();
            setTimeout(function(){
                 self.setContextAddBtn();
            },50);          
        },

        // contextRow 삭제 
        delContextRow: function(e){
            var self = this;
            var target = e.currentTarget;
            var delOrder = $(target).data('order');
            var contextRow = this.options.contextListRow;
            
            // order 값 으로 지우기 
            $(contextRow).each(function(){
                var order = $(this).data('order');
                if(order==delOrder){
                    if(order>1) $(this).remove();  
                    else $(this).find('input').val(''); 
                } 
            });

            if(delOrder>1){
                // 추가버튼 세팅  
                setTimeout(function(){
                     self.setContextAddBtn();
                },50);
            }
                      
        },

        // respondGroup 기본 세팅 
        setDefaultRespondGroup: function(){
            this.addRespond();
        },
   
        // filter 리스트 템플릿 
        getFilterListTpl: function(data){
            var role = data.role;
            var uid = data.uid;
            var type = data.type;
            var name = data.name;
            var is_new = data.is_new;
            var filter = data.filter;
            var Tpl;
            var sys_mark;
            //console.log([data,mark]);
            // sys-mark : V or S 
            if(role=='entity-panelItem'||role=='intent-panelItem'){
               sys_mark = type=='S'?'<span class="badge" data-tooltip="tooltip" title="시스템 제공">S</span>':'';   
            }
            else sys_mark ='';

            // if(role=='intent-item'|| role=='intent-panelItem'||(role=='filter-item'&&name==callIntent)) mark ='#';
            // else if(role=='entity-item' || role=='entity-panelItem' || (role=='filter-item'&&name==callEntity)) mark='@';

            if(is_new){
                Tpl='<a href="#" class="list-group-item list-group-item-action" data-role="add-filterItem" data-name="'+name+'" data-filter="'+filter+'">'+filter+name+' <strong>추가하기</strong></a>';
                 
            }else{
                Tpl='<a href="#" class="list-group-item list-group-item-action" data-role="'+role+'" data-name="'+name+'" data-filter="'+filter+'" data-uid="'+uid+'" data-type="'+type+'">'+filter+name+sys_mark+'</a>'; 
            }  

            return Tpl;
        },

        // if(조건타입) 응답그룹 inputFilter ####################################################################### 

        if_showFilterBox: function(e) {
            var target = e.currentTarget;
            var self = this;
            var filterBox = $(target).parent().parent().parent().parent().find('[data-role="if-selectFilterBox"]');
            var inputFilterWrapper = $(target).parent().parent(); // 현재 input 의 wrapper
            var inputFilterWrapper_parent = $(inputFilterWrapper).parent(); // input 전체 wrapper
            var filterBoxTitle = $(filterBox).find('[data-role="ifFilterBox-title"]');
            var filterListBox = $(filterBox).find('[data-role="ifFilterListBox"]');
            var list ='';
            var intent_data = {role:"ifFilter-item",filter:"#",name: callIntent};
            var entity_data = {role:"ifFilter-item",filter:"@",name: callEntity};
            list += this.getFilterListTpl(intent_data);
            list += this.getFilterListTpl(entity_data);

            // 최초 #인텐트, @엔터티 선택 리스트 가져오기 
            $(filterBoxTitle).html(this.options.if_filterBoxTitleText); 
            $(filterListBox).html(list);
            
            // 전체 input deactive 처리 
            $(inputFilterWrapper_parent).find('[data-role="ifInputFilter-Box"]').removeClass('active'); 
            // input active 처리 
            $(inputFilterWrapper).addClass('active');

            $(filterBox).show();

        },

        // filter input 입력/백스페이스 체크  
        if_keyUpFilterInput: function(e){
            var target = e.currentTarget;
            var val = $(target).val();
            var filterBox = $(target).parent().parent().parent().parent().find('[data-role="if-selectFilterBox"]');
            var inputFilterWrapper = $(target).parent().parent(); // 현재 input 의 wrapper
            var inputFilterWrapper_parent = $(inputFilterWrapper).parent(); // input 전체 wrapper
            var filterListBox = $(filterBox).find('[data-role="ifFilterListBox"]');
            var filterItem = $(filterListBox).find('.list-group-item');
            var data = {str: val}
            var entity_operator = this.hasFilterOperator(data);
            if(val==''){
                this.if_showFilterBox(e);
            }else{                 
                var filter ='';
                var i = 0;
                $(filterItem).each(function () {
                    filter = $(this).data('filter');
                    if ($(this).text().search(val) > -1) {
                        $(this).show();
                        i++;
                    } else {
                        $(this).hide();
                    }
                }); 

                // // 검색된 것이 하나도 없는 경우 추가 
                // if(i==0){
                //     var role_arr = {"#":"ifIntent-item","@":"ifEntity-item"};
                //     var data = {"role":role_arr[filter],"filter": filter,"name": val.replace(filter,''), "uid":null,"is_new":true};
                //     var filterHtml = this.getFilterListTpl(data);
                //     $(filterItem).parent().append(filterHtml);
                //     //this.delegateEvents();
                // }
                if(entity_operator!=null){
                    $(filterBox).hide();
                }else{
                    this.if_showFilterBox(e);
                } 

            }
              
        },

        // 필터박스 닫기 공통함수 
        if_commonHideFilterBox: function(data){
            var filterBox = data.filterBox;
            var ifMenuInputWrapper = data.ifMenuInputWrapper;

            $(filterBox).hide();
            // 전체 input deactive 처리 
            $(ifMenuInputWrapper).find('[data-role="ifInputFilter-Box"]').removeClass('active');
        },

        // 필터(intent,entity) 박스 숨기기 
        if_hideFilterBox: function(e) {
            var target = e.currentTarget;
            var ifMenuInputWrapper = $(target).parent().parent().parent();
            var filterBox = $(target).parent().parent();
            var data = {filterBox: filterBox,ifMenuInputWrapper: ifMenuInputWrapper};
            this.if_commonHideFilterBox(data);            
        },
 
        // 필터 박스에서 #인텐트, @엔터티 클릭시 인텐트/엔터티 데이타 가져와서 출력    
        if_showFilterData: function(e){            
            e.preventDefault();
            var self = this;
            var target = e.currentTarget;
            var filter = $(target).data('filter'); // intent or entiry 
            var filterListBox = $(target).parent();
            var filterBoxTitle = $(target).parent().parent().find('[data-role="ifFilterBox-title"]');
            var activeInputBox = $(target).parent().parent().parent().parent().children().find('.active'); 
            var activeInput = $(activeInputBox).find('input');
            var showIntentList = function(){
                var data = {role:'ifIntent-item',filter: "#" };
                var intentList = self.getIntentList(data); 
                $(filterBoxTitle).html(self.options.if_intentListBoxTitleText); 
                $(filterListBox).html(intentList);
            };
            var showEntityList = function(){
                var data = {role:'ifEntity-item',filter: "@"};
                var entityList = self.getEntityList(data); 
                $(filterBoxTitle).html(self.options.if_entityListBoxTitleText); 
                $(filterListBox).html(entityList);
            };

            // @ or # input 에 입력하고 포커싱  
            $(activeInput).val(filter).focus();

            if(filter=='@') showEntityList();
            else if(filter=='#') showIntentList();             
            
        },

        // 필터(# or @) 선택시 이벤트 
        if_setFilterData: function(e){
            e.preventDefault();
            var self = this;
            var target = e.currentTarget;
            var filterName = $(target).data('name');
            var filterUid = $(target).data('uid');
            var filterMark = $(target).data('filter');
            var filterType = $(target).data('type');
            var filterLabel = $(target).text();
            var activeInputBox = $(target).parent().parent().parent().parent().children().find('.active'); 
            var activeInput = $(activeInputBox).find('[data-role="input-ifFilterData"]');
            var activeRecCondition = $(activeInput).parent().parent().find('[data-role="if-recCondition"]');
            var ifMenuInputWrapper = $(target).parent().parent().parent().parent();
            var filterBox = $(target).parent().parent().parent();
            var data = {filterBox: filterBox,ifMenuInputWrapper: ifMenuInputWrapper};

            // 선택한 값 저장  
            $(activeInput).val(filterLabel);
            $(activeRecCondition).val(filterMark+'|'+filterUid+'|'+filterName+'|'+filterType);
            
            setTimeout(function(){
                self.if_commonHideFilterBox(data);
            },10);
            setTimeout(function(){
                self.if_setInputFilterAddBtn(data);      
            },10);

            
            // 쿼리 엘리먼트 보여주기 
            //this.if_showQueryEle(activeInput);            
        },
        
        // inputFilter 추가버튼 생성   
        if_setInputFilterAddBtn: function(data){
            var self = this;
            var inputWrapper = data.ifMenuInputWrapper;
            var lastInputFilter = $(inputWrapper).find('[data-role="ifInputFilter-Box"]:last-child');
            var order = $(lastInputFilter).data('order');
            var addBtn_tpl = this.template['if_inputFilter_addBtn'];  
            var inputFilter = $(lastInputFilter).find('input[name="if_recCondition[]"]');
            // 마지막 inputBox input 에 값이 있는 경우에 add btn 추가 
                         
            addBtn_tpl = addBtn_tpl.replace(/\{\$input_order}/gi,order);
            $(lastInputFilter).append(addBtn_tpl);
                        
        },

        
        // 인풋 필터 개채 수 체크 
        if_getInputFilterBoxTotal: function(){
           var filterInputBox = $(this.options.filterInputBox);
           var total= 0;
           $.each(filterInputBox,function(key,ele){
               if(ele) total++;
           });
          
           return total;
        },

        // input 필터 추가 
        if_addInputFilter: function(e){
            e.preventDefault();
            var self = this;
            var target = e.currentTarget;
            var _target = target;
            var order = $(target).data('order');
            var new_order = parseInt(order)+1;
            var ifMenuInputWrapper = $(target).parent().parent();
            var inputFilterBox_tpl = this.template['if_inputFilter_inputBox'];
   
            inputFilterBox_tpl = inputFilterBox_tpl.replace(/\{\$input_order}/gi,new_order);
            inputFilterBox_tpl = inputFilterBox_tpl.replace(/\{\$filter_val}/gi,'');
            inputFilterBox_tpl = inputFilterBox_tpl.replace(/\{\$filter_label}/gi,'');
                
            //inputFilter Box 추가 
            $(inputFilterBox_tpl).appendTo(ifMenuInputWrapper); 
            
            // 본인 자신 삭제 (플러스 아이콘)             
            $(ifMenuInputWrapper).find('[data-role="add-ifInputFilter"]').remove();                  
            setTimeout(function(){
                var data = {ifMenuInputWrapper: ifMenuInputWrapper};
                self.if_setInputFilterAddBtn(data); 
            },50);
                
        },

        // input 필터 삭제  
        if_delInputFilter: function(e){
            e.preventDefault();
            var self= this;
            var target = e.currentTarget;
            var order = $(target).data('order');
            var ifMenuInputWrapper = $(target).parent().parent();
            var data = {ifMenuInputWrapper: ifMenuInputWrapper}
            if(order==1){
                var cfm = confirm("해당 조건을 삭제하시겠습니까?");
                if (cfm == false) {
                     return false;
                }else{
                    var ifItem = $(target).parent().parent().parent().parent().parent();
                    var data = $(ifItem).data();
                    $(ifItem).remove();
                    data['linkType'] = "delete-ifResItem";
                    this.linkServerData(data);
                } 
            }  
            else{
                $(target).parent().remove();
                setTimeout(function(){
                    self.if_setInputFilterAddBtn(data); 
                },50);                
            }   
        },
        
        

        // if(조건타입) 응답그룹 inputFilter #######################################################################


        // 필터(intent,entity) 박스 보이기 
        showFilterBox: function(e) {
            var target = e.currentTarget;
            var self = this;
            var filterBox = this.options.filterBox;
            var list ='';
            var intent_data = {role:"filter-item",filter:"#",name: callIntent};
            var entity_data = {role:"filter-item",filter:"@",name: callEntity};
            list += this.getFilterListTpl(intent_data);
            list += this.getFilterListTpl(entity_data);

            this.activeInput = target;

            $(filterBox).show();

            $(this.options.filterBoxTitle).html(this.options.filterBoxTitleText); 
            $(this.options.filterListBox).html(list);

            // 전체 필터 인풋 노멀라이즈 
            var filterInputBox = $(this.options.filterInputBox);
            $.each(filterInputBox,function(key,ele){
                 self.normalizeInput(ele);
            });

            // 필터 인풋 강조
            var this_filterInputBox = $(target).parent().parent(); 
            this.highlightInput(this_filterInputBox);
        },

        // 필터(intent,entity) 박스 숨기기 
        hideFilterBox: function() {
            var filterBox = this.options.filterBox;
            var activeInput = this.activeInput;
            var filterInputBox = $(activeInput).parent().parent();
            $(filterBox).hide();

            // 필터 인풋 초기화   
            this.normalizeInput(filterInputBox);
        },
        
        // input 박스 하일라이트 
        highlightInput: function(inputEle){
            var highlightColor = this.options.highlightColor;
            $(inputEle).css("border-bottom","solid 2px #"+highlightColor);   
        },
        
        // input 박스 하일라이트 해제 
        normalizeInput: function(inputEle){
            var normalizeColor = this.options.normalizeColor;
            $(inputEle).css("border-bottom","none");   
        },

        // 필터 박스에서 #인텐트, @엔터티 클릭시 인텐트/엔터티 데이타 가져와서 출력    
        showFilterData: function(e){            
            e.preventDefault();
            var self = this;
            var target = e.currentTarget;
            var filter = $(target).data('filter'); // intent or entiry 
            var filterInput = this.options.filterInput;
            
            // @ or # input 에 입력하고 포커싱  
            $(this.activeInput).val(filter).focus();

            if(filter=='@') this.showEntityList();
            else if(filter=='#') this.showIntentList();             
            
        },
        
        // 인텐트 리스트 보여주기  
        getIntentList: function(data){
            var self = this;
            var intentArray = this.intent;
            var role = data.role;
            var filter = data.filter;
            var html='';
            $.each(intentArray,function(key,arr){
                var data = {role: role,filter:filter,uid: arr.uid,name: arr.name,type: arr.type};
                html+= self.getFilterListTpl(data);
            });
            return html;  
        },

        showIntentList: function(e){
           var data = {role:'intent-item',filter: "#" };
           var intentList = this.getIntentList(data); 
           $(this.options.filterBoxTitle).html(this.options.intentListBoxTitleText); 
           $(this.options.filterListBox).html(intentList);
        },

        // 엔터티 리스트 보여주기  
        getEntityList: function(data){
            var self = this;
            var entityArray;
            var role = data.role;
            var filter = data.filter;
            var html='';
            
            // 패널 엔터티는 vendor 것만 노출 
            if(role=='entity-panelItem') entityArray = this.vendorEntity; 
            else entityArray = this.entity;

            $.each(entityArray,function(key,data){
                var data = {role: role,filter:filter,uid:data.uid,name:data.name,type:data.type};
                html+= self.getFilterListTpl(data);
            });
            return html;  
        },

        showEntityList: function(e){
           var data = {role:'entity-item',filter: "@"};
           var entityList = this.getEntityList(data); 
           $(this.options.filterBoxTitle).html(this.options.entityListBoxTitleText); 
           $(this.options.filterListBox).html(entityList);
        },

        // 서버 작업 실행 함수 
        linkServerData: function(data){
            var module = this.options.module;
            var self = this;
            var linkType = data.linkType;
            var return_val = ["getIntent","getEntity","getTemplate"];
            var resultContainer = data.resultContainer?data.resultContainer:null;
            var eTarget = data.eTarget?data.eTarget:null;
            data['vendor'] = this.vendor;
            data['bot'] = this.bot;
            data['dialog'] = this.dialog;
            // 멀티메뉴 active tab
            data['nowActiveMultiMenuTab'] = this.nowActiveMultiMenuTab;  
            //console.log(data);
                      
            if(linkType=='getDataTable'){
                var dataTableContainer = data.dataTableContainer;
                var dataTableWrapper = data.dataTableWrapper;
            }              
            // 다이얼로그 노드 클릭해서 패널 오픈된 경우  
            if(this.node!=null){
                data['node'] = this.node;
                data['nodeParent'] = this.nodeParent;
                data['nodeName'] = data['nodeName'];
            } 

            //console.log(data);
            
            // var _data = $.param(data);
            $.ajax({
                url: rooturl+'/?r='+raccount+'&m='+module+'&a=do_dialogPanelAction',
                type: 'post',
                data: data,
                cache: false,
                success: function(response){
                    var result=$.parseJSON(response);
                    if(linkType=='getIntent') self.intent = result.content;
                    else if(linkType=='getEntity') self.entity = result.content;
                    else if(linkType=='getTemplate') self.setTemplate(result); // 템플릿 세팅
                    else if(linkType=='initData'){
                        self.intent = result.intent;
                        self.entity = result.entity;
                        self.vendorEntity = result.vendorEntity;
                        self.nodeList = result.nodeList;
                        self.apiList = result.apiList;
                        self.multiMenuHeaderData = result.multiMenuHeaderData;
                        //console.log(self);
                    } 
                    else if(linkType=='getNodeData') self.setNodeData(result); // node 데이타 세팅  
                    else if(linkType=='saveNode'){
                        // graph 도 저장
                        var data = {"linkType":"getNodeData"};
                        //self.linkServerData(data);
                        setTimeout(function(){
                            self.showNotify('#node_name','저장되었습니다.');
                        },100);
                        setTimeout(function(){
                            self.initFunc();
                        },300);
                    }else if(linkType=='save-graph'){ // dialog 값 세팅
                        self.dialog = result.content;

                    }else if(linkType=='show-resGroup'||linkType=='hide-resGroup'){
                        var tabContainer = self.options.resHeaderContainer;
                        var msg = result.content;
                        self.showNotify(tabContainer,msg);
                    }else if(linkType=='addNode'){
                        var uid = result.content;
                        var data = {"uid": uid}; 
                        self.options.callBackGraph(data); // 그래프 쪽으로 신규 uid 를 넘겨준다. 
                    }else if(linkType=='deleteNode'){
                        self.closeSetNodePanel();
                    }else if(linkType=='get-recommendData'){
                        var word_array = result.word;
                        var textLoader = self.options.recommendTextLoader;
                        // $.each(word_array,function(i,word){
                        //     $(textLoader).append(' '+word);
                        // });
                        // $(textLoader).letterfx({"fx":"spin fade grow smear swirl wave fly-bottom fly-right","backwards":false,"timing":30,"fx_duration":"600ms","letter_end":"restore","element_end":"restore"});
                        $(textLoader).rotator({
                            starting: 0,
                            ending: 100,
                            percentage: true,
                            color: '#1caafc',
                            lineWidth: 7,
                            timer: 20,
                            radius: 40,
                            fontStyle: 'Calibri',
                            fontSize: '20pt',
                            fontColor: '#1caafc',
                            backgroundColor: 'lightgray',
                            callback: function () {}
                        });
                        setTimeout(function(){
                            //alert('완룔');
                            $(textLoader).css('display','none');
                            $(resultContainer).html(result.list);
                        },2100);
                        
                    }else if(linkType=='getIntentEx'){
                        $(resultContainer).html(result.content);
                    }else if(linkType=='save-intent'){
                        var intent_uid = result.intent_uid;
                        var intent_name = result.intent_name;
                        var data = {linkType: "getIntent"}
                        self.linkServerData(data);
                        setTimeout(function(){
                            var _data = {role: "open-all", uid: intent_uid, name: intent_name};
                            self.controlIntentPanel(_data);
                            self.showNotify('#intentEx-submitForm','저장되었습니다.');
                        },100);
                    }else if(linkType=='delete-intentEx'){
                        setTimeout(function(){
                            self.showNotify('#intentEx-submitForm','삭제되었습니다.');
                        },100);
                    }else if(linkType=='delete-intent'){
                        var data = {linkType: "getIntent"}
                        self.linkServerData(data);
                        setTimeout(function(){
                            var _data = {role: "deleted-intent"};
                            self.controlIntentPanel(_data);
                            self.showNotify('#intentEx-submitForm','삭제되었습니다.');
                        },100);
                    }else if(linkType=='getEntityEx'){
                        $(resultContainer).html(result.content);
                    }else if(linkType=='save-entity'){
                        var entity_uid = result.entity_uid;
                        var entity_name = result.entity_name;
                        var data = {linkType: "getEntity"}
                        self.linkServerData(data);
                        setTimeout(function(){
                            var _data = {role: "open-all", uid: entity_uid, name: entity_name};
                            self.controlEntityPanel(_data);
                            self.showNotify('#entityEx-submitForm','저장되었습니다.');
                        },100);
                    }else if(linkType=='delete-entityEx'){
                        setTimeout(function(){
                            self.showNotify('#entityEx-submitForm','삭제되었습니다.');
                        },100);
                    }else if(linkType=='delete-entity'){
                        var data = {linkType: "getEntity"}
                        self.linkServerData(data);
                        setTimeout(function(){
                            var _data = {role: "deleted-entity"};
                            self.controlEntityPanel(_data);
                            self.showNotify('#entityEx-submitForm','삭제되었습니다.');
                        },100);
                    }else if(linkType=='saveEntityData'){
                        //self.showNotify('#dataSetPanel-top','저장되었습니다.');
                    }else if(linkType=='getDataTable'){
                        $(dataTableContainer).find('[data-role="dataTable-scroll"]').html(result.content);
                        $(dataTableContainer).find('[data-role="dataTable-scroll"]').css({"width": result.width});
                        //$(dataTableWrapper).append(result.entityTable);
                    
                    }else if(linkType =='get-legacyApiParam'){
                        var _data = result;
                        _data['role'] = 'get';
                        self.controlSetApiPanel(_data);

                        setTimeout(function(){
                            self.initFunc();
                        },300);

                    }else if(linkType =='test-legacyApiParam'){
                        self.showJsonEditor(result);
                        //console.log(response);
                    }else if(linkType =='save-dialogResApiParamOutput'){
                         self.showNotify('#apiCode-wrapper','출력 내용이 저장되었습니다.');
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
        getRespondToken : function(type){
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
        
        // 질문시작 시간 
        getStartTime : function(){
            var mtime = this.getMicrotime();
            var mtime_arr = mtime.split(' ');
            var result = parseInt(mtime_arr[0])+parseInt(mtime_arr[1]);

            return result;
        },  
       
    };

    $.fn.KRE_Panel = function(options) {
        return this.each(function() {
            var panel = Object.create(KRE_Panel);
            $.data(this, 'panel', panel);
            panel.init(options || {}, this);
        });
    };
}));