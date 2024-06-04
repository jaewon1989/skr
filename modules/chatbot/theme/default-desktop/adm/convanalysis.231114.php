<?php
$chatbot->vendor = $V['uid'];
$vendor = $V['uid'];
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
<style>
    .btn-info {padding:6px 10px; font-size:13px;}
    .pd15 {padding:15px !important;}
    .bottom0 {padding-bottom:0 !important; margin-bottom:0 !important;}
    .col-in {padding: 0px;}
    .col-in h3 {font-size: 30px; text-align:center;}
    .text-muted {color: #8d9ea7; text-align:center;}
    .aleft {text-align:left !important;}
    .ul_info {display:block; text-align:center; padding-bottom:10px !important; border-bottom:1px solid #e4e7ea;}
    .ul_info li {display:inline-block; text-align:center; width:19%;}
    .ul_info li span {display:block; text-align:center;}
    .ul_info li span.info_num {font-size:30px; font-weight:bold; color:#009efb;}
    .node_box_wrap {padding-bottom:25px !important;}
    .node_box {clear:both; position:relative; padding:10px; border:1px solid #e4e7ea;}
    .node_box_guide {display:block; text-align:center; color:#bbb; line-height:30px;}
    .nodeInfoWrap {min-height:400px;}
    .conv_btn_wrap {margin-top:15px; text-align:center;}
    #stateWrap {position:relative; overflow:hidden; overflow-y:auto;}
    .ul_node_box {display:none;}
    .ul_node_box li {float:left; position:relative; margin-right:25px;}
    .ul_node_box li:last-child {margin-right:0;}
    .ul_node_box li span {display:block; line-height:30px; background:#68839a; color:#fff; border-radius:5px; padding:0 10px; cursor:pointer;}
    .ul_node_box li span:after {position:absolute; right:-20px; color:#333; font-size:12px; content:'▶'}
    .ul_node_box li:last-child span:after {content:'';}
    .ul_node_box li span.off {background:#dcdcdc; color:#999;}
    .ul_node_box:after {clear:both; display:block; content:'';}
</style>

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">통계/분석 > 대화 분석</h4>
        </div>
    </div>
    <!-- row -->
    <div class="row">
        <div class="col-md-12">
            <form class="form-horizontal rb-form" name="logForm">
                <input type="hidden" name="mod" value="month" />
                <div class="white-box pd15">
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
    
    <div class="row">
        <!-- 그래프 -->
        <div class="col-sm-6">
            <div id="node_chart" class="white-box bottom0 nodeInfoWrap" style="padding:0;">            
            </div>
        </div>
        
        <!-- 대화상자 현황 -->
        <div class="col-sm-6">
            <div id="stateWrap" class="white-box bottom0 nodeInfoWrap">
                <div class="top_info">
                    <ul class="ul_info">
                        <li>
                            <span>총 대화상자 수</span>
                            <span id="totalNode" class="info_num counter">0</span>
                        </li>
                        <li>
                            <span>총 대화 수</span>
                            <span id="totalChat" class="info_num counter">0</span>
                        </li>
                        <li>
                            <span>인당 대화 수</span>
                            <span id="perChat" class="info_num counter">0</span>
                        </li>
                        <li>
                            <span>답변 못한 대화</span>
                            <span id="totalUnknown" class="info_num counter">0</span>
                        </li>
                        <li>
                            <span>총 누적 접속 수</span>
                            <span id="totalAccess" class="info_num counter">0</span>
                        </li>
                    </ul>
                </div>
                
                <!-- 대화상자 박스 -->
                <div class="node_box_wrap" style="margin-top:20px;">
                    <h3 class="box-title" style="float:left;width:20%;">선택한 대화상자</h3>
                    <div class="node_box">
                        <ul class="ul_node_box">
                            <li><span>인사</span></li>
                            <li><span>인재채용</span></li>
                        </ul>
                        <div class="node_box_guide">왼쪽 그래프에서 대화상자 선택 시 선택한 대화상자의 대화 상세 내역 확인이 가능합니다.</div>
                    </div>
                    
                    <!-- 대화 상세 내역 리스트 -->
                    <div id="node_conv" class="listWrap" style="margin-top:25px;">
                        <h3 class="box-title" style="float:left;width:50%;">
                            대화내역 상세
                            <span id="pageHtml" style="display:inline-block; margin-left:30px;"></span>
                            <input type="hidden" name="conv_page" value="1" /> 
                        </h3>
                        <h3 class="box-title" style="float:right;width:40%;text-align:right;font-size:15px;">
                            <button class="btn btn-info btn_excel" mod="node_conversation">엑셀파일 다운로드</button>
                        </h3>
                        <div class="fixed_table_container" style="width:100%;">
                            <div class="fixed_table_header_bg"></div>
                            <div class="fixed_table_wrapper">
                                <table class="fixed_table" id="tbl-wordgroup">
                                    <thead>                            
                                        <tr>
                                            <th style="width:20%;"><div class="th_text">대화상자</div></th>
                                            <th style="width:25%;"><div class="th_text">질문</div></th>
                                            <th style="width:20%;"><div class="th_text">엔터티</div></th>
                                            <th style="width:35%;"><div class="th_text">답변</div></th>
                                        </tr>                            
                                    </thead>
                                    <tbody id="convHtml"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 대화상자 현황 리스트 -->
                <div class="listWrap" style="margin-top:25px;">
                    <h3 class="box-title" style="float:left;width:20%;">대화상자</h3>
                    <h3 class="box-title" style="float:right;width:40%;text-align:right;font-size:15px;">
                        <button class="btn btn-info btn_excel" mod="node_analysis">엑셀파일 다운로드</button>
                    </h3>
                    <div class="fixed_table_container" style="width:100%;">
                        <div class="fixed_table_header_bg"></div>
                        <div class="fixed_table_wrapper">
                            <table class="fixed_table" id="tbl-wordgroup">
                                <thead>                            
                                    <tr>
                                        <th style="width:10%;"><div class="th_text">No</div></th>
                                        <th style="width:40%;"><div class="th_text">대화상자</div></th>
                                        <th style="width:25%;"><div class="th_text">질문수</div></th>
                                        <th style="width:25%;"><div class="th_text">질문율</div></th>
                                    </tr>                            
                                </thead>
                                <tbody id="stateHtml">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form name="exportNodeForm" id="exportNodeForm" action="/" method="post" target="_action_frame_<?php echo $m?>" enctype="multipart/form-data"> 
    <input type="hidden" name="r" value="<?php echo $r?>" />
    <input type="hidden" name="m" value="<?php echo $m?>" />
    <input type="hidden" name="vendor" value="<?php echo $vendor?>" />
    <input type="hidden" name="botuid" value="<?php echo $botuid?>" />
    <input type="hidden" name="a" value="get_StatisticsChart">
    <input type="hidden" name="mod" value="excel_export">
    <input type="hidden" name="linkType" value=""/> 
    <input type="hidden" name="nodeNames" value=""/>
</form>

<!-- 상단 통계숫자  에니메이션 카운팅 --> 
<script src="<?php echo $g['url_layout']?>/_js/chart.min.js"></script>
<script src="<?php echo $g['url_layout']?>/_js/jquery.waypoints.min.js"></script>
<script src="<?php echo $g['url_layout']?>/_js/jquery.counterup.min.js"></script>

<script src="<?php echo $g['url_layout']?>/_js/d3.min.js"></script>
<script src="<?php echo $g['url_layout']?>/_js/sunburst-chart.js"></script>

<?php getImport('bootstrap-datepicker','css/datepicker3',false,'css')?>
<?php getImport('bootstrap-datepicker','js/bootstrap-datepicker',false,'js')?>
<?php getImport('bootstrap-datepicker','js/locales/bootstrap-datepicker.kr',false,'js')?>
<script>
    // 인텐트 지정 모달 
    var setIntentModal = '#modal-setIntent';

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
    
    // 대화상자 개별 온오프
    $(document).on('click','.btn_node_box',function(){
        $(this).toggleClass('off').promise().done(function() {
            Load_BotChart('node_conversation');
        });
    });
    
    // 대화내역 상세 버튼
    $("#btn_conv_list").on("click", function() {
        $("#node_conv").toggle();
    });
    
    // 페이징
    $(document).on('click', '.btn_page',function(){
        var page = $(this).data('page') ? $(this).data('page') : 1;
        $(":input:hidden[name=conv_page]").val(page);
        Load_BotChart('node_conversation');
    });
    
    $(document).on('click', '.btn_excel', function(){
        var form = $('#exportNodeForm');        
        $(form).find('input:hidden[name=linkType]').val($(this).attr('mod')); 
        if($(this).attr('mod') == 'node_conversation') {
            var nodeNames = $('.ul_node_box li span').not('.off').map(function() {return $(this).text();}).get().join('|');
            $(form).find('input:hidden[name=nodeNames]').val(nodeNames);
        }
        $(form).submit(); 
    });
    
    //-------------------------------------------
    function Load_BotChart(linkType) {
        var linkType = linkType == undefined || linkType == "" ? "node_analysis" : linkType;
        var nodeNames = '', page = '';
        if(linkType == 'node_conversation') {
            nodeNames = $('.ul_node_box li span').not('.off').map(function() {return $(this).text();}).get().join('|');
            page = $(":input:hidden[name=conv_page]").val();
        }
        $.post(rooturl+'/?r='+raccount+'&m=<?=$m?>&a=get_StatisticsChart',{
            linkType : linkType,
            vendor : '<?=$vendor?>',
            botuid : '<?=$botuid?>',
            d_start : $('input[name="d_start"]').val(),
            d_end : $('input[name="d_end"]').val(),
            nodeNames : nodeNames,
            page: page
        },function(response){
            checkLogCountdown();
            var result=$.parseJSON(response);
            
            if(linkType == 'node_analysis') {                
                $.each(result.total, function(key, value) {
                    $('#'+key).text(value);
                });
                $(".counter").counterUp({delay: 100, time: 1200});                
                $('#stateHtml').html(result.stateHtml);                
                getChartView(result.nodes);
                
            } else if(linkType == 'node_conversation') {
                $('#convHtml').html(result.convHtml);
                $('#pageHtml').html(result.pageHtml);
            }
        });
    }
    
    // 그래프 그리기
    const color = d3.scaleOrdinal(d3.schemeCategory10); //schemePaired, schemeCategory10, schemeTableau10, schemeAccent, schemeDark2, schemeSet1, schemeSet2, schemeSet3
    function getChartView(data) {
        $("#node_chart").empty();
        var myChart = Sunburst();
        myChart
            .data(data)
            .label('name')
            .size('size')
            .width(($("#node_chart").height()))
            .height(($("#node_chart").height()))        
            .color((d, parent) => color(parent ? parent.data.name : null))
            //.color('color')
            .tooltipContent((d, node) => {return '';})
            .onNodeClick(node => {
                getNodeBoxView(node);
            })
        (document.getElementById('node_chart'));
    }
    
    // 그래프 선택 대화상자 표시
    function getNodeBoxView(data) {
        if(data.length == 0) {
            $(".node_box_guide").show();
            $(".ul_node_box").hide();
        } else {
            var nodeHtml = "";
            $.each(data, function(i, node) {
                nodeHtml +="<li><span class='btn_node_box' noduid='"+node.nodeid+"'>"+node.name+"</span></li>"; 
            });
            
            $(".node_box_guide").hide();
            $(".ul_node_box").html(nodeHtml).show().promise().done(function() {
                Load_BotChart('node_conversation');
            });
        }
    }
    
    function getBoxHeight() {
        $(".nodeInfoWrap").css("height", ($(window).height() - 305)+"px");
    }
    
    getBoxHeight();
    Load_BotChart();
    
    
</script>