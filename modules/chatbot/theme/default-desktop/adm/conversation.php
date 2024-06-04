<?php
$recnum = 10;

$dToday = date("Y-m-d", time());
$aDate = explode("-", $dToday);
$dYear = $aDate[0];
$dMonth = $aDate[1];
$dDay = $aDate[2];
$aLogDate = array();
$aLogDate[] = array("어제", date("Y-m-d", mktime(0, 0, 0, $dMonth, $dDay - 1, $dYear)), date("Y-m-d", mktime(0, 0, 0, $dMonth, $dDay - 1, $dYear)));
$aLogDate[] = array("오늘", $dToday, $dToday);
$aLogDate[] = array("일주", date("Y-m-d", mktime(0, 0, 0, $dMonth, $dDay - 7, $dYear)), $dToday);
$aLogDate[] = array("한달", date("Y-m-d", mktime(0, 0, 0, $dMonth - 1, $dDay, $dYear)), $dToday);
$aLogDate[] = array("당월", date('Y-m') . "-01", $dToday);
$aLogDate[] = array("전체", "", "");
$d_start = isset($_GET['d_start']) ? $_GET['d_start'] : date("Y-m-d", mktime(0, 0, 0, $dMonth, $dDay - 30, $dYear));
$d_end = isset($_GET['d_end']) ? $_GET['d_end'] : $dToday;

$due = isset($_GET['due']) ? $_GET['due'] : 3;
$chField = isset($_GET['chField']) ? $_GET['chField'] : '';
$chFind = isset($_GET['chFind']) ? $_GET['chFind'] : '';

/*if($d_start || $d_end){
    if($d_start){
        $s_val = explode(' ',$d_start);
        $s_hm = $s_val[0];
        $s_ap = $s_val[1];
        $s_hm_arr = explode(':',$s_hm);
        $s_h = $s_hm_arr[0];// 시간
        $s_m = $s_hm_arr[1];// 분
        if($s_ap=='PM') $last_s_h = $s_h+12;
        else $last_s_h = $s_h;
        $last_s_hm = $last_s_h.$s_m;
    }
    if($d_end){
        $e_val = explode(' ',$d_end);
        $e_hm = $e_val[0];
        $e_ap = $e_val[1];
        $e_hm_arr = explode(':',$e_hm);
        $e_h = $e_hm_arr[0];// 시간
        $e_m = $e_hm_arr[1];// 분
        if($e_ap=='PM') $last_e_h = $e_h+12;
        else $last_e_h = $e_h;
        $last_e_hm = $last_e_h.$e_m;
    }
}*/

$vendor = $V['uid'];

if($d_start) $_WHERE = ' A.d_regis > '.str_replace('-','',$d_start).'000000';
if($d_end) $_WHERE .= ' and A.d_regis < '.str_replace('-','',$d_end).'235959';
if($botuid) $_WHERE .=' and A.bot='.$botuid;


if($chFind != ''){
    if($chField != ''){
        $_WHERE .=' and A.'.$chField.'=\''.$chFind.'\'';
    }
}

$_WHERE .= " and A.vendor=".$vendor." and A.bot=".$bot." and A.roomToken <> '' group by A.roomToken, left(A.d_regis, 8) ";

$query = "Select A.uid, A.userName, A.userUid, A.d_regis, A.roomToken, count(*) as nCntChat From ".$table[$m.'chatLog']." A ";
//$query .= "join ".$table[$m.'bot']." B on A.bot = B.uid ";
$query .="Where ".$_WHERE." ";
$query .="Order by A.uid DESC ";
$query .="Limit ".($p-1)*$recnum.", ".$recnum." ";
$RCD = db_query($query, $DB_CONNECT);

$query = "Select count(*) as nCnt From ( ";
$query .="  Select count(*) From ".$table[$m.'chatLog']." A Where ".$_WHERE." ";
$query .=") as A ";
$NCD = db_fetch_assoc(db_query($query, $DB_CONNECT));
$NUM = $NCD['nCnt'];

$TPG = getTotalPage($NUM,$recnum);
?>
<link href="<?php echo $g['url_layout']?>/_css/cb.css" rel="stylesheet">
<link href="<?php echo $g['url_module_skin']?>/css/chatLog.css" rel="stylesheet">
<link href="<?php echo $g['s']?>/_core/css/form.css" rel="stylesheet">
<!-- bootstrap css -->

