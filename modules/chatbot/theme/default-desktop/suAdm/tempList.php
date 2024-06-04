<?php
$chatbot->vendor = $V['uid'];

$data = array();
$data['vendor'] = $vendor = $V['uid'];
$botTempData = $chatbot->getBotTempData($data);
$botCat = $chatbot->getBotCategory($data);
?>

<link href="<?php echo $g['url_module_skin']?>/css/dialog.css" rel="stylesheet">
<link href="<?php echo $g['url_module_skin']?>/css/graphTable.css" rel="stylesheet">


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
                <button class="btn btn-primary btn-rounded waves-effect waves-light tool-btn" data-toggle="modal" data-target="#modal-addBot">
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
                            <th class="intEnt-name">템플릿명</th>                                
                            <th class="intEnt-des">설명</th>
                            <th class="intEnt-des">접속주소</th>
                            <th class="intEnt-des">상태</th>
                            <th class="intEnt-ex">관리</th>
                        </tr>
                    </thead> 
                    <tbody>    
                        <?php foreach ($botTempData as $row):?>
                        <?php
                            $data= array();
                            $data['vendor'] = $V['uid'];
                            $data['bot'] = $row['uid'];
                            $dialog = $chatbot->getVendorAdmDialog($data);
                            $is_active = $row['active']?true:false;
                        ?>
                        <tr>
                            <?php $node = getDbRows($table[$m.'dialogNode'],'vendor='.$vendor.' and bot='.$row['uid'],'uid')?>
                            <td class="intEnt-chk">
                                <input type="checkbox" data-role="select-all" data-uid="<?php echo $row['uid']?>"/>
                            </td>
                            <td class="txt-oflo intEnt-name">
                                <span class="entityName" data-role="temp-name" data-bot="<?php echo $row['uid']?>">
                                    <?php echo $row['name']?>
                                </span>
                            </td>
                            <td class="txt-oflo intEnt-des">
                                  <?php echo $row['intro']?$row['intro']:$row['service']?>
                            </td>
                            <td>
                                <?php echo '/R2'.$row['id']?>
                            </td>
                            <td>
                                <div class="select-show" id="showHide-wrapper-text">
                                    <div class="cb-cell cb-cell-right<?php echo $is_active?' botSwitch-on':' botSwitch-off'?>">
                                        <div class="cb-switch" data-role="control-botActive" data-uid="<?php echo $row['uid']?>">
                                            <span class="cb-switch-text" data-role="botActive-label-<?php echo $row['uid']?>">
                                                <?php echo $is_active?'ON':'OFF'?></span>
                                            <span class="cb-switch-button"></span>
                                         </div>
                                    </div>
                                </div>
                            </td>
                            <td class="txt-oflo tempList-manager">
                                <a href="/suAdm/tempGraph?bot=<?php echo $row['uid']?>&amp;dialog=<?php echo $dialog?>" data-tooltip="tooltip" title="대화그래프" >
                                   <i class="fa fa-wrench fa-fw" aria-hidden="true"></i>       
                                </a>
                                <a href="#" data-role="open-tempDataPanel" data-uid="<?php echo $row['uid']?>" data-vendor="<?php echo $vendor?>" data-dialog="<?php echo $dialog?>" data-tooltip="tooltip" title="데이타셋" >
                                    <i class="fa fa-database fa-fw" aria-hidden="true"></i>
                                </a>
                                <a href="#" data-role="open-tempLabelPanel" data-uid="<?php echo $row['uid']?>" data-vendor="<?php echo $vendor?>" data-dialog="<?php echo $dialog?>" data-tooltip="tooltip" title="데이터셋 정렬" >
                                    <i class="fa fa-list fa-fw" aria-hidden="true"></i>
                                </a>
                            </td>
                  
                        </tr>

                        <?php endforeach?>                          
                    </tbody>
                </table>
            </div>
            <div class="right-panel right-tempDataPanel" data-role="rightPanel">
                <div class="rightPanel-inner">
                    <span data-role="close-tempDataPanel" class="btn-close">
                        <span class="cb-icon cb-icon-close"></span>
                    </span>
                    <div data-role="graphTable-wrapper" class="graphTable-wrapper">
                        <table class="table table-inverse">
                            <colgroup>
                                <col width="13%"></col>
                                <col width="30%"></col>
                                <col width="57%"></col>
                            </colgroup>
                            <thead>
                                <tr class="sm-text">
                                    <th data-type="competency">대화상자명</th>
                                    <th class="text-align-center">인풋</th>
                                    <th data-type="average-score">아웃풋</th>
                                </tr>
                            </thead>
                        </table>
                        <div class="graphTable-tbodyWrapper" data-role="graphTable-tbodyWrapper">
                            <table class="table treeTable">
                                <colgroup>
                                    <col width="13%"></col>
                                    <col width="30%"></col>
                                    <col width="57%"></col>
                                </colgroup>
                                <thead>
                                </thead>  
                                <tbody data-role="resultBody-wrapper">
                                      <!-- 대화상자 테이블 동적 출력 -->                            
                                 </tbody>
                             </table>
                         </div> 
                    </div>
                </div>                
            </div>
            <div class="right-panel right-tempDataPanel" data-role="tempLabelPanel">
                <div class="rightPanel-inner">
                    <span data-role="close-tempLabelPanel" class="btn-close">
                        <span class="cb-icon cb-icon-close"></span>
                    </span>
                    <div data-role="graphTable-wrapper" class="graphTable-wrapper">
                        <table class="table table-inverse">
                            <colgroup>
                                <col width="30%"></col>
                                <col width="70%"></col>
                            </colgroup>
                            <thead>
                                <tr class="sm-text">
                                    <th data-type="competency">라벨명</th>
                                    <th class="text-align-center">대화상자 위치</th>
                                </tr>
                            </thead>
                        </table>
                        <div class="graphTable-tbodyWrapper" data-role="graphTable-tbodyWrapper">
                            <!-- data-bot, data-vendor 는 패널 오픈시 동적 할당 -->
                            <div class="dd nestable-menu" data-role="tempLabelList-div" data-bot="" data-vendor=""> 
                                <ul class="dd-list" data-role="tempLabelList-ul">
                                    <!-- 동적 출력 -->
                                </ul>
                            </div>
                        </div> 
                    </div>
                </div>                
            </div>
        </div>         
    </div>
