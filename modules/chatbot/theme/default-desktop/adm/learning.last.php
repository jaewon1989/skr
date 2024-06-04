<?php
$chatbot->vendor = $V['uid'];
// 실시간 이용자 수 
$liveAccessUser = $chatbot->getLiveAccessUser('num');

$botuid = $bot?$bot:'';

// 총 누적 이용자수 
$_wh_user = 'vendor='.$V['uid'];
if($botuid) $_wh_user.= ' and bot='.$botuid;
$_wh_user.=' group by userUid';

$TCD = db_query("select count(*) from ".$table[$m.'chatLog']." where ".$_wh_user,$DB_CONNECT);

$i =0;
while ($T = db_fetch_array($TCD)) $i++;
$totalUser = $i;

// 총 채팅 수  
$_wh_chat = 'vendor='.$V['uid'];
if($botuid) $_wh_chat.= ' and bot='.$botuid;
$totalChat = getDbRows($table[$m.'chatLog'],$_wh_chat);

// 인당 대화 건수 
$chatPerUser = round($totalChat/$totalUser);

// 답변못한 질문 리스트 
$data = array();
$data['bot'] = $botuid?$botuid:'';
$getUnKnownData = $chatbot->getUnKnownData($data);
$unKnownList = $getUnKnownData[1];
$unKnownPageBtn = $getUnKnownData[2];

// 많이한 질문 리스트 
$data = array();
$data['vendor'] = $V['uid'];
$data['bot'] = $botuid?$botuid:'';
$data['mod'] = 'question';
$questionData = $chatbot->getFavorateQuestionData($data);

// 많이한 단어 리스트 
$data = array();
$data['vendor'] = $V['uid'];
$data['bot'] = $botuid?$botuid:'';
$data['mod'] = 'word';
$wordData = $chatbot->getFavorateQuestionData($data);
?>

<!-- bootstrap css -->
<script src="<?php echo $g['url_layout']?>/_js/chart.min.js"></script>

<input type="hidden" name="mod" value="month" />

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><?php echo $pageTitle?></h4>
        </div>
<!--         <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12"> <a href="http://wrappixel.com/templates/pixeladmin/" target="_blank" class="btn btn-danger pull-right m-l-20 btn-rounded btn-outline hidden-xs hidden-sm waves-effect waves-light">Upgrade to Pro</a>
            <ol class="breadcrumb">
                <li><a href="#">Dashboard</a></li>
                <li class="active">Fontawesome Icons</li>
            </ol>
        </div> -->
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-sm-5">
            <div class="white-box" style="min-height:720px;">
                <input type="hidden" value="wait" data-role="unknownMod-input"/>
                <input type="hidden" value="1" data-role="unknownPage-input"/> 
                <h3 class="box-title" style="float:left;width:70%;">
                    <span>답변 못한 문장</span>
                </h3>
                <h3 class="box-title" style="float:right;width:30%;text-align:right;" data-role="unknownPage-wrapper">
                    <?php echo $unKnownPageBtn?>
                </h3>
                 <ul class="nav nav-tabs unknown-state">
                     <li class="active unknown-mod"><a data-toggle="tab" data-role="change-unknownMod" data-mod="wait" id="learn-wait">학습대기</a></li>
                     <li class="unknown-mod"><a data-toggle="tab" data-role="change-unknownMod" data-mod="done">학습완료</a></li>
                 </ul>

                <div class="table-responsive clearfix" style="position:relative;margin-top:10px;padding-bottom:20px;clear:both;">
                    <table class="table" id="tbl-unknown">
                        <colgroup>
                            <col width="5%">
                            <col width="77%">
                            <col width="18%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th><input type="checkbox" data-role="select-all" data-parent="#tbl-unknown" style="margin:0;" /></th>
                                <th>질문내용</th>
                                <th data-role="unknown-dateLabel">등록일 </th>
                            </tr>
                        </thead>
                        <tbody data-role="unknownList-wrapper">
                            <?php echo $unKnownList;?>                        
                        </tbody>
                    </table>
                    <div class="col-sm-12">
                        <button class="btn btn-success" data-role="change-learnState" data-state="done" data-msg="학습완료">학습완료 처리</button>
                        <button class="btn btn-danger" data-role="change-learnState" data-state="wait" data-msg="학습대기" style="margin-left:5px">학습대기 처리</button>
                        <button class="btn btn-info" data-role="change-learnState" data-state="intent" style="margin-left:5px"><?php echo $chatbot->callIntent?> 지정</button>
                    </div>
                </div>
                
            </div>
        </div>
        
        <div class="col-sm-4">
            <div class="white-box" style="min-height:720px;">
                <h3 class="box-title" style="float:left;width:60%;">많이 한 질문</h3> 
                <h3 class="box-title" style="float:right;width:40%;text-align:right;font-size:15px;" data-role="questionPage-wrapper">
                    <?php echo $questionData[2]?>
                </h3>
                <div class="table-responsive" style="clear:both;">
                    <table class="table ">
                        <thead>
                            <tr>
                                <th style="width:80%">질문내용</th>                                
                                <th>횟수</th>
                            </tr>
                        </thead>
                        <tbody data-role="questionList-wrapper">
                            <?php echo $questionData[1];?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-sm-3">
            <div class="white-box" style="min-height:720px;">
                <h3 class="box-title" style="float:left;width:50%;">많이 사용한 단어</h3>
                <h3 class="box-title" style="float:right;width:50%;text-align:right;font-size:15px;" data-role="wordPage-wrapper">
                    <?php echo $wordData[2]?>
                </h3>
                <div class="table-responsive" style="clear:both;">
                    <table class="table ">
                        <thead>
                            <tr>
                                <th style="width:80%">단어</th>                                
                                <th>횟수</th>
                            </tr>
                        </thead>
                        <tbody data-role="wordList-wrapper">
                            <?php echo $wordData[1];?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->
