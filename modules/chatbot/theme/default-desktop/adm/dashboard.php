<?php

    require_once 'dashboard/controller/DashBoardController.php';

    $dashboardController = new DashBoardController();

    $chatbot->vendor = $V['uid'];
    $botuid = $bot?$bot:'';

    $response = $dashboardController->getDashboard(new Bot((new BotBuilder())->setUid($botuid)->setVendor($V['uid'])));
?>
<?php echo $response['dashboard']?>
<!-- 공통 엘리먼트 -->
<?php echo $response['commonElement']?>
<!-- bootstrap css -->
<style>
    .white-box:after{
        clear:both;display:table;content:'';
    }
</style>

<script src="<?php echo $g['url_layout']?>/_js/jquery.chosen.js"></script>
<link rel="stylesheet" href="<?php echo $g['url_layout']?>/_css/chosen.css">

<!-- 상단 통계숫자  에니메이션 카운팅 -->
<script src="<?php echo $g['url_layout']?>/_js/chart.min.js"></script>
<script src="<?php echo $g['url_layout']?>/_js/jquery.waypoints.min.js"></script>
<script src="<?php echo $g['url_layout']?>/_js/jquery.counterup.min.js"></script>

<?php getImport('bootstrap-datepicker','css/datepicker3',false,'css')?>
<?php getImport('bootstrap-datepicker','js/bootstrap-datepicker',false,'js')?>
<?php getImport('bootstrap-datepicker','js/locales/bootstrap-datepicker.kr',false,'js')?>

<script type="text/javascript">
    // 인텐트 지정 모달
    var setIntentModal = '#modal-setIntent';
    var module = $('input[name="module_value"]').val();
    var vendor = $('input[name="vendor_value"]').val();
    var botuid = $('input[name="botUid_value"]').val();

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
        var module = 'chatbot';
        var vendor = $('input[name="vendor_value"]').val();
        var botuid = $('input[name="botUid_value"]').val();
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
            vendor : $('input[name="vendor_value"]').val(),
            botuid : $('input[name="botUid_value"]').val(),
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