<div class="form-group itemName-wrapper">
    <input type="hidden" name="topicTemp_uid" data-role="topicTemp-uid"/>
    <div class="input-group panelIntentName-wrapper">
        <input type="text" class="form-control itemName" name="topic_name" data-role="topic-nameEle" placeholder="생성할 토픽명을 입력해주세요" autocomplete="off">
    </div>
</div>
<div class="form-group middle-wrapper">
    <h4 class="panelTop-title">추가방식 선택</h4>
</div>
<div class="form-group middle-wrapper">
    <div class="col-md-12 panel-col">
         <div class="radio-list">
            <label class="radio-inline">
                <input type="radio" name="addMethod" value="blank" checked=""> 처음부터 시작  
            </label>
            <label class="radio-inline">
                <input type="radio" name="addMethod" value="temp"> 템플릿 선택 
            </label>
        </div>
    </div>
</div>

<div class="panelScroll" >
    <div class="list-group" data-role="topicTempList-wrapper" style="display:none;">
         <!-- 서버에서 가져온다.-->
    </div>
</div>
<div class="form-group btnSave-wrapper" id="intentEx-submitForm">
    <div class="row">
        <div class="col-md-8 col-md-offset-4"> 
             <button data-role="btn-topicPNsubmit" data-tact="save" class="btn btn-primary">저장</button> 
             <button data-role="btn-topicPNsubmit" data-tact="close" class="btn btn-default">취소</button>
        </div>
    </div>    
</div>
