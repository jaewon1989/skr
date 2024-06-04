<?php
$chatbot->vendor = $V['uid'];
$botuid = $bot?$bot:'';

$dToday = date("Y-m-d", time());
$aDate = explode("-", $dToday);
$dYear = $aDate[0];
$dMonth = $aDate[1];
$dDay = $aDate[2];
$aLogDate = array();
$aLogDate[] = array("어제", date("Y-m-d",mktime(0,0,0,$dMonth,$dDay-1,$dYear)), date("Y-m-d",mktime(0,0,0,$dMonth,$dDay-1,$dYear)));
$aLogDate[] = array("오늘", $dToday, $dToday);
$aLogDate[] = array("일주", date("Y-m-d",mktime(0,0,0,$dMonth,$dDay-7,$dYear)), $dToday);
$aLogDate[] = array("한달", date("Y-m-d",mktime(0,0,0,$dMonth-1,$dDay,$dYear)), $dToday);
$aLogDate[] = array("당월", date('Y-m')."-01", $dToday);
$aLogDate[] = array("전체", "", "");
$d_start = date("Y-m-d",mktime(0,0,0,$dMonth,$dDay-30,$dYear));
$d_end = $dToday;

?>

<!-- bootstrap css -->
<style>.white-box:after{clear:both;display:table;content:'';}</style>
<div class="container-fluid">
    <!--
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">통계/분석 > 통계관리</h4>
        </div>
    </div>
    -->
    <div class="overview">
        <div class="page-title">통계관리</div>
        <div class="sub-frame">
            <div class="sub-title">SK telecom AICC / <?php echo $pageTitle?></div>
        </div>
    </div>
    <!-- row -->
    <!-- 기간 설정 -->
    <div class="row">
        <div class="col-md-12">
            <h3 class="box-title" style="padding-left:10px; font-size:18px; font-weight:600;"><i class="fa fa-info-circle"></i> 사용 현황</h3>
        </div>

        <div class="col-md-12">
            <form class="form-horizontal rb-form" name="logForm">
                <input type="hidden" name="mod" value="month" />
                <div class="white-box">
                    <div class="form-group" style="margin-bottom:0; text-align:center;">
                        <div style="display:inline-block; width:40%;">
                            <div id="datepicker" class="input-daterange input-group bot_log">
                                <input type="text" class="form-control" id="d_start" name="d_start" placeholder="시작일 선택" autocomplete="off" value="<?=$d_start?>">
                                <span class="input-group-addon">~</span>
                                <input type="text" class="form-control" id="d_end" name="d_end" placeholder="종료일 선택" autocomplete="off" value="<?=$d_end?>">
                                <span class="input-group-btn">
                                    <button type="button" id="search" class="btn btn_search btn-default">검색</button>
                                </span>
                            </div>
                        </div>
                        <div style="display:inline-block; width:20%;">
                            <span class="input-group-btn log_btn">
                                <?foreach($aLogDate as $aDate) {?>
                                    <button type="button" class="btn btn-default <?=($aDate[0]=='한달' ? 'btn-primary' : '')?>" sDate="<?=$aDate[1]?>" eDate="<?=$aDate[2]?>"><?=$aDate[0]?></button>
                                <?}?>
                            </span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- 기간 설정 끝 -->

    <div class="overview-frame">
        <div>
            <div class="frame-title"><?=$getListBot['bottype'] == 'call' ? '콜봇' :  '챗봇' ?>명</div>
            <div>
                <div class="data">
                    <div><?=$getListBot['name']?></div>
                </div>
            </div>
        </div>
        <div>
            <div class="frame-title">인텐트 수</div>
            <div>
                <div class="data">
                    <div id="totalIntent" class="counter"></div>
                </div>
            </div>
        </div>
        <div>
            <div class="frame-title">엔터티 수</div>
            <div>
                <div class="data">
                    <div id="totalEntity" class="counter"></div>
                </div>
            </div>
        </div>
        <div>
            <div class="frame-title">대화상자 수</div>
            <div>
                <div class="data">
                    <div id="totalNode" class="counter"></div>
                </div>
            </div>
        </div>
        <div>
            <div class="frame-title">접속 수</div>
            <div>
                <div class="data">
                    <div id="userTotalAccessCount" class="counter"></div>
                </div>
            </div>
        </div>
        <div>
            <div class="frame-title">재방문률</div>
            <div>
                <div class="data">
                    <div id="userTotalRevisitAccessRate" class="counter"></div>
                </div>
            </div>
        </div>
        <div>
            <div class="frame-title">미응답 수</div>
            <div>
                <div class="data">
                    <div id="totalUnAnsweredCount" class="counter"></div>
                </div>
            </div>
        </div>
        <div>
            <div class="frame-title">응답률</div>
            <div>
                <div class="data">
                    <div id="totalAnsweredRate" class="counter"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- /.row -->
    <div class="row chart_status_board">
        <div class="col-sm-4">
            <div class="white-box" style="min-height:300px;">
                <h3 class="box-title" style="float:left;width:60%;">대화상자 순위</h3>
                <h3 class="box-title" id="nodeBtn" style="float:right;width:40%;text-align:right;height:30px;"></h3>
                <input type="hidden" value="1" data-role="nodePage-input"/>
                <div class="table-responsive table-container table-aicc-skin" style="clear:both;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="text-align: center;">대화상자</th>
                                <th style="text-align: center;">횟수</th>
                            </tr>
                        </thead>
                        <tbody id="nodeHtml"></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="white-box" style="min-height:300px;">
                <h3 class="box-title" style="float:left;width:60%;">많이 한 질문</h3>
                <h3 class="box-title" id="questionBtn" style="float:right;width:40%;text-align:right;"></h3>
                <input type="hidden" value="1" data-role="questionPage-input"/>
                <div class="table-responsive table-container table-aicc-skin" style="clear:both;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="text-align: center;">질문내용</th>
                                <th style="text-align: center;">횟수</th>
                            </tr>
                        </thead>
                        <tbody id="questionHtml"></tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-sm-4">
            <div class="white-box" style="min-height:300px;">
                <h3 class="box-title" style="float:left;width:60%;">많이 사용한 단어</h3>
                <h3 class="box-title" id="wordBtn" style="float:right;width:40%;text-align:right;"></h3>
                <input type="hidden" value="1" data-role="wordPage-input"/>
                <div class="table-responsive table-container table-aicc-skin" style="clear:both;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="text-align: center;">단어</th>
                                <th style="text-align: center;">횟수</th>
                            </tr>
                        </thead>
                        <tbody id="wordHtml"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->
    
    <!--사용자 현황 -->
    <div class="row">
        <div class="col-md-12">
            <div class="white-box">
                <div id="total-wrap" style="height:40vh;"><!-- load 접속통계 차트 --></div>
            </div>
        </div>
    </div>
    <!--row -->
    
    <!--대화 현황 -->
    <div class="row">
        <div class="col-md-12">
            <div class="white-box">
                <div id="conversation-wrap" style="height:40vh;"><!-- load 접속통계 차트 --></div>
            </div>
        </div>
    </div>
    <!--row -->
    
    <!--row -->
    <div class="row">
        <div class="col-lg-6 col-md-4 col-sm-12 col-xs-12">
            <div class="white-box h_400">
                <div class="col-in row">
                    <h3 class="box-title">많이 클릭한 버튼</h3>
                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <th style="border-top:1px solid #333;">버튼명</th>
                                <th style="border-top:1px solid #333;">횟수</th>
                            </tr>
                            <tbody id="btnLogHtml1"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 col-md-4 col-sm-12 col-xs-12">
            <div class="white-box h_400">
                <div class="col-in row">
                    <h3 class="box-title">&nbsp;</h3>
                    <div class="table-responsive">
                        <table class="table ">
                            <tr>
                                <th style="border-top:1px solid #333;">버튼명</th>
                                <th style="border-top:1px solid #333;">횟수</th>
                            </tr>
                            <tbody id="btnLogHtml2"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
            
    </div>
    <!--row -->
