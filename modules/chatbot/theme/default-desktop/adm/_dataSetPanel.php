
<div class="form-group panel-menu dataTable-wrapper" id="dataSetPanel-top" style="position:relative;margin-bottom:0;">
    <h4 class="panelTop-title dataTable-title">데이타셋</h4>
    <div class="alert alert-info alert-dismissable dataTable-guide">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <ul class="dataTable-ul">
            <li>
        대화상자 패널의 텍스트 응답 설정시 <code>{#<?php echo $callIntent?> @<?php echo $callEntity?>}</code> 형식으로 표기한 후 이 테이블에서 해당 <?php echo $callIntent?> 와 <?php echo $callEntity?> 가 교차하는 칸에 데이터를  입력해주면 응답시 출력됩니다.    
             </li>
             <li>
                예를 들어, 커피메뉴별 가격을 알려줘야 한다면, <code>{#가격문의 @커피종류}</code> 라고 표기하고 해당 <code>@커피메뉴 벨류(아메리키노,라떼 등)</code>와 <code>#가격문의</code> 가 <strong>교차하는 칸</strong>에 각가의 가격을 입력해주면 됩니다.  
             </li>
        </ul>  
    </div>
    <ul class="panelTop-menuWrapper">
    <!--     <li>
           <select class="form-control" data-role="select-dataSetIntent">
                <option value=""><?php echo $callIntent?> 선택 </option>
                <?php foreach ($intentData as $intent):?>
                <option value="<?php echo $intent['uid']?>"><?php echo '#'.$intent['name']?></option>
                <?php endforeach?>
           </select> 
        </li>
        <li>
            <select class="form-control" data-role="select-dataSetEntity">
                <option value=""><?php echo $callEntity?> 선택 </option>
                <?php foreach ($entityData as $entity):?>
                <option value="<?php echo $entity['uid']?>"><?php echo '#'.$entity['name']?></option>
                <?php endforeach?>
           </select>
        </li> -->
    </ul>   
</div>
<div id="dataTable-Parent" data-role="dataTable-wrapper">
    <div id="dataTable-wrapper" >
        <div id="dataTable-container" data-role="dataTable-container">
            <div>
                <div class="panelScroll">
                    <div  data-role="dataTable-scroll">
                        <div id="dataTable-loader" style="height:400px;width:100%;display:none">
                        <!-- 동적으로 가져온다 -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-group btnSave-wrapper">
    <div class="row">
        <div class="col-md-6 col-md-offset-6"> 
             <button data-role="btn-closeDataSetPanel" class="btn btn-default">닫기</button>
        </div>
    </div>    
</div>