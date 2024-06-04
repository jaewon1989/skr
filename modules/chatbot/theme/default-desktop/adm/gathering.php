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
<div class="container-fluid">
    <!--
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">통계/분석 > 대화 분석</h4>
        </div>
    </div>
    -->
    <div class="overview">
        <div class="page-title">군집 분석</div>
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

    <div class="row">
        <div class="col-sm-6">
            <div id="word_cloud" class="white-box" style="position:relative; min-height:720px;">

            </div>
        </div>

        <div class="col-sm-6 table-fluid">
            <div class="white-box" style="min-height:720px;">
                <input type="hidden" value="1" data-role="wordgroupPage-input"/>
                <h3 class="box-title" style="float:left;width:20%;">사용자 질문</h3>
                <h3 class="box-title" style="float:left;width:30%;">
                    <button class="btn btn-info" data-role="change-learnState" data-state="intent">인텐트 지정</button>
                </h3>
                <h3 id="wordBtn" class="box-title" style="float:right;width:40%;text-align:right;font-size:15px;">
                </h3>
                <div class="table-responsive table-container table-aicc-skin" style="clear:both;">
                    <table class="table" id="tbl-wordgroup">
                        <colgroup>
                            <col width="5%">
                            <col width="*">
                        </colgroup>
                        <thead>
                            <tr>
                                <th><input type="checkbox" data-role="select-all" data-parent="#tbl-wordgroup"/></th>
                                <th>질문내용</th>
                            </tr>
                        </thead>
                        <tbody id="wordHtml">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

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
                        <option value="">인텐트 선택</option>
                        <?php
                            $sql = "vendor='".$V['uid']."' and bot='".$botuid."' and hidden=0";
                            $ICD = getDbArray($table['chatbotintent'],$sql,'uid,name','name','asc','',1);
                            while($IT = db_fetch_array($ICD)) {
                                echo '<option value="'.$IT['uid'].'">'.$IT['name'].'</option>';
                            }
                        ?>
                     </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-role="btn-setIntent" data-depth="">저장하기</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <input type="hidden" name="unknownItemsUid" />
            </div>
        </div>
    </div>
</div>

<script src="<?php echo $g['url_layout']?>/_js/jquery.chosen.js"></script>
<link rel="stylesheet" href="<?php echo $g['url_layout']?>/_css/chosen.css">

<!-- 상단 통계숫자  에니메이션 카운팅 -->
<script src="<?php echo $g['url_layout']?>/_js/chart.min.js"></script>
<script src="<?php echo $g['url_layout']?>/_js/jquery.waypoints.js"></script>
<script src="<?php echo $g['url_layout']?>/_js/jquery.counterup.min.js"></script>

<link rel="stylesheet" href="<?php echo $g['url_layout']?>/_css/jqcloud.css">
<script src="<?php echo $g['url_layout']?>/_js/jqcloud.min.js"></script>