<input type="hidden" name="mod" value="month" />

<div class="container-fluid table-fluid">
    <!--
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><?php echo $pageTitle?></h4>
        </div>
    </div>
    -->
    <div class="overview">
        <div class="page-title">대화 로그</div>
        <div class="sub-frame">
            <div class="sub-title">SK telecom AICC / <?php echo $pageTitle?></div>
        </div>
    </div>
    <div style="margin: 15px 25px;">
        <div style="text-align: right;">
            <form class="form-horizontal rb-form" id="frmSearch" name="frmSearch" >
                <input type="hidden" name="vendor" value="<?=$vendor?>">
                <input type="hidden" name="bot" value="<?=$bot?>">
                <input type="hidden" name="p" value="<?=$p?>">
                <input type="hidden" name="due">
                <input type="hidden" name="linkType" value="search">
                <div class="frmSearch">
                    <div>
                        <div>
                            <div style="display:inline-block; width:40%;">
                                <div id="datepicker" class="input-daterange input-group bot_log">
                                    <input type="text" class="form-control" id="d_start" name="d_start" placeholder="시작일 선택" autocomplete="off" value="<?=$d_start?>">
                                    <span class="input-group-addon">~</span>
                                    <input type="text" class="form-control" id="d_end" name="d_end" placeholder="종료일 선택" autocomplete="off" value="<?=$d_end?>">
                                    <span class="input-group-btn">
                                </span>
                                </div>
                            </div>
                            <div style="display:inline-block; width:20%;">
                                <span class="input-group-btn log_btn">
                                    <?
                                        $idx = 0;
                                        foreach($aLogDate as $aDate) {
                                    ?>
                                        <button type="button" class="btn btn-default <?=($due == $idx ? 'btn-primary' : '')?>" sDate="<?=$aDate[1]?>" eDate="<?=$aDate[2]?>"><?=$aDate[0]?></button>
                                    <?
                                            $idx++;
                                        }
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <select id="chField" name="chField" class="form-control">
                            <option value="userName" <?=$chField == 'userName' ? 'selected': ''?>>전화번호</option>
                            <option value="roomToken" <?=$chField == 'roomToken' ? 'selected': ''?>>룸토큰</option>
                        </select>
                        <input type="text" id="chFind" name="chFind" class="form-control" value="<?=$chFind?>">
                        <button id="btn_search" class="btn btn-default">검색</button>
                    </div>
                </div>
            </form>
        </div>

    </div>
    <!-- /.row -->
    <div class="table-container table-aicc-skin">
        <div class="intEntTable-wrapper">
            <div class="table-wrapper" data-role="table-wrapper">
                <table class="table table-full" id="tbl-conversation" data-role="tbl-conversation" style="text-align: center;"><!-- table-striped -->
                    <thead>
                        <tr class="table-header">
                            <th class="intEnt-chk" style="text-align:center;"><input type="checkbox" data-role="select-all" data-parent="#tbl-conversation"/></th>
                            <th class="intEnt-name" style="text-align:center;">아이디(전화번호/룸토큰)</th>
                            <th class="intEnt-des" style="text-align:center;">시작일시</th>
                            <th class="intEnt-des" style="text-align:center;">종료일시</th>
                            <th class="intEnt-ex" style="text-align:center;">대화흐름 수</th>
                            <th class="intEnt-ex" style="text-align:center;">응답못한 대화</th>
                            <th class="intEnt-ex" style="text-align:center;">채팅내역</th>
                            <th class="intEnt-ex" style="text-align:center;">상세로그</th>
                        </tr>
                    </thead>
                    <?php while($R = db_fetch_array($RCD)):?>
                        <?php
                            $userPic = $chatbot->getUserAvatar($R['userUid'],'src');
                            $chatUrl = $chatbot->getChatUrl($R);
                            $userName = $chatbot->getUserName($R['userUid']);

                            $_where = "vendor=".$vendor." and bot=".$bot." and roomToken='".$R['roomToken']."' and left(d_regis, 8) = '".$R['d_regis']."' and is_unknown=1";
                            $nCntUnknown = getDbRows($table[$m.'chatLog'], $_where);

                            $query = "Select max(d_regis) as d_regis From ".$table[$m.'botChatLog']." Where bot=".$bot." and roomToken='".$R['roomToken']."'";
                            $endTime = db_fetch_assoc(db_query($query, $DB_CONNECT));
                            $endTime = $endTime['d_regis'];
                        ?>
                    <tbody>
                        <tr>
                            <?php $example = getDbRows($table[$m.'intentEx'],'intent='.$row['uid'],'uid')?>
                            <td class="intEnt-chk"><input type="checkbox" data-role="select-all" data-uid="<?php echo $row['uid']?>"/></td>
                            <td class="txt-oflo intEnt-name">
                                <img src="<?php echo $userPic?>" alt="viewchat search result" class="conver-userPic" />
                                <span class="cb-name"><?php echo $userName.'('.($R['userName'] ? $R['userName'] : $R['roomToken']).')'?></span>
                            </td>
                            <td class="txt-oflo"><?php echo date("Y-m-d H:i:s", strtotime($R['d_regis']))?></td>
                            <td class="txt-oflo"><?php echo date("Y-m-d H:i:s", strtotime($endTime))?></td>
                            <td class="txt-oflo"><?=number_format($R['nCntChat'])?></td>
                            <td class="txt-oflo"><?=number_format($nCntUnknown)?></td>
                            <td class="txt-oflo">
                                <a href="#" data-toggle="modal" data-target="#modal-chatbox" data-role="getComponent" data-id="<?php echo $bot.'-'.$R['userUid'].'-'.$R['roomToken']?>" data-markup="userChatBox" class="cb-button">채팅내역</a>
                            </td>
                            <td class="txt-oflo">
                                <a href="#" data-role="getComponent" data-id="<?php echo $bot.'-'.$R['userUid'].'-'.$R['roomToken']?>" data-markup="userChatLog" class="cb-button">상세로그 <i class="fa fa-angle-down"></i></a>
                            </td>
                        </tr>
                        <tr class="chatLog">
                            <td colspan="7">
                                <div class="chatLogWrap">
                                    <table class="tbHeader">
                                        <colgroup>
                                            <col width="5%">
                                            <col width="15%">
                                            <col width="8%">
                                            <col width="7%">
                                            <col width="20%">
                                            <col width="10%">
                                            <col width="7%">
                                            <col width="28%">
                                            <col width="17">
                                        </colgroup>
                                        <tr>
                                            <th>발화타입</th>
                                            <th>발화</th>
                                            <th>인텐트</th>
                                            <th>매칭률</th>
                                            <th>엔터티</th>
                                            <th>대화상자</th>
                                            <th>응답타입</th>
                                            <th>응답</th>
                                            <th></th>
                                        </tr>
                                    </table>
                                    <div id="logListWrap" class="logListWrap">
                                        <table class="tbLogList">
                                            <colgroup>
                                                <col width="5%">
                                                <col width="15%">
                                                <col width="8%">
                                                <col width="7%">
                                                <col width="20%">
                                                <col width="10%">
                                                <col width="7%">
                                                <col width="28%">
                                            </colgroup>
                                            <tbody class="tBodyLogList">
                                                <tr>
                                                    <td>텍스트</td>
                                                    <td class="aleft">어떤 회사인지 회사소개나 연혁을 알고 싶어요</td>
                                                    <td>회사소개</td>
                                                    <td>78.2%</td>
                                                    <td class="aleft">
                                                        회사소개: 연혁, 회사소개: 회사소개
                                                    </td>
                                                    <td>기업소개</td>
                                                    <td>텍스트</td>
                                                    <td class="aleft">저희 회사에 대해서 소개해드리겠습니다. 아래의 버튼을 누르시면 자세한 설명을 보실 수 있습니다.</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <?php endwhile?>
                </table>
                <div class="text-center pt" >
                    <ul class="pagination pagination-sm">
                        <script>getPageLink(5,<?php echo $p?>,<?php echo $TPG?>,'');</script>
                    </ul>
                </div>
            </div>
            <div class="right-panel" data-role="rightPanel">
                <div class="rightPanel-inner">
                    <span data-role="close-rightPanel" class="btn-close">
                        <span class="cb-icon cb-icon-close"></span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="modal-chatbox" class="modal fade" style="z-index:99999;">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div id="chatBox-container" class="modal-body" data-role="content" style="padding:0;"></div>
        </div>
    </div>
