<div class="form-group itemName-wrapper">
    <input type="hidden" name="panelIntent_uid" />
    <div class="input-group panelIntentName-wrapper">
        <input type="text" class="form-control itemName" name="panelIntent_name" placeholder="생성할 <?php echo $callIntent?>명을 입력해주세요">
        <span class="input-group-btn">
            <button class="btn btn-default hidden" data-role="del-intent" data-uid="" >
                <i class="fa fa-trash-o"></i>
            </button>
        </span>
    </div>
</div>
<div class="form-group middle-wrapper">
    <h4 class="panelTop-title" style="width: 70%">관련 예시문장</h4>
    <ul class="panelTop-menuWrapper">
        <li data-role="add-intentEx">
            <button type="button" class="btn btn-default btn-add"><i class="fa fa-plus"></i> 추가</button> 
        </li>
    </ul>   
</div>
<div class="panelScroll" >
    <div data-role="intentEx-wrapper">
         <!-- 서버에서 가져온다.-->
    </div>
</div>
<div class="form-group btnSave-wrapper" id="intentEx-submitForm">
    <div class="row">
        <div class="col-md-8 col-md-offset-4"> 
             <button data-role="btn-intentPNsubmit" data-iact="save" class="btn btn-primary">저장</button> 
             <button data-role="btn-intentPNsubmit" data-iact="close-right" class="btn btn-default">취소</button>
        </div>
    </div>    
</div>
