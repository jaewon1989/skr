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
    .nodeInfoWrap {height:500px;}
    .conv_btn_wrap {margin-top:15px; text-align:center;}
    .ul_node_box {position:relative;}
    .ul_node_box li {float:left; position:relative; margin:1px 17px 1px 0;}
    .ul_node_box li:last-child {margin-right:0;}
    .ul_node_box li span {display:block; line-height:25px; background:#829aaf; color:#fff; font-size:12px; border-radius:5px; padding:0 8px;}
    .ul_node_box li span:after {position:absolute; right:-15px; color:#333; font-size:12px; content:'▶'}
    .ul_node_box li:last-child span:after {content:'';}
    .ul_node_box:after {clear:both; display:block; content:'';}
    
    .node rect {fill-opacity:.9; shape-rendering:crispEdges; stroke-width:0; cursor:pointer;}
    .node text {text-shadow:0 1px 0 rgba(255,255,255,0.5);}
    .link {fill:none; stroke:#000; stroke-opacity:.1;}
    .link:hover {stroke-opacity:.5 !important;}
</style>

<div class="container-fluid">
    <!--
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">통계/분석 > 대화 흐름 분석</h4>
        </div>
    </div>
    -->
    <div class="overview">
        <div class="page-title">대화 흐름 분석</div>
        <div class="sub-frame">
            <div class="sub-title">SK telecom AICC / <?php echo $pageTitle?></div>
        </div>
    </div>
    <!-- row -->
    <div class="row">
        <div class="col-md-12">
            <form class="form-horizontal rb-form" name="searchForm">
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
        <div class="col-md-12">
             <div class="white-box pd15">
                <div id="node_chart" class="white-box nodeInfoWrap" style="padding:0;">            
                </div>
             </div>
        </div>
    </div>
    
    <div class="row chart_status_board">
        <!-- 리스트 -->
        <div class="col-md-12">
            <div class="white-box pd15">
                <div class="listWrap" style="margin-top:25px;">
                    <h3 class="box-title" style="float:left;width:20%;">
                        대화 흐름
                        <span id="pageHtml" style="display:inline-block; margin-left:30px;"></span>
                        <input type="hidden" name="conv_page" value="1" /> 
                        <input type="hidden" name="hTokens" value="" /> 
                    </h3>
                    <h3 class="box-title" style="float:right;width:40%;text-align:right;font-size:15px;">
                        <button class="btn btn-info btn_excel" mod="node_flow">엑셀파일 다운로드</button>
                    </h3>
                    <div class="fixed_table_container table-container table-aicc-skin" style="width:100%;">
                        <div class="fixed_table_header_bg"></div>
                        <div class="fixed_table_wrapper">
                            <table class="fixed_table">
                                <thead>                            
                                    <tr>
                                        <th style="width:9%;"><div class="th_text">룸토큰</div></th>
                                        <th style="width:8%;"><div class="th_text">채널</div></th>
                                        <th style="width:8%;"><div class="th_text">날짜</div></th>
                                        <th style="width:8%;"><div class="th_text">시작</div></th>
                                        <th style="width:8%;"><div class="th_text">종료</div></th>
                                        <th style="width:8%;"><div class="th_text">이용</div></th>
                                        <th style="width:8%;"><div class="th_text">종료구분</div></th>
                                        <th style="width:43%;"><div class="th_text">대화흐름</div></th>                                        
                                    </tr>                            
                                </thead>
                                <tbody id="convHtml"></tbody>
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
    <input type="hidden" name="d_start" value="">
    <input type="hidden" name="d_end" value="">
</form>

<script src="<?php echo $g['url_layout']?>/_js/d3.v3.min.js"></script>
<script src="<?php echo $g['url_layout']?>/_js/sankey.js"></script>
<script src="<?php echo $g['url_layout']?>/_js/d3.chart.min.js"></script>
<script src="<?php echo $g['url_layout']?>/_js/d3.chart.sankey.min.js"></script>

<?php getImport('bootstrap-datepicker','css/datepicker3',false,'css')?>
<?php getImport('bootstrap-datepicker','js/bootstrap-datepicker',false,'js')?>
<?php getImport('bootstrap-datepicker','js/locales/bootstrap-datepicker.kr',false,'js')?>
<script>
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

        $("#d_start").val($(this).attr("sDate"));
        $("#d_end").val($(this).attr("eDate"));
        $(":input:hidden[name=conv_page]").val(1);
        $(":input:hidden[name=hTokens]").val("");
        Load_BotChart();
    });
    $('.btn_search').on('click',function(){
        if(!$("#d_start").val()) {
            alert('검색 시작일을 선택해주세요.'); return false;
        }
        if(!$("#d_end").val()) {
            alert('검색 종료일을 선택해주세요.'); return false;
        }
        $(":input:hidden[name=conv_page]").val(1);
        $(":input:hidden[name=hTokens]").val("");
        Load_BotChart();  
    });

    // 기간 검색버튼 클릭 이벤트 
    $('[data-role="btn-search"]').on('click',function(){
        $('input[name="mod"]').val('');
        $('.cb-statistic-byrange').find('li').removeClass('cb-selected');        
        Load_BotChart();
    });
    
    // 페이징
    $(document).on('click', '.btn_page',function(){
        var page = $(this).data('page') ? $(this).data('page') : 1;
        $(":input:hidden[name=conv_page]").val(page);
        Load_BotChart('node_flow');
    });
    
    $(document).on('click', '.btn_excel', function(){
        var form = $('#exportNodeForm');        
        $(form).find('input:hidden[name=linkType]').val($(this).attr('mod'));
        $(form).find('input:hidden[name=d_start]').val($("#d_start").val());
        $(form).find('input:hidden[name=d_end]').val($("#d_end").val());
        $(form).submit(); 
    });
    
    //-------------------------------------------
    function Load_BotChart() {
        var linkType = "node_flow";
        var page = $(":input:hidden[name=conv_page]").val();
        var hTokens = $(":input:hidden[name=hTokens]").val();
        
        $.post(rooturl+'/?r='+raccount+'&m=<?=$m?>&a=get_StatisticsChart',{
            linkType : linkType,
            vendor : '<?=$vendor?>',
            botuid : '<?=$botuid?>',
            d_start : $("#d_start").val(),
            d_end : $("#d_end").val(),
            hTokens : hTokens,
            page: page
        },function(response){
            checkLogCountdown();
            var result=$.parseJSON(response);
            if(hTokens == "" && result.hasOwnProperty('node_json')) {
                getChartView(result.node_json);
            }
            $('#convHtml').html(result.convHtml);
            $('#pageHtml').html(result.pageHtml);
        });
    }
    
    // 그래프 그리기
    function getChartView(json) {
        $("#node_chart").empty();
        
        var margin = {top: 1, right: 1, bottom: 6, left: 1},
            width = $("#node_chart").width() - margin.left - margin.right,
            height = $("#node_chart").height() - margin.top - margin.bottom;
            
        var formatNumber = d3.format(",.0f"),
            format = function(d) { return formatNumber(d); },
            color = d3.scale.category20();
            
        var svg = d3.select("#node_chart").append("svg")
            .attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
            .append("g")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");
                
        var sankey = d3.sankey()
            .nodeWidth(15)
            .nodePadding(10)
            .size([width, height]);
            
        var path = sankey.link();
        sankey
            .nodes(json.nodes)
            .links(json.links)
            .layout(32);
            
        var link = svg.append("g").selectAll(".link")
            .data(json.links)
            .enter().append("path")
            .attr("class", "link")
            .attr("d", path)
            .attr("id", function(d,i){
                d.id = i;
                return "link-"+i;
            })
            .style("stroke-width", function(d) { return Math.max(1, d.dy); })
            .sort(function(a, b) { return b.dy - a.dy; });
            
        link.append("title")
            .text(function(d) { return d.source.name + "->" + d.target.name; });
        link.on("click", function(d) { console.log(d.source.name + "->" + d.target.name);});
            
        var node = svg.append("g").selectAll(".node")
            .data(json.nodes)
            .enter().append("g")
            .attr("class", "node")
            .attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; })
            .on("click",highlight_node_links)
            .call(d3.behavior.drag()
            .origin(function(d) { return d; })
            // interfering with click .on("dragstart", function() { this.parentNode.appendChild(this); })
            .on("drag", dragmove));
            
        node.append("rect")
            .attr("height", function(d) { return d.dy; })
            .attr("width", sankey.nodeWidth())
            .style("fill", function(d) { return d.color = color(d.name.replace(/ .*/, "")); })
            .style("stroke", function(d) { return d3.rgb(d.color).darker(2); })
            .append("title")
            .text(function(d) { return d.name; });
            
        node.append("text")
            .attr("x", -6)
            .attr("y", function(d) { return d.dy / 2; })
            .attr("dy", ".35em")
            .attr("text-anchor", "end")
            .attr("transform", null)
            .text(function(d) { return d.name; })
            .filter(function(d) { return d.x < width / 2; })
            .attr("x", 6 + sankey.nodeWidth())
            .attr("text-anchor", "start");
            
        function dragmove(d) {
            d3.select(this).attr("transform", "translate(" + d.x + "," + (d.y = Math.max(0, Math.min(height - d.dy, d3.event.y))) + ")");
            sankey.relayout();
            link.attr("d", path);
        }
        
        function highlight_node_links(node,i){
            var remainingNodes=[],
            nextNodes=[];
            
            $("svg .node").attr("data-clicked", "0");
            $("svg path.link").css("stroke-opacity", "0.1");
    
            d3.select(this).attr("data-clicked","1");
            var stroke_opacity = 0.5;
            
            var traverse = [{
                linkType : "sourceLinks",
                nodeType : "target"
            },{
                linkType : "targetLinks",
                nodeType : "source"
            }];
            
            var aLinkRoomToken = [];
            
            traverse.forEach(function(step){
                node[step.linkType].forEach(function(link) {
                    remainingNodes.push(link[step.nodeType]);
                    highlight_link(link.id, stroke_opacity);
                });
                
                while (remainingNodes.length) {
                    nextNodes = [];
                    remainingNodes.forEach(function(node) {
                        node[step.linkType].forEach(function(link) {
                            nextNodes.push(link[step.nodeType]);
                            highlight_link(link.id, stroke_opacity);
                            if(aLinkRoomToken.indexOf(link.token) == -1) {
                                aLinkRoomToken.push(link.token);
                            }
                        });
                    });
                    remainingNodes = nextNodes;
                }
            });
            
            // 노드 클릭시 해당 노드 관련 roomToken 조회
            if( d3.select(this).attr("data-clicked") == "1" ){
                $(":input:hidden[name=conv_page]").val(1);
                $(":input:hidden[name=hTokens]").val(aLinkRoomToken.toString());                
            } else {
                $(":input:hidden[name=hTokens]").val("");
            }
            Load_BotChart();
        }
        
        function highlight_link(id,opacity){
            d3.select("#link-"+id).style("stroke-opacity", opacity);
        }
    }
    
    Load_BotChart();
</script>