</div>

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

$('#btn_search').on('click', function(){
    $('.log_btn button').each(function(index){
        if($(this).hasClass("btn-primary")){
            $("input[name=due]").val(index);
        }
    });

    $('#frmSearch').submit();
});

$('.log_btn .btn').on('click',function(){
    $('.btn_search, .log_btn .btn').removeClass("btn-primary");
    $(this).addClass('btn-primary');

    $('input[name="d_start"]').val($(this).attr("sDate"));
    $('input[name="d_end"]').val($(this).attr("eDate"));
});

$('[data-role="btn-search"]').on('click',function(){
   var f = document.procForm;
   f.submit();
});
var module = '<?php echo $m?>';
// 컴포넌트 호출시 해당 페이지 세팅하는 함수
$(document).on('click','[data-role="getComponent"]',function(){
    var $this = $(this);
    var component = $this.attr('data-toggle');
    var mod = $this.attr('data-mod'); // 쪽지인 경우 write, view, list
    var position = $this.attr('data-position'); // 쪽지인 경우 write, view, list
    var title = $this.attr('data-title'); // 제목 : share 이벤트시 필요
    var markup = $this.attr('data-markup');
    var target = $this.attr('data-target'); // 액션대상 PK (회원,피드,댓글...)
    var register = $this.attr('data-register'); // 피드, 댓글인 경우 등록자 PK
    var registerid = $this.attr('data-registerid'); // 피드, 댓글인 경우 등록자 userid
    var need_login = ['paper','regis','recommended','notice','invite','settings']
    var uid = $this.attr('data-uid'); // 대상 PK
    var id = $this.attr('data-id');
    var addData=null;
    if($.inArray(markup,need_login)!=-1){
        if(memberid ==''){
           alert('로그인을 먼저 해주세요. ');
           //$(target).modal('hide'); // paper-write 페이지 호출방지
           $(modal_login).modal();
           return false;
        }
    }
    if(markup=='share'){
        var meta_title= $this.attr('data-metaTitle');
        var meta_url= $this.attr('data-metaUrl');
        var meta_desc= $this.attr('data-metaDesc');
        var meta_img= $this.attr('data-metaImg');
        var dataArray={"meta_title":meta_title,"meta_url":meta_url,"meta_desc":meta_desc,"meta_img":meta_img};
        addData = JSON.stringify(dataArray);

        // 메타정보 변경
        setMetaContent(meta_title,meta_url,meta_desc,meta_img);
    }

    if(markup == 'userChatLog') {
        if($this.children('i').hasClass('fa-angle-up')) {
            $this.children('i').removeClass('fa-angle-up').addClass('fa-angle-down');
            $this.parents('tr').next('tr').removeClass('active');
            return false;
        }
    }

    $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=get_Component_Page',{
        title : title,
        uid : uid,
        id : id,
        register : register,
        markup : markup,
        mod : mod,
        position : position,
        addData : addData,
        vendor : '<?=$vendor?>',
        bot : '<?=$bot?>'
    },function(response){
        checkLogCountdown();
        var result = $.parseJSON(response);
        var content=result.content;

        if(markup == 'userChatBox') {
            var chat_info = result.chat_info;
            $(target).find('[data-role="content"]').html(content).promise().done(function() {
                $('.cb-chatting-sender img').on("error", function (){
                    this.src = '/_core/images/bot_avatar_blank.png';
                });
                $('[data-role="cardType-resItem"]').parent().parent().attr('data-centeredslides', false);
                RC_initSwiper();
            });
        } else if(markup == 'userChatLog') {
            $this.parents('tr').next('tr').find('.tBodyLogList').html(content).promise().done(function() {
                $('table[data-role=tbl-conversation] tr.chatLog').removeClass('active');
                $('table[data-role=tbl-conversation] td [data-markup=userChatLog] i').removeClass('fa-angle-up').addClass('fa-angle-down');

                $this.children('i').removeClass('fa-angle-down').addClass('fa-angle-up');
                $this.parents('tr').next('tr').addClass('active');
            });
        }
    });
});

$(document).ready(function(){
    $('.table-wrapper').height(($(window).height()-235)+'px');
    $(document).on('click', '[data-role=chat-exit]', function() {
        $('#modal-chatbox').modal("hide");
    });
});
</script>