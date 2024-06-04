
<div class="form-group panel-menu">
    <h4 class="panelTop-title"><?php echo $callEntity?></h4>
    <ul class="panelTop-menuWrapper">
        <li data-role="btn-entityPanelTop" data-type="ai" data-tooltip="tooltip" title="보톡스 AI 추천받기">
            <button type="button" data-role="ai-recommend" data-rtype="entity" class="btn btn-default">봇톡스 AI 추천받기 </button>
        </li>
        <li data-role="btn-entityPanelTop" data-type="add" data-tooltip="tooltip" title="인텐트 추가등록">
            <button type="button" data-role="add-entity" class="btn btn-default"><?php echo $callEntity?>생성</button>
        </li>
        <li data-role="btn-entityPanelTop" data-type="csv" data-tooltip="tooltip" title="csv 업로드">
            <button type="button" data-role="upload-entity" class="btn btn-default">CSV</button> 
        </li>
    </ul>   
</div>
<div class="form-group panel-search">
    <div class="input-group">
        <input type="text" class="form-control" placeholder="<?php echo $callEntity?>명을 입력해주세요">
        <span class="input-group-addon"><i class="fa fa-search"></i></span>
    </div>
</div>
<div class="panelScroll">
    <div class="list-group" data-role="entityList-Box">
        <!-- 패널 오픈시 동적 업데이트 -->
    </div>
</div>
<div class="form-group btnSave-wrapper">
    <div class="row">
        <div class="col-md-7 col-md-offset-5"> 
    
             <button data-role="btn-entityPNsubmit" data-iact="close-left" class="btn btn-default">닫기</button>
        </div>
    </div>    
</div>


