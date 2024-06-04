
<div class="form-group panel-menu" id="dataSetPanel-top" style="position:relative;margin-bottom:0;">
    <h4 class="panelTop-title">데이타셋</h4>
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
                    <!-- 동적으로 가져온다 -->
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