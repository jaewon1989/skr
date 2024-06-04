<?php
$chatbot->vendor = $V['uid'];

$data = array();
$data['vendor'] = $V['uid'];
$data['bot'] = $bot;
$data['vendorOnly'] = true;
$get_entityData = $chatbot->getEntityData($data);
$entityData = $get_entityData['content']; 
$itemName = '레거시';
?>
<div class="container-fluid table-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><?php echo $pageTitle?></h4>
        </div>
    </div>
    <!-- /.row -->
    <div class="table-container">
        <div class="row table-tool">
            <div class="col-lg-12 col-md-12 hidden-sm">
                <button class="btn btn-primary btn-rounded waves-effect waves-light tool-btn" data-toggle="modal" data-target="#modal-addItem">
                    <i class="fa fa-plus"></i> 추가하기
                </button>
             </div>

            <!-- /.col-lg-12 -->
        </div>
        <div class="intEntTable-wrapper"> 
            <div class="table-responsive table-wrapper" data-role="table-wrapper">
                <table class="table table-striped table-full" id="tbl-entitySet" data-role="tbl-entitySet">
                    <thead>
                        <tr class="table-header">
                            <th class="intEnt-chk"><input type="checkbox" data-role="select-all" data-parent="#tbl-entitySet"/></th>
                            <th class="intEnt-name">레거시명</th>
                            <th class="intEnt-des">설명</th>
                            <th class="intEnt-ex">API 수</th>
                        </tr>
                    </thead> 
                    <tbody>    
                        <?php foreach ($entityData as $row):?>
                        <tr>
                            <?php $example = getDbRows($table[$m.'entityVal'],'entity='.$row['uid'],'uid')?>
                            <td class="intEnt-chk"><input type="checkbox" data-role="select-all" data-uid="<?php echo $row['uid']?>"/></td>
                            <td class="txt-oflo intEnt-name">
                                <span class="entityName" data-role="page-item" data-type="entity" data-uid="<?php echo $row['uid']?>" data-name="<?php echo $row['name']?>">
                                    @<?php echo $row['name']?>
                                </span>
                            </td>
                            <td class="txt-oflo intEnt-des"></td>
                            <td class="txt-oflo intEnt-ex"><?php echo $example?></td>
                        </tr>

                        <?php endforeach?>                          
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

<!-- 레거시 추가 모달-->
<div id="modal-addItem" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" data-role="addModal-title"><?php echo $itemName?> 추가하기</h4>
            </div>
            <div class="modal-body">  
                <form id="botForm" data-role="settingsLegacyForm" autocomplete="off">
                    <div class="form-group row">
                        <label class="col-md-3 control-label"><?php echo $itemName?>명</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="name">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 control-label"><?php echo $itemName?> 설명</label>
                        <div class="col-md-9">
                            <textarea class="form-control ta-content" row="4" name="intro"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 control-label"> 기본 URI</label>
                        <div class="col-md-9">
                            <textarea class="form-control ta-content" row="4" name="url"></textarea>
                        </div>
                    </div>
                </form>                   
            </div>                    
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-role="save-legacySettings">추가하기</button> 
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
           
        </div>
    </div>
</div>
 
