 <div class="rightPanel-inner">
    <div class="topABS-div">
        <span class="topBtn">
             <button data-role="btn-saveTest" data-type="save" class="btn btn-primary">저장</button>
        </span>
        <span data-role="close-setApiPanel" class="btn-close">                        
            <span class="cb-icon cb-icon-close"></span>
        </span>
    </div>
    <div class="form-group row itemName-wrapper" style="margin-bottom: 0;">
        <input type="hidden" name="api" />
        <input type="hidden" name="req" />
        <input type="hidden" name="method" value="GET"/>
        <input type="hidden" name="statusCode" value="0"/>
        <label class="col-md-1 itemName apiSet-label"><span>API 이름</span></label>
        <div class="col-md-11 panelIntentName-wrapper">
            <input type="text" class="form-control itemName" name="item_name" placeholder="API 이름을 입력해주세요(필수)">
        </div>
    </div>
    <div class="form-group row itemName-wrapper" style="padding-top:0;margin-top: 0;margin-bottom:0">
        <label class="col-md-1 itemName apiSet-label"><span>API 요약</span></label>
        <div class="col-md-11 panelIntentName-wrapper">
            <input type="text" class="form-control itemName" name="item_intro" placeholder="API 설명을 입력해주세요(선택)">
        </div>
    </div>
