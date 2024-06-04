<?php
$_wh = 'hidden=0 and vendor='.$vendor.' and dialog='.$dialog.' and bot='.$bot;
$RCD = getDbArray($table[$module.'dialogNode'],$_wh,'*',$sort,$order,'',1);
?>
<div class="container-fluid table-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><?php echo $pageTitle?></h4>
        </div>
    </div>
    <!-- /.row -->
    <div class="table-container">
        <div class="intEntTable-wrapper"> 
            <div class="table-responsive table-wrapper" data-role="table-wrapper">
                <table class="table table-striped table-full" id="tbl-entitySet" data-role="tbl-entitySet">
                    <thead>
                        <tr class="table-header">
                            <th class="intEnt-chk"><input type="checkbox" data-role="select-all" data-parent="#tbl-entitySet"/></th>
                            <th class="intEnt-name">대화상자명</th>                                
                            <th class="intEnt-des">조건</th>
                            <th class="intEnt-ex">답변그룹</th>
                            <th class="intEnt-ex">설정</th>

                        </tr>
                    </thead> 
                    <tbody>    
                        <?php while($R = db_fetch_array($RCD)):?>
                        <tr>
                            <?php $example = getDbRows($table[$m.'entityVal'],'entity='.$row['uid'],'uid')?>
                            <td class="intEnt-chk"><input type="checkbox" data-role="select-all" data-uid="<?php echo $row['uid']?>"/></td>
                            <td class="txt-oflo intEnt-name">
                                <span class="entityName" data-role="page-item" data-type="entity" data-uid="<?php echo $row['uid']?>" data-name="<?php echo $row['name']?>">
                                </span>
                            </td>
                            <td class="txt-oflo intEnt-des"></td>
                            <td class="txt-oflo intEnt-ex"></td>
                            <td class="txt-oflo intEnt-ex"></td>
                        </tr>

                        <?php endwhile?>                          
                    </tbody>
                </table>
            </div>
            <div class="right-panel" data-role="rightPanel">
                <div class="rightPanel-inner">
                    <span data-role="close-rightPanel" class="btn-close">
                        <span class="cb-icon cb-icon-close"></span>
                    </span>
                    <div class="form-group itemName-wrapper">
                        <input type="hidden" name="item_uid" />
                        <div class="input-group panelIntentName-wrapper">
                            <input type="text" class="form-control itemName" name="item_name" placeholder="생성할 <?php echo $callIntent?>명을 입력해주세요">
                            <span class="input-group-btn">
                                <button class="btn btn-default hidden" data-role="del-item" data-uid="" >
                                    <i class="fa fa-trash-o"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                    <div class="form-group middle-wrapper">
                        <h4 class="panelTop-title" style="width: 70%">관련 예시문장</h4>
                        <ul class="panelTop-menuWrapper">
                            <li data-role="add-itemEx" data-type="entityEx">
                                <button type="button" class="btn btn-default btn-add"><i class="fa fa-plus"></i> 추가</button> 
                            </li>
                        </ul>   
                    </div>
                    <div class="panelScroll" data-role="panel-scroll" >
                        <div data-role="itemEx-wrapper" class="itemEx-wrapper">
                            <!-- entity 상세내용 동적출력 -->
                        </div>
                    </div> 
                    <div class="form-group btnSave-wrapper" id="action-form">
                        <div class="row">
                            <div class="col-md-12 text-center"> 
                                 <button data-role="btn-save" data-type="entity" class="btn btn-primary">저장</button> 
                                 <button data-role="close-rightPanel" data-pact="cancel" class="btn btn-default">취소</button>
                            </div>
                        </div>    
                    </div>
                </div>                
            </div>
        </div>         
    </div>

</div>
