<div class="form-group panel-menu">
    <h4 class="panelTop-title" style="width: 100%;">봇톡스 AI 추천받기</h4>
</div>
<div class="form-group panel-search">
    <div class="input-group">
        <input type="text" class="form-control" placeholder="추천받을 내용을 입력해주세요" data-role="rec-keyword">
        <span class="input-group-addon" data-role="get-recommendData"><i class="fa fa-search"></i></span>
    </div>
</div>
<div class="panelScroll">
    <div class="list-group" data-role="recommendList-Box">
        <!-- 데이터 동적 업데이트 -->        
    </div>
    <div id="textAni-loader" style="height:400px;width:100%;"><!-- 동적 업데이트 전 에니메이션 효과 문자 입력 --></div>
</div>
<div class="form-group btnSave-wrapper">
    <div class="row">
        <div class="col-md-7 col-md-offset-5"> 
    
             <button data-role="close-recommendPanel" class="btn btn-default">닫기</button>
        </div>
    </div>    
</div>
