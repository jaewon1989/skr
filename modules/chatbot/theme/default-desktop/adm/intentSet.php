<?php
$chatbot->vendor = $V['uid'];

$data = array();
$data['vendor'] = $V['uid'];
$data['bot'] = $bot;
$data['vendorOnly'] = $my['super'] ? false : true; // sys에서만 true
$get_intentData = $chatbot->getIntentData($data);
$intentData = $get_intentData['content'];

?>

<div class="container-fluid table-fluid">
    <!--
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><?php echo $pageTitle?></h4>
        </div>
    </div>
    -->
    <div class="overview">
        <div class="page-title">인텐트 관리</div>
        <div class="sub-frame">
            <div class="sub-title">SK telecom AICC / <?php echo $pageTitle?></div>
        </div>
    </div>
    <!-- /.row -->
    <div class="table-container">
        <div class="row table-tool">
            <div class="col-lg-12 col-md-12 hidden-sm">
                <button class="btn btn-default btn-rounded waves-effect waves-light tool-btn" data-role="delete-item" data-type="intent" data-mod="page">
                    <i class="fa fa-minus"></i> 삭제
                </button>
                <button class="btn btn-primary btn-rounded waves-effect waves-light tool-btn" data-role="add-item" data-type="intent" data-mod="new">
                    <i class="fa fa-plus"></i> 추가하기
                </button>
                <button class="btn btn-info btn-rounded btn-outline waves-effect waves-light tool-btn" data-role="import-data" data-type="intent" data-mod="page">
                    <i class="fa fa-file-excel-o"></i> 엑셀 파일 업로드
                </button>
                 <button class="btn btn-success btn-rounded btn-outline waves-effect waves-light tool-btn" data-role="export-data" data-link="export-intentForm" data-mod="page">
                     <i class="fa fa-download"></i>  <strong>업로드 양식</strong> 다운로드
                </button>
                 <button class="btn btn-info btn-rounded btn-outline waves-effect waves-light tool-btn" data-role="export-data" data-link="export-intent" data-mod="page">
                    <i class="fa fa-download"></i> 전체 <?php echo $chatbot->callIntent?> 데이터 다운로드
                </button>
                <button class="btn btn-danger btn-rounded waves-effect waves-light tool-btn" data-role="learning-intent" data-mod="page">
                    <i class="fa fa-share-alt"></i> 인텐트 학습
                </button>
            </div>

            <!-- /.col-lg-12 -->
        </div>
        <div class="intEntTable-wrapper">
            <div class="table-responsive table-wrapper" data-role="table-wrapper">
                <table class="table table-striped table-full" id="tbl-intentSet" data-role="tbl-intentSet">
                    <thead>
                        <tr class="table-header">
                            <th class="intEnt-chk"><input type="checkbox" data-role="select-all" data-parent="#tbl-intentSet"/></th>
                            <th class="intEnt-name" style="width:70%;"><?php echo $callIntent?>명</th>
                            <th class="intEnt-ex">예문</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($intentData as $row):?>
                        <tr>
                            <?php $example = getDbRows($table[$m.'intentEx'],'intent='.$row['uid'],'uid')?>
                            <td class="intEnt-chk"><input type="checkbox" data-role="select-all" data-uid="<?php echo $row['uid']?>"/></td>
                            <td class="txt-oflo intEnt-name">
                                <span class="intentName" data-role="page-item" data-type="intent" data-uid="<?php echo $row['uid']?>" data-oname="<?php echo $row['name']?>" data-name="<?php echo $row['name']?>" data-mod="item">
                                    #<?php echo $row['name']?>
                                </span>
                            </td>
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
                                <button class="btn btn-default" data-role="del-item" data-type="intent" data-uid="" data-tooltip="tooltip" title="<?php echo $callIntent?> 삭제">
                                    <i class="fa fa-trash-o"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                    <div class="form-group middle-wrapper">
                        <h4 class="panelTop-title" style="width: 70%">관련 예시문장</h4>
                        <ul class="panelTop-menuWrapper">
                            <li data-role="add-itemEx" data-type="intentEx">
                                <button type="button" class="btn btn-default btn-add"><i class="fa fa-plus"></i> 추가</button>
                            </li>
                        </ul>
                    </div>
                    <div class="panelScroll" data-role="panel-scroll" >
                        <div data-role="itemEx-wrapper" class="itemEx-wrapper">
                            <!-- intent 상세내용 동적출력 -->
                        </div>
                    </div>
                    <div class="form-group btnSave-wrapper" id="action-form">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                 <button data-role="btn-save" data-type="intent" data-mod="new" class="btn btn-primary">저장</button>
                                 <button data-role="close-rightPanel" data-pact="cancel" class="btn btn-default">취소</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<form name="exportIntentForm" id="exportIntentForm" action="/" method="post" target="_action_frame_<?php echo $m?>" enctype="multipart/form-data">
    <input type="hidden" name="r" value="<?php echo $r?>" />
    <input type="hidden" name="m" value="<?php echo $m?>" />
    <input type="hidden" name="vendor" value="<?php echo $V['uid']?>" />
    <input type="hidden" name="bot" value="<?php echo $bot?>" />
    <input type="hidden" name="a" value="do_VendorAction">
    <input type="hidden" name="linkType" value=""/>
</form>

<script>

 $('[data-role="export-data"]').on('click',function(){
    var type = $(this).data('type');
    var linkType = $(this).data('link');
    var form = $('#exportIntentForm');
    $(form).find('input[name="linkType"]').val(linkType);
    $(form).submit();

 })

</script>


