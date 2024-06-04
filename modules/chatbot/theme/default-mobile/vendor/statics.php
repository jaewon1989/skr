<!-- bootstrap css -->
<?php getImport('bootstrap','css/bootstrap',false,'css')?>
<script src="<?php echo $g['url_layout']?>/_js/chart.min.js"></script>
<input type="hidden" name="mod" value="month" />
<div class="cb-viewchat">
    <div class="cb-viewchat-search" style="height: auto;">
        <div class="cb-statistic-search">
            <ul class="cb-statistic-byrange" id="statics-ul">
                <li id="d-start">
                    <div class="input-daterange">
                        <input class="cb-viewchat-search-datebox" placeholder="시작일자" type="text" name="d_start">
                    </div>   
                </li>
                <li id="d-space">
                    ~
                </li>
                <li id="d-end" >
                   <div class="input-daterange">
                       <input class="cb-viewchat-search-datebox" placeholder="종료일자" type="text" name="d_end">
                    </div>
                </li>
                <li id="d-space">
                </li>
                <li class="cb-selected" data-role="btn-dateSearch">
                    조회
                </li>
            </ul>
        </div>
        <div class="cb-statistic-search">
            <ul class="cb-statistic-byrange" id="smod-ul">
                <li data-smod="day">
                    일간
                </li>
                <li data-smod="week">
                    주간
                </li>
                <li class="cb-selected" data-smod="month">
                    월간
                </li>
                <li id="d-space"></li>
                <li id="select-wrap">
                    <?php $_WHERE='vendor='.$V['uid'].' and type=1';?>
                    <?php $RCD = getDbArray($table[$m.'bot'],$_WHERE,'*','gid','asc','',1);?>
                    <div class="cb-viewchat-search-timebox" style="vertical-align:middle;height:100%;">
                        <select name="botuid" style="font-size:inherit;height:100%;">
                            <?php $i=1;while($R=db_fetch_array($RCD)):?>
                            <option value="<?php echo $R['uid']?>"><?php echo $R['service']?></option>
                            <?php $i++;endwhile?> 
                        </select>
                    </div>
                </li>
            </ul>
        </div>
        <div class="cb-statistic-box-wrapper">
            <h1>총 채팅창 유입수</h1>
            <div class="cb-statistic-box cb-statistic-incoming">
                 <div id="total-wrap">
                   <!-- load total chart -->
                 </div>
            </div>
        </div>

        <div class="cb-statistic-box-wrapper">
            <h1>유입 대상자</h1>
            <div class="cb-statistic-box cb-statistic-incoming">
                <div style="width: 100%;" id="gender-wrap">
                   <!-- load gender chart -->
                </div>
                <div id="age-wrap" style="padding-top:70px;">
                   <!-- load age chart -->
                </div>
            </div>
        </div>
    </div>
</div>
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
        $('#gender-wrap').html(gender_chart);
        $('#age-wrap').html(age_chart);
        //$('#total-wrap').html(response);

    }); 
}

// 일단,주간,월간 버튼 클릭 이벤트 
$('[data-smod]').on('click',function(){
    $('#smod-ul').find('li').removeClass('cb-selected');
    $(this).addClass('cb-selected');
    var mod = $(this).attr('data-smod');
    $('input[name="d_start"]').val('');
    $('input[name="d_end"]').val(''); 
    $('input[name="mod"]').val(mod);
    Load_BotChart();  
});

// 기간 검색버튼 클릭 이벤트 
$('[data-role="btn-dateSearch"]').on('click',function(){
    $('input[name="mod"]').val('');
    $('#smod-ul').find('li').removeClass('cb-selected');
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