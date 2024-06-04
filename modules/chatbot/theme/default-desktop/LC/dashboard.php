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
$data['recnum'] =5;
$getUnKnownData = $chatbot->getUnKnownData($data);
$unKnownList = $getUnKnownData[1];
$unKnownPageBtn = $getUnKnownData[2];

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
    <!-- row -->
    <div class="row">
        <!--col -->
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <div class="white-box">
                <div class="col-in row">
                    <div class="col-md-6 col-sm-6 col-xs-6"> <i data-icon="E" class="linea-icon linea-basic"></i>
                        <h5 class="text-muted vb">총 누적이용자수</h5> </div>
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <h3 class="counter text-right m-t-15 text-danger"><?php echo $totalUser?></h3> </div>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="progress">
                            <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%"> </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.col -->
        <!--col -->
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <div class="white-box">
                <div class="col-in row">
                    <div class="col-md-6 col-sm-6 col-xs-6"> <i class="linea-icon linea-basic" data-icon=""></i>
                        <h5 class="text-muted vb">실시간 이용자수</h5> </div>
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <h3 class="counter text-right m-t-15 text-megna"><?php echo $liveAccessUser?></h3> </div>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="progress">
                            <div class="progress-bar progress-bar-megna" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.col -->
        <!--col -->
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <div class="white-box">
                <div class="col-in row">
                    <div class="col-md-6 col-sm-6 col-xs-6"> <i class="linea-icon linea-basic" data-icon=""></i>
                        <h5 class="text-muted vb">인당 대화건수</h5> </div>
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <h3 class="counter text-right m-t-15 text-primary"><?php echo $chatPerUser?></h3> </div>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="progress">
                            <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-sm-4">
            <div class="white-box">
                <h3 class="box-title">답변 못한 질문</h3>            
                <div class="table-responsive" >
                    <table class="table" id="tbl-unknown">
                        <colgroup>
                            <col width="5%">
                            <col width="77%">
                            <col width="18%">
                        </colgroup>
                        <tbody data-role="unknownList-wrapper">
                             <tr>
                                <th><input type="checkbox" data-role="select-all" data-parent="#tbl-unknown"/></th>
                                <th>질문내용</th>
                                <th data-role="unknown-dateLabel">등록일 </th>
                            </tr>
                            <?php echo $unKnownList;?>                        
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>
        <div class="col-sm-4">
            <div class="white-box">
                <h3 class="box-title">많이 한 질문</h3>                   
                <div class="table-responsive">
                    <table class="table ">
                        <tbody>
                            <?php
                                $tbl = $table[$m.'chatStsLog'];
                                $_wh = 'vendor='.$V['uid'];
                                if($botuid) $_wh.=' and bot='.$botuid;

                                $query = sprintf("SELECT * FROM `%s` WHERE %s GROUP BY `uid` ORDER BY `hit` DESC LIMIT 0,5", $tbl,$_wh);
                                $rows=$chatbot->getAssoc($query);
                            ?>
                            <tr>
                                <th>질문내용</th>                                
                                <th>횟수</th>
                            </tr>
                            <?php foreach ($rows as $row):?>
                            <tr>
                                <td class="txt-oflo"><?php echo $row['sentence']?></td>
                                <td class="txt-oflo"><?php echo $row['hit']?></td>
                            </tr>
                            <?php endforeach?>                           
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-sm-4">
            <div class="white-box">
                <h3 class="box-title">많이 사용한 단어</h3>                   
                <div class="table-responsive">
                    <table class="table ">
                        <tbody>
                            <?php
                                $tbl = $table[$m.'chatWordLog'];
                                $_wh = 'vendor='.$V['uid'];
                                if($botuid) $_wh.=' and bot='.$botuid;

                                $query = sprintf("SELECT * FROM `%s` WHERE %s GROUP BY `uid` ORDER BY `hit` DESC LIMIT 0,5", $tbl,$_wh);
                                $rows=$chatbot->getAssoc($query); 
                            ?>
                            <tr>
                                <th>단어</th>                                
                                <th>횟수</th>
                            </tr>
                            <?php foreach ($rows as $row):?>
                            <tr>
                                <td class="txt-oflo"><?php echo $row['keyword']?></td>
                                <td class="txt-oflo"><?php echo $row['hit']?></td>
                            </tr>
                            <?php endforeach?>                          
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->
    <!--row -->
    <div class="row">
        <div class="col-md-12">
            <div class="white-box">
                <h3 class="box-title">접속통계</h3>
                <div id="total-wrap">         
                   <!-- load 접속통계 차트 -->       
                </div>
            </div>
        </div>
    </div>
    <!--row -->
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
                <h4 class="modal-title">인텐트 지정하기</h4>
            </div>
            <div class="modal-body" data-role="content">
                <div class="form-group" style="width:100%;">
                    <select data-placeholder="인텐트를 선택해주세요" name="intent" id="#set-intent" class="chosen-select" tabindex="8" style="width:100%;">
                        <option value=""></option>
                        <?php $ICD = getDbArray($table[$m.'category'],'hidden=0','uid,name','gid','asc','',1)?>
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
var botuid =$('select[name="botuid"]').val();

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
    var unknownItems = $(document).find('input[name="unknownItem[]"]:checked').map(function(){return $(this).val()}).get();
    var unknownItemsName = $(document).find('input[name="unknownItem[]"]:checked').map(function(){return $(this).attr('rel')}).get();
    var data = {"page": page,"state": state,"unknownItems":unknownItems,"unknownItemsName":unknownItemsName};
    if(unknownItems.length==0){
        alert('질문을 선택해주세요');
        return false;
    }
    // 인텐트 지정 
    if(state=='intent'){
       getIntentModal(data);   
    }
    else getUnKnownList(data);
    
});