</div>
<!-- 템플릿 추가 모달-->
<div id="modal-addBot" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" data-role="addBotModal-title">템플릿 추가하기</h4>
            </div>
            <div class="modal-body">  
                <form id="botForm">
                    <input type="hidden" name="vendor" value="<?php echo $V['uid']?>" /> 
                    <input type="hidden" name="uid" value="" /> 
                    <input type="hidden" name="mod" value="suAdm" /> 
                    <input type="hidden" name="is_temp" value="1" /> 
                    <div class="form-group row">
                        <label class="col-md-3 control-label">템플릿명</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="name">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 control-label">템플릿 설명</label>
                        <div class="col-md-9">
                            <textarea class="form-control ta-content" row="4" name="intro"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 control-label">업종</label>
                        <div class="col-md-9">
                            <select name="induCat" class="form-control">
                                <option value="">업종 선택</option>
                                <?php foreach ($botCat as $cat):?>
                                <option value="<?php echo $cat['uid']?>"><?php echo $cat['name']?></option>
                                <?php endforeach?>
                            </select>
                        </div>
                    </div>
                </form>                   
            </div>                    
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-role="btn-saveBot" data-depth="">추가하기</button> 
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
           
        </div>
    </div>
</div>
<!-- 라벨 순서 변경 -->
<?php getImport('nestable','jquery.nestable',false,'js') ?>
<script>
var vendor = '<?php echo $V['uid']?>';
var module ='<?php echo $m?>';
var addBotModal = $('#modal-addBot');


// 템플릿 업데이트 함수 (add, edit, delete, change gid.. )
var updateBot = function(data){
    var botListWrapper = $('[data-role="botList-wrapper"]');
    var role = data.role;
    $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=regis_bot',{
        data: data
    },function(response){
        var result=$.parseJSON(response);//$.parseJSON(response);

        if(role=='add'){
            $(addBotModal).modal('hide');
            location.reload();
        } 
    }); 
};

// 챗봇 등록폼 전송  
(function ($) {
    $.fn.serializeFormJSON = function () {

        var o = {};
        var a = this.serializeArray();
        $.each(a, function () {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };
})(jQuery);

// 노출/숨김 활성화  
$('[data-role="btn-showHide"]').on("click", function() {
     var pDiv = $(this).parent();
    var botuid = $(this).data('uid');
    var notify_container = $(this).data('container');
    var botActive_label = $('[data-role="botActive-label-'+botuid+'"]');

    if($(pDiv).hasClass("botSwitch-off")) {
        $(pDiv).removeClass("botSwitch-off");
        $(pDiv).addClass("botSwitch-on");
        $(botActive_label).text('ON');
       // setBotActive('on',botuid,notify_container);  
    }else{
        $(pDiv).addClass("botSwitch-off");
        $(pDiv).removeClass("botSwitch-on");
        $(botActive_label).text('OFF');
        //setBotActive('off',botuid,notify_container);      
    }

});

// bo
$('[data-role="btn-saveBot"]').on('click',function(e){
    var form = $('#botForm');
    var data = $('#botForm').serializeFormJSON();
    data['role'] = 'add';
    var induCat = $('select[name="induCat"]').val();
    var name = $('input[name="name"]').val();
    if(!name){
        alert('템플릿명을 선택해주세요');
        setTimeout(function(){
           $('input[name="name"]').focus();
        },10);
        
        return false;
    }else if(!induCat){
         alert('업종을 선택해주세요');
        return false;
    }
    else updateBot(data);
       
});

$('body').tooltip({
    selector: '[data-tooltip=tooltip]',
    container: 'body'
}); 

</script> 