<!--        <div class="form-group row itemName-wrapper" style="padding-top:0;margin-top: 0;">
        <label class="col-md-1 itemName apiSet-label"><span>API 응답</span></label>
        <div class="col-md-11 panelIntentName-wrapper">
            <input type="hidden" name="node_path" />
            <input type="text" class="form-control itemName" name="nodePath_label" placeholder="아래 결과값(Response)에서 선택해주세요." disabled>
        </div>
    </div> -->
    <div class="panelScroll" data-role="panel-scroll" >
        <div class="row">
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default" data-role="method-wrapper">GET</button>
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span class="caret"></span></button>
                        <ul class="dropdown-menu">
                            <li><a href="#" data-role="method-item" data-method="GET">GET</a></li>
                            <li><a href="#" data-role="method-item" data-method="POST">POST</a></li>
                            <li><a href="#" data-role="method-item" data-method="PUT">PUT</a></li>
                            <li><a href="#" data-role="method-item" data-method="DEL">DETETE</a></li>

                        </ul>
                    </div>
                    <input type="text" name="basic_url" class="form-control" value="">
                    <div class="input-group-btn">
                         <button data-role="btn-saveTest" data-type="test" class="btn btn-danger"> 테스트 전송</button>
                    </div>
                </div> 
            </div>                            
        </div>
        <div class="row">
            <div class="col-md-offset-1 col-md-11" id="showHide-queryParam">
                <a href="##" data-role="showHide-queryParam" id="queryParamText-wrapper">
                    <span id="queryParem-text">Query 파라미터</span>  
                </a>
                <div class="queryParamList-wrapper tbl-param" data-role="queryParamList-wrapper">
                    <table class="table table-bordered">
                        <colgroup>
                            <col width="4%">
                            <col width="45%">
                            <col width="1%">
                            <col width="46%">
                            <col width="4%">
                        </colgroup>
                        <thead>
                        <tbody data-role="queryParam-wrapper">
                        </tbody>
                    </table>
                    <div class="headerParam-controlWrapper">
                        <span>
                            <button class="btn btn-outline btn-default btn-rounded" data-role="btn-addParam" data-type="query">
                                <i class="fa fa-plus"></i> Query 추가
                            </button>
                        </span>
                    </div> 
                </div>
            </div>   
        </div>
        <div class="row">
            <div class="col-md-offset-1 col-md-11" id="showHide-pathParam">
                <a href="##" data-role="showHide-pathParam" id="pathParamText-wrapper">
                    <span id="queryParem-text">Path 파라미터</span>  
                </a>
                <div class="pathParamList-wrapper tbl-param" data-role="pathParamList-wrapper">
                    <table class="table table-bordered">
                        <colgroup>
                            <col width="4%">
                            <col width="45%">
                            <col width="1%">
                            <col width="46%">
                            <col width="4%">
                        </colgroup>
                        <thead>
                        <tbody data-role="pathParam-wrapper">
                        </tbody>
                    </table>
                    <div class="headerParam-controlWrapper">
                        <span>
                            <button class="btn btn-outline btn-default btn-rounded" data-role="btn-addParam" data-type="path">
                                <i class="fa fa-plus"></i> Path 추가
                            </button>
                        </span>
                    </div> 
                </div>
            </div>   
        </div>
        <div class="row headerBody-wrapper">
            <div class="col-md-6">
                <div class="white-box param-whiteBox">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#headerBody-header" data-toggle="tab" class="box-title" >Headers</a>
                        </li>
                        <li>
                            <a href="#headerBody-body" data-toggle="tab" class="box-title">Body</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="headerBody-header">
                            <div class="headerParam-tblWrapper tbl-param">
                                <table class="table table-bordered">
                                    <colgroup>
                                        <col width="4%">
                                        <col width="45%">
                                        <col width="1%">
                                        <col width="46%">
                                        <col width="4%">
                                    </colgroup>
                                    <thead>
                                    <tbody data-role="headerParam-wrapper">
                                    </tbody>
                                </table>                                        
                            </div>
                            <div class="headerParam-controlWrapper">
                                <span>
                                    <button class="btn btn-outline btn-default btn-rounded" data-role="btn-addParam" data-type="header">
                                        <i class="fa fa-plus"></i> Header 추가
                                    </button>
                                </span>
                                <span class="ml15">
                                    <button class="btn btn-outline btn-default btn-rounded" data-role="btn-addParam" data-type="auth">
                                        <i class="fa fa-key"></i> Authonrization 추가
                                    </button>
                                </span>
                            </div>
                        </div>
                        <div class="tab-pane" id="headerBody-body">
                             <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    <div class="api-bodyWrapper">
                                         <p class="apiBody-guide" data-role="apiBody-guide">
                                            Payloads 는 <code>POST,PUT</code> 요청인 경우 허용됩니다.
                                        </p>
                                        <div class="apiBody-wrapper" data-role="apiBody-wrapper">
                                            <div class="apiBody-ddMenu">
                                                <ul class="nav nav-tabs apiBody-right">
                                                    <li class="dropdown left">
                                                        <input type="hidden" name="body_type" value="text"/>
                                                        <a href="##" class="dropdown-toggle" data-toggle="dropdown"><i></i><span data-role="bodyType-label">Text</span> <span class="caret"></span></a>
                                                        <ul class="dropdown-menu pull-right apiBody-ddLi">
                                                            <li><a href="#" data-role="select-bodyType" data-type="text"><i></i> Text </a></li>
                                                            <li><a href="#" data-role="select-bodyType" data-type="form"><i></i> Form </a></li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </div>       
                                            <div class="apiBody-content" id="apiBody-textWrapper" data-role="bodyTypeText-wrapper">
                                                <input type="hidden" name="body_uid"/>
                                                <textarea class="form-control" row="30" name="body_val"></textarea>
                                            </div>
                                            <div class="apiBody-content" id="apiBody-formWrapper" data-role="formParamList-wrapper">
                                                 <div class="formParamList-wrapper tbl-param" >
                                                    <table class="table table-bordered">
                                                        <colgroup>
                                                            <col width="4%">
                                                            <col width="45%">
                                                            <col width="1%">
                                                            <col width="46%">
                                                            <col width="4%">
                                                        </colgroup>
                                                        <thead>
                                                        <tbody data-role="formParam-wrapper">
                                                        </tbody>
                                                    </table>
                                                    <div class="headerParam-controlWrapper">
                                                        <span>
                                                            <button class="btn btn-outline btn-default btn-rounded" data-role="btn-addParam" data-type="form">
                                                                <i class="fa fa-plus"></i> form 파라미터 추가
                                                            </button>
                                                        </span>
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
            <div class="col-md-6">
                <div class="white-box param-whiteBox">
                    <h3 class="box-title">Response <span class="status-code sc-default" data-role="apiSCD-wrapper"><i class="fa fa-circle"></i></span></h3>
                    <div class="result-wrapper" data-role="apiResult-wrapper" >
                        <p class="result-guide" data-role="result-guide">
                            [테스트 전송] 을 하시면 응답결과가 출력됩니다.
                        </p>
                        <div id="jsonEditor-wrapper">
                        </div>                       
                    </div>
                </div>   
            </div>
        </div>

    </div> 
</div>  