// 답변 못한 문장 리스트 > 학습완료 / 학습대기 sort 탭 이벤트 
$('[data-role="change-unknownMod"]').on('click',function(){
    var mod = $(this).data('mod');
    var page = 1;
    var data = {"mod":mod,"page":page}
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

</script>
<!-- 상단 통계숫자  에니메이션 카운팅 --> 
<script src="<?php echo $g['url_layout']?>/_js/jquery.waypoints.js"></script>
<script src="<?php echo $g['url_layout']?>/_js/jquery.counterup.min.js"></script>
<script>
$(".counter").counterUp({
    delay: 100,
    time: 1200
});
</script>
<!-- End of  bootstrap-timepicker,  https://github.com/jdewit/bootstrap-timepicker/ , http://jdewit.github.io/bootstrap-timepicker/ : 메뉴얼 -->
<?php getImport('bootstrap-timepicker','js/bootstrap-timepicker.min',false,'js')?>
<?php getImport('bootstrap-timepicker','css/bootstrap-timepicker.min',false,'css')?>
<script>
 $('.tpicker').timepicker({
    defaultTime : '',
    //showSeconds : true, // 초 노출
    showMeridian:true, // 24시 모드 
    maxHours: 24,
    minuteStep : 15
 });

</script>
<!-- bootstrap-datepicker,  http://eternicode.github.io/bootstrap-datepicker/  -->
<?php getImport('bootstrap-datepicker','css/datepicker3',false,'css')?>
<?php getImport('bootstrap-datepicker','js/bootstrap-datepicker',false,'js')?>
<?php getImport('bootstrap-datepicker','js/locales/bootstrap-datepicker.kr',false,'js')?>
<script>
// 날짜 선택 
$('.input-daterange').datepicker({
    format: "yyyy-mm-dd",
    todayBtn: "linked",
    language: "kr",
    calendarWeeks: true,
    todayHighlight: true,
    autoclose: true
});

var Load_BotChart = function(){
    var module='chatbot';
    var vendor='<?php echo $V['uid']?>';
    var botuid =$('select[name="botuid"]').val();
    var mod = $('input[name="mod"]').val();
    var d_start = $('input[name="d_start"]').val();
    var d_end = $('input[name="d_end"]').val();    
    $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=get_StatisticsChart',{
        vendor : vendor,
        botuid : botuid,
        mod : mod,
        d_start : d_start,
        d_end : d_end
    },function(response){
        var result=$.parseJSON(response);//$.parseJSON(response);
        var total_chart=result.total_chart;
        var gender_chart=result.gender_chart;
        var age_chart=result.age_chart;
        
        // insert chart 
        $('#total-wrap').html(total_chart);
        // $('#gender-wrap').html(gender_chart);
        // $('#age-wrap').html(age_chart);
        //$('#total-wrap').html(response);

    }); 
}

// 일단,주간,월간 버튼 클릭 이벤트 
$('[data-smod]').on('click',function(){
    $('.cb-statistic-byrange').find('li').removeClass('cb-selected');
    $(this).addClass('cb-selected');
    var mod = $(this).attr('data-smod');
    $('input[name="d_start"]').val('');
    $('input[name="d_end"]').val('');
    $('input[name="mod"]').val(mod);
    Load_BotChart();  
});

// 기간 검색버튼 클릭 이벤트 
$('[data-role="btn-search"]').on('click',function(){
    $('input[name="mod"]').val('');
    $('.cb-statistic-byrange').find('li').removeClass('cb-selected');
    Load_BotChart();
});

// 봇 select change 이벤트 
$('select[name="botuid"]').on('change',function(){
   Load_BotChart(); 
});

// 최초 로딩시 
$(document).ready(function(){
   Load_BotChart();    
});

</script>