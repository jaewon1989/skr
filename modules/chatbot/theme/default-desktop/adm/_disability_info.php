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
    <style>
        body {font-family: "Nanum Gothic", sans-serif !important;}
        .white-box {background:#fff; padding:25px;}
        tr.r_finish>td {background-color:#e8f4f9}
    </style>
    <link href="/layouts/vendor-desktop/_css/kiere.css" rel="stylesheet">
    <link href="<?php echo $g['url_module_skin']?>/css/chatLog.css" rel="stylesheet">
    <script src="/plugins/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <div id="_modal_header" class="hidden">
    	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    	<h4 class="modal-title">장애접수 정보</h4>
    </div>

    <div class="row" style="margin:20px; height:750px; overflow:hidden; overflow-y:auto;">
        <div class="row">
            <div class="col-md-12">
                <form class="form-horizontal rb-form" name="logForm">
                    <input type="hidden" name="mod" value="month" />
                    <div class="white-box" style="padding:15px;">
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

        <!--row -->
        <div class="row" style="margin-top:20px;">
            <div class="col-md-12">
                <div class="white-box">
                    <h3 class="box-title">접속통계</h3>
                    <div id="total-wrap" style="min-height:150px;">
                       <!-- load 접속통계 차트 -->
                    </div>
                </div>
            </div>
        </div>
        <!--row -->

        <!--row -->
        <div class="row" style="margin-top:20px;">
            <div class="col-md-12">
                <div class="white-box" style="padding-top:10px;">
                    <div style="text-align:right; margin-bottom:10px;">
                        <button id="btn_excel_disability" class="btn btn-info btn_excel">엑셀 다운로드</button>
                    </div>
                    <div class="col-in row">
                        <div class="table-responsive" style="min-height:400px;">
                            <table class="table table-striped">
                                <tr>
                                    <th style="width:15%;border-top:1px solid #333;text-align:center;">등록일</th>
                                    <th style="width:15%;border-top:1px solid #333;text-align:center;">소속(부서)</th>
                                    <th style="width:10%;border-top:1px solid #333;text-align:center;">이름</th>
                                    <th style="width:15%;border-top:1px solid #333;text-align:center;">연락처</th>
                                    <th style="width:35%;border-top:1px solid #333;text-align:center;">장애증상</th>
                                    <th style="width:10%;border-top:1px solid #333;text-align:center;">상담여부</th>
                                </tr>
                                <tbody id="total-list"></tbody>
                            </table>

                            <div class="text-center">
                                <input type="hidden" name="p" value="1" />
                                <ul id="pageLink" class="pagination pagination-sm" style="margin:10px 0 0">
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--row -->
    </div>

    <link rel="stylesheet" href="/layouts/vendor-desktop/_css/chosen.css">
    <link href="/plugins/bootstrap-datepicker/1.3.0/css/datepicker3.css" rel="stylesheet">
    <script src="/layouts/vendor-desktop/_js/jquery.chosen.js"></script>
    <script src="/layouts/vendor-desktop/_js/chart.min.js"></script>
    <script src="/layouts/vendor-desktop/_js/jquery.waypoints.js"></script>
    <script src="/plugins/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>
    <script src="/plugins/bootstrap-datepicker/1.3.0/js/locales/bootstrap-datepicker.kr.js"></script>


<script type="text/javascript">
    function modalSetting() {
    	var ht = document.body.scrollHeight;

    	parent.getId('modal_window').style.zIndex = '9999';
    	parent.getId('modal_window_dialog_modal_window').style.width = '100%';
    	parent.getId('modal_window_dialog_modal_window').style.paddingRight = '20px';
    	parent.getId('modal_window_dialog_modal_window').style.maxWidth = '70%';
    	parent.getId('_modal_iframe_modal_window').style.height = ht+'px'
    	parent.getId('_modal_body_modal_window').style.height = ht+'px';
    	parent.getId('_modal_body_modal_window').style.background = '#edf1f5';

    	parent.getId('_modal_header_modal_window').innerHTML = getId('_modal_header').innerHTML;
    	parent.getId('_modal_header_modal_window').className = 'modal-header';
    	parent.getId('_modal_body_modal_window').style.padding = '0';
    	parent.getId('_modal_body_modal_window').style.margin = '0';
    }
    document.body.onresize = document.body.onload = function() {
    	setTimeout("modalSetting();",100);
    	setTimeout("modalSetting();",200);
    }

    $("body").css("background-color", "transparent");

    var module='chatbot';
    var vendor='<?php echo $V['uid']?>';
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

    // 페이지 링크 관련
    $(document).on("click", "ul.pagination li a", function(e) {
        e.preventDefault();
        if($(this).attr("href").indexOf("p=") > -1) {
            var _p = $(this).attr("href").replace("&p=", "");
            $('input[name="p"]').val(_p);
            $('input[name="mod"]').val('paging');
            Load_BotChart();
        }
    });

    // 상담여부
    $(document).on("change", ".s_status", function() {
        $this = $(this);
        var uid = $this.attr("uid");
        var status = $("> option:selected", this).val();
        if(uid) {
            $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=get_Disability',{
                linkType : 'status',
                vendor : vendor,
                botuid : botuid,
                uid : uid,
                status : status
            },function(response){
                var result=$.parseJSON(response);
                if(result.error) {
                    alert(result.msg);
                } else {
                    if(status == 'finish') $this.parent().parent().addClass("r_finish");
                    else $this.parent().parent().removeClass("r_finish");
                }
            });
        }
    });

    // 엑셀 다운
    $(document).on("click", "#btn_excel_disability", function() {
        location.href = rooturl+'/?r='+raccount+'&m='+module+'&a=get_Disability&linkType=excel_down&vendor='+vendor+'&botuid='+botuid;
    });

    // 최초 로딩시
    $(document).ready(function(){
        $('[data-toggle=tooltip]').tooltip();
        Load_BotChart();
    });

    $(document).on("click", ".rsv_info", function() {
        if($(this).children('i').hasClass('fa-angle-up')) {
            $(this).children('i').removeClass('fa-angle-up').addClass('fa-angle-down');
            $(this).parents('tr').next('tr').removeClass('active');
        } else {
            $(this).children('i').removeClass('fa-angle-down').addClass('fa-angle-up');
            $(this).parents('tr').next('tr').addClass('active');
        }
    });

    function Load_BotChart(){
        var mod = $('input[name="mod"]').val();
        var d_start = $('input[name="d_start"]').val();
        var d_end = $('input[name="d_end"]').val();
        var p = $('input[name="p"]').val();
        $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=get_Disability',{
            linkType : 'reserve',
            vendor : vendor,
            botuid : botuid,
            mod : mod,
            d_start : d_start,
            d_end : d_end,
            p : p
        },function(response){
            var result=$.parseJSON(response);//$.parseJSON(response);

            $('#total-wrap').html(result.total_chart);
            $('#total-list').html(result.totalList);
            $('#pageLink').html(result.pageLink);
        });
    }
</script>