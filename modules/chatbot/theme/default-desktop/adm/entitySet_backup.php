<?php
$chatbot->vendor = $V['uid'];

$data = array();
$data['vendor'] = $V['uid'];
$data['bot'] = $bot;
$get_entityData = $chatbot->getEntityData($data);
$entityData = $get_entityData['content']; 


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
                <button class="btn btn-primary btn-rounded waves-effect waves-light tool-btn"><i class="fa fa-plus"></i> 추가하기
                </button>
                <button class="btn btn-info btn-rounded btn-outline waves-effect waves-light tool-btn" data-role="import-data" data-type="entity" data-mod="page">
                    <i class="fa fa-file-excel-o"></i> XLS 파일 업로드 
                </button>

            </div>

            <!-- /.col-lg-12 -->
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-full" id="tbl-entitySet">
                 <colgroup>
                        <col width="5%">
                        <col width="25%">
                        <col width="">
                        <col width="15%">
                    </colgroup>
                <thead> 
                   <tr class="table-header">
                        <th><input type="checkbox" data-role="select-all" data-parent="#tbl-entitySet"/></th>
                        <th>엔터티</th>                                
                        <th>설명</th>
                        <th>예문</th>
                    </tr>
                </thead>
                <tbody> 
                    <?php foreach ($entityData as $row):?>
                    <tr>
                        <?php $example = getDbRows($table[$m.'entityVal'],'entity='.$row['uid'],'uid')?>
                        <td><input type="checkbox" data-role="select-all" data-uid="<?php echo $row['uid']?>"/></td>
                        <td class="txt-oflo">@<?php echo $row['name']?></td>
                        <td class="txt-oflo"></td>
                        <td class="txt-oflo"><?php echo $example?></td>
                    </tr>

                    <?php endforeach?>                          
                </tbody>
            </table>
        </div>
    </div>
</div>
