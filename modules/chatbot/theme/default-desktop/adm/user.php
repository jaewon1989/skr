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
<div class="container-fluid">
    <!--
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">통계/분석 > 사용자</h4>
        </div>
    </div>
    -->
    <div class="overview">
        <div class="page-title">사용자 현황</div>
        <div class="sub-frame">
            <div class="sub-title">SK telecom AICC / <?php echo $pageTitle?></div>
        </div>
    </div>
    <!-- row -->
    <div class="row">
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
    
    <!-- 사용현황 -->
    <div class="row">
        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <div class="white-box">
                <div class="col-in row">
                    <div class="col-md-6 col-sm-6 col-xs-6"> <i data-icon="E" class="linea-icon linea-basic"></i>
                        <h5 class="text-muted vb">총 누적 접속 수</h5> </div>
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <h3 id="totalAccess" class="counter text-right m-t-15 text-danger"></h3> </div>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="progress">
                            <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%"> </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <div class="white-box">
                <div class="col-in row">
                    <div class="col-md-6 col-sm-6 col-xs-6"> <i class="linea-icon linea-basic" data-icon=""></i>
                        <h5 class="text-muted vb">총 누적 세션수</h5> </div>
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <h3 id="totalUser" class="counter text-right m-t-15 text-megna"></h3> </div>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="progress">
                            <div class="progress-bar progress-bar-megna" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <div class="white-box">
                <div class="col-in row">
                    <div class="col-md-6 col-sm-6 col-xs-6"> <i class="linea-icon linea-basic" data-icon=""></i>
                        <h5 class="text-muted vb">총 누적 대화 수</h5> </div>
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <h3 id="totalChat" class="counter text-right m-t-15 text-primary"></h3> </div>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="progress">
                            <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <div class="white-box">
                <div class="col-in row">
                    <div class="col-md-6 col-sm-6 col-xs-6"> <i class="linea-icon linea-basic" data-icon=""></i>
                        <h5 class="text-muted vb">인당 대화 수</h5> </div>
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <h3 id="perChat" class="counter text-right m-t-15 text-primary"></h3> </div>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="progress">
                            <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 사용현황 끝 -->
    
    <!--row -->
    <div class="row">
        <div class="col-md-12">
            <div class="white-box">
                <h3 class="box-title">접속통계</h3>
                <div id="total-wrap" style="height: 40vh;">
                   <!-- load 접속통계 차트 -->       
                </div>
            </div>
        </div>
    </div>
    <!--row -->
    
    <!--row -->
    <div class="row">
        <div class="col-md-12">
            <div class="white-box h_400">
                <div class="col-in row">
                    <div class="table-responsive table-container table-aicc-skin">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">일자</th>
                                    <th style="text-align: center;">누적 사용자</th>
                                    <th style="text-align: center;">재방문</th>
                                    <th style="text-align: center;">신규</th>
                                    <th style="text-align: center;">대화수</th>
                                </tr>
                            </thead>
                            <tbody id="total-list"></tbody>
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
<script src="<?php echo $g['url_layout']?>/_js/chart.min.4.4.2.js"></script>
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
            linkType : 'user',
            vendor : vendor,
            botuid : botuid,
            mod : mod,
            d_start : d_start,
            d_end : d_end
        },function(response){
            checkLogCountdown();
            var result=$.parseJSON(response);//$.parseJSON(response);
            var total_chart=result.total_chart;
            
            // insert chart 
            $('#totalAccess').html(result.totalAccess);
            $('#totalUser').html(result.totalUser);
            $('#totalChat').html(result.totalChat);  
            $('#perChat').html(result.perChat);          
            $(".counter").counterUp({
                delay: 100, time: 1200
            });            
            $('#total-wrap').html(total_chart);
            $('#total-list').html(result.totalList);
        }); 
    }

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