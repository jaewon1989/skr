<?php
$chatbot->vendor = $V['uid'];
$botuid = $bot?$bot:'';

$dToday = date("Y-m-d", time());
$aDate = explode("-", $dToday);
$dYear = $aDate[0];
$dMonth = $aDate[1];
$dDay = $aDate[2];
$d_start = date("Y-m-d",mktime(0,0,0,$dMonth,$dDay-30,$dYear));
$d_end = $dToday;
?>

<!-- bootstrap css -->
<style>.white-box:after{clear:both;display:table;content:'';}</style>
<input type="hidden" name="mod" value="month" />
<input type="hidden" name="d_start" value="<?=$d_start?>" />
<input type="hidden" name="d_end" value="<?=$d_end?>" />

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><?php echo $pageTitle?></h4>
        </div>
    </div>
    <!-- row -->
    <div class="row">
        <div class="col-md-12">
            <h3 class="box-title" style="padding-left:25px; font-size:13px;"><i class="fa fa-calendar"></i> 최근 한달 (<?=$d_start?> ~ <?=$d_end?>)</h3>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <div class="white-box" style="padding:15px 25px;">
                <div class="col-in row" style="padding:10px 20px;">
                    <div class="col-md-4 col-sm-6 col-xs-6">
                        <h5 class="text-muted vb">챗봇명</h5> 
                    </div>
                    <div class="col-md-12 col-sm-6 col-xs-6">
                        <h3 class="text-right" style="width:100%;font-size:20px;font-weight:400;"><?=$getListBot['name']?></h3> 
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <div class="white-box" style="padding:15px 25px;">
                <div class="col-in row" style="padding:10px 20px;">
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <h5 class="text-muted vb">인텐트 수</h5>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <h3 id="totalIntent" class="counter text-right m-t-15 text-danger"></h3> 
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <div class="white-box" style="padding:15px 25px;">
                <div class="col-in row" style="padding:10px 20px;">
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <h5 class="text-muted vb">엔터티 수</h5> 
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <h3 id="totalEntity" class="counter text-right m-t-15 text-danger"></h3> 
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <div class="white-box" style="padding:15px 25px;">
                <div class="col-in row" style="padding:10px 20px;">
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <h5 class="text-muted vb">대화상자 수</h5> 
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <h3 id="totalNode" class="counter text-right m-t-15 text-danger"></h3> 
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 사용현황 -->
    <div class="row">
        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <div class="white-box" style="padding:15px 25px;">
                <div class="col-in row" style="padding:10px 20px;">
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <h5 class="text-muted vb">총 누적 접속 수</h5> 
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <h3 id="totalAccess" class="counter text-right m-t-15 text-danger"></h3> 
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <div class="white-box" style="padding:15px 25px;">
                <div class="col-in row" style="padding:10px 20px;">
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <h5 class="text-muted vb">총 누적 세션 수</h5> 
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <h3 id="totalUser" class="counter text-right m-t-15 text-danger"></h3> 
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <div class="white-box" style="padding:15px 25px;">
                <div class="col-in row" style="padding:10px 20px;">
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <h5 class="text-muted vb">총 누적 대화 수</h5> 
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <h3 id="totalChat" class="counter text-right m-t-15 text-danger"></h3> 
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <div class="white-box" style="padding:15px 25px;">
                <div class="col-in row" style="padding:10px 20px;">
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <h5 class="text-muted vb">인당 대화 수</h5> 
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <h3 id="perChat" class="counter text-right m-t-15 text-danger"></h3> 
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 사용현황 끝 -->
    
    <!-- /.row -->
    <div class="row">
        <div class="col-sm-4">
            <div class="white-box" style="min-height:665px;">                
                <h3 class="box-title" style="float:left;width:60%;">대화상자 순위</h3>
                <h3 class="box-title" id="nodeBtn" style="float:right;width:40%;text-align:right;"></h3>
                <input type="hidden" value="1" data-role="nodePage-input"/>
                <div class="table-responsive" style="clear:both;">
                    <table class="table">
                        <tr>
                            <th style="border-top:1px solid #333;">대화상자</th>
                            <th style="border-top:1px solid #333;">횟수</th>
                        </tr>
                        <tbody id="nodeHtml"></tbody>
                    </table>
                </div>                
            </div>
        </div>
        <div class="col-sm-4">
            <div class="white-box" style="min-height:665px;">
                <h3 class="box-title" style="float:left;width:60%;">많이 한 질문</h3>
                <h3 class="box-title" id="questionBtn" style="float:right;width:40%;text-align:right;"></h3>
                <input type="hidden" value="1" data-role="questionPage-input"/>
                <div class="table-responsive" style="clear:both;">
                    <table class="table">
                        <tr>
                            <th style="border-top:1px solid #333;">질문내용</th>
                            <th style="border-top:1px solid #333;">횟수</th>
                        </tr>
                        <tbody id="questionHtml"></tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-sm-4">
            <div class="white-box" style="min-height:665px;">
                <h3 class="box-title" style="float:left;width:60%;">많이 사용한 단어</h3>
                <h3 class="box-title" id="wordBtn" style="float:right;width:40%;text-align:right;"></h3>
                <input type="hidden" value="1" data-role="wordPage-input"/>
                <div class="table-responsive" style="clear:both;">
                    <table class="table">
                        <tr>
                            <th style="border-top:1px solid #333;">단어</th>
                            <th style="border-top:1px solid #333;">횟수</th>
                        </tr>
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
                <h3 class="box-title">사용자 현황</h3>
                <div id="total-wrap">         
                   <!-- load 접속통계 차트 -->       
                </div>
            </div>
        </div>
    </div>
    <!--row -->
    
    <!--대화 현황 -->
    <div class="row">
        <div class="col-md-12">
            <div class="white-box">
                <h3 class="box-title">대화 현황</h3>
                <div id="conversation-wrap">         
                   <!-- load 접속통계 차트 -->       
                </div>
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
        calendarWeeks: true,
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
            var total_chart=result.total_chart;
            
            // insert chart 
            $('#totalIntent').html(result.totalIntent);
            $('#totalEntity').html(result.totalEntity);
            $('#totalNode').html(result.totalNode);
            $('#totalAccess').html(result.totalAccess);
            $('#totalUser').html(result.totalUser);
            $('#totalChat').html(result.totalChat);  
            $('#perChat').html(result.perChat);          
            $(".counter").counterUp({
                delay: 100, time: 1200
            });
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