<?php getImport('bootstrap-datepicker','css/datepicker3',false,'css')?>
<?php getImport('bootstrap-datepicker','js/bootstrap-datepicker',false,'js')?>
<?php getImport('bootstrap-datepicker','js/locales/bootstrap-datepicker.kr',false,'js')?>
<script>
    //------------------------------------------
    // 인텐트 지정 모달
    var setIntentModal = '#modal-setIntent';
    var module='<?php echo $m?>';
    var vendor='<?php echo $V['uid']?>';
    var botuid='<?php echo $botuid?>';

    // 선택박스 체크 이벤트 핸들러
    $('[data-role="select-all"]').click(function(){
        var parent = $(this).data('parent');
        $(parent).find('tbody [data-role="checkbox"]').prop("checked",$(this).prop("checked"));
    });

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
        var mod = 'wordgroup';
        var d_start = $('input[name="d_start"]').val();
        var d_end = $('input[name="d_end"]').val();
        $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=get_StatisticsChart',{
            linkType : 'wordgroup',
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
            /*
            $(".counter").counterUp({
                delay: 100, time: 1200
            });
            */

            $("#word_cloud").jQCloud('destroy');
            $("#word_cloud").jQCloud(result.aWordJson, {
                autoResize: true,
                afterCloudRender: function() {
                    $("#word_cloud a:eq(0)").css("color", "#ff7600");
                }
            });
            $('#wordHtml').html(result.wordHtml);
            $('#wordBtn').html(result.wordBtn);
        });
    }

    var getWordGroupList = function(data){
        var keyword = data.keyword;
        var mod = 'wordgroup';
        var page = data.page?data.page:$('[data-role="'+mod+'Page-input"]').val();
        var pageWrapper = $('[data-role="'+mod+'Page-wrapper"]');
        var listWrapper = $('[data-role="'+mod+'List-wrapper"]');
        var d_start = $('input[name="d_start"]').val();
        var d_end = $('input[name="d_end"]').val();

        $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=get_StatisticsChart',{
            linkType : 'page_data',
            vendor : '<?php echo $V['uid']?>',
            botuid : '<?php echo $botuid?>',
            mod : mod,
            page : page,
            d_start : d_start,
            d_end : d_end,
            keyword : keyword
        },function(response){
            checkLogCountdown();
            var result=$.parseJSON(response);
            $('#wordHtml').html(result.chHtml);
            $('#wordBtn').html(result.chBtn);
        });
    }

    // 페이징
    $(document).on('click','[data-role="btn-paging"]',function(){
        var mod = 'wordgroup';
        var data = $(this).data();
        var keyword = data.keyword;
        var page = $(this).data('page')?$(this).data('page'):1;
        $('[data-role="'+mod+'Page-input"]').val(page);
        var data = {"mod":mod,"page":page, "keyword":keyword};
        getWordGroupList(data);
    });

    // 단어 클릭
    $(document).on('click','#word_cloud a',function(e){
        e.preventDefault();

        $("#word_cloud a").attr("style", "");
        $(this).css("color", "#ff7600");

        var keyword = $(this).text();
        var mod = 'wordgroup';
        var page = 1;
        var data = {"mod":mod,"page":page, "keyword":keyword};
        getWordGroupList(data);
    });

    // 답변 못한 문장 리스트 > 페이징
    $(document).on('click','[data-role="btn-paging"]',function(){
        var mod = 'wordgroup';
        var page = $(this).data('page')?$(this).data('page'):1;
        var keyword = $(this).data('keyword');
        var data = {"mod":mod,"page":page, "keyword":keyword};
        getWordGroupList(data);
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

    //-------------------------------------------
    $(document).on('click','[data-role="change-learnState"]',function(){
        var state = $(this).data('state');
        var page = $('[data-role="unknownPage-input"]').val();
        var unknownItems = $(document).find('input[name="unknownItem[]"]:checked').map(function(){return $(this).val()}).get();
        var data = {"state": state,"unknownItems":unknownItems};
        if(unknownItems.length==0){
            alert('질문을 선택해주세요');
            return false;
        }
        // 인텐트 지정
        if(state=='intent'){
           getIntentModal(data);
        }
    });

    // 인텐트 지정 프로세스 진행 함수
    var setIntent = function(data){
        $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=update_log_intent',{
            vendor : vendor,
            botuid : botuid,
            intent : data.intent,
            unknownItemsUid : data.unknownItemsUid
        },function(response){
            checkLogCountdown();
            var result=$.parseJSON(response);
            alert("인텐트 지정되었습니다.");
            $(setIntentModal).modal('hide');
            $('input:checkbox').prop("checked", false);
        });
    }

    // 인텐트 지정 모달 호출
    var getIntentModal = function(data){
        checkLogCountdown();
        $(setIntentModal).modal();
        $(setIntentModal).find('input[name="unknownItemsUid"]').val(data.unknownItems);
    }

    // 인텐트 지정내용 저장
    $(document).on('click','[data-role="btn-setIntent"]',function(){
        var intent = $(".chosen-select").chosen().val();// $(setIntentModal).find('input[name="intent"]').val();
        var unknownItemsUid = $(setIntentModal).find('input[name="unknownItemsUid"]').val();
        if(intent==''){
            alert('인텐트를 선택해주세요.');
            return false;
        }else{
            var data = {"intent":intent,"unknownItemsUid":unknownItemsUid};
            setIntent(data); // 인텐트 지정 프로세스 진행
        }
    });

    // 최초 로딩시
    $(document).ready(function(){
        $('[data-toggle=tooltip]').tooltip();
        Load_BotChart();
    });
</script>