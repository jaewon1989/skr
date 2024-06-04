<?php
$chatbot->vendor = $V['uid'];

$data = array();
$data['vendor'] = $V['uid'];
$data['bot'] = $bot;

$get_intentData = $chatbot->getIntentData($data);
$intentData = $get_intentData['content']; 

?>
<script type="text/javascript" src="<?php echo $g['url_module'].'/lib/js/rotator.js'?>"></script>
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
                <button class="btn btn-info btn-rounded btn-outline waves-effect waves-light tool-btn" data-role="import-data" data-type="chat_TSTest" data-mod="page">
                    <i class="fa fa-file-excel-o"></i> 엑셀 파일 업로드 
                </button>
                <button class="btn btn-info btn-rounded btn-outline waves-effect waves-light tool-btn" data-role="export-data" data-link="export-TSTest" data-mod="page" style="display:none;">
                    <i class="fa fa-download"></i> 결과 다운로드 
                </button>
            </div>

            <!-- /.col-lg-12 -->
        </div>
        <div class="intEntTable-wrapper"> 
            <div class="table-responsive table-wrapper" data-role="table-wrapper">
                <table id="chatTestResult" class="table tbody-striped table-full">
                    <colgroup>
                        <col width="70px">
                        <col width="15%">
						<col width="7%">
						<col width="7%">
						<col width="20%">
						<col width="7%">
						<col width="7%">
						<col width="25%">
						<col width="*">
					</colgroup>
                    <thead>
                        <tr class="table-header">
                            <th>순번</th>
                            <th>발화</th>
                            <th>인텐트</th>
                            <th>인텐트분석점수</th>
                            <th>엔터티</th>
                            <th>대화상자</th>
                            <th>응답타입</th>
                            <th>응답</th>
                            <th>매칭</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>         
    </div>
</div>

<form name="exportEntityForm" id="exportEntityForm" action="/" method="post" target="_action_frame_<?php echo $m?>" enctype="multipart/form-data"> 
    <input type="hidden" name="r" value="<?php echo $r?>" />
    <input type="hidden" name="m" value="<?php echo $m?>" />
    <input type="hidden" name="vendor" value="<?php echo $V['uid']?>" />
    <input type="hidden" name="bot" value="<?php echo $bot?>" />
    <input type="hidden" name="a" value="do_VendorAction">
    <input type="hidden" name="linkType" value=""/>
    <input type="hidden" name="fileName" value=""/> 
</form>

<script>
    $('.table-wrapper').css('height', ($(window).height() - ($('.table-fluid').outerHeight(true)+$(".navbar-fixed-top").height()) - 50)+'px');
    
    $('[data-role="export-data"]').on('click',function(){
        var type = $(this).data('type');
        var linkType = $(this).data('link');
        var fileName = $(this).data('file');
        var form = $('#exportEntityForm');
        $(form).find('input[name="linkType"]').val(linkType);
        $(form).find('input[name="fileName"]').val(fileName);
        $(form).submit();
    });
</script>