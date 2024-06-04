<script type="text/javascript" src="<?php echo $g['url_module'].'/lib/js/respondGroup_sortable2.js'?>"></script>
<script type="text/javascript" src="<?php echo $g['url_module'].'/lib/js/rotator.js'?>"></script>


    <input type="hidden" name="node_id" data-role="input-nodeId"/> <!-- 그래프 쪽에서 랜덤 생성 -->
    <input type="hidden" name="node_parent" data-role="input-nodeParentId"/> <!-- 그래프 쪽에서 랜덤 생성 -->
    <input type="hidden" name="is_unknown" value="0"/> <!-- 대화상자 못찾은 경우 응답하는 대화상자 여부값 -->
    <input type="hidden" name="use_topic" value="0" /> <!-- 토픽 값 -->
    
    <div class="form-group ele-hideFilterBox" id="node_name">
        <input type="text" name="node_name" value="" autocomplete="off" class="form-control" data-role="input-nodeName" placeholder="대화상자명을 입력해주세요">
    </div>
    <div class="panelScroll">
        <form autocomplete="off"> 
            <div class="form-group" data-role="filterBox-formgroup">
                <label for="recognize">
                    인풋 
                    <i class="fa fa-question-circle guide-question guide-input" aria-hidden="true" data-toggle="tooltip" id="guide-input"></i>
                </label>
                <div class="inputFilter-wrapper" data-role="inputFilter-wrapper">
                     <!-- 조건값들 동적 추출 -->
                </div>
                <div id="filterBox" data-role="selectFilterBox">
                    <div id="filter-wrapper">
                        <button type="button" class="close" data-role="hide-filterBox" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                        </button>
                        <h5 id="filter-guide" data-role="filterBox-title">
                            아래에서 검색대상을 선택해주세요
                        </h5>
                        <div class="list-group" id="filter-list" data-role="filterListBox">
                            <a href="#" class="list-group-item list-group-item-action" data-role="filter-item" data-filter="#">#<?php echo $callIntent?></a>
                            <a href="#" class="list-group-item list-group-item-action" data-role="filter-item" data-filter="@">@<?php echo $callEntity?></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group ele-hideFilterBox hide" id="nodeContext-container" data-role="nodeContext-container">
                <label><?php echo $callContext?></label> <!-- 컨텍스트 라벨 -->
                <div class="context-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="contextName"><?php echo $callContext?> 변수</th>
                                <th class="contextValue" colspan="2"><?php echo $callContext?> 값</th>
                            </tr>

                        </thead>
                        <tbody data-role="nodeContextList-wrapper">
                             <!-- 컨텍스트 리스트  -->        
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- 아웃풋 시작 -->            
            <div class="form-group ele-hideFilterBox" id="output-formGroup">
                <div id="btn-outputMenu">
                    <div class="dropdown">
                        <span class="dropdown-toggle" id="show-outputMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                           <i class="fa fa-ellipsis-v" aria-hidden="true"></i>  
                        </span> 
                        <ul class="dropdown-menu" aria-labelledby="show-outputMenu">
                            <li class="dropdown-item" data-role="select-outputMenu" data-type="contextForm">
                                 <?php echo $callContext?> 폼 출력 
                            </li>
                            <!--
                            <li class="dropdown-item" data-role="select-outputPrintMethod" data-method="">
                                아웃풋 <code data-role="outputPrintMethod-wrapper"></code> 
                            </li>
                            -->
                        </ul>
                    </div>
                </div>
                <div class="form-group" style="display:none;" data-role="topicList-guide">
                    <h5 class="topicList-guide">아래의 토픽을 선택해주세요</h5>
                    <div class="list-group" data-role="topicList-wrapper" style="display:none;">
                         <!-- 토픽 리스트 동적 출력 -->
                    </div>
                </div>    
                <div data-role="setOutput-form">
                    <label for="recognize" data-role="test-click">아웃풋 </label>
                    <div class="dd respondGroup">
                        <div id="btn-addRespond">
                            <div class="dropdown">
                                <span class="dropdown-toggle" id="add-respond" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                   <i class="fa fa-plus-circle" aria-hidden="true"></i>  
                                </span> 
                                <ul class="dropdown-menu" aria-labelledby="add-respond">
                                    <li class="dropdown-item" data-role="btn-addRespond" data-type="text">
                                        <i class="fa fa-file-text-o" aria-hidden="true"></i>텍스트
                                    </li>    
                                    <li class="dropdown-item" data-role="btn-addRespond" data-type="card">
                                        <i class="fa fa-square-o" aria-hidden="true"></i>카드
                                    </li>    
                                    <li class="dropdown-item" data-role="btn-addRespond" data-type="img">
                                        <i class="fa fa-picture-o" aria-hidden="true"></i>이미지 
                                    </li>    
                                    <li class="dropdown-item" data-role="btn-addRespond" data-type="hMenu">
                                        <i class="fa fa-th-large" aria-hidden="true"></i>버튼 메뉴
                                    </li>
                                    <li class="dropdown-item" data-role="btn-addRespond" data-type="if">
                                        <i class="fa fa-info" aria-hidden="true"></i><i class="fa fa-facebook-f" aria-hidden="true"></i>조건
                                    </li>    
                                    <!-- <li class="dropdown-item" data-role="btn-addRespond" data-type="qrcode">
                                        <i class="fa fa-qrcode" aria-hidden="true"></i>QR코드
                                    </li>    
                                    <li class="dropdown-item" data-role="btn-addRespond" data-type="map">
                                        <i class="fa fa-map-marker" aria-hidden="true"></i>지도
                                    </li>   -->  
                                </ul>
                            </div>
                        </div>
                        <ul id="respondGroup-tab" class="nav nav-tabs" data-role="resHeaderContainer">
                             <!-- 아웃풋 탭 출력  -->
                        </ul>
                        <div class="tab-content" data-role="resBodyContainer" >
                            <!-- 아웃풋 content 출력  -->
                        </div>
                    </div>           
                    <div class="form-group ele-hideFilterBox">
                        <label for="recognize" data-role="test-click">액션 </label>
                        <div data-role="nodeAction-container">
                            <!-- nodeAction html 출력  -->  
                        </div>     
                    </div>
                </div>    
            </div>
            <!-- 아웃풋 끝 -->

         </form>   
     
    </div>
    <div class="btnSave-wrapper" id="action-form">
        <button data-role="btn-submit" data-pact="config" class="btn btn-secondary">설정</button>
        <button data-role="btn-submit" data-pact="save" class="btn btn-primary">저장</button>
        <button data-role="btn-submit" data-pact="delete" class="btn btn-info btn-dark" id="pact_delete" >삭제</button>
    </div>


