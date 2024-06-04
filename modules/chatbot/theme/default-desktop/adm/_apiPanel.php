
<link href="<?php echo $g['url_module_skin']?>/css/setNodePanel_legacy.css" rel="stylesheet">
<div class="right-panel" data-role="rightPanel">
    <div class="rightPanel-inner">
        <div class="topABS-div">
            <span class="topBtn">
                 <!--<button data-role="btn-applyApi" data-type="save" class="btn btn-danger">적용</button>-->
            </span>
            <span data-role="close-setApiPanel" class="btn-close">                        
                <span class="cb-icon cb-icon-close"></span>
            </span>
        </div>
        <div class="form-group row itemName-wrapper" style="margin-bottom: 0;">
            <input type="hidden" name="api" />
            <input type="hidden" name="req" />
            <input type="hidden" name="itemOC" />
            <input type="hidden" name="itemID" />
            <input type="hidden" name="method" value="GET"/>
            <input type="hidden" name="statusCode" value="0"/>
            <label class="col-md-2 itemName apiSet-label"><span>API 이름</span></label>
            <div class="col-md-10 panelIntentName-wrapper">
                <input type="text" class="form-control itemName" name="item_name" placeholder="API 이름을 입력해주세요(필수)" disabled>
            </div>
        </div>
        <div class="form-group row itemName-wrapper api-formGroup">
            <label class="col-md-2 itemName apiSet-label"><span>API 요약</span></label>
            <div class="col-md-10 panelIntentName-wrapper">
                <input type="text" class="form-control itemName" name="item_intro" placeholder="API 설명을 입력해주세요(선택)" disabled>
            </div>
        </div>

        <div class="form-group row itemName-wrapper api-formGroup">
            <label class="col-md-2 itemName apiSet-label"><span>기본 URI</span></label>
            <div class="col-md-10 panelIntentName-wrapper">
                <div class="input-group">
                    <div class="input-group-btn">
                        <span class="label label-rouded label-default" data-role="method-wrapper">GET</span>
                    </div>
                    <input type="text" name="basic_url" class="form-control itemName" value="" disabled>
                </div>
            </div> 
        </div>
        <div class="form-group row itemName-wrapper reqPrint-tab">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#reqPrint-request" data-toggle="tab" class="box-title" >요청값 설정</a>
                </li>
                <li>
                    <a href="#reqPrint-print" data-toggle="tab" class="box-title">출력값 설정</a>
                </li>
            </ul>
        </div>                            
        <div class="panelScroll tab-content" >
            <div class="tab-pane active" id="reqPrint-request">
                <div class="queryParamList-wrapper tbl-param" data-role="queryParamList-wrapper">
                    <div class="queryParam-controlWrapper paramControl-wrapper">
                        <h5 class="paramList-title">Query
                       
                        </h5>
                    </div>  
                    <table class="table table-bordered">
                        <thead>
                            <colgroup>
                                <col width="48%">
                                <col width="1%">
                                <col width="48%">
                            </colgroup>
                        </thead>
                        <tbody data-role="queryParam-wrapper">
                        </tbody>
                    </table>
                </div>
                <div class="pathParamList-wrapper tbl-param" data-role="pathParamList-wrapper">
                    <div class="pathParam-controlWrapper paramControl-wrapper">
                        <h5 class="paramList-title">Path
                            
                        </h5>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <colgroup>
                                <col width="48%">
                                <col width="1%">
                                <col width="48%">
                            </colgroup>
                        </thead>
                        <tbody data-role="pathParam-wrapper">
                        </tbody>
                    </table>             
                </div>
                <div class="headerParamList-wrapper tbl-param" data-role="headerParamList-wrapper">
                    <div class="headerParam-controlWrapper paramControl-wrapper">
                        <h5 class="paramList-title">Header
                           
                        </h5>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <colgroup>
                                <col width="48%">
                                <col width="1%">
                                <col width="48%">
                            </colgroup>
                        </thead>
                        <tbody data-role="headerParam-wrapper">
                        </tbody>
                    </table>                                        
                </div>
                <div class="bodyParamList-wrapper tbl-param" data-role="bodyParamList-wrapper">
                    <div class="bodyParam-controlWrapper paramControl-wrapper" data-role="addFormParam-wrapper">
                        <h5 class="paramList-title">Body
                            
                        </h5>
                    </div>
                    <div class="api-bodyWrapper">
                         <p class="apiBody-guide" data-role="apiBody-guide">
                            Payloads 는 <code>POST,PUT</code> 요청인 경우 허용됩니다.
                        </p>
                        <div class="apiBody-wrapper" data-role="apiBody-wrapper">
                            <div class="apiBody-content" id="apiBody-textWrapper" data-role="bodyTypeText-wrapper">
                                <input type="hidden" name="body_uid"/>
                                <textarea class="form-control" row="30" name="body_val"></textarea>
                            </div>
                            <div class="apiBody-content" id="apiBody-formWrapper" data-role="formParamList-wrapper">
                                 <div class="formParamList-wrapper tbl-param" >
                                    <table class="table table-bordered">
                                        <colgroup>
                                            <col width="48%">
                                            <col width="1%">
                                            <col width="48%">
                                        </colgroup>
                                        <thead>
                                        <tbody data-role="formParam-wrapper">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                 </div>   
            </div>
            <div class="tab-pane" id="reqPrint-print">
                <div class="row">
                    <div class="col-md-6" data-role="apiResult-wrapper" >
                        <div id="jsonEditor-wrapper">
                        </div>
                    </div>
                    <div class="col-md-6" id="apiCode-wrapper">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="input-group" >
                                    
                                    <input type="text" data-role="show-apiResEle" placeholder="좌측에서 적용할 response 값을 클릭해주세요" class="form-control" >
                                    <div class="input-group-btn">
                                         <button data-role="clipboard-copy" data-clipboard-text="" data-container="#apiCode-wrapper" data-feedback="API 코드가 복사되었습니다." class="btn btn-default"> 복사</button>
                                    </div>
                                </div>
                                <div class="apiRes-wrapper">
                                    <ul class="nav nav-tabs">
                                        <li class="active">
                                            <a href="#legacy-text" data-toggle="tab">
                                                <i class="fa fa-file-text-o" aria-hidden="true"></i> 텍스트  
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#legacy-context" data-toggle="tab">
                                                <i class="fa fa-dollar-sign" aria-hidden="true"></i> 컨텍스트  
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="legacy-text">
                                            <div class="resItem">
                                                <textarea class="form-control" rows="10" placeholder="여기에 메세지를 입력해주세요" data-role="apiRes-text"></textarea>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="legacy-context">
                                            <div class="resItem">
                                                <div class="context-wrapper multiMenuContext-container" data-role="res-context">
                                                    <table class="table">
                                                        <tbody data-role="apiResContextList-wrapper" class="multiMenuContext-wrapper">
                                                            <!-- context 리스트 동적 출력 -->                                                            
                                                        </tbody>
                                                    </table>
                                                </div>
                                   
                                            </div>
                                        </div>
                                    </div>
                                </div>    
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </div>                
</div>