</div>
<script src="<?php echo $g['url_layout']?>/_js/jquery.chosen.js"></script>

<link rel="stylesheet" href="<?php echo $g['url_layout']?>/_css/chosen.css">

<!-- 인텐트 지정 모달-->
<div id="modal-setIntent" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $chatbot->callIntent?> 지정하기</h4>
            </div>
            <div class="modal-body" data-role="content">
                <div class="form-group" style="width:100%;">
                    <select data-placeholder="<?php echo $chatbot->callIntent?>를 선택해주세요" name="intent" id="#set-intent" class="chosen-select" tabindex="8" style="width:100%;">
                        <option value=""></option>
                        <?php $sql = "vendor='".$V['uid']."' and bot='".$bot."' and hidden=0";?>
                        <?php $ICD = getDbArray($table[$m.'intent'],$sql,'uid,name','gid','asc','',1)?>
                        <?php while($IT = db_fetch_array($ICD)):?>
                        <option value="<?php echo $IT['uid']?>"><?php echo $IT['name']?></option>
                        <?php endwhile?> 
                     </select>
                    
                </div>
                <div class="form-group">
                    <input type="hidden" name="unknownItemsUid" />
                    <textarea class="form-control" rows="5" name="unknownItemsName"></textarea>
                </div>    
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-role="btn-setIntent" data-depth="">저장하기</button> 
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// 인텐트 지정 모달 
var setIntentModal = '#modal-setIntent';
var module='<?php echo $m?>';
var vendor='<?php echo $V['uid']?>';
var botuid ='<?php echo $bot?>';

// Start of  chosen select 
$(".chosen-select").chosen({
    no_results_text: '(신규 등록시 Enter 키를 눌러주세요)',
});

// 인텐트 못찾은 경우 해당 입력값 출력 
$(".chosen-select").on('change',function(e,data){
    // $(setIntentModal).find('input[name="intent"]')val(data);
});

// End of  chosen select 

// 툴팁 이벤트
$(document).ready(function() {
    $('[data-toggle=tooltip]').tooltip();
});

// 선택박스 체크 이벤트 핸들러
$('[data-role="select-all"]').click(function(){
    var parent = $(this).data('parent'); 
    $(parent).find('tbody [data-role="checkbox"]').prop("checked",$(this).prop("checked"));
});

// unknownList 가져오기
var getUnKnownList = function(data){
    var mod = data.mod?data.mod:$('[data-role="unknownMod-input"]').val();
    var page = data.page?data.page:$('[data-role="unknownPage-input"]').val();
    var unknownItems = data.unknownItems?data.unknownItems:null;
    var state = data.state?data.state:null;
    var pageWrapper = $('[data-role="unknownPage-wrapper"]');
    var listWrapper = $('[data-role="unknownList-wrapper"]');

    $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=get_UnknownList',{
        vendor : vendor,
        botuid : botuid,
        mod : mod,
        page : page,
        unknownItems : unknownItems,
        state : state
    },function(response){
        var result=$.parseJSON(response);
        var pageBtn = result.pageBtn;
        var list = result.list;
        $(pageWrapper).html(pageBtn);
        $(listWrapper).html(list);        
    }); 
}