</div>
<script src="<?php echo $g['url_layout']?>/_js/jquery.chosen.js"></script>
<link rel="stylesheet" href="<?php echo $g['url_layout']?>/_css/chosen.css">

<!-- 상단 통계숫자  에니메이션 카운팅 --> 
<script src="<?php echo $g['url_layout']?>/_js/chart.min.js"></script>
<script src="<?php echo $g['url_layout']?>/_js/jquery.waypoints.min.js"></script>
<script src="<?php echo $g['url_layout']?>/_js/jquery.counterup.min.js"></script>

<?php getImport('bootstrap-datepicker','css/datepicker3',false,'css')?>
<?php getImport('bootstrap-datepicker','js/bootstrap-datepicker',false,'js')?>
<?php getImport('bootstrap-datepicker','js/locales/bootstrap-datepicker.kr',false,'js')?>
<script>
    // 인텐트 지정 모달 
    var setIntentModal = '#modal-setIntent';
    var module='<?php echo $m?>';
    var vendor='<?php echo $V['uid']?>';
    //var botuid =$('select[name="botuid"]').val();
    var botuid='<?php echo $botuid?>';

    // 날짜 선택 
    $('.input-daterange').datepicker({
        format: "yyyy-mm-dd",
        todayBtn: "linked",
        language: "kr",
        todayHighlight: true,
        autoclose: true
    }).on('changeDate', function(e) {
        $('.btn_search').addClass("btn-primary");
        $('.log_btn .btn').removeClass("btn-primary");
    });

    var Load_BotChart = function(){
        var module='chatbot';
        var vendor='<?php echo $V['uid']?>';
        var botuid='<?php echo $botuid?>';
        var mod = $('input[name="mod"]').val();
        var d_start = $('input[name="d_start"]').val();
        var d_end = $('input[name="d_end"]').val();        
        $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=get_StatisticsChart',{
            linkType : 'all_data',
            vendor : vendor,
            botuid : botuid,
            mod : mod,
            d_start : d_start,
            d_end : d_end
        },function(response){
            checkLogCountdown();
            var result=$.parseJSON(response);//$.parseJSON(response);
            //var total_chart=result.total_chart;
            
            // insert chart 
            $('#totalIntent').html(result.totalIntent);
            $('#totalEntity').html(result.totalEntity);
            $('#totalNode').html(result.totalNode);
            /*$('#totalAccess').html(result.totalAccess);
            $('#totalUser').html(result.totalUser);
            $('#totalChat').html(result.totalChat);  
            $('#perChat').html(result.perChat);*/
            $('#userTotalAccessCount').html(result.userTotalAccessCount);
            $('#userTotalRevisitAccessRate').html(result.userTotalRevisitAccessRate + '%');
            $('#totalUnAnsweredCount').html(result.totalUnAnsweredCount);
            $('#totalAnsweredRate').html(result.totalAnsweredRate + '%');

            /*$(".counter").counterUp({
                delay: 100, time: 1200
            });*/
            $('#nodeHtml').html(result.nodeHtml);
            $('#nodeBtn').html(result.nodeBtn);
            $('#questionHtml').html(result.questionHtml);
            $('#questionBtn').html(result.questionBtn);
            $('#wordHtml').html(result.wordHtml);
            $('#wordBtn').html(result.wordBtn);
            $('#btnLogHtml1').html(result.btnLogHtml1);
            $('#btnLogHtml2').html(result.btnLogHtml2);
            $('#total-wrap').html(result.total_chart);
            $('#conversation-wrap').html(result.conversation_chart);
        });
    }
    
    // 페이징
    $(document).on('click','[data-role="btn-paging"]',function(){
        var mod = $(this).data('mod');
        var page = $(this).data('page')?$(this).data('page'):1;
        var d_start = $('input[name="d_start"]').val();
        var d_end = $('input[name="d_end"]').val();
        $('[data-role="'+mod+'Page-input"]').val(page);

        $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=get_StatisticsChart',{
            linkType : 'page_data',
            vendor : '<?php echo $V['uid']?>',
            botuid : '<?php echo $botuid?>',
            mod : mod,
            page : page,
            d_start : d_start,
            d_end : d_end
        },function(response){
            checkLogCountdown();
            var result=$.parseJSON(response);//$.parseJSON(response);
            
            $('#'+mod+'Html').html(result.chHtml);
            $('#'+mod+'Btn').html(result.chBtn);
        });
    });

    // 일단,주간,월간 버튼 클릭 이벤트 
    $('.log_btn .btn').on('click',function(){
        $('.btn_search, .log_btn .btn').removeClass("btn-primary");
        $(this).addClass('btn-primary');
        
        $('input[name="d_start"]').val($(this).attr("sDate"));
        $('input[name="d_end"]').val($(this).attr("eDate"));
        Load_BotChart();  
    });
    $('.btn_search').on('click',function(){
        if(!$('input[name="d_start"]').val()) {
            alert('검색 시작일을 선택해주세요.'); return false;
        }
        if(!$('input[name="d_end"]').val()) {
            alert('검색 종료일을 선택해주세요.'); return false;
        }        
        Load_BotChart();  
    });

    // 기간 검색버튼 클릭 이벤트 
    $('[data-role="btn-search"]').on('click',function(){
        $('input[name="mod"]').val('');
        $('.cb-statistic-byrange').find('li').removeClass('cb-selected');
        Load_BotChart();
    });

    // 최초 로딩시 
    $(document).ready(function(){
        $('[data-toggle=tooltip]').tooltip();
        Load_BotChart();    
    });

</script>