var getQuestionList = function(data){
    var mod = data.mod;
    var page = data.page?data.page:$('[data-role="'+mod+'Page-input"]').val();
    var pageWrapper = $('[data-role="'+mod+'Page-wrapper"]');
    var listWrapper = $('[data-role="'+mod+'List-wrapper"]');

    $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=get_UnknownList',{
        vendor : vendor,
        botuid : botuid,
        mod : mod,
        page : page
    },function(response){
        var result=$.parseJSON(response);
        var pageBtn = result.pageBtn;
        var list = result.list;
        $(pageWrapper).html(pageBtn);
        $(listWrapper).html(list);        
    }); 
}

// 인텐트 지정 프로세스 진행 함수 
var setIntent = function(data){
    $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=update_intent',{
        vendor : vendor,
        botuid : botuid,
        intent : data.intent,
        unknownItemsUid : data.unknownItemsUid,
        unknownItemsName : data.unknownItemsName
    },function(response){
        var result=$.parseJSON(response);
        $(setIntentModal).modal('hide');
        setTimeout(function(){
            getUnKnownList(data);  
        },10);     
    }); 
}


// 인텐트 지정 모달 호출 
var getIntentModal = function(data){
   $(setIntentModal).modal();  
   $(setIntentModal).find('textarea[name="unknownItemsName"]').val(data.unknownItemsName);
   $(setIntentModal).find('input[name="unknownItemsUid"]').val(data.unknownItems); 
}

// 인텐트 지정내용 저장 
$(document).on('click','[data-role="btn-setIntent"]',function(){
   var intent = $(".chosen-select").chosen().val();// $(setIntentModal).find('input[name="intent"]').val(); 
   var unknownItemsName = $(setIntentModal).find('textarea[name="unknownItemsName"]').val();
   var unknownItemsUid = $(setIntentModal).find('input[name="unknownItemsUid"]').val();
   if(intent==''){
       alert('인텐트를 선택해주세요.');
       return false;
   }else{
      var data = {"intent":intent,"unknownItemsUid":unknownItemsUid,"unknownItemsName":unknownItemsName};
      setIntent(data); // 인텐트 지정 프로세스 진행 
   }
    
});


// 답변 못한 문장 리스트 > 학습완료 / 학습대기 / 인텐트 지정  state 변경 이벤트 
$(document).on('click','[data-role="change-learnState"]',function(){
    var state = $(this).data('state');
    var page = $('[data-role="unknownPage-input"]').val();
    var msg = $(this).data('msg')+' 처리 되었습니다.';
    var unknownItems = $(document).find('input[name="unknownItem[]"]:checked').map(function(){return $(this).val()}).get();
    var unknownItemsName = $(document).find('input[name="unknownItem[]"]:checked').map(function(){return $(this).attr('rel')}).get();
    var data = {"page": page,"state": state,"unknownItems":unknownItems,"unknownItemsName":unknownItemsName};
    var showNotify = function(message){
        var container = $('#tbl-unknown');
        var notify_msg ='<div id="kiere-notify-msg">'+message+'</div>';
        var notify = $('<div/>', { id: 'kiere-notify', html: notify_msg})
            .addClass('active')
           .appendTo(container)
        setTimeout(function(){
                $(notify).removeClass('active');
                $(notify).remove();
         }, 1500);
    }

    if(unknownItems.length==0){
        alert('질문을 선택해주세요');
        return false;
    }
    // 인텐트 지정 
    if(state=='intent'){
       getIntentModal(data);   
    }
    else getUnKnownList(data);

     setTimeout(function(){
        showNotify(msg);
    },10);
    
});

// 답변 못한 문장 리스트 > 학습완료 / 학습대기 sort 탭 이벤트 
$('[data-role="change-unknownMod"]').on('click',function(){
    var mod = $(this).data('mod');
    
    var page = 1;
    var data = {"mod":mod,"page":page};
    
    $('[data-role="unknownMod-input"]').val(mod);
    $('[data-role="unknownPage-input"]').val(page);
    
    getUnKnownList(data);
   
});

// 답변 못한 문장 리스트 > 페이징  
$(document).on('click','[data-role="unknown-paging"]',function(){
    var mod = $(this).data('mod');
    var page = $(this).data('page')?$(this).data('page'):1;
    var data = {"mod":mod,"page":page}    
    $('[data-role="unknownPage-input"]').val(page);

    getUnKnownList(data);
});
$(document).on('click','[data-role="question-paging"]',function(){
    var mod = $(this).data('mod');
    var page = $(this).data('page')?$(this).data('page'):1;
    var data = {"mod":mod,"page":page};
    getQuestionList(data);
});
